<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PartnerpersonaltrainersModel extends CI_Model
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
    
        function randomPassword()
    {
        $pass = rand(100000,999999);
        return $pass;
    }
    
    public function signup($category, $type, $name, $email, $phone, $gender, $dob, $token, $agent)
     {
        if ($name != '' && $email != ''  && $phone != '') {			
			$vendor_id=$type;				
            $query = $this->db->query("SELECT id from users WHERE phone='$phone' ");
            $count = $query->num_rows();            
            $query2 = $this->db->query("SELECT id from users WHERE email='$email' ");
            $count2 = $query2->num_rows();           
            
            if ($count > 0) {
                return array(
                    'status' => 208,
                    'message' => 'Phone number already exist'
                );
            } 
             else if ($count2 > 0) {
                return array(
                    'status' => 208,
                    'message' => 'Email id already exist'
                );
            }  
            else {
                date_default_timezone_set('Asia/Kolkata');
                $updated_at = date('Y-m-d H:i:s');
				
				
                $pas       = $this->randomPassword();
                $vendor_id = $vendor_id;
                $password  = md5($pas);
						
                $user_data = array(
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'gender' => $gender,
                    'dob' => $dob,
                    'vendor_id' => $vendor_id,
                    'password' => $password,
                    'token' => $token,  
                    'agent' => $agent,
                    'token_status' => '1',
                    'created_at' => $updated_at
                );
	
                $success = $this->db->insert('users', $user_data);
                $id = $this->db->insert_id();
				
			    $personal_trainers_data = array( 
					'user_id' => $id,
					'category_id' => $category,               
                    'center_name' => $name,              
                    'email' => $email,
                    'contact' => $phone,
                    'date' => $updated_at,
                    'image' => ""
                );
                $success = $this->db->insert('personal_trainers', $personal_trainers_data);
                if ($success) {
                    $date_array = array(
                        'listing_id' => $id,
                        'name' => $name,
                        'type' => $vendor_id,
                        'phone' => $phone,
                        'email' => $email
                    );

                    return array(
                        'status' => 201,
                        'message' => 'success',
                        'data' => $date_array
                    );
                } else {
                    return array(
                        'status' => 208,
                        'message' => 'failed'
                    );
                }
			}
		}
	}
 
 
  	public function personaltrainers_profile_pic($listing_id, $profile_pic_file)
    {
        $query = $this->db->query("UPDATE personal_trainers SET image='$profile_pic_file' WHERE user_id='$listing_id'");
        
        $usr_query    = $this->db->query("SELECT avatar_id FROM users WHERE id='$listing_id'");
        $get_usr      = $usr_query->row_array();
        $avatar_id    = $get_usr['avatar_id'];
        date_default_timezone_set('Asia/Kolkata');
        $updated_at = date('Y-m-d H:i:s');
        
          
        if($avatar_id=='0'){
            
           $media_data = array(
                'title' => $profile_pic_file,
                'type' => 'image',
                'source' => $profile_pic_file,
                'created_at' => $updated_at,
                'deleted_at' => $updated_at
            );

        $media_insert = $this->db->insert('media', $media_data);
        $a_id = $this->db->insert_id();     
        $query = $this->db->query("UPDATE users SET avatar_id='$a_id' WHERE id='$listing_id'");
        }  
        else{
        $query = $this->db->query("UPDATE media SET title='$profile_pic_file',source='$profile_pic_file' WHERE id='$avatar_id'");   
        }

        return array(
            'status' => 200,
            'message' => 'success'
        );
    }




