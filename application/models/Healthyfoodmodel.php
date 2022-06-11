<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Healthyfoodmodel extends CI_Model {

    var $client_service = "frontend-client";
    var $auth_key       = "medicalwalerestapi";

    public function check_auth_client(){
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key  = $this->input->get_request_header('Auth-Key', TRUE);
        if($client_service == $this->client_service && $auth_key == $this->auth_key){
            return true;
        } else {
            return json_output(401,array('status' => 401,'message' => 'Unauthorized.'));
        }
    }

    public function auth()
    {
        date_default_timezone_set('Asia/Kolkata');
        $users_id  = $this->input->get_request_header('User-ID', TRUE);
        $token     = $this->input->get_request_header('Authorizations', TRUE);
        $q  = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id',$users_id)->where('token',$token)->get()->row();
        if($q == ""){
            return json_output(401,array('status' => 401,'message' => 'Unauthorized.'));
        } else {
            if($q->expired_at < date('Y-m-d H:i:s')){
                return json_output(401,array('status' => 401,'message' => 'Your session has been expired.'));
            } else {
                $updated_at = date('Y-m-d H:i:s');
                $expired_at = '2018-11-12 08:57:58';
                $this->db->where('users_id',$users_id)->where('token',$token)->update('api_users_authentication',array('expired_at' => $expired_at,'updated_at' => $updated_at));
                return array('status' => 200,'message' => 'Authorized.');
            }
        }
    }
    
     public function yogapulp_home($user_id,$listing_id)
    {
        $expiry_date="2 Years from the date of manufacturing.";
       
        
        $query = $this->db->query("SELECT * FROM `healthy_food_product` ORDER BY id desc");
        $count = $query->num_rows();
        foreach ($query->result_array() as $row) {
                        $id = $row['id'];
                         $name=$row['name'];
                        $yogapulp_user_id=$row['user_id'];
                         $product_code='YP'.$id.'000'.$id;
                         $rating='4';
                         $review='0';
                       
                         $price=$row['price'];
                         $availibility=$row['availibility'];
                         $discount=$row['discount'];
                        $discount_price_1 = $price - ($price * ( $discount / 100)); 
                        $discount_price = "$discount_price_1";
                        $description=$row['description'];
                        
                        $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
                        $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
                        $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();
            
                        if ($is_follow > 0) {
                            $is_follow = 'Yes';
                        } else {
                            $is_follow = 'No';
                        }

                        
                        $img1='';
                	
                		
                		$img1=$row['image'];
                	
                		if($img1!='')
                		{ 
                	    $image1='https://d2c8oti4is0ms3.cloudfront.net/images/Healthyfood_images/'.$row['image']; 
                	 
                		}
                		else
                		{
                		  $image1='';  
                		}	
                        $image=$image1;
                        
                        
                        
                	    $healthy_food_product_view_query = $this->db->query("SELECT id FROM `healthy_food_product_view` where product_id='$id'");
		                $product_view = $healthy_food_product_view_query->num_rows();
                
                $data[]=array(
                            		     "id" => $id,
                            		     "yogapulp_user_id"=>$yogapulp_user_id,
                                         "name"=>$name,
                                         "image"=>$image,
                                         "price"=>$price,
                                         "product_code" => $product_code,
                                         "description" => $description ,
                                         "rating" => $rating,
                                         "review" => $review,
                                         "availibility"=>$availibility,
                                         "discount" => $discount,
                                         "discount_price" => $discount_price,
                                         "product_view" => $product_view,
                                         "follower" => $followers,
                                         "following" => $following,
                                         "is_follow"=>$is_follow,
                                         
                            			);
            }    
            
            $query = $this->db->query("SELECT * FROM `healthy_food_aboutus` ORDER BY id desc");
            $count = $query->num_rows();
            foreach ($query->result_array() as $row) {
                
                $name=$row['name'];
                $about_us=$row['about_us'];
                $email=$row['email'];
                $phone=$row['phone'];
                $address=$row['address'];
            
             $data_about[]=array(
                            		     "name" => $name,
                                         "about_us"=>$about_us,
                                         "email"=>$email,
                                         "phone"=>$phone,
                                         "address" => $address,
                                        
                            			);    
                
                
            }
            
        $resultpost = array(
            'status'=> "200",
            'msg' => "success",
            'yoga pulp' => $data,
            'about_us' => $data_about,
            
            
            );
            
             return $resultpost;
        } 
        
        
        public function yogapulp_get_quotes($pincode)
       {
		$query = $this->db->query("SELECT id,pincode FROM healthy_food_pincode where pincode='$pincode' order by pincode asc");
		$pincode_count= $query->num_rows();
		if($pincode_count>0){

		 $resultpincode='100';
		 return  array("status" => 200,"message" => "success","delivery" => $resultpincode);

		}
		else{
	       return array("status" => 404,"message" => "failure");
		}

     }
     
      public function yogapulp_pincode_check($pincode)
     {
		$query = $this->db->query("select id from healthy_food_pincode WHERE pincode='$pincode' limit 1");
		$pincode_count= $query->num_rows();
		if($pincode_count>0){


		 return  array("status" => 200,"message" => "success");

		}
		else{
	       return array("status" => 404,"message" => "failure");
		}

     }
        
    
      public function yogapulp_cart_order($user_id,$address_id,$product_id,$product_quantity,$product_price)
       {

		$status="Pending";
		$product_status='Pending';

		date_default_timezone_set('Asia/Kolkata');
		$date=date('Y-m-d');
		$b = date("Y"); $c = date("m"); $d = date("d"); $e = date("H"); $f = date("i"); $g = date("s");
		$uni_id=$b.$c.$d.$e.$f.$g;

		$discount='0';
		$grand_total='0';
		$final_total='0';
	    $discount_rate='0';
	    $payType='0';
	    $store_status='0';
	    $customer_status='0';
	  


		$product_id= explode(",",$product_id);
		$product_quantity= explode(",",$product_quantity);
		$product_price= explode(",",$product_price);
		$cnt=count($product_id);
		for($i=0;$i<$cnt;$i++)
		{
		$final_total=$final_total+($product_price[$i]*$product_quantity[$i]);
		}
		$grand_total=$final_total;


		$address_query = $this->db->query("SELECT name,address1,address2,mobile,landmark FROM `user_address` WHERE address_id='$address_id' AND user_id='$user_id'");
        
	    $get_list = $address_query->row();


	    if($get_list)
		{
		$name=$get_list->name;
		$address1=$get_list->address1;
		$address2=$get_list->address2;
		$mobile=$get_list->mobile;
		$landmark=$get_list->landmark;
		}
		else{
	    $name='';
		$address1='';
		$address2='';
		$mobile='';
		$landmark='';
		}


		$organicindia_cart_order_data = array(
			'user_id'=>$user_id,
			'address_id'=>$address_id,
			'uni_id'=>$uni_id,
			'name'=>$name,
			'date'=>$date,
			'status'=>$status,
			'store_status'=>$store_status,
			'customer_status'=>$customer_status,
			'total'=>$grand_total,
			'discount'=>$discount,
			'payType'=>$payType,
			'address1'=>$address1,
			'address2'=>$address2,
			'mobile'=>$mobile,
			'landmark'=>$landmark
			);
		$insert1=$this->db->insert('healthy_food_cart_order',$organicindia_cart_order_data);
		$order_id=$this->db->insert_id();

		$cnt=count($product_id);

		for($i=0;$i<$cnt;$i++)
		{
		$sub_total=$product_price[$i]*$product_quantity[$i];

		$cart_order_products_data = array(
		'order_id'=>$order_id,
		'product_id'=>$product_id[$i],
		'product_quantity'=>$product_quantity[$i],
		'product_price'=>$product_price[$i],
		'sub_total'=>$sub_total,
		'product_status'=>'pending',
		'product_status_type'=>'',
		'product_status_value'=>'',
		'order_status'=>'pending',
		'uni_id'=>$uni_id

		);
		$insert2=$this->db->insert('healthy_food_cart_order_products',$cart_order_products_data);
		}

		if($insert1 & $insert2)
		{
		return array('status' => 200,'message' => 'success');
		}
		else{
		return array('status' => 404,'message' => 'failure');
		}

	}
    
    public function yogapulp_cart_order_list($user_id)
       {

		$query = $this->db->query("SELECT healthy_food_cart_order.date,healthy_food_cart_order.status,healthy_food_cart_order_products.order_id,healthy_food_cart_order_products.uni_id  FROM `healthy_food_cart_order`
		INNER JOIN `healthy_food_cart_order_products`
		ON healthy_food_cart_order.id=healthy_food_cart_order_products.order_id
		WHERE healthy_food_cart_order.user_id='$user_id' GROUP BY healthy_food_cart_order.uni_id");
		$count= $query->num_rows();
		if($count>0){
        foreach($query->result_array() as $row){
		$order_id=$row['order_id'];
		$order_no=$row['uni_id'];
		$order_status=$row['status'];
		$order_date=$row['date'];

		$resultpost[] = array(
			"order_id" => $order_id,
			"order_no"=>$order_no,
			'order_status' => $order_status,
			'order_date'=>$order_date);
		}
		}
		else {
			$resultpost = array();
		}

	   return $resultpost;

     }
     
     public function yogapulp_cart_order_details($user_id,$order_id)
       {

		$query = $this->db->query("SELECT healthy_food_cart_order.uni_id,healthy_food_cart_order.date,healthy_food_cart_order.status,GROUP_CONCAT(healthy_food_product.name) AS product_name,GROUP_CONCAT(healthy_food_product.price) AS product_price,GROUP_CONCAT(healthy_food_cart_order_products.product_quantity) AS product_quantity,healthy_food_product.image,healthy_food_cart_order.name ,healthy_food_cart_order.mobile,healthy_food_cart_order.address1,healthy_food_cart_order.address2,healthy_food_cart_order.landmark
		FROM `healthy_food_cart_order`
		INNER JOIN `healthy_food_cart_order_products`
		ON healthy_food_cart_order.id=healthy_food_cart_order_products.order_id
		INNER JOIN healthy_food_product
		ON healthy_food_product.id=healthy_food_cart_order_products.product_id
		WHERE healthy_food_cart_order.user_id='$user_id' AND healthy_food_cart_order.id='$order_id'");

		$count= $query->num_rows();
		if($count>0){
        foreach($query->result_array() as $row){
$order_no=$row['uni_id'];
$order_date=$row['date'];
$order_status=$row['status'];
$product_name=$row['product_name'];
$product_price=$row['product_price'];
$product_quantity=$row['product_quantity'];
$name=$row['name'];

//$addr_patient_name=$firstname.' '.$lastname;
$addr_address1=$row['address1'];
$addr_address2=$row['address2'];
$addr_landmark=$row['landmark'];
$addr_mobile=$row['mobile'];

		$resultpost[] = array(
		'order_no'=>$order_no,
		'order_date'=>$order_date,
		'order_status'=>$order_status,
		'product_name'=>$product_name,
		'product_price'=>$product_price,
		'product_quantity'=>$product_quantity,
		'addr_patient_name'=>$name,
		'addr_address1'=>$addr_address1,
		'addr_address2'=>$addr_address2,
		'addr_landmark'=>$addr_landmark,
		'addr_mobile'=>$addr_mobile);
		}
	   }
		else {
			$resultpost = array();
		}

	   return $resultpost;

     }
    
    
     public function yogapulp_product_review($user_id, $listing_id, $rating, $review, $service)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date         = date('Y-m-d H:i:s');
        $review_array = array(
            'user_id' => $user_id,
            'product_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('healthy_food_product_review', $review_array);
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    
     public function yogapulp_product_review_list($user_id, $listing_id)
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
        $resultpost   = array();
        $review_count = $this->db->select('id')->from('healthy_food_product_review')->where('product_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT healthy_food_product_review.id,healthy_food_product_review.user_id,healthy_food_product_review.product_id,healthy_food_product_review.rating,healthy_food_product_review.review, healthy_food_product_review.service,healthy_food_product_review.date as review_date,users.id as user_id,users.name as firstname FROM `healthy_food_product_review` INNER JOIN `users` ON healthy_food_product_review.user_id=users.id WHERE healthy_food_product_review.product_id='$listing_id' order by healthy_food_product_review.id desc");

            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                if(base64_encode(base64_decode($review)) === $review){
                    $review=base64_decode($review);
                }			
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);

                $like_count  = $this->db->select('id')->from('healthy_food_product_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('healthy_food_product_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('healthy_food_product_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

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
            $resultpost =  array();
        }

        return $resultpost;
    }
    
    
    public function yogapulp_product_review_likes($user_id, $post_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from healthy_food_product_review_likes where post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `healthy_food_product_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from healthy_food_product_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $healthy_food_product_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('healthy_food_product_review_likes', $healthy_food_product_review_likes);
            $like_query = $this->db->query("SELECT id from healthy_food_product_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }
    
    
     public function yogapulp_product_review_comment($user_id, $post_id, $comment)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');

        $healthy_food_product_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('healthy_food_product_review_comment', $healthy_food_product_review_comment);
        $healthy_food_product_review_comment_query = $this->db->query("SELECT id from healthy_food_product_review_comment where post_id='$post_id'");
        $total_comment = $healthy_food_product_review_comment_query->num_rows();
        return array(
            'status' => 200,
            'message' => 'success',
            'comment' => '1',
            'total_comment' => $total_comment
        );
    }
    
    
     public function yogapulp_product_review_comment_like($user_id, $comment_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from healthy_food_product_review_comment_like where comment_id='$comment_id' and user_id='$user_id'");
        $count= $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `healthy_food_product_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from healthy_food_product_review_comment_like where comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $healthy_food_product_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('healthy_food_product_review_comment_like', $healthy_food_product_review_comment_like);
            $comment_query= $this->db->query("SELECT id from healthy_food_product_review_comment_like where comment_id='$comment_id'");
            $total_comment_like = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'comment_like' => '1',
                'total_comment_like' => $total_comment_like
            );
        }
    }
    
    
     public function yogapulp_product_review_comment_list($user_id, $post_id)
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
        $review_list_count = $this->db->select('id')->from('healthy_food_product_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT healthy_food_product_review_comment.id,healthy_food_product_review_comment.post_id,healthy_food_product_review_comment.comment as comment,healthy_food_product_review_comment.date,users.name,healthy_food_product_review_comment.user_id as post_user_id FROM healthy_food_product_review_comment INNER JOIN users on users.id=healthy_food_product_review_comment.user_id WHERE healthy_food_product_review_comment.post_id='$post_id' order by healthy_food_product_review_comment.id asc");

            foreach ($query->result_array() as $row) {
                $id           = $row['id'];
                $post_id      = $row['post_id'];
                $comment      = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if(base64_encode(base64_decode($comment)) === $comment){
                    $comment=base64_decode($comment);
                }	

                $username     = $row['name'];
                $date         = $row['date'];
                $post_user_id = $row['post_user_id'];

                $like_count   = $this->db->select('id')->from('healthy_food_product_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no  = $this->db->select('id')->from('healthy_food_product_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
                $comment_date = get_time_difference_php($date);

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();

                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file      = $profile_query->source;
                    $userimage     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $date         = get_time_difference_php($date);
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
    
    
    
    
}