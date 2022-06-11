<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class NursingattendantModel extends CI_Model {

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

    public function nursingattendant_list($user_id, $mlat, $mlng) {
        $radius = '5';
        
        $sql = sprintf("SELECT nursing_attendant.*,IFNULL(rating,'') AS rating, IFNULL(review,'') AS review, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM nursing_attendant  where is_active = '1' HAVING distance < '%s' or all_india='1' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius));
      //  echo "SELECT nursing_attendant.*,IFNULL(rating,'') AS rating, IFNULL(review,'') AS review, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM nursing_attendant  where is_active = '1' HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius);
        //$query = $this->db->query("SELECT nursing_attendant.*,IFNULL(rating,'') AS rating, IFNULL(review,'') AS review from nursing_attendant  where is_active = '1'");
        $query = $this->db->query($sql);
        $count = $query->num_rows();
     //   print_r ($query);
        //echo $count;
      
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                 $id = $row['id'];
                $name = $row['name'];
                
                $about_us = $row['about_us'];
                $establishment_year = $row['establishment_year'];
                $certificates = $row['certificates'];
                $address = $row['address'];
                $lat = $row['lat'];
                $lng = $row['lng'];
                $pincode = $row['pincode'];
                $mobile = $row['contact'];
                $city = $row['city'];
                $state = $row['state'];
                $email = $row['email'];
                $image = $row['image'];
                $rating = $row['rating'];
                //$reviews                = $row['review'];        
                 $nursingattendant_user_id = $row['user_id'];
                //$profile_views          = '1558';
                $service_availability = $row['service_availability'];

                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                } else {
                    $image = '';
                }

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $nursingattendant_user_id)->get()->num_rows();

                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $nursingattendant_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $nursingattendant_user_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $nursingattendant_user_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $nursingattendant_user_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }

                $profile_views = $this->db->select('id')->from('nursing_attendant_views')->where('listing_id', $nursingattendant_user_id)->get()->num_rows();
                $reviews = $this->db->select('id')->from('nursing_attendant_review')->where('nursing_attendant_id', $id)->get()->num_rows();


                $nursingattendant_service_list = array();
                $nursingattendant_services_query = $this->db->query("SELECT IFNULL(id,'') AS id,IFNULL(rate,'') AS rate,IFNULL(description,'') AS description,IFNULL(service_name,'') AS service_name,IFNULL(user_discount,'') AS user_discount, IFNULL(medicalwale_discount,'') AS medicalwale_discount FROM `nursing_attendant_services` WHERE `user_id`='$nursingattendant_user_id'");
                foreach ($nursingattendant_services_query->result_array() as $get_serlist) {
                    $service_id = $get_serlist['id'];
                    $package_name = $get_serlist['service_name'];
                    $service_desc = $get_serlist['description'];
                    $service_rate = $get_serlist['rate'];
                    $service_user_discount = $get_serlist['user_discount'];
                    $service_medicalwale_discount = $get_serlist['medicalwale_discount'];
                    

                    $nursingattendant_service_list[] = array(
                        "package_id" => $service_id,
                        "package_name" => $package_name,
                        "package_details" => $service_desc,
                        "price" => $service_rate,
                        "user_discount" => $service_user_discount,
                        "medicalwale_discount" => $service_medicalwale_discount
                    );
                }


                $gallery_list = array();
                $gallery_query = $this->db->query("SELECT `id`,`name`, `type` FROM `nursing_gallery` WHERE `user_id`='$nursingattendant_user_id' ");
                $gallery_array = array();
                $gallery_count = $gallery_query->num_rows();
                if ($gallery_count > 0) {
                    foreach ($gallery_query->result_array() as $rows) {
                        $media_name = $rows['name'];
                        $type = $rows['type'];
                        $gallery = 'https://d2c8oti4is0ms3.cloudfront.net/images/nursing_attendant_images/' . $type . '/' . $media_name;

                        $gallery_list[] = array(
                            "image" => $gallery,
                            "type" => $type
                        );
                    }
                }

 $specialization = $row['services']; 