public function partner_statistics($listing_id, $type)
    {
        $rating        = '5.0';
        $profile_views = '0';
        $reviews       = '0';

        $query = $this->db->query("SELECT `image`,lat_lng_status,free_trial_session_status,kyc_pic_status FROM `personal_trainers` WHERE user_id='$listing_id'");

		$row   = $query->row_array();
		$profile_pic                = $row['image'];
		$lat_lng_status             = $row['lat_lng_status'];  
		$free_trial_session_status  = $row['free_trial_session_status'];
		$kyc_pic_status             = $row['kyc_pic_status'];

		
		    if ($lat_lng_status == '1') {
                $lat_lng_status = '10';
            } else {
                $lat_lng_status = '0';
            }  

			if ($free_trial_session_status == '1') {
                $free_trial_session_status = '10';
            } else {
                $free_trial_session_status = '0';
            } 

			if ($kyc_pic_status == '1') {
                $kyc_pic_status = '10';
            } else {
                $kyc_pic_status = '0';
            }
			
			if ($profile_pic!= '' ) {
                $profile_pic = '15';
            } else {
                $profile_pic = '0';
            }
			
		$step1 = '25';
		
        $profile_completed = $step1 + $profile_pic + $lat_lng_status + $free_trial_session_status + $kyc_pic_status ;

        $followers    = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
        $following    = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
        $resultpost = array(
            'rating' => (double)$rating,
            'profile_views' => (int)$profile_views,
            'reviews' => (int)$reviews,
            'profile_completed' => (double)$profile_completed,
            'followers' => (int)$followers,
            'following' => (int)$following
        );

        return $resultpost;

    }



 public function personaltrainers_details($listing_id,$type)
    {
        $query = $this->db->query("SELECT personal_trainers.date,personal_trainers.image, personal_trainers.center_name, personal_trainers.is_approval, personal_trainers.is_active, users.phone, users.email,users.gender, users.dob FROM personal_trainers LEFT JOIN users ON personal_trainers.user_id=users.id WHERE personal_trainers.user_id='$listing_id'");


        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {

                $reg_date        = $row['date'];
                $name    		 = $row['center_name'];        
                $email     		 = $row['email'];            
                $gender          = $row['gender'];
                $dob             = $row['dob']; 
                $phone           = $row['phone'];
                $is_approval     = $row['is_approval'];
                $is_active       = $row['is_active'];
               
                $profile_pic        = $row['image'];
                if ($row['image'] != '') {
                    $profile_pic = $row['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }


                $resultpost = array(
                    "reg_date" => $reg_date,
                    "name" => $name,
                    "profile_pic" => $profile_pic,           
                    "email" => $email,                
                    "gender" => $gender,
                    "dob" => $dob,                 
                    "is_approval" => (int)$is_approval,
                    "is_active" => (int)$is_active,
					"phone"=>  $phone
                   
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;

    }
    
    
    
    public function personaltrainers_lat_log($latitude, $longitude,$radius, $listing_id)
    {
        
      $query = $this->db->query("UPDATE personal_trainers SET lat='$latitude',lng='$longitude',radius='$radius',lat_lng_status='1'  WHERE user_id='$listing_id'");

        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
      
    
   public function personaltrainers_is_free_trial_session($listing_id,$is_free_trial_session)
    {
        
      $query = $this->db->query("UPDATE personal_trainers SET is_free_trial_session='$is_free_trial_session',free_trial_session_status='1' WHERE user_id='$listing_id'");

        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    
   public function personaltrainers_kyc_pic($listing_id, $kyc_pic_file)
    {
        
    $query = $this->db->query("UPDATE personal_trainers SET kyc_pic='$kyc_pic_file',kyc_pic_status='1' WHERE user_id='$listing_id'");

        return array(
            'status' => 200,
            'message' => 'success'
        );
    }  
 
    

    public function personaltrainers_is_approval($listing_id)
    {
        	date_default_timezone_set('Asia/Kolkata');
        	$approval_date = date('Y-m-d H:i:s');    
        	 
        $query = $this->db->query("UPDATE personal_trainers SET is_approval='1',approval_date='$approval_date' WHERE user_id='$listing_id'");



        function send_gcm_notify($title,$reg_id,$msg,$img_url,$tag,$agent)
        {
		date_default_timezone_set('Asia/Kolkata');
		$approval_send_date = date('j M Y h:i A');

            define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
            $fields  = array(
                'to' => $reg_id,
                'priority' => "high",
                 $agent === 'android' ? 'data' : 'notification' => array(
                    "title" => $title,
                    "message" => $msg,
                    "image" => $img_url,
                    "tag" => $tag,
					'sound' => 'default',
					"notification_type" => "approval_sent",
					"approval_send_date"=>$approval_send_date

			//public static final String SENT_FOR_APPROVAL = "approval_sent";
			//public static final String APPROVAL_FROM_ADMIN = "approval_received";
			//approval_send_date -mobile
            //approval_receive_date -admin

                )
            );
            $headers = array(
                GOOGLE_GCM_URL,
                'Content-Type: application/json',
    
				//'Authorization: key=AIzaSyDTr3BzDHKj_6FnUiZXT__VCmamH-_gupM' android users
				//'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' android partner
				//'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE' ios partner
				$agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
             
        
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
            //echo $result;
           // echo $reg_id;
        }

       
        $customer_token = $this->db->select('token,agent,token_status')->from('users')->where('id', $listing_id)->get()->row();
        $token_status   = $customer_token->token_status;

        if ($token_status > 0) {
            $agent    = $customer_token->agent;
            $reg_id    = $customer_token->token;
            $img_url   = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/logo.png';
            $tag       = 'text';
            $key_count = '1';

            $title = 'Thank you for sending your profile';
            $msg   = 'We will review your profile and update you soon.';

			//When active by admin
            //$title = 'Welcome, your pharmacy has been approved.';
			//$msg   = 'Congratulations! Your pharmacy listing has been live now.';

            send_gcm_notify($title,$reg_id,$msg,$img_url,$tag,$agent);

        }

        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
     public function personaltrainers_my_profile_details($listing_id)
    {


        $query = $this->db->query("SELECT `lat`, `lng`, `radius`,`lat_lng_status`,`is_free_trial_session`, `free_trial_session_status`, `kyc_pic`, `kyc_pic_status`,`is_approval`,`is_active`  FROM `personal_trainers` WHERE user_id='$listing_id'");
        $count = $query->num_rows();
        if ($count > 0) {
                $personaltrainers_lat_lat  	 = array();
                $free_trial_session       	 = array();
                $personaltrainers_kyc_pic	 = array();
                $approval_and_active		 = array();
			
			
        foreach ($query->result_array() as $row) {


                //personaltrainers_lat_lat  (1)
                $lat   = $row['lat'];
                $lng   = $row['lng'];
                $radius   = $row['radius'];
                $lat_lng_status   = $row['lat_lng_status'];
                $personaltrainers_lat_lat = array(
                    'status' 	=>  (int)$lat_lng_status,
                    'latitude'	=>  (double)$lat,
                    'longitude' =>  (double)$lng,
                    'radius' 	=>  (int)$radius
                );


                //free_trial_session  (2)
                $is_free_trial_session     = $row['is_free_trial_session'];        
                $free_trial_session_status = $row['free_trial_session_status'];

                $free_trial_session = array(
                    'status' =>  (int)$free_trial_session_status,
                    'is_free_trial_session' => $is_free_trial_session
                );


                //personaltrainers_kyc_pic  (3)
                $kyc_pic        = $row['kyc_pic'];
                $kyc_pic        = str_replace(' ', '%20', $kyc_pic);
                $kyc_pic        = 'https://d2c8oti4is0ms3.cloudfront.net/images/personal_trainers_images/' .$kyc_pic;
                $kyc_pic_status = $row['kyc_pic_status'];

                $personaltrainers_kyc_pic = array(
                    'status' => (int)$kyc_pic_status,
                    'kyc_pic' => $kyc_pic
                );


               //is_approval is_active
                $is_approval = $row['is_approval'];
                $is_active = $row['is_active'];

                $approval_and_active = array(
                    'is_approval' =>  (int)$is_approval,
                    'is_active' => (int)$is_active
                );

           


                $data = array(
                    "personaltrainers_lat_lat" => $personaltrainers_lat_lat,
                    "free_trial_session" => $free_trial_session,
                    "personaltrainers_kyc_pic" => $personaltrainers_kyc_pic,             
                    "approval_and_active" => $approval_and_active,
                  
                );

                $resultpost = array(
                    "status" => 200,
                    "message" => 'success',
                    "data" => $data
                );
            }
        } else {
            $data=array();
            return array(
			"status" => 200,
			"message" => "success",
			"count"=>0,
			"data"=>$data
			);
        }
        return $resultpost; 
    }



   public function personaltrainers_update_profile($listing_id,$name, $email, $phone, $gender, $dob)
    {
           $query = $this->db->query("SELECT id from users WHERE phone='$phone' AND id <> '$listing_id'");
            $count = $query->num_rows();            
            $query2 = $this->db->query("SELECT id from users WHERE email='$email' AND id <> '$listing_id'");
            $count2 = $query2->num_rows();           
            
            if ($count > 0) {
                return array(
                    'status' => 208,
                    'message' => 'Phone number already exist'
                );
            } 
             else if ($count2 > 0) {
                return array(
                    'status' => 208,
                    'message' => 'Email id already exist'
                );
            }  
            else {
                
		$query = $this->db->query("UPDATE personal_trainers INNER JOIN users ON personal_trainers.user_id=users.id
		SET
		users.phone = '$phone',
		users.email = '$email',
		users.gender = '$gender',
		users.dob = '$dob',	
		users.name = '$name',
		personal_trainers.center_name = '$name',
		personal_trainers.contact = '$phone',
		personal_trainers.email = '$email'
		WHERE personal_trainers.user_id ='$listing_id';");

        return array(
            'status' => 200,
            'message' => 'success'
        );
       }

    }

}