<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ayurvedamodel extends CI_Model {

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

    public function ayurveda_list() {

        $query = $this->db->query("SELECT id,user_id,ayurveda_name,profile_pic FROM ayurveda WHERE is_approval='1' AND is_active='1' order by id desc");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $listing_id = $row['user_id'];
                $ayurveda_name = $row['ayurveda_name'];
                $profile_pic = $row['profile_pic'];

                if ($profile_pic != '') {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/ayurveda_images/' . $profile_pic;
                } else {
                    $profile_pic = '';
                }

                $ayurveda_views = $this->db->select('id')->from('ayurveda_view')->where('ayurveda_id', $listing_id)->get()->num_rows();
                $query = $this->db->query("SELECT ROUND(AVG(rating),1) AS avg_rating FROM ayurveda_review WHERE ayurveda_id='$listing_id' ");
                $row = $query->row_array();
                $total_review = $row['avg_rating'];

                $resultpost[] = array(
                    'listing_id' => $listing_id,
                    'listing_type' => "1",
                    'ayurveda_name' => $ayurveda_name,
                    'profile_pic' => $profile_pic,
                    'ayurveda_views' => $ayurveda_views,
                    'total_review' => $total_review,
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function ayurveda_home($listing_id) {

        $query_banner = $this->db->query("SELECT image FROM `ayurveda_banner` WHERE user_id='$listing_id'");
        $get_banner = $query_banner->row_array();
        $banner_image = $get_banner['image'];
        $banner_image = array_filter(explode(",", $banner_image));
        $cnt = count($banner_image);
        for ($i = 0; $i < $cnt; $i++) {
            $banner_array[] = array(
                "image" => 'https://d2c8oti4is0ms3.cloudfront.net/images/ayurveda_images/' . $banner_image[$i]
            );
        }



        $query_new_arrival = $this->db->query("SELECT p.id AS product_id,p.category AS category_id,p.sub_category AS sub_category_id,p.product_name ,p.product_price,p.quantity,p.product_description,p.how_to_use,p.p_code,p.discount,p.in_stock,p.image,ac.category,asb.subcategory FROM product as p LEFT JOIN ayurveda_category AS ac ON (p.category=ac.id) LEFT JOIN ayurveda_subcategory AS asb ON (p.sub_category=asb.id) WHERE p.user_id='$listing_id' order by p.id desc LIMIT 0,12");
        $count_new_arrival = $query_new_arrival->num_rows();
        if ($count_new_arrival > 0) {
            foreach ($query_new_arrival->result_array() as $row) {
                $image_array = '';
                $category_id = $row['category_id'];
                $sub_category_id = $row['sub_category_id'];
                $product_id = $row['product_id'];
                $product_code = $row['p_code'];
                $category = $row['category'];
                $subcategory = $row['subcategory'];
                $product_description = $row['product_description'];
                $product_name = $row['product_name'];
                $product_price = $row['product_price'];
                $availibility = $row['in_stock'];

                if ($availibility == '1') {
                    $availibility = 'In Stock';
                } else {
                    $availibility = 'Out of Stock';
                }

                $image = $row['image'];

                $image = array_filter(explode(",", $image));
                $cnt = count($image);
                for ($i = 0; $i < $cnt; $i++) {
                    $image_array[] = array(
                        "image" => 'https://d2c8oti4is0ms3.cloudfront.net/images/ayurveda_images/' . $image[$i]
                    );
                }

                $discount = $row['discount'];
                $discount_price = $product_price - ($product_price * ($discount / 100));

                $review = $this->db->select('id')->from('ayurveda_product_review')->where('ayurveda_id', $listing_id)->where('product_id', $product_id)->get()->num_rows();

                $query_product = $this->db->query("SELECT  ROUND(AVG(rating),1) AS avg_rating FROM ayurveda_product_review WHERE ayurveda_id='$listing_id' AND  product_id='$product_id' ");
                $row_prod = $query_product->row_array();
                $rating = $row_prod['avg_rating'];
                if ($rating === NULL) {
                    $rating = '0';
                }

                $product_description = str_replace('’', "'", $product_description);
                $product_description = "<style>strong{color:#404547;font-weight:400}p{color:#768188;text-align:justify;}</style>" . $row['product_description'];
                $how_to_use = $row['how_to_use'];

                $product_view = $this->db->select('id')->from('ayurveda_product_view')->where('ayurveda_id', $listing_id)->where('product_id', $product_id)->get()->num_rows();

                $data_new_arrival[] = array(
                    "product_id" => $product_id,
                    "category_id" => $category_id,
                    "sub_category_id" => $sub_category_id,
                    "product_code" => $product_code,
                    "product_name" => $product_name,
                    "images" => $image_array,
                    "availibility" => $availibility,
                    "product_price" => $product_price,
                    "discount" => $discount,
                    "discount_price" => $discount_price,
                    "rating" => (string) $rating,
                    "review" => $review,
                    "product_description" => $product_description,
                    "how_to_use" => $how_to_use,
                    "product_view" => $product_view
                );
            }
        } else {
            $data_new_arrival = array();
        }


        $query_best_seller = $this->db->query("SELECT p.id AS product_id,p.category AS category_id,p.sub_category AS sub_category_id,p.product_name ,p.product_price,p.quantity,p.product_description,p.how_to_use,p.p_code,p.discount,p.in_stock,p.image,ac.category,asb.subcategory FROM product as p LEFT JOIN ayurveda_category AS ac ON (p.category=ac.id) LEFT JOIN ayurveda_subcategory AS asb ON (p.sub_category=asb.id) WHERE p.user_id='$listing_id' AND is_best_seller='1' order by p.id desc LIMIT 0,12");
        $count_best_seller = $query_best_seller->num_rows();
        if ($count_best_seller > 0) {
            foreach ($query_best_seller->result_array() as $row) {
                $image_array = '';
                $category_id = $row['category_id'];
                $sub_category_id = $row['sub_category_id'];
                $product_id = $row['product_id'];
                $product_code = $row['p_code'];
                $category = $row['category'];
                $subcategory = $row['subcategory'];
                $product_description = $row['product_description'];
                $product_name = $row['product_name'];
                $product_price = $row['product_price'];
                $availibility = $row['in_stock'];

                if ($availibility == '1') {
                    $availibility = 'In Stock';
                } else {
                    $availibility = 'Out of Stock';
                }

                $image = $row['image'];

                $image = array_filter(explode(",", $image));
                $cnt = count($image);
                for ($i = 0; $i < $cnt; $i++) {
                    $image_array[] = array(
                        "image" => 'https://d2c8oti4is0ms3.cloudfront.net/images/ayurveda_images/' . $image[$i]
                    );
                }


                $discount = $row['discount'];
                $discount_price = $product_price - ($product_price * ($discount / 100));
                $review = $this->db->select('id')->from('ayurveda_product_review')->where('ayurveda_id', $listing_id)->where('product_id', $product_id)->get()->num_rows();

                $query_product = $this->db->query("SELECT  ROUND(AVG(rating),1) AS avg_rating FROM ayurveda_product_review WHERE ayurveda_id='$listing_id' AND  product_id='$product_id' ");
                $row_prod = $query_product->row_array();
                $rating = $row_prod['avg_rating'];
                if ($rating === NULL) {
                    $rating = '0';
                }

                $product_description = str_replace('’', "'", $product_description);
                $product_description = "<style>strong{color:#404547;font-weight:400}p{color:#768188;text-align:justify;}</style>" . $row['product_description'];
                $how_to_use = $row['how_to_use'];


                $product_view = $this->db->select('id')->from('ayurveda_product_view')->where('ayurveda_id', $listing_id)->where('product_id', $product_id)->get()->num_rows();


                $data_best_seller[] = array(
                    "product_id" => $product_id,
                    "category_id" => $category_id,
                    "sub_category_id" => $sub_category_id,
                    "product_code" => $product_code,
                    "product_name" => $product_name,
                    "images" => $image_array,
                    "availibility" => $availibility,
                    "product_price" => $product_price,
                    "discount" => $discount,
                    "discount_price" => $discount_price,
                    "rating" => (string) $rating,
                    "review" => $review,
                    "product_description" => $product_description,
                    "how_to_use" => $how_to_use,
                    "product_view" => $product_view
                );
            }
        } else {
            $data_best_seller = array();
        }

        $resultpost = array(
            'status' => "200",
            'msg' => "success",
            'user_id' => $listing_id,
            'image_banner' => $banner_array,
            'data_new_arrival' => $data_new_arrival,
            'data_best_seller' => $data_best_seller,
        );

        return $resultpost;
    }

    public function ayurveda_about_us($user_id, $listing_id) {


        $about_query = $this->db->query("SELECT ayurveda_name,contact_no,profile_pic,about_us FROM `ayurveda` WHERE user_id='$listing_id'");
        $get_about = $about_query->row_array();
        $ayurveda_name = $get_about['ayurveda_name'];
        $phone = $get_about['contact_no'];
        $about_us = $get_about['about_us'];
        $profile_pic = $get_about['profile_pic'];

        if ($profile_pic != '') {
            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/ayurveda_images/' . $profile_pic;
        } else {
            $profile_pic = '';
        }

        $gallery_array = '';
        $gallery_query = $this->db->query("SELECT title,media,type FROM `ayurveda_gallery` WHERE user_id='$listing_id'");
        foreach ($gallery_query->result_array() as $row) {
            $title = $row['title'];
            $media = 'https://d2c8oti4is0ms3.cloudfront.net/images/ayurveda_images/' . $row['media'];
            $type = $row['type'];
            $gallery_array[] = array(
                'title' => $title,
                'media' => $media,
                'type' => $type
            );
        }

        $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS avg_rating FROM ayurveda_review WHERE ayurveda_id='$listing_id' ");
        $row_rating = $query_rating->row_array();
        $rating = $row_rating['avg_rating'];

        $profile_views = $this->db->select('id')->from('ayurveda_view')->where('ayurveda_id', $listing_id)->get()->num_rows();

        $reviews = $this->db->select('id')->from('ayurveda_review')->where('ayurveda_id', $listing_id)->get()->num_rows();


        $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
        $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
        $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();

        if ($is_follow > 0) {
            $is_follow = 'Yes';
        } else {
            $is_follow = 'No';
        }


        $resultpost[] = array(
            'ayurveda_name' => $ayurveda_name,
            'phone' => $phone,
            'about_us' => $about_us,
            'profile_pic' => $profile_pic,
            'rating' => $rating,
            'followers' => $followers,
            'following' => $following,
            'profile_views' => $profile_views,
            'reviews' => $reviews,
            'is_follow' => $is_follow,
            "media" => $gallery_array
        );
        return $resultpost;
    }

    public function ayurveda_contact_us($listing_id, $user_id, $name, $message, $mobile) {

        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $ayurveda_contact_us_data = array(
            'user_id' => $listing_id,
            'sender_user_id' => $user_id,
            'name' => $name,
            'message' => $message,
            'mobile' => $mobile,
            'date' => $date
        );
        $insert = $this->db->insert('ayurveda_contactus', $ayurveda_contact_us_data);

        if ($insert) {
            return array('status' => 201, 'message' => 'success');
        } else {

            return array('status' => 404, 'message' => 'failure');
        }
    }

    public function ayurveda_category($listing_id) {
        $query = $this->db->query("SELECT id,category,image FROM `ayurveda_category` WHERE user_id='$listing_id' order by id asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $category = $row['category'];
                $image = $row['image'];
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/ayurveda_images/' . $image;

                $resultpost[] = array(
                    "category_id" => $id,
                    "category_name" => $category,
                    'image' => $image
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function ayurveda_subcategory($category_id) {
        $query = $this->db->query("SELECT id,category_id,subcategory FROM `ayurveda_subcategory` WHERE category_id='$category_id' order by id asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $subcategory = $row['subcategory'];

                $resultpost[] = array(
                    "sub_category_id" => $id,
                    "subcategory" => $subcategory,
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function ayurveda_products($listing_id, $category_id, $sub_category_id) {


        $query = $this->db->query("SELECT p.id AS product_id,p.category AS category_id,p.sub_category AS sub_category_id,p.product_name ,p.product_price,p.quantity,p.product_description,p.how_to_use,p.p_code,p.discount,p.in_stock,p.image,ac.category,asb.subcategory FROM product as p LEFT JOIN ayurveda_category AS ac ON (p.category=ac.id) LEFT JOIN ayurveda_subcategory AS asb ON (p.sub_category=asb.id) WHERE p.user_id='$listing_id' AND p.category='$category_id' AND p.sub_category='$sub_category_id' order by p.id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            $count = $query->num_rows();
            foreach ($query->result_array() as $row) {
                $image_array = '';
                $category_id = $row['category_id'];
                $sub_category_id = $row['sub_category_id'];
                $product_id = $row['product_id'];
                $product_code = $row['p_code'];
                $discount = $row['discount'];
                $category = $row['category'];
                $subcategory = $row['subcategory'];
                $product_description = $row['product_description'];
                $product_name = $row['product_name'];
                $product_price = $row['product_price'];

                $discount_price = $product_price - ($product_price * ($discount / 100));

                $availibility = $row['in_stock'];

                if ($availibility == '1') {
                    $availibility = 'In Stock';
                } else {
                    $availibility = 'Out of Stock';
                }

                $image = $row['image'];


                $image = array_filter(explode(",", $image));
                $cnt = count($image);
                for ($i = 0; $i < $cnt; $i++) {
                    $image_array[] = array(
                        "image" => 'https://d2c8oti4is0ms3.cloudfront.net/images/ayurveda_images/' . $image[$i]
                    );
                }

                $discount = $row['discount'];

                $review = $this->db->select('id')->from('ayurveda_product_review')->where('ayurveda_id', $listing_id)->where('product_id', $product_id)->get()->num_rows();

                $query_product = $this->db->query("SELECT  ROUND(AVG(rating),1) AS avg_rating FROM ayurveda_product_review WHERE ayurveda_id='$listing_id' AND  product_id='$product_id' ");
                $row_prod = $query_product->row_array();
                $rating = $row_prod['avg_rating'];

                $product_description = str_replace('’', "'", $product_description);
                $product_description = "<style>strong{color:#404547;font-weight:400}p{color:#768188;text-align:justify;}</style>" . $row['product_description'];
                $how_to_use = $row['how_to_use'];

                $product_view = $this->db->select('id')->from('ayurveda_product_view')->where('ayurveda_id', $listing_id)->where('product_id', $product_id)->get()->num_rows();
                $resultpost[] = array(
                    "product_id" => $product_id,
                    "category_id" => $category_id,
                    "sub_category_id" => $sub_category_id,
                    "product_code" => $product_code,
                    "product_name" => $product_name,
                    "images" => $image_array,
                    "availibility" => $availibility,
                    "product_price" => $product_price,
                    "discount" => $discount,
                    "discount_price" => $discount_price,
                    "rating" => $rating,
                    "review" => $review,
                    "product_description" => $product_description,
                    "how_to_use" => $how_to_use,
                    "product_view" => $product_view);
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function ayurveda_related_products($product_id, $listing_id, $category_id, $sub_category_id) {


        $query = $this->db->query("SELECT p.id AS product_id,p.category AS category_id,p.sub_category AS sub_category_id,p.product_name ,p.product_price,p.quantity,p.product_description,p.how_to_use,p.p_code,p.discount,p.in_stock,p.image,ac.category,asb.subcategory FROM product as p LEFT JOIN ayurveda_category AS ac ON (p.category=ac.id) LEFT JOIN ayurveda_subcategory AS asb ON (p.sub_category=asb.id) WHERE p.user_id='$listing_id' AND p.category='$category_id' AND p.sub_category='$sub_category_id' AND p.id<>'$product_id'  order by p.id desc");
        $count = $query->num_rows();
        if ($count > 0) {

            foreach ($query->result_array() as $row) {
                $image_array = '';
                $category_id = $row['category_id'];
                $sub_category_id = $row['sub_category_id'];
                $product_id = $row['product_id'];
                $product_code = $row['p_code'];
                $category = $row['category'];
                $subcategory = $row['subcategory'];
                $product_description = $row['product_description'];
                $product_name = $row['product_name'];
                $product_price = $row['product_price'];
                $availibility = $row['in_stock'];

                if ($availibility == '1') {
                    $availibility = 'In Stock';
                } else {
                    $availibility = 'Out of Stock';
                }

                $image = $row['image'];

                $image = array_filter(explode(",", $image));
                $cnt = count($image);
                for ($i = 0; $i < $cnt; $i++) {
                    $image_array[] = array(
                        "image" => 'https://d2c8oti4is0ms3.cloudfront.net/images/ayurveda_images/' . $image[$i]
                    );
                }

                $discount = $row['discount'];
                $discount_price = $product_price - ($product_price * ($discount / 100));

                $rating = '4';
                $review = '0';
                $product_description = str_replace('’', "'", $product_description);
                $product_description = "<style>strong{color:#404547;font-weight:400}p{color:#768188;text-align:justify;}</style>" . $row['product_description'];
                $how_to_use = $row['how_to_use'];

                $product_view = '0';
                $resultpost[] = array(
                    "product_id" => $product_id,
                    "category_id" => $category_id,
                    "sub_category_id" => $sub_category_id,
                    "product_code" => $product_code,
                    "product_name" => $product_name,
                    "images" => $image_array,
                    "availibility" => $availibility,
                    "product_price" => $product_price,
                    "discount" => $discount,
                    "discount_price" => $discount_price,
                    "rating" => $rating,
                    "review" => $review,
                    "product_description" => $product_description,
                    "how_to_use" => $how_to_use,
                    "product_view" => $product_view);
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function ayurveda_get_quotes($listing_id, $pincode) {
        $query = $this->db->query("SELECT id,pincode,rate FROM ayurveda_store_pincode WHERE pincode='$pincode' AND user_id='$listing_id' ");
        $pincode_count = $query->num_rows();
        if ($pincode_count > 0) {
            $row = $query->row_array();
            $rate = $row['rate'];

            return array("status" => 200, "message" => "Delivery Available on this pincode", "delivery" => $rate);
        } else {
            return array("status" => 200, "message" => "Delivery not available on this pincode", "delivery" => "");
        }
    }

    public function ayurveda_product_review($listing_id, $user_id, $product_id, $rating, $review, $service) {

        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'ayurveda_id' => $listing_id,
            'user_id' => $user_id,
            'product_id' => $product_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('ayurveda_product_review', $review_array);
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    public function edit_ayurveda_product_review($review_id,$user_id, $listing_id, $rating, $review, $service) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
        );
           $this->db->where('id',$review_id);
           $this->db->where('user_id',$user_id);
           $this->db->update('ayurveda_product_review',$review_array);
        //$this->db->insert('fitness_center_review', $review_array);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    public function ayurveda_product_review_list($user_id, $product_id) {

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
        $review_count = $this->db->select('id')->from('ayurveda_product_review')->where('product_id', $product_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT ayurveda_product_review.id,ayurveda_product_review.user_id,ayurveda_product_review.product_id,ayurveda_product_review.rating,ayurveda_product_review.review, ayurveda_product_review.service,ayurveda_product_review.date as review_date,users.id as user_id,users.name as firstname FROM `ayurveda_product_review` INNER JOIN `users` ON ayurveda_product_review.user_id=users.id WHERE ayurveda_product_review.product_id='$product_id' order by ayurveda_product_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                $decrypt = $this->decrypt($review);
                $encrypt = $this->encrypt($decrypt);
                if ($encrypt == $review) {
                    $review = $decrypt;
                }

                $service = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('ayurveda_product_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('ayurveda_product_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('ayurveda_product_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

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

    public function ayurveda_product_review_likes($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from ayurveda_product_review_likes WHERE post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `ayurveda_product_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from ayurveda_product_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $ayurveda_product_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('ayurveda_product_review_likes', $ayurveda_product_review_likes);
            $like_query = $this->db->query("SELECT id from ayurveda_product_review_likes WHERE post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }

    public function ayurveda_product_review_comment($user_id, $post_id, $comment) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');

        $ayurveda_product_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('ayurveda_product_review_comment', $ayurveda_product_review_comment);
        $ayurveda_product_review_comment_query = $this->db->query("SELECT id from ayurveda_product_review_comment WHERE post_id='$post_id'");
        $total_comment = $ayurveda_product_review_comment_query->num_rows();
        return array(
            'status' => 200,
            'message' => 'success',
            'comment' => '1',
            'total_comment' => $total_comment
        );
    }

    public function ayurveda_product_review_comment_like($user_id, $comment_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from ayurveda_product_review_comment_like WHERE comment_id='$comment_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `ayurveda_product_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from ayurveda_product_review_comment_like where comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $ayurveda_product_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('ayurveda_product_review_comment_like', $ayurveda_product_review_comment_like);
            $comment_query = $this->db->query("SELECT id from ayurveda_product_review_comment_like WHERE comment_id='$comment_id'");
            $total_comment_like = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'comment_like' => '1',
                'total_comment_like' => $total_comment_like
            );
        }
    }

    public function ayurveda_product_review_comment_list($user_id, $post_id) {

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

        $review_list_count = $this->db->select('id')->from('ayurveda_product_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT ayurveda_product_review_comment.id,ayurveda_product_review_comment.post_id,ayurveda_product_review_comment.comment as comment,ayurveda_product_review_comment.date,users.name,ayurveda_product_review_comment.user_id as post_user_id FROM ayurveda_product_review_comment INNER JOIN users on users.id=ayurveda_product_review_comment.user_id WHERE ayurveda_product_review_comment.post_id='$post_id' order by ayurveda_product_review_comment.id asc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                $decrypt = $this->decrypt($comment);
                $encrypt = $this->encrypt($decrypt);
                if ($encrypt == $comment) {
                    $comment = $decrypt;
                }
                $username = $row['name'];
                $date = $row['date'];
                $post_user_id = $row['post_user_id'];

                $like_count = $this->db->select('id')->from('ayurveda_product_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('ayurveda_product_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
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

    public function ayurveda_review($user_id, $listing_id, $rating, $review, $service) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'user_id' => $user_id,
            'ayurveda_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('ayurveda_review', $review_array);
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }

    public function ayurveda_review_list($user_id, $listing_id) {

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
        $review_count = $this->db->select('id')->from('ayurveda_review')->where('ayurveda_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT ayurveda_review.id,ayurveda_review.user_id,ayurveda_review.ayurveda_id,ayurveda_review.rating,ayurveda_review.review, ayurveda_review.service,ayurveda_review.date as review_date,users.id as user_id,users.name as firstname FROM `ayurveda_review` INNER JOIN `users` ON ayurveda_review.user_id=users.id WHERE ayurveda_review.ayurveda_id='$listing_id' order by ayurveda_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '2') {
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

                $like_count = $this->db->select('id')->from('ayurveda_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('ayurveda_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('ayurveda_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

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

    public function ayurveda_review_likes($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from ayurveda_review_likes WHERE post_id='$post_id' AND user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `ayurveda_review_likes` WHERE user_id='$user_id' AND post_id='$post_id'");
            $like_query = $this->db->query("SELECT id FROM ayurveda_review_likes WHERE post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $ayurveda_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('ayurveda_review_likes', $ayurveda_review_likes);
            $like_query = $this->db->query("SELECT id from ayurveda_review_likes WHERE post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }

    public function ayurveda_review_comment($user_id, $post_id, $comment) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');

        $ayurveda_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('ayurveda_review_comment', $ayurveda_review_comment);
        $ayurveda_review_comment_query = $this->db->query("SELECT id FROM ayurveda_review_comment WHERE post_id='$post_id'");
        $total_comment = $ayurveda_review_comment_query->num_rows();
        return array(
            'status' => 200,
            'message' => 'success',
            'comment' => '1',
            'total_comment' => $total_comment
        );
    }

    public function ayurveda_review_comment_like($user_id, $comment_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from ayurveda_review_comment_like WHERE comment_id='$comment_id' AND user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `ayurveda_review_comment_like` WHERE user_id='$user_id' AND comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id FROM ayurveda_review_comment_like WHERE comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $ayurveda_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('ayurveda_review_comment_like', $ayurveda_review_comment_like);
            $comment_query = $this->db->query("SELECT id FROM ayurveda_review_comment_like WHERE comment_id='$comment_id'");
            $total_comment_like = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'comment_like' => '1',
                'total_comment_like' => $total_comment_like
            );
        }
    }

    public function ayurveda_review_comment_list($user_id, $post_id) {

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

        $review_list_count = $this->db->select('id')->from('ayurveda_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT ayurveda_review_comment.id,ayurveda_review_comment.post_id,ayurveda_review_comment.comment as comment,ayurveda_review_comment.date,users.name,ayurveda_review_comment.user_id as post_user_id FROM ayurveda_review_comment INNER JOIN users on users.id=ayurveda_review_comment.user_id WHERE ayurveda_review_comment.post_id='$post_id' order by ayurveda_review_comment.id asc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                $decrypt = $this->decrypt($comment);
                $encrypt = $this->encrypt($decrypt);
                if ($encrypt == $comment) {
                    $comment = $decrypt;
                }
                $username = $row['name'];
                $date = $row['date'];
                $post_user_id = $row['post_user_id'];

                $like_count = $this->db->select('id')->from('ayurveda_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('ayurveda_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
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

    public function ayurveda_product_view($ayurveda_id, $user_id, $product_id) {
        $query = $this->db->query("SELECT * FROM ayurveda_product_view WHERE ayurveda_id='$ayurveda_id' AND user_id='$user_id' AND product_id='$product_id' limit 1");
        $count = $query->num_rows();
        if ($count > 0) {
            $status = 200;
            $message = "success";
            $query1 = $this->db->query("SELECT * FROM ayurveda_product_view WHERE ayurveda_id='$ayurveda_id' AND product_id='$product_id'");
            $query1_count = $query1->num_rows();
            return array(
                "status" => $status,
                "message" => $message,
                "Count" => $query1_count);
        } else {
            $ayurveda_product_view = array(
                'ayurveda_id' => $ayurveda_id,
                'user_id' => $user_id,
                'product_id' => $product_id
            );
            $this->db->insert('ayurveda_product_view', $ayurveda_product_view);
            $view_query = $this->db->query("SELECT id FROM ayurveda_product_view WHERE ayurveda_id='$ayurveda_id' AND product_id='$product_id'");
            $total_view = $view_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'total_view' => $total_view
            );
        }
    }

    public function ayurveda_view($ayurveda_id, $user_id) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $ayurveda_view_array = array(
            'ayurveda_id' => $ayurveda_id,
            'user_id' => $user_id
        );
        $this->db->insert('ayurveda_view', $ayurveda_view_array);

        $ayurveda_view = $this->db->select('id')->from('ayurveda_view')->where('ayurveda_id', $ayurveda_id)->get()->num_rows();

        return array(
            'status' => 200,
            'message' => 'success',
            'ayurveda_views' => $ayurveda_view
        );
    }

    public function organicindia_home() {
        $expiry_date = "2 Years from the date of manufacturing.";
        $query = $this->db->query("SELECT * FROM `organicindia_banner` ORDER BY id DESC");
        $count = $query->num_rows();
        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            $image = $row['image'];

            $banner = $image;
        }

        $query = $this->db->query("SELECT organicindia_product.id, organicindia_product.subcategory_id, organicindia_product.name, organicindia_product.image1, organicindia_product.image2, organicindia_product.image3, organicindia_product.description, organicindia_product.discount, organicindia_product.price, organicindia_product.weight, organicindia_product.availibility ,  organicindia_subcategory.id AS sub_category_id, organicindia_subcategory.subcategory FROM `organicindia_product` INNER JOIN `organicindia_subcategory` ON organicindia_subcategory.id=organicindia_product.subcategory_id ORDER BY organicindia_product.id desc LIMIT 0,12");
        $count = $query->num_rows();
        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            $name = $row['name'];
            $price = $row['price'];
            $weight = $row['weight'];

            $availibility = $row['availibility'];
            $subcategory = $row['subcategory'];
            $subcategory_id = $row['subcategory_id'];
            $product_code = 'OI' . $subcategory_id . '000' . $id;
            $rating = '4';
            $review = '0';
            $price = $row['price'];
            $description = "<style>strong{color:#404547;font-weight:400}p{color:#768188;text-align:justify;}</style>" . $row['description'];
            $discount = $row['discount'];
            $discount_price = $price - ($price * ( $discount / 100));
            $img1 = '';
            $img2 = '';
            $img3 = '';

            $img1 = $row['image1'];
            $img2 = $row['image2'];
            $img3 = $row['image3'];

            if ($img1 != '') {
                $image1 = 'https://d2c8oti4is0ms3.cloudfront.net/images/organicindia_images/' . $row['image1'] . ',';
            } else {
                $image1 = '';
            }
            if ($img2 != '') {

                $image2 = 'https://d2c8oti4is0ms3.cloudfront.net/images/organicindia_images/' . $row['image2'] . ',';
            } else {
                $image2 = '';
            }
            if ($img3 != '') {
                $image3 = 'https://d2c8oti4is0ms3.cloudfront.net/images/organicindia_images/' . $row['image3'];
            } else {
                $image3 = '';
            }


            $image = $image1 . $image2 . $image3;
            $organicindia_product_view_query = $this->db->query("SELECT id FROM `organicindia_product_view` where product_id='$id'");
            $product_view = $organicindia_product_view_query->num_rows();

            $data_new_arrival[] = array(
                "id" => $id,
                "subcategory_id" => $subcategory_id,
                "name" => $name,
                "image" => $image,
                "price" => $price,
                "weight" => $weight,
                "description" => $description,
                "availibility" => $availibility,
                "subcategory" => $subcategory,
                "expiry_date" => $expiry_date,
                "product_code" => $product_code,
                "rating" => $rating,
                "review" => $review,
                "discount" => $discount,
                "discount_price" => $discount_price,
                "product_view" => $product_view
            );
        }


        $query = $this->db->query("SELECT organicindia_product.id, organicindia_product.subcategory_id, organicindia_product.name, organicindia_product.image1, organicindia_product.image2, organicindia_product.image3, organicindia_product.description,  organicindia_product.discount, organicindia_product.price, organicindia_product.weight, organicindia_product.availibility ,  organicindia_subcategory.id AS sub_category_id, organicindia_subcategory.subcategory FROM `organicindia_product` INNER JOIN `organicindia_subcategory` ON organicindia_subcategory.id=organicindia_product.subcategory_id ORDER BY rand() desc LIMIT 0,12");
        $count = $query->num_rows();
        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            $name = $row['name'];
            $image1 = $row['image1'];
            $price = $row['price'];
            $weight = $row['weight'];
            $availibility = $row['availibility'];
            $subcategory = $row['subcategory'];
            $subcategory_id = $row['subcategory_id'];
            $product_code = 'OI' . $subcategory_id . '000' . $id;
            $rating = '4';
            $review = '0';
            $price = $row['price'];
            $description = "<style>strong{color:#404547;font-weight:400}p{color:#768188;text-align:justify;}</style>" . $row['description'];
            $discount = $row['discount'];
            $discount_price = $price - ($price * ( $discount / 100));

            $img1 = '';
            $img2 = '';
            $img3 = '';

            $img1 = $row['image1'];
            $img2 = $row['image2'];
            $img3 = $row['image3'];

            if ($img1 != '') {
                $image1 = 'https://d2c8oti4is0ms3.cloudfront.net/images/organicindia_images/' . $row['image1'] . ',';
            } else {
                $image1 = '';
            }
            if ($img2 != '') {

                $image2 = 'https://d2c8oti4is0ms3.cloudfront.net/images/organicindia_images/' . $row['image2'] . ',';
            } else {
                $image2 = '';
            }
            if ($img3 != '') {
                $image3 = 'https://d2c8oti4is0ms3.cloudfront.net/images/organicindia_images/' . $row['image3'];
            } else {
                $image3 = '';
            }


            $image = $image1 . $image2 . $image3;
            $organicindia_product_view_query = $this->db->query("SELECT id FROM `organicindia_product_view` where product_id='$id'");
            $product_view = $organicindia_product_view_query->num_rows();

            $data_best_seller[] = array(
                "id" => $id,
                "subcategory_id" => $subcategory_id,
                "name" => $name,
                "image" => $image1,
                "price" => $price,
                "weight" => $weight,
                "description" => $description,
                "availibility" => $availibility,
                "subcategory" => $subcategory,
                "expiry_date" => $expiry_date,
                "product_code" => $product_code,
                "rating" => $rating,
                "review" => $review,
                "discount" => $discount,
                "discount_price" => $discount_price,
                "product_view" => $product_view
            );
        }

        $resultpost = array(
            'status' => "200",
            'msg' => "success",
            'user_id' => 576,
            'image_banner' => $banner,
            'data_new_arrival' => $data_new_arrival,
            'data_best_seller' => $data_best_seller,
        );

        return $resultpost;
    }

    public function organicindia_about_us($user_id, $listing_id) {
        $organicindia_array = array();
        $about_query = $this->db->query("SELECT about_us FROM `organicindia_aboutus`");
        $organicindia_count1 = $about_query->num_rows();

        $gallery_query = $this->db->query("SELECT GROUP_CONCAT(`title`) AS title, GROUP_CONCAT(CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/organicindia_images',media)) AS media FROM `organicindia_gallery`");
        if ($organicindia_count1 > 0) {

            $row = $about_query->row();
            $row2 = $gallery_query->row();

            $about_us = $row->about_us;
            $images = $row2->media;
            $image_title = $row2->title;

            $organicindia_array[] = array(
                'images' => $images,
                'image_title' => $image_title);

            $rating = '4.3';
            $profile_views = '244';
            $reviews = '240';
            $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
            $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();


            $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();

            if ($is_follow > 0) {
                $is_follow = 'Yes';
            } else {
                $is_follow = 'No';
            }

            return array(
                "status" => 200,
                "message" => "success",
                "count" => sizeof($organicindia_array),
                'about_us' => $about_us,
                'rating' => $rating,
                'followers' => $followers,
                'following' => $following,
                'profile_views' => $profile_views,
                'reviews' => $reviews,
                'is_follow' => $is_follow,
                "data" => $organicindia_array
            );
        } else {
            return array("status" => 404, "message" => "failure", "count" => 0);
        }
    }

    public function organicindia_contact_us($user_id, $name, $message, $mobile) {

        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $organicindia_contactus_data = array(
            'user_id' => $user_id,
            'name' => $name,
            'message' => $message,
            'mobile' => $mobile,
            'date' => $date
        );
        $insert = $this->db->insert('organicindia_contactus', $organicindia_contactus_data);

        if ($insert) {
            return array('status' => 201, 'message' => 'success');
        } else {

            return array('status' => 404, 'message' => 'failure');
        }
    }

    public function organicindia_category() {
        $query = $this->db->query("SELECT id,category,image FROM `organicindia_category` order by id asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $category = $row['category'];
                $image = $row['image'];
                //$image='https://d2c8oti4is0ms3.cloudfront.net/images/organicindia_images/'.$image;

                $resultpost[] = array(
                    "category_id" => $id,
                    "category_name" => $category,
                    'image' => $image
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function organicindia_subcategory($category_id) {
        $query = $this->db->query("SELECT id,category_id,subcategory FROM `organicindia_subcategory` WHERE category_id='$category_id' order by id asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $subcategory = $row['subcategory'];

                $resultpost[] = array(
                    "sub_category_id" => $id,
                    "subcategory" => $subcategory,
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function organicindia_products($sub_category_id) {
        $expiry_date = "2 Years from the date of manufacturing.";
        $query = $this->db->query("SELECT organicindia_product.description,organicindia_category.id AS category_id,organicindia_category.category,organicindia_subcategory.id AS sub_category_id,organicindia_subcategory.subcategory,organicindia_product.id AS product_id,organicindia_product.name AS product_name,organicindia_product.image1,organicindia_product.image2,organicindia_product.image3,organicindia_product.price,organicindia_product.discount,organicindia_product.availibility,organicindia_product.description,organicindia_product.how_to_use_app,organicindia_product.specification FROM `organicindia_category`
		INNER JOIN `organicindia_subcategory` ON organicindia_category.id=organicindia_subcategory.category_id
		INNER JOIN organicindia_product ON organicindia_product.subcategory_id=organicindia_subcategory.id
		WHERE organicindia_product.subcategory_id='$sub_category_id' ORDER BY organicindia_product.id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            $count = $query->num_rows();
            foreach ($query->result_array() as $row) {


                $category_id = $row['category_id'];
                $sub_category_id = $row['sub_category_id'];
                $product_id = $row['product_id'];
                $product_code = 'OI' . $sub_category_id . '000' . $product_id;

                $category = $row['category'];
                $description = $row['description'];
                $subcategory = $row['subcategory'];
                $product_name = $row['product_name'];
                $img1 = '';
                $img2 = '';
                $img3 = '';

                $img1 = $row['image1'];
                $img2 = $row['image2'];
                $img3 = $row['image3'];

                if ($img1 != '') {
                    $image1 = 'https://d2c8oti4is0ms3.cloudfront.net/images/organicindia_images/' . $row['image1'] . ',';
                } else {
                    $image1 = '';
                }
                if ($img2 != '') {
                    $image2 = 'https://d2c8oti4is0ms3.cloudfront.net/images/organicindia_images/' . $row['image2'] . ',';
                } else {
                    $image2 = '';
                }
                if ($img3 != '') {
                    $image3 = 'https://d2c8oti4is0ms3.cloudfront.net/images/organicindia_images/' . $row['image3'];
                } else {
                    $image3 = '';
                }

                $image = $image1 . $image2 . $image3;
                $image = rtrim($image, ',');
                $price = $row['price'];
                $availibility = $row['availibility'];
                $discount = $row['discount'];
                $discount_price = $price - ($price * ( $discount / 100));
                $discount_price = (string) $discount_price;
                $rating = '4';
                $review = '0';
                $description = str_replace('’', "'", $description);
                $description = "<style>strong{color:#404547;font-weight:400}p{color:#768188;text-align:justify;}</style>" . $row['description'];
                $how_to_use = $row['how_to_use_app'];
                $specification = $row['specification'];

                $organicindia_product_view_query = $this->db->query("SELECT id FROM `organicindia_product_view` where product_id='$product_id'");
                $product_view = $organicindia_product_view_query->num_rows();

                $resultpost[] = array(
                    "category_id" => $category_id,
                    "sub_category_id" => $sub_category_id,
                    "product_id" => $product_id,
                    "product_code" => $product_code,
                    "product_name" => $product_name,
                    "image" => $image,
                    "availibility" => $availibility,
                    "price" => $price,
                    "discount" => $discount,
                    "discount_price" => $discount_price,
                    "rating" => $rating,
                    "review" => $review,
                    "description" => $description,
                    "how_to_use" => $how_to_use,
                    "product_view" => $product_view,
                    "Expiry_date" => $expiry_date);
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function organicindia_related_products($sub_category_id, $product_id) {
        $expiry_date = "2 Years from the date of manufacturing.";
        $query = $this->db->query("SELECT organicindia_product.description,organicindia_category.id AS category_id,organicindia_category.category,organicindia_subcategory.id AS sub_category_id,organicindia_subcategory.subcategory,organicindia_product.id AS product_id,organicindia_product.name AS product_name,organicindia_product.image1,organicindia_product.image2,organicindia_product.image3,organicindia_product.price,organicindia_product.discount,organicindia_product.availibility,organicindia_product.description,organicindia_product.how_to_use_app,organicindia_product.specification FROM `organicindia_category`
		INNER JOIN `organicindia_subcategory` ON organicindia_category.id=organicindia_subcategory.category_id
		INNER JOIN organicindia_product ON organicindia_product.subcategory_id=organicindia_subcategory.id
		WHERE organicindia_product.subcategory_id='$sub_category_id' AND organicindia_product.id<>$product_id ORDER BY organicindia_product.id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            $count = $query->num_rows();
            foreach ($query->result_array() as $row) {

                $category_id = $row['category_id'];
                $sub_category_id = $row['sub_category_id'];
                $product_id = $row['product_id'];
                $product_code = 'OI' . $sub_category_id . '000' . $product_id;
                $description = $row['description'];
                $category = $row['category'];
                $subcategory = $row['subcategory'];
                $product_name = $row['product_name'];
                $availibility = $row['availibility'];
                $img1 = '';
                $img2 = '';
                $img3 = '';

                $img1 = $row['image1'];
                $img2 = $row['image2'];
                $img3 = $row['image3'];

                if ($img1 != '') {
                    $image1 = 'https://d2c8oti4is0ms3.cloudfront.net/images/organicindia_images/' . $row['image1'] . ',';
                } else {
                    $image1 = '';
                }
                if ($img2 != '') {
                    $image2 = 'https://d2c8oti4is0ms3.cloudfront.net/images/organicindia_images/' . $row['image2'] . ',';
                } else {
                    $image2 = '';
                }
                if ($img3 != '') {
                    $image3 = 'https://d2c8oti4is0ms3.cloudfront.net/images/organicindia_images/' . $row['image3'];
                } else {
                    $image3 = '';
                }

                $image = $image1 . $image2 . $image3;
                $image = rtrim($image, ',');
                $price = $row['price'];

                $discount = $row['discount'];
                $discount_price = $price - ($price * ( $discount / 100));
                $discount_price = (string) $discount_price;
                $rating = '4';
                $review = '0';
                $description = str_replace('’', "'", $description);
                $description = "<style>strong{color:#404547;font-weight:400}p{color:#768188;text-align:justify;}</style>" . $row['description'];
                $how_to_use = $row['how_to_use_app'];
                $specification = $row['specification'];

                $organicindia_product_view_query = $this->db->query("SELECT id FROM `organicindia_product_view` where product_id='$product_id'");
                $product_view = $organicindia_product_view_query->num_rows();

                $resultpost[] = array(
                    "category_id" => $category_id,
                    "subcategory_id" => $sub_category_id,
                    "id" => $product_id,
                    "product_code" => $product_code,
                    "name" => $product_name,
                    "image" => $image,
                    "price" => $price,
                    "discount" => $discount,
                    "discount_price" => $discount_price,
                    "rating" => $rating,
                    "review" => $review,
                    "product_view" => $product_view,
                    "description" => $description,
                    "how_to_use" => $how_to_use,
                    "product_view" => $product_view,
                    "availibility" => $availibility);
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function organicindia_country() {
        $query = $this->db->query("SELECT id,country FROM organicindia_country order by country asc");
        $count = $query->num_rows();
        if ($count > 0) {

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $country = $row['country'];

                $resultpost[] = array(
                    "id" => $id,
                    "country" => $country);
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function organicindia_state($country) {
        $query = $this->db->query("SELECT id,city as state FROM organicindia_city where country='$country' order by city asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $state = $row['state'];

                $resultpost[] = array(
                    "id" => $id,
                    "state" => $state);
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function organicindia_get_quotes($pincode) {
        $query = $this->db->query("SELECT id,pincode FROM organicindia_pincode where pincode='$pincode' order by pincode asc");
        $pincode_count = $query->num_rows();
        if ($pincode_count > 0) {

            $resultpincode = '100';
            return array("status" => 200, "message" => "success", "delivery" => $resultpincode);
        } else {
            return array("status" => 404, "message" => "failure");
        }
    }

    public function organicindia_pincode_check($pincode) {
        $query = $this->db->query("select id from organicindia_pincode WHERE pincode='$pincode' limit 1");
        $pincode_count = $query->num_rows();
        if ($pincode_count > 0) {


            return array("status" => 200, "message" => "success");
        } else {
            return array("status" => 404, "message" => "failure");
        }
    }

    public function organicindia_cart_order($user_id, $address_id, $product_id, $product_quantity, $product_price) {

        $status = "Pending";
        $product_status = 'Pending';

        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d');
        $b = date("Y");
        $c = date("m");
        $d = date("d");
        $e = date("H");
        $f = date("i");
        $g = date("s");
        $uni_id = $b . $c . $d . $e . $f . $g;

        $discount = '0';
        $grand_total = '0';
        $final_total = '0';
        $discount_rate = '0';
        $payType = '0';
        $store_status = '0';
        $customer_status = '0';



        $product_id = explode(",", $product_id);
        $product_quantity = explode(",", $product_quantity);
        $product_price = explode(",", $product_price);
        $cnt = count($product_id);
        for ($i = 0; $i < $cnt; $i++) {
            $final_total = $final_total + ($product_price[$i] * $product_quantity[$i]);
        }
        $grand_total = $final_total;


        $address_query = $this->db->query("SELECT name,address1,address2,mobile,landmark FROM `user_address` WHERE address_id='$address_id' AND user_id='$user_id'");

        $get_list = $address_query->row();


        if ($get_list) {
            $name = $get_list->name;
            $address1 = $get_list->address1;
            $address2 = $get_list->address2;
            $mobile = $get_list->mobile;
            $landmark = $get_list->landmark;
        } else {
            $name = '';
            $address1 = '';
            $address2 = '';
            $mobile = '';
            $landmark = '';
        }


        $organicindia_cart_order_data = array(
            'user_id' => $user_id,
            'address_id' => $address_id,
            'uni_id' => $uni_id,
            'name' => $name,
            'date' => $date,
            'status' => $status,
            'store_status' => $store_status,
            'customer_status' => $customer_status,
            'total' => $grand_total,
            'discount' => $discount,
            'payType' => $payType,
            'address1' => $address1,
            'address2' => $address2,
            'mobile' => $mobile,
            'landmark' => $landmark
        );
        $insert1 = $this->db->insert('organicindia_cart_order', $organicindia_cart_order_data);
        $order_id = $this->db->insert_id();

        $cnt = count($product_id);

        for ($i = 0; $i < $cnt; $i++) {
            $sub_total = $product_price[$i] * $product_quantity[$i];

            $cart_order_products_data = array(
                'order_id' => $order_id,
                'product_id' => $product_id[$i],
                'product_quantity' => $product_quantity[$i],
                'product_price' => $product_price[$i],
                'sub_total' => $sub_total,
                'product_status' => 'pending',
                'product_status_type' => '',
                'product_status_value' => '',
                'order_status' => 'pending',
                'uni_id' => $uni_id
            );
            $insert2 = $this->db->insert('organicindia_cart_order_products', $cart_order_products_data);
        }

        if ($insert1 & $insert2) {
            return array('status' => 200, 'message' => 'success');
        } else {
            return array('status' => 404, 'message' => 'failure');
        }
    }

    public function organicindia_cart_order_list($user_id) {

        $query = $this->db->query("SELECT organicindia_cart_order.date,organicindia_cart_order.status,organicindia_cart_order_products.order_id,organicindia_cart_order_products.uni_id  FROM `organicindia_cart_order`
		INNER JOIN `organicindia_cart_order_products`
		ON organicindia_cart_order.id=organicindia_cart_order_products.order_id
		WHERE organicindia_cart_order.user_id='$user_id' GROUP BY organicindia_cart_order.uni_id");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $order_id = $row['order_id'];
                $order_no = $row['uni_id'];
                $order_status = $row['status'];
                $order_date = $row['date'];

                $resultpost[] = array(
                    "order_id" => $order_id,
                    "order_no" => $order_no,
                    'order_status' => $order_status,
                    'order_date' => $order_date);
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }

    public function organicindia_cart_order_details($user_id, $order_id) {

        $query = $this->db->query("SELECT organicindia_cart_order.uni_id,organicindia_cart_order.date,organicindia_cart_order.status,GROUP_CONCAT(organicindia_product.name) AS product_name,GROUP_CONCAT(organicindia_product.price) AS product_price,GROUP_CONCAT(organicindia_cart_order_products.product_quantity) AS product_quantity,organicindia_product.image1,organicindia_cart_order.name ,organicindia_cart_order.mobile,organicindia_cart_order.address1,organicindia_cart_order.address2,organicindia_cart_order.landmark
		FROM `organicindia_cart_order`
		INNER JOIN `organicindia_cart_order_products`
		ON organicindia_cart_order.id=organicindia_cart_order_products.order_id
		INNER JOIN organicindia_product
		ON organicindia_product.id=organicindia_cart_order_products.product_id
		WHERE organicindia_cart_order.user_id='$user_id' AND organicindia_cart_order.id='$order_id'");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $order_no = $row['uni_id'];
                $order_date = $row['date'];
                $order_status = $row['status'];
                $product_name = $row['product_name'];
                $product_price = $row['product_price'];
                $product_quantity = $row['product_quantity'];
                $name = $row['name'];

//$addr_patient_name=$firstname.' '.$lastname;
                $addr_address1 = $row['address1'];
                $addr_address2 = $row['address2'];
                $addr_landmark = $row['landmark'];
                $addr_mobile = $row['mobile'];

                $resultpost[] = array(
                    'order_no' => $order_no,
                    'order_date' => $order_date,
                    'order_status' => $order_status,
                    'product_name' => $product_name,
                    'product_price' => $product_price,
                    'product_quantity' => $product_quantity,
                    'addr_patient_name' => $name,
                    'addr_address1' => $addr_address1,
                    'addr_address2' => $addr_address2,
                    'addr_landmark' => $addr_landmark,
                    'addr_mobile' => $addr_mobile);
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }

    public function organicindia_product_review($user_id, $listing_id, $rating, $review, $service) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'user_id' => $user_id,
            'product_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('organicindia_product_review', $review_array);
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }

    public function organicindia_product_review_list($user_id, $listing_id) {

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
        $review_count = $this->db->select('id')->from('organicindia_product_review')->where('product_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT organicindia_product_review.id,organicindia_product_review.user_id,organicindia_product_review.product_id,organicindia_product_review.rating,organicindia_product_review.review, organicindia_product_review.service,organicindia_product_review.date as review_date,users.id as user_id,users.name as firstname FROM `organicindia_product_review` INNER JOIN `users` ON organicindia_product_review.user_id=users.id WHERE organicindia_product_review.product_id='$listing_id' order by organicindia_product_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '4') {
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

                $like_count = $this->db->select('id')->from('organicindia_product_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('organicindia_product_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('organicindia_product_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

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

    public function organicindia_product_review_likes($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from organicindia_product_review_likes where post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `organicindia_product_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from organicindia_product_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $organicindia_product_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('organicindia_product_review_likes', $organicindia_product_review_likes);
            $like_query = $this->db->query("SELECT id from organicindia_product_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }

    public function organicindia_product_review_comment($user_id, $post_id, $comment) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');

        $organicindia_product_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('organicindia_product_review_comment', $organicindia_product_review_comment);
        $organicindia_product_review_comment_query = $this->db->query("SELECT id from organicindia_product_review_comment where post_id='$post_id'");
        $total_comment = $organicindia_product_review_comment_query->num_rows();
        return array(
            'status' => 200,
            'message' => 'success',
            'comment' => '1',
            'total_comment' => $total_comment
        );
    }

    public function organicindia_product_review_comment_like($user_id, $comment_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from organicindia_product_review_comment_like where comment_id='$comment_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `organicindia_product_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from organicindia_product_review_comment_like where comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $organicindia_product_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('organicindia_product_review_comment_like', $organicindia_product_review_comment_like);
            $comment_query = $this->db->query("SELECT id from organicindia_product_review_comment_like where comment_id='$comment_id'");
            $total_comment_like = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'comment_like' => '1',
                'total_comment_like' => $total_comment_like
            );
        }
    }

    public function organicindia_product_review_comment_list($user_id, $post_id) {

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

        $review_list_count = $this->db->select('id')->from('organicindia_product_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT organicindia_product_review_comment.id,organicindia_product_review_comment.post_id,organicindia_product_review_comment.comment as comment,organicindia_product_review_comment.date,users.name,organicindia_product_review_comment.user_id as post_user_id FROM organicindia_product_review_comment INNER JOIN users on users.id=organicindia_product_review_comment.user_id WHERE organicindia_product_review_comment.post_id='$post_id' order by organicindia_product_review_comment.id asc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if ($id > '6') {
                    $decrypt = $this->decrypt($comment);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $comment) {
                        $comment = $decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($comment)) === $comment) {
                        $comment = base64_decode($comment);
                    }
                }
                $username = $row['name'];
                $date = $row['date'];
                $post_user_id = $row['post_user_id'];

                $like_count = $this->db->select('id')->from('organicindia_product_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('organicindia_product_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
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

    public function organicindia_review($user_id, $listing_id, $rating, $review, $service) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'user_id' => $user_id,
            'organicindia_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('organicindia_review', $review_array);
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }

    public function organicindia_review_list($user_id, $listing_id) {

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
        $review_count = $this->db->select('id')->from('organicindia_review')->where('organicindia_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT organicindia_review.id,organicindia_review.user_id,organicindia_review.organicindia_id,organicindia_review.rating,organicindia_review.review, organicindia_review.service,organicindia_review.date as review_date,users.id as user_id,users.name as firstname FROM `organicindia_review` INNER JOIN `users` ON organicindia_review.user_id=users.id WHERE organicindia_review.organicindia_id='$listing_id' order by organicindia_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '4') {
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

                $like_count = $this->db->select('id')->from('organicindia_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('organicindia_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('organicindia_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

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

    public function organicindia_review_likes($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from organicindia_review_likes where post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `organicindia_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from organicindia_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $organicindia_product_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('organicindia_review_likes', $organicindia_product_review_likes);
            $like_query = $this->db->query("SELECT id from organicindia_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }

    public function organicindia_review_comment($user_id, $post_id, $comment) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');

        $organicindia_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('organicindia_review_comment', $organicindia_review_comment);
        $organicindia_review_comment_query = $this->db->query("SELECT id from organicindia_review_comment where post_id='$post_id'");
        $total_comment = $organicindia_review_comment_query->num_rows();
        return array(
            'status' => 200,
            'message' => 'success',
            'comment' => '1',
            'total_comment' => $total_comment
        );
    }

    public function organicindia_review_comment_like($user_id, $comment_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from organicindia_review_comment_like where comment_id='$comment_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `organicindia_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from organicindia_review_comment_like where comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $organicindia_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('organicindia_review_comment_like', $organicindia_review_comment_like);
            $comment_query = $this->db->query("SELECT id from organicindia_review_comment_like where comment_id='$comment_id'");
            $total_comment_like = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'comment_like' => '1',
                'total_comment_like' => $total_comment_like
            );
        }
    }

    public function organicindia_review_comment_list($user_id, $post_id) {

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

        $review_list_count = $this->db->select('id')->from('organicindia_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT organicindia_review_comment.id,organicindia_review_comment.post_id,organicindia_review_comment.comment as comment,organicindia_review_comment.date,users.name,organicindia_review_comment.user_id as post_user_id FROM organicindia_review_comment INNER JOIN users on users.id=organicindia_review_comment.user_id WHERE organicindia_review_comment.post_id='$post_id' order by organicindia_review_comment.id asc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if ($id > '14') {
                    $decrypt = $this->decrypt($comment);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $comment) {
                        $comment = $decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($comment)) === $comment) {
                        $comment = base64_decode($comment);
                    }
                }
                $username = $row['name'];
                $date = $row['date'];
                $post_user_id = $row['post_user_id'];

                $like_count = $this->db->select('id')->from('organicindia_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('organicindia_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
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

    public function organicindia_view($user_id, $product_id) {
        $query = $this->db->query("SELECT * FROM organicindia_product_view WHERE user_id='$user_id' and product_id='$product_id' limit 1");
        $count = $query->num_rows();
        if ($count > 0) {
            $status = 200;
            $message = "success";
            $query1 = $this->db->query("SELECT * FROM organicindia_product_view WHERE product_id='$product_id'");
            $query1_count = $query1->num_rows();
            return array(
                "status" => $status,
                "message" => $message,
                "Count" => $query1_count);
        } else {
            $organicindia_view = array(
                'user_id' => $user_id,
                'product_id' => $product_id
            );
            $this->db->insert('organicindia_product_view', $organicindia_view);
            $view_query = $this->db->query("SELECT id from organicindia_product_view where product_id='$product_id'");
            $total_view = $view_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'total_view' => $total_view
            );
        }
    }

}