$area_expertise       = array();
        $query_sp             = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'" . $specialization . "')");
        $total_category       = $query_sp->num_rows();
        if ($total_category > 0) {
            foreach ($query_sp->result_array() as $get_sp) {
                $id               = $get_sp['id'];
                $area_expertised  = $get_sp['area_expertise'];
                $area_expertise[] =  $area_expertised;
                
            }
        } else {
           
            $area_expertise[] = explode(",",$specialization) ;
        }


                $resultpost[] = array(
                    'nursing_attendant_id' => $id,
                    'nursing_user_id' => $nursingattendant_user_id,
                    'name' => $name,
                    'listing_type' => "12",
                    'about_us' => $about_us,
                    'establishment_year' => $establishment_year,
                    'specialization' => $area_expertise,
                    'nursingattendant_service_list' => $nursingattendant_service_list,
                    'gallery_list' => $gallery_list,
                    'address' => $address,
                    'mobile' => $mobile,
                    "exotel_no" => '02233721563',
                    'lat' => $lat,
                    'lng' => $lng,
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'rating' => $rating,
                    'review' => $reviews,
                    'image' => $userimage,
                    'followers' => $followers,
                    'following' => $following,
                    'profile_views' => $profile_views,
                    'is_follow' => $is_follow,
                    'service_availability' => $service_availability
                );
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }
    
    public function nursung_appoinment($user_id,$listing_id,$booking_id,$type,$payment_method,$amount) {
        
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $appointment_array = array(
            'user_id' => $user_id,
            'listing_id' => $listing_id,
            'booking_id' => $booking_id,
            'payment_method' => $payment_method,
            'type' => $type,
            'amount' => $amount
        );
        $this->db->insert('user_appointments', $appointment_array);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }
    
    public function nursingattendant_book_package($user_id,$listing_id,$package_id,$name,$gender, $patiente_name, $patiente_age, $patiente_gender, $city, $patiente_condition, $attendent_time, $attendant_hour, $tentative_intime, $tentative_outtime, $nursing_gender, $attendant_needed, $appoinment_booking_date) {
        $lastid ="";
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $booking_id           = date("YmdHis");
        $gallery_query = $this->db->query("SELECT email,phone,avatar_id FROM users WHERE id='$user_id' ");
        $email = $gallery_query->row()->email;
        $phone = $gallery_query->row()->phone;
        
        $gallery_query2 = $this->db->query("SELECT email,phone,avatar_id FROM users WHERE id='$listing_id' ");
        $avatar_id = $gallery_query2->row()->avatar_id;
        
        //echo "SELECT source FROM media WHERE id='$avatar_id'";
        $_query = $this->db->query("SELECT source FROM media WHERE id='$avatar_id'");
        $package_image = $_query->row()->source;
        
        $Insert_health_record  = array(
            'user_id' => $user_id,
            'patient_name' => $patiente_name,
            'patient_age' => $patiente_age,
            'patient_city' =>$city,
            'gender' => $patiente_gender,
            'created_at'=>date('Y-m-d H:i:s'),
            'patient_condition'=>$patiente_condition/*,
            'vendor_type'=>'12'*/
        );
        $this->db->insert('health_record', $Insert_health_record);
        $patient_id=$this->db->insert_id();
        
        
        $appointment_array = array(
            'user_id' => $user_id,
            'listing_id' => $listing_id,
            'patient_id' => $patient_id,
            'booking_id' => $booking_id,
            'package_id'=>$package_id,
            'user_name' => $name,
            'user_gender' => $gender,
            'user_email' => $email,
            'user_mobile' => $phone,
            'booking_date'=>$date,
            'joining_date'=>$appoinment_booking_date,
            'status' => 'pending',
            'vendor_id' => '12' 
        );
        
        $gallery_query = $this->db->query("SELECT * FROM nursing_attendant_services WHERE id='$package_id'");
        $num_count_data = $gallery_query->num_rows();
        if($num_count_data > 0){
        $package_name = $gallery_query->row()->service_name; 
        $package_amount = $gallery_query->row()->rate;
        }else{
            $package_name = ""; 
            $package_amount = ""; 
        }
        
        
        $nursing_booking_details = array(
            'user_id' => $user_id,
            'booking_id' => $booking_id,
            'patient_id' => $patient_id,
            'patient_name' => $patiente_name,
            'patient_age' => $patiente_age,
            'patient_gender' => $patiente_gender,
            'patient_city' => $city,
            'patiente_condition' => $patiente_condition,
            
            'package_id' => $package_id,
            'package_name' => $package_name,
            'package_amount' => $package_amount,
            'package_image' => $package_image,
            'listing_id' => $listing_id,
            
            'attendent_time' => $attendent_time,
            'attendant_hour' => $attendant_hour,
            'tentative_intime'=>$tentative_intime,
            'tentative_outtime'=>$tentative_outtime,
            'nursing_gender' => $nursing_gender,
            'attendant_needed' => $attendant_needed,
            'created_at'=>date('Y-m-d H:i:s'),
            'updated_at'=>date('Y-m-d H:i:s')
        );
        
        
        
        $booking_master_query = $this->db->query("SELECT * FROM booking_master WHERE user_id='$user_id' AND  listing_id = '$listing_id' AND vendor_id='12' AND status = 'pending'");
        $booking_master_count = $booking_master_query->num_rows();
        //die();
        //if ($booking_master_count == 1) {
             
             $this->db->insert('booking_master', $appointment_array);
             $lastid=$this->db->insert_id();
             $this->db->insert('nursing_booking_details', $nursing_booking_details);
             if($lastid){
                 $this->insert_notification_book_package($user_id, $lastid, 'user',$listing_id);
             }
        //}
          return  $lastid;
    }
    
    public function insert_notification_book_package($user_id, $post_id, $name, $listing_id){
        $data = array(
							'user_id'		=> $user_id,
							'post_id' 		=> $post_id,
							'timeline_id'   => $user_id,
							'type'          => 'booking',
							'seen'          => '1',
							'notified_by'   => $listing_id,
							'description'   => ' booked a package from nursing attandent',
							'created_at'	=> curr_date(), 
							'updated_at'	=> curr_date()
							
					);
		//print_r($data);
		$this->db->insert("notifications", $data);

		if($this->db->affected_rows() > 0)
		{
		    
		    return true; // to the controller
		}
		else{
			return false;
		}
	}
    
    public function nursingattendant_details($user_id,$listing_id) {

        $query = $this->db->query("SELECT nursing_attendant.*,IFNULL(rating,'') AS rating, IFNULL(review,'') AS review from nursing_attendant  where is_active = '1' AND user_id='$listing_id'");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $name = $row['name'];
                $specialization = explode(',', $row['services']);
                $about_us = $row['about_us'];
                $establishment_year = $row['establishment_year'];
                $certificates = $row['certificates'];
                $address = $row['address'];
                $lat = $row['lat'];
                $lng = $row['lng'];
                $pincode = $row['pincode'];
                $mobile = $row['contact'];
                $city = $row['city'];
                $state = $row['state'];
                $email = $row['email'];
                $image = $row['image'];
                $rating = $row['rating'];
                //$reviews                = $row['review'];        
                $nursingattendant_user_id = $row['user_id'];
                //$profile_views          = '1558';
                $service_availability = $row['service_availability'];

                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                } else {
                    $image = '';
                }

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $nursingattendant_user_id)->get()->num_rows();

                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $nursingattendant_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $nursingattendant_user_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $nursingattendant_user_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $nursingattendant_user_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }

                $profile_views = $this->db->select('id')->from('nursing_attendant_views')->where('listing_id', $nursingattendant_user_id)->get()->num_rows();
                $reviews = $this->db->select('id')->from('nursing_attendant_review')->where('nursing_attendant_id', $id)->get()->num_rows();


                $nursingattendant_service_list = array();
                $nursingattendant_services_query = $this->db->query("SELECT IFNULL(id,'') AS id,IFNULL(rate,'') AS rate,IFNULL(description,'') AS description,IFNULL(service_name,'') AS service_name FROM `nursing_attendant_services` WHERE `user_id`='$nursingattendant_user_id'");
                foreach ($nursingattendant_services_query->result_array() as $get_serlist) {
                    $service_id = $get_serlist['id'];
                    $package_name = $get_serlist['service_name'];
                    $service_desc = $get_serlist['description'];
                    $service_rate = $get_serlist['rate'];

                    $nursingattendant_service_list[] = array(
                        "package_id" => $service_id,
                        "package_name" => $package_name,
                        "package_details" => $service_desc,
                        "price" => $service_rate
                    );
                }


                $gallery_list = array();
                $gallery_query = $this->db->query("SELECT `id`,`name`, `type` FROM `nursing_gallery` WHERE `user_id`='$nursingattendant_user_id' ");
                $gallery_array = array();
                $gallery_count = $gallery_query->num_rows();
                if ($gallery_count > 0) {
                    foreach ($gallery_query->result_array() as $rows) {
                        $media_name = $rows['name'];
                        $type = $rows['type'];
                        $gallery = 'https://d2c8oti4is0ms3.cloudfront.net/images/nursing_attendant_images/' . $type . '/' . $media_name;

                        $gallery_list[] = array(
                            "image" => $gallery,
                            "type" => $type
                        );
                    }
                }


                $resultpost[] = array(
                    'nursing_attendant_id' => $id,
                    'nursing_user_id' => $nursingattendant_user_id,
                    'name' => $name,
                    'listing_type' => "12",
                    'about_us' => $about_us,
                    'establishment_year' => $establishment_year,
                    'specialization' => $specialization,
                    'nursingattendant_service_list' => $nursingattendant_service_list,
                    'gallery_list' => $gallery_list,
                    'address' => $address,
                    'mobile' => $mobile,
                    'lat' => $lat,
                    'lng' => $lng,
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'rating' => $rating,
                    'review' => $reviews,
                    'image' => $userimage,
                    'followers' => $followers,
                    'following' => $following,
                    'profile_views' => $profile_views,
                    'is_follow' => $is_follow,
                    'service_availability' => $service_availability
                );
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }

    public function add_review($user_id, $listing_id, $rating, $review, $service) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'user_id' => $user_id,
            'nursing_attendant_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('nursing_attendant_review', $review_array);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    public function review_list($user_id, $listing_id) {

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
        $review_count = $this->db->select('id')->from('nursing_attendant_review')->where('nursing_attendant_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT nursing_attendant_review.id,nursing_attendant_review.user_id,nursing_attendant_review.nursing_attendant_id,nursing_attendant_review.rating,nursing_attendant_review.review, nursing_attendant_review.service,nursing_attendant_review.date as review_date,users.id as user_id,users.name as firstname FROM `nursing_attendant_review` INNER JOIN `users` ON nursing_attendant_review.user_id=users.id WHERE nursing_attendant_review.nursing_attendant_id='$listing_id' order by nursing_attendant_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $user_id = $row['user_id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '7') {
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

                $like_count = $this->db->select('id')->from('nursing_attendant_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('nursing_attendant_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('nursing_attendant_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

                $resultpost[] = array(
                    'id' => $id,
                    'user_id' => $user_id,
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

 public function review_with_comment($user_id, $listing_id) {

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
        $review_count = $this->db->select('id')->from('nursing_attendant_review')->where('nursing_attendant_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT nursing_attendant_review.id,nursing_attendant_review.user_id,nursing_attendant_review.nursing_attendant_id,nursing_attendant_review.rating,nursing_attendant_review.review, nursing_attendant_review.service,nursing_attendant_review.date as review_date,users.id as user_id,users.name as firstname FROM `nursing_attendant_review` INNER JOIN `users` ON nursing_attendant_review.user_id=users.id WHERE nursing_attendant_review.nursing_attendant_id='$listing_id' order by nursing_attendant_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $user_id = $row['user_id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                /*if ($id > '7') {
                    $decrypt = $this->decrypt($review);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $review) {
                        $review = $decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($review)) === $review) {
                        $review = base64_decode($review);
                    }
                }*/
                $service = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('nursing_attendant_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('nursing_attendant_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('nursing_attendant_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

        $review_list_count = $this->db->select('id')->from('nursing_attendant_review_comment')->where('post_id', $id)->get()->num_rows();
        if ($review_list_count) {
             $resultcomment = array();
            $querycomment = $this->db->query("SELECT nursing_attendant_review_comment.id,nursing_attendant_review_comment.post_id,nursing_attendant_review_comment.comment as comment,nursing_attendant_review_comment.date,users.name,nursing_attendant_review_comment.user_id as post_user_id FROM nursing_attendant_review_comment INNER JOIN users on users.id=nursing_attendant_review_comment.user_id WHERE nursing_attendant_review_comment.post_id='$id' order by nursing_attendant_review_comment.id asc");

            foreach ($querycomment->result_array() as $rowc) {
                $comment_id = $rowc['id'];
                $post_id = $rowc['post_id'];
                $comment = $rowc['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
               /* if ($id > '8') {
                    $comment_decrypt = $this->decrypt($comment);
                    $comment_encrypt = $this->encrypt($comment_decrypt);
                    if ($comment_encrypt == $comment) {
                        $comment = $comment_decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($comment)) === $comment) {
                        $comment = base64_decode($comment);
                    }
                }*/
                $usernamec = $rowc['name'];
                $date = $rowc['date'];
                $post_user_id = $rowc['post_user_id'];

                $like_countc = $this->db->select('id')->from('nursing_attendant_review_comment_like')->where('comment_id', $comment_id)->get()->num_rows();
                $like_yes_noc = $this->db->select('id')->from('nursing_attendant_review_comment_like')->where('comment_id', $comment_id)->where('user_id', $user_id)->get()->num_rows();
                $comment_date = get_time_difference_php($date);

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();

                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimagec = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimagec = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $date = get_time_difference_php($date);
                $resultcomment[] = array(
                    'id' => $comment_id,
                    'username' => $usernamec,
                    'userimage' => $userimagec,
                    'like_count' => $like_countc,
                    'like_yes_no' => $like_yes_noc,
                    'post_id' => $post_id,
                    'comment' => $comment,
                    'comment_date' => $comment_date
                );
            }
        } else {
            $resultcomment = array();
        }



                $resultpost[] = array(
                    'id' => $id,
                    'user_id' => $user_id,
                    'username' => $username,
                    'rating' => $rating,
                    'review' => $review,
                    'service' => $service,
                    'review_date' => $review_date,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'comment_count' => $post_count,
                    'comments'=>$resultcomment
                );
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }




    public function review_like($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from nursing_attendant_review_likes where post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `nursing_attendant_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from nursing_attendant_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $nursing_attendant_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('nursing_attendant_review_likes', $nursing_attendant_review_likes);
            $like_query = $this->db->query("SELECT id from nursing_attendant_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }

    public function review_comment($user_id, $post_id, $comment) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');

        $nursing_attendant_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('nursing_attendant_review_comment', $nursing_attendant_review_comment);
        $nursing_attendant_review_comment_query = $this->db->query("SELECT id from nursing_attendant_review_comment where post_id='$post_id'");
        $total_comment = $nursing_attendant_review_comment_query->num_rows();
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
        $count_query = $this->db->query("SELECT id from nursing_attendant_review_comment_like where comment_id='$comment_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `nursing_attendant_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from nursing_attendant_review_comment_like where comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $nursing_attendant_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('nursing_attendant_review_comment_like', $nursing_attendant_review_comment_like);
            $comment_query = $this->db->query("SELECT id from nursing_attendant_review_comment_like where comment_id='$comment_id'");
            $total_comment_like = $comment_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'comment_like' => '1',
                'total_comment_like' => $total_comment_like
            );
        }
    }

    public function review_comment_list($user_id, $post_id) {

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

        $review_list_count = $this->db->select('id')->from('nursing_attendant_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT nursing_attendant_review_comment.id,nursing_attendant_review_comment.post_id,nursing_attendant_review_comment.comment as comment,nursing_attendant_review_comment.date,users.name,nursing_attendant_review_comment.user_id as post_user_id FROM nursing_attendant_review_comment INNER JOIN users on users.id=nursing_attendant_review_comment.user_id WHERE nursing_attendant_review_comment.post_id='$post_id' order by nursing_attendant_review_comment.id asc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
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
                $username = $row['name'];
                $date = $row['date'];
                $post_user_id = $row['post_user_id'];

                $like_count = $this->db->select('id')->from('nursing_attendant_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('nursing_attendant_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
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

    public function nursing_views($user_id, $listing_id) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $nursing_views_array = array(
            'user_id' => $user_id,
            'listing_id' => $listing_id,
            'date' => $date
        );
        $this->db->insert('nursing_attendant_views', $nursing_views_array);

        $nursing_attendant_views = $this->db->select('id')->from('nursing_attendant_views')->where('listing_id', $listing_id)->get()->num_rows();

        return array(
            'status' => 200,
            'message' => 'success',
            'fitness_views' => $nursing_attendant_views
        );
    }
    
    /*public function nursing_booking_cancel($user_id, $listing_id, $booking_id, $status){
        
        
        $nursing_booking_cancel_array = array(
            'status' => $status
        );
        
        $this->db->where('user_id',$user_id);
        $this->db->where('listing_id',$listing_id);
        $this->db->where('booking_id',$booking_id);
        $this->db->update('booking_master', $nursing_booking_cancel_array);
        
        if($this->db->affected_rows()){
            return array(
                'status' => 200,
                'message' => 'success'
            );
        }
    }*/

}
