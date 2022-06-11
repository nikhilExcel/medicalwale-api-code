<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LoginModel extends CI_Model
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
    
    public function login($username, $password)
    {
        date_default_timezone_set('Asia/Kolkata');
        $q = $this->db->select('password,id')->from('api_users')->where('username', $username)->get()->row();
        if ($q == "") {
            return array(
                'status' => 204,
                'message' => 'Username not found.'
            );
        } else {
            $hashed_password = $q->password;
            $id              = $q->id;
            if (hash_equals($hashed_password, crypt($password, $hashed_password))) {
                $token = '25iwFyq/LSO1U';
                $this->db->trans_start();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array(
                        'status' => 500,
                        'message' => 'Internal server error.'
                    );
                } else {
                    $this->db->trans_commit();
                    return array(
                        'status' => 200,
                        'message' => 'Successfully login.',
                        'id' => $id,
                        'token' => $token
                    );
                }
            } else {
                return array(
                    'status' => 204,
                    'message' => 'Wrong password.'
                );
            }
        }
    }
    
    public function sales_login($username, $password)
    {
        date_default_timezone_set('Asia/Kolkata');
        $q = $this->db->select('name,email,password,id')->from('vendor_user')->where('email', $username)->get()->row();
        if ($q == "") {
            return array(
                'status' => 204,
                'message' => 'Username not found.'
            );
        } else {
            $hashed_password =$q->password;
            $password = md5($password);
            $id       = $q->id;
            $name     = $q->name;
            $email    = $q->email;
            if ($hashed_password==$password) {
                return array(
                        'status' => 200,
                        'message' => 'success',
                        'id' => (int)$id,
                        'name' => $name,
                        'email' => $email
                    );
            } else {
                return array(
                    'status' => 204,
                    'message' => 'failure',
                        'id' => 0,
                        'name' => '',
                        'email' => ''
                );
            }
        }
    }
    
    public function logout()
    {
        $users_id = $this->input->get_request_header('User-ID', TRUE);
        $token    = $this->input->get_request_header('Authorization', TRUE);
        $this->db->where('users_id', $users_id)->where('token', $token)->delete('api_users_authentication');
        return array(
            'status' => 200,
            'message' => 'Successfully logout.'
        );
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
                'message' => 'Unauthorizedq.'
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
	
	public function sendotp($phone)
    {
        $otp_code = rand(100000, 999999);
        $message  = 'Please enter this One Time Password : '.$otp_code.' to verify your mobile number.';
		$post_data = array('From' => '02233721563','To' => $phone,'Body' => $message);
		$exotel_sid = "aegishealthsolutions";
		$exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
		$url = "https://".$exotel_sid.":".$exotel_token."@twilix.exotel.in/v1/Accounts/".$exotel_sid."/Sms/send";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FAILONERROR, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
		$http_result = curl_exec($ch);
		curl_close($ch);
		
		$querys = $this->db->query("UPDATE `users` SET `otp_code`='$otp_code' WHERE phone='$phone'");
		
        return array(
            'status' => 204,
            'message' => 'success',
            'otp_code' => $otp_code
        );
    }
    
    public function userdetails($listing_id, $user_id)
    {
        if ($listing_id == "") {
            return array(
                'status' => 208,
                'message' => 'Please enter listing_id'
            );
        } else {
            $check_user = $this->db->select('id')->from('users')->where('id', $user_id)->get()->row();
            if ($check_user != "") {
                $id = $check_user->id;
                	$q  = $this->db->select('password,id,name,IFNULL(activity_level,"") AS activity_level,phone,email,gender,dob,IFNULL(height,"") AS height,IFNULL(weight,"") AS weight,IFNULL(marital_status,"") AS marital_status,IFNULL(blood_group,"") AS blood_group,IFNULL(blood_is_active,"") AS blood_is_active,IFNULL(diet_fitness,"") AS diet_fitness,IFNULL(organ_donor,"") AS organ_donor,sex_history,exercise_level,health_condition,health_insurance,addiction,allergies,IFNULL(bmi,"") AS bmi ,IFNULL(heradiatry_problem,"") AS heradiatry_problem,IFNULL(lat,"") AS lat,IFNULL(lng,"") AS lng,IFNULL(map_location,"") AS map_location')->from('users')->where('users.id', $listing_id)->get()->row();
                
                $id               = $q->id;
                $name             = $q->name;
                $phone            = $q->phone;
                $email            = $q->email;
                $gender           = $q->gender;
                $dob              = $q->dob;
                $height           = $q->height;
                $weight           = $q->weight;
                $marital_status   = $q->marital_status;
                $blood_group      = $q->blood_group;
                $diet_fitness     = $q->diet_fitness;
                $organ_donor      = $q->organ_donor;
                $sex_history      = $q->sex_history;
                $exercise_level   = $q->exercise_level;
                $activity_level   = $q->activity_level;
                $health_condition = $q->health_condition;
                $allergies        = $q->allergies;
                $bmi              = $q->bmi;
                $health_insurance = $q->health_insurance;
                $addiction        = $q->addiction;
                $blood_is_active        = $q->blood_is_active;
                $heradiatry_problem = $q->heradiatry_problem;
                $lat = $q-> lat;
                $lng = $q-> lng;
                $map_location = $q-> map_location;
                $img_count        = $this->db->select('media.id')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $id)->get()->num_rows();
                
                if ($img_count > 0) {
                    $media    = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $id)->get()->row();
                    $img_file = $media->source;
                    $image    = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                     $image_status = '5' ;
                } else {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    $image_status = '0' ;
                }
                
                 if ($height == '') {
                $height_status = '0';
            } else {
                $height_status = '5';
            }
 if ($weight == '') {
                $weight_status = '0';
            } else {
                $weight_status = '5';
            }
 if ($marital_status == '') {
                $marital_status_is = '0';
            } else {
                $marital_status_is = '5';
            }
 if ($blood_group == '') {
                $blood_group_status = '0';
            } else {
                $blood_group_status = '5';
            }
 if ($diet_fitness == '') {
                $diet_fitness_status = '0';
            } else {
                $diet_fitness_status = '5';
            }
 if ($organ_donor == '') {
                $organ_donor_status = '0';
            } else {
                $organ_donor_status = '5';
            }
 if ($sex_history == '') {
                $sex_history_status = '0';
            } else {
                $sex_history_status = '5';
            }
 if ($activity_level == '') {
                $activity_level_status = '0';
            } else {
                $activity_level_status = '5';
            }
 if ($health_condition == '') {
                $health_condition_is = '0';
            } else {
                $health_condition_is = '5';
            }
 if ($allergies == '') {
                $allergies_status = '0';
            } else {
                $allergies_status = '5';
            }
 if ($bmi == '') {
                $bmi_status = '0';
            } else {
                $bmi_status = '5';
            }
 if ($health_insurance == '') {
                $health_insurance_status = '0';
            } else {
                $health_insurance_status = '5';
            }	
 if ($addiction == '') {
                $addiction_status = '0';
            } else {
                $addiction_status = '5';
            }
 if ($heradiatry_problem == '') {
                $heradiatry_problem_status = '0';
            } else {
                $heradiatry_problem_status = '5';
            }				
			
	 if ($lat == '' && $lng == '') {
                $location_status = '0';
            } else {
                $location_status = '5';
            }		
        
        $step1 = '20';

        $profile_completed = $step1 + $image_status + $height_status + $weight_status + $marital_status_is + $blood_group_status + $diet_fitness_status + $organ_donor_status + $sex_history_status + $activity_level_status + $health_condition_is + $allergies_status + $bmi_status + $health_insurance_status + $addiction_status + $heradiatry_problem_status + $location_status;
                
                
                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();
                
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                } 
                
                
                
                
                return array(
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'image' => $image,
                    'gender' => $gender,
                    'dob' => $dob,
                    'height' => $height,
                    'weight' => $weight,
                    'exercise_level' => $exercise_level,
                    'health_condition' => $health_condition,
                    'addiction' => str_replace('null','',$addiction),
                    'health_insurance' => str_replace('null','',$health_insurance),
                    'allergies' => $allergies,
                    'marital_status' => $marital_status,
                    'blood_group' => $blood_group,
                    'blood_is_active'=>$blood_is_active,
                    'activity_level' => $activity_level,
                    'diet_fitness' => $diet_fitness,
                    'organ_donor' => $organ_donor,
                    'bmi' => $bmi,
                    'sex_history' => $sex_history,
                    'followers' => $followers,
                    'following' => $following,
                    'reviews_done' => '0',
                    'is_follow' => $is_follow,
                    'heradiatry_problem'=>$heradiatry_problem,
                    'lat'=>$lat,
                    'lng'=>$lng,
                    'map_location'=>$map_location,
                    'profile_completed' =>$profile_completed,
                    
                );
            } else {
                return array(
                    'status' => 209,
                    'message' => 'user not found'
                );
            }
        }
    }
    
    public function userlogin($phone, $token, $agent)
    {
        if ($phone == "") {
            return array(
                'status' => 208,
                'message' => 'Please enter phone no.'
            );
        } else {
            $check_user = $this->db->select('id')->from('users')->where('phone', $phone)->get()->row();
            if ($check_user != "") {
                $id = $check_user->id;
                $q  = $this->db->select('password,id,name,phone,email,gender,dob,height,weight,marital_status,blood_group,diet_fitness,organ_donor,sex_history')->from('users')->where('users.id', $id)->get()->row();
                
                $id             = $q->id;
                $name           = $q->name;
                $phone          = $q->phone;
                $email          = $q->email;
                $gender         = $q->gender;
                $dob            = $q->dob;
                $height         = $q->height;
                $weight         = $q->weight;
                $marital_status = $q->marital_status;
                $blood_group    = $q->blood_group;
                $diet_fitness   = $q->diet_fitness;
                $organ_donor    = $q->organ_donor;
                $sex_history    = $q->sex_history;
                
                $img_count = $this->db->select('media.id')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $id)->get()->num_rows();                
                if ($img_count > 0) {
                    $media    = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $id)->get()->row();
                    $img_file = $media->source;
                    $image    = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                $last_login = date('Y-m-d H:i:s', strtotime('+12 hours'));
                $this->db->where('id', $id)->update('users', array(
                    'last_login' => $last_login
                ));                
                
                $otp_code = rand(100000, 999999);
                $querys   = $this->db->query("UPDATE `users` SET `otp_code`='$otp_code',`token`='$token',`agent`='$agent',`token_status`='1' WHERE id='$id'");
     
				$message  = 'Please enter this One Time Password : '.$otp_code.' to verify your mobile number.';
				$post_data = array('From' => '02233721563','To' => $phone,'Body' => $message);
				$exotel_sid = "aegishealthsolutions";
				$exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
				$url = "https://".$exotel_sid.":".$exotel_token."@twilix.exotel.in/v1/Accounts/".$exotel_sid."/Sms/send";
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_VERBOSE, 1);
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_FAILONERROR, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
				$http_result = curl_exec($ch);
				curl_close($ch);
				
				
                return array(
                    'otp_code' => $otp_code,
                    'user' => 'old',
                    'id' => $id,
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'image' => $image,
                    'gender' => $gender,
                    'dob' => $dob,
                    'height' => $height,
                    'weight' => $weight,
                    'marital_status' => $marital_status,
                    'blood_group' => $blood_group,
                    'diet_fitness' => $diet_fitness,
                    'organ_donor' => $organ_donor,
                    'sex_history' => $sex_history
                );
            } else {
                $otp_code = rand(100000, 999999);
				$message  = 'Please enter this One Time Password : '.$otp_code.' to verify your mobile number.';
				$post_data = array('From' => '02233721563','To' => $phone,'Body' => $message);
				$exotel_sid = "aegishealthsolutions";
				$exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
				$url = "https://".$exotel_sid.":".$exotel_token."@twilix.exotel.in/v1/Accounts/".$exotel_sid."/Sms/send";
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_VERBOSE, 1);
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_FAILONERROR, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
				$http_result = curl_exec($ch);
				curl_close($ch);
				
				
                return array(
                    'otp_code' => $otp_code,
                    'user' => 'new',
                    'id' => '',
                    'name' => '',
                    'email' => '',
                    'phone' => $phone,
                    'image' => '',
                    'gender' => '',
                    'dob' => '',
                    'height' => '',
                    'weight' => '',
                    'marital_status' => '',
                    'blood_group' => '',
                    'diet_fitness' => '',
                    'organ_donor' => '',
                    'sex_history' => ''
                );
            }
        }
    }
    
    public function usersignup($name, $phone, $email, $gender, $dob, $password, $image, $token, $agent)
    {
        $count_user1 = $this->db->select('id')->from('users')->where('email', $email)->get()->num_rows();
        $count_user2 = $this->db->select('id')->from('users')->where('phone', $phone)->get()->num_rows();
        if ($count_user1 > 0) {
            return array(
                'status' => 208,
                'message' => 'Email id already exist'
            );
        } elseif ($count_user2 > 0) {
            return array(
                'status' => 208,
                'message' => 'Phone number already exist'
            );
        } else {
            if ($name != '' && $phone != '' && $email != '' && $gender != '' && $password != '') {
                date_default_timezone_set('Asia/Kolkata');
                $updated_at = date('Y-m-d H:i:s');
                if ($image != '') {
                    $type       = 'image';
                    $image_data = array(
                        'title' => $image,
                        'type' => $type,
                        'source' => $image,
                        'created_at' => $updated_at,
                        'updated_at' => $updated_at
                    );
                    
                    $this->db->insert('media', $image_data);
                    $media_id = $this->db->insert_id();
                    $profile  = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                } else {
                    $media_id = '0';
                    $profile  = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                $user_type    = 'user';
                $userpassword = md5($password);
                $user_data    = array(
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'gender' => $gender,
                    'dob' => $dob,
                    'password' => $userpassword,
                    'token' => $token,
                    'agent' => $agent,
                    'token_status' => '1',
                    'created_at' => $updated_at
                );
                
                $this->db->insert('users', $user_data);
                $id = $this->db->insert_id();                
                //$this->db->insert('users',$data);               
                
                if ($agent == 'ios') {
                    return array(
                        'status' => 201,
                        'message' => 'success',
                        'user' => 'old',
                        'id' => $id,
                        'name' => $name,
                        'email' => $email,
                        'phone' => $phone,
                        'image' => $image,
                        'gender' => $gender,
                        'dob' => $dob,
                        'height' => '',
                        'weight' => '',
                        'marital_status' => '',
                        'blood_group' => '',
                        'diet_fitness' => '',
                        'organ_donor' => '',
                        'sex_history' => ''
                    );
                } else {
                    return array(
                        'status' => 201,
                        'message' => 'success',
                        'id' => $id,
                        'name' => $name,
                        'phone' => $phone,
                        'email' => $email,
                        'gender' => $gender,
                        'dob' => $dob,
                        'image' => $profile
                    );
                }
                
            } else {
                return array(
                    'status' => 208,
                    'message' => 'Please enter all fields'
                );
            }
        }
    }
    
    public function userprofile($user_id, $marital_status, $blood_group, $height, $weight, $diet_fitness, $exercise_level, $health_condition, $allergies, $ask_saheli, $organ_donor, $bmi, $activity_level,$addiction, $health_insurance,$blood_is_active,$heradiatry_problem,$lat,$lng,$map_location)
    {
        $count_user = $this->db->select('id')->from('users')->where('id', $user_id)->get()->num_rows();
        if ($count_user > 0) {
            if ($user_id != '') {
                date_default_timezone_set('Asia/Kolkata');
                $updated_at = date('Y-m-d H:i:s');                
                $user_data = array(
                    
                    'marital_status' => $marital_status,
                    'blood_group' => $blood_group,
                    'blood_is_active'=>$blood_is_active,
                    'diet_fitness' => $diet_fitness,
                    'height' => $height,
                    'weight' => $weight,
                    'organ_donor' => $organ_donor,
                    'exercise_level' => $exercise_level,
                    'health_condition' => $health_condition,
                    'allergies' => $allergies,
                    'ask_saheli' => $ask_saheli,
                    'updated_at' => $updated_at,
                    'bmi' => $bmi,
                    'activity_level' => $activity_level,
                    'addiction'=>$addiction, 
                    'health_insurance'=>$health_insurance,
                    'heradiatry_problem'=>$heradiatry_problem,
                    'map_location'=>$map_location,
                    'lng'=>$lng,
                    'lat'=>$lat
                );                
                $this->db->where('id', $user_id)->update('users', $user_data);                
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'id' => $user_id,
                    'blood_is_active' => $blood_is_active
                );
            } else {
                return array(
                    'status' => 208,
                    'message' => 'Please enter all fields'
                );
            }
            
        } else {
            return array(
                'status' => 208,
                'message' => 'User not found'
            );
        }
    }
    
    
    
     public function blood_group_update($user_id, $blood_group,$blood_is_active)
    {
        $count_user = $this->db->select('id')->from('users')->where('id', $user_id)->get()->num_rows();
        if ($count_user > 0) {
            if ($user_id != '') {
                date_default_timezone_set('Asia/Kolkata');
                $updated_at = date('Y-m-d H:i:s');                
                $user_data = array(
                    
                    'blood_group' => $blood_group,
                    'blood_is_active'=>$blood_is_active
                    
                );                
                $this->db->where('id', $user_id)->update('users', $user_data);                
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'id' => $user_id
                );
            } else {
                return array(
                    'status' => 208,
                    'message' => 'Please enter all fields'
                );
            }
            
        } else {
            return array(
                'status' => 208,
                'message' => 'User not found'
            );
        }
    }
    
    public function follow($user_id, $following_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from follow_user where user_id='$user_id' and parent_id='$following_id'");
        $count       = $count_query->num_rows();        
        if ($count > 0) {
            $this->db->query("DELETE FROM `follow_user` WHERE user_id='$user_id' and parent_id='$following_id'");
            $follow_query = $this->db->query("SELECT id from follow_user where parent_id='$following_id'");
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
                'parent_id' => $following_id,
                'created_at' => $created_at,
                'deleted_at' => $created_at
            );
            $this->db->insert('follow_user', $follow_user);
            $follow_query = $this->db->query("SELECT id from follow_user where parent_id='$following_id'");
            $total_follow = $follow_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'follow' => '1',
                'total_follow' => $total_follow
            );
        }
    }
    
    public function following_list($user_id)
    {
        $count_query = $this->db->query("SELECT follow_user.user_id as user_id, follow_user.parent_id, users.id, users.name, users.avatar_id, media.id, media.title  from follow_user
		INNER JOIN users ON users.id = follow_user.parent_id 
		LEFT JOIN media ON media.id = users.avatar_id 
		where user_id='$user_id'");
        $count       = $count_query->num_rows();
        
        if ($count > 0) {
            foreach ($count_query->result_array() as $row) {
                $user_id   = $row['parent_id'];
                $name      = $row['name'];
                $avatar_id = $row['avatar_id'];
                $title     = $row['title'];
                if ($avatar_id != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $title;
                } elseif ($avatar_id == 0) {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }                
                $resultpost[] = array(
                    "user_id" => $user_id,
                    "name" => $name,
                    "image" => $image                    
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;        
    }    
    
    public function follower_list($user_id)
    {
        $count_query = $this->db->query("SELECT follow_user.user_id, follow_user.parent_id, users.id, users.name, users.avatar_id, media.id, media.title  from follow_user
		LEFT JOIN users ON users.id = follow_user.user_id 
		LEFT JOIN media ON media.id = users.avatar_id 
		where parent_id='$user_id'");
        $count       = $count_query->num_rows();        
        if ($count > 0) {
            foreach ($count_query->result_array() as $row) {
                $user_id   = $row['user_id'];
                $name      = $row['name'];
                $avatar_id = $row['avatar_id'];
                $title     = $row['title'];
                if ($avatar_id != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $title;
                } elseif ($avatar_id == 0) {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }      
                
                $resultpost[] = array(
                    "user_id" => $user_id,
                    "name" => $name,
                    "image" => $image
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    public function address_list($user_id)
    {
        $query = $this->db->query("SELECT address_id,name,mobile,pincode,address1,address2,landmark,city,state,address_type FROM `user_address` WHERE user_id='$user_id' order by address_id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $address_id = $row['address_id'];
                $name       = $row['name'];
                $mobile     = $row['mobile'];
                $address1   = $row['address1'];
                $address2   = $row['address2'];
                $landmark   = $row['landmark'];
                $city       = $row['city'];
                $state      = $row['state'];
                $pincode    = $row['pincode']; 
                $address_type    = $row['address_type'];            
                $resultpost[] = array(
                    "address_id" => $address_id,
                    "name" => $name,
                    "mobile" => $mobile,
                    'address1' => $address1,
                    'address2' => $address2,
                    'landmark' => $landmark,
                    'city' => $city,
                    'state' => $state,
                    'pincode' => $pincode,
                     'address_type' => $address_type
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;        
    }
    
    public function address_add($user_id, $name, $mobile, $pincode, $address1, $address2, $landmark, $city, $state,$address_type)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at   = date('Y-m-d H:i:s');
        $address_data = array(
             'address_type' => $address_type,
            'user_id' => $user_id,
            'name' => $name,
            'address1' => $address1,
            'address2' => $address2,
            'landmark' => $landmark,
            'mobile' => $mobile,
            'city' => $city,
            'state' => $state,
            'pincode' => $pincode,
            'date' => $created_at
        );
        $this->db->insert('user_address', $address_data);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }
    
    public function address_update($user_id, $address_id, $name, $address1, $address2, $landmark, $mobile, $city, $state, $pincode,$address_type)
    {
        $query = $this->db->query("UPDATE `user_address` SET `address_type`='$address_type',`name`='$name',`mobile`='$mobile',`address1`='$address1',`address2`='$address2',`landmark`='$landmark',`city`='$city',`state`='$state',`pincode`='$pincode' WHERE address_id='$address_id' and user_id='$user_id'");
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }
    
    public function address_delete($user_id, $address_id)
    {
        $query = $this->db->query("DELETE FROM `user_address` WHERE address_id='$address_id' AND user_id='$user_id'");
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }
    
    public function country()
    {
        $query = $this->db->query("select id,name FROM countries order by name asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $country_id   = $row['id'];
                $country      = $row['name'];
                $resultpost[] = array(
                    "country_id" => $country_id,
                    "country" => $country
                );
            }
        } else {
            $resultpost = array();
        }
        
        return $resultpost;
    }
    
    public function state($country_id)
    {
        $query = $this->db->query("select state_id,state_name FROM states WHERE country_id='$country_id' order by state_name asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $state_id     = $row['state_id'];
                $state_name   = $row['state_name'];
                $resultpost[] = array(
                    "state_id" => $state_id,
                    "state" => $state_name
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    public function city($state_id)
    {
        $query = $this->db->query("select city_id,city_name FROM cities WHERE state_id='$state_id' order by city_name asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $city_id      = $row['city_id'];
                $city         = $row['city_name'];
                $resultpost[] = array(
                    "city_id" => $city_id,
                    "city" => $city
                );
            }
        } else {
            $resultpost = array();
        }        
        return $resultpost;
    }
    
    public function state_city()
    {
        $query = $this->db->query("select DISTINCT state from city_state_region order by state asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $state      = $row['state'];
                $city_list  = '';
                $city_query = $this->db->query("select DISTINCT city from city_state_region where state='$state' order by city asc");
                $city_count = $city_query->num_rows();
                if ($city_count > 0) {
                    foreach ($city_query->result_array() as $row) {
                        $city        = $row['city'];
                        $city_list[] = array(
                            "city_name" => $city
                        );
                    }
                }
                $resultpost[] = array(
                    "state" => $state,
                    "city" => $city_list
                );
            }
        } else {
            $resultpost = array();
        }        
        return $resultpost;
    }  
	
    public function organicindia_banner()
    {
        $query = $this->db->query("SELECT * FROM `organicindia_banner` ORDER BY id DESC");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id    = $row['id'];
                $image = $row['image'];
                
                return array(
                    'status' => 200,
                    'message' => 'success',
                    'id' => $id,
                    'banner' => $image
                );
            }
        } else {
            $resultpost = '';
        }        
        return $resultpost;
    }
    
    public function userupdate($user_id, $gender, $dob, $name, $phone, $email, $profile_pic)
    {      
        $Q         = "SET source = '$profile_pic', title='$profile_pic'";
        $sql       = "SELECT avatar_id FROM users  WHERE users.id ='" . $user_id . "'";
        $avatar_id = $this->db->query($sql)->row()->avatar_id;
        if ($profile_pic != "") {
            if ($avatar_id == 0) {
                date_default_timezone_set('Asia/Kolkata');
                $date = date('Y-m-d H:i:s');                
                $data = array(
                    'title' => $profile_pic,
                    'type' => 'image',
                    'source' => $profile_pic,
                    'created_at' => $date,
                    'updated_at' => $date
                    
                );                
                $this->db->insert('media', $data);
                $aid = $this->db->insert_id();                
                $this->db->query("UPDATE users SET avatar_id = '" . $aid . "' WHERE id = '" . $user_id . "'");
            } else {
                $this->db->query("UPDATE media $Q WHERE id = '" . $avatar_id . "'");
            }
        }  
        $query = $this->db->query("UPDATE users SET `gender`='$gender',`dob`='$dob',`name`='$name',`phone`='$phone',`email`='$email' WHERE id='$user_id'");
        
        if ($profile_pic != '') {
            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
        } elseif ($avatar_id == 0) {
            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
        } else {
            $sql2   = "SELECT source FROM media  WHERE id ='" . $avatar_id . "'";
            $source = $this->db->query($sql2)->row()->source;
            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $source;
        }   
        return array(
            'status' => 200,
            'message' => 'success',
            'image' => $image,
            'name' => $name,
            'gender' => $gender,
            'dob' => $dob,
            'phone' => $phone,
            'email' => $email
        );
    }
    
    public function all_review_list($user_id)
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
        $resultpost   = '';
        $review_count = $this->db->select('id')->from('ambulance_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT ambulance_review.id,ambulance_review.user_id,ambulance_review.ambulance_id,ambulance_review.rating,ambulance_review.review, ambulance_review.service,ambulance_review.date as review_date,users.id as user_id,users.name as firstname FROM `ambulance_review` INNER JOIN `users` ON ambulance_review.user_id=users.id WHERE ambulance_review.user_id = '$user_id' order by ambulance_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('ambulance_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('ambulance_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('ambulance_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
                $ambulance_review[] = array(
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
                    'type' => 'Ambulance'
                );
            }
        } else {
            $ambulance_review = array();
        }
        
        $review_count = $this->db->select('id')->from('babysitter_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT babysitter_review.id,babysitter_review.user_id,babysitter_review.babysitter_id,babysitter_review.rating,babysitter_review.review, babysitter_review.service,babysitter_review.date as review_date,users.id as user_id,users.name as firstname FROM `babysitter_review` INNER JOIN `users` ON babysitter_review.user_id=users.id WHERE babysitter_review.user_id = '$user_id' order by babysitter_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('babysitter_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('babysitter_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('babysitter_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
                $babysitter_review[] = array(
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
                    'type' => 'Babysitter'
                );
            }
        } else {
            $babysitter_review = array();
        }
        $review_count = $this->db->select('id')->from('blood_bank_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT blood_bank_review.id,blood_bank_review.user_id,blood_bank_review.blood_bank_id,blood_bank_review.rating,blood_bank_review.review, blood_bank_review.service,blood_bank_review.date as review_date,users.id as user_id,users.name as firstname FROM `blood_bank_review` INNER JOIN `users` ON blood_bank_review.user_id=users.id WHERE blood_bank_review.user_id = '$user_id' order by blood_bank_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('blood_bank_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('blood_bank_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('blood_bank_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
                $blood_bank_review[] = array(
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
                    'type' => 'Blood Bank'
                );
            }
        } else {
            $blood_bank_review = array();
        }
        
        $review_count = $this->db->select('id')->from('child_physiotherapy_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT child_physiotherapy_review.id,child_physiotherapy_review.user_id,child_physiotherapy_review.product_id,child_physiotherapy_review.rating,child_physiotherapy_review.review, child_physiotherapy_review.service,child_physiotherapy_review.date as review_date,users.id as user_id,users.name as firstname FROM `child_physiotherapy_review` INNER JOIN `users` ON child_physiotherapy_review.user_id=users.id WHERE child_physiotherapy_review.user_id = '$user_id' order by child_physiotherapy_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('child_physiotherapy_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('child_physiotherapy_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('blood_bank_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
                $child_physiotherapy[] = array(
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
                    'type' => 'Child Physiotherapy'
                );
            }
        } else {
            $child_physiotherapy = array();
        }
        
        $review_count = $this->db->select('id')->from('counselling_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT counselling_review.id,counselling_review.user_id,counselling_review.doctor_id,counselling_review.rating,counselling_review.review, counselling_review.service,counselling_review.date as review_date,users.id as user_id,users.name as firstname FROM `counselling_review` INNER JOIN `users` ON counselling_review.user_id=users.id WHERE counselling_review.user_id = '$user_id' order by counselling_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('counselling_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('counselling_review_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('counselling_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
                $counselling_review[] = array(
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
                    'type' => 'Counselling'
                );
            }
        } else {
            $counselling_review = array();
        }
        
        $review_count = $this->db->select('id')->from('cuppingtherapy_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT cuppingtherapy_review.id,cuppingtherapy_review.user_id,cuppingtherapy_review.cuppingtherapy_id,cuppingtherapy_review.rating,cuppingtherapy_review.review, cuppingtherapy_review.service,cuppingtherapy_review.date as review_date,users.id as user_id,users.name as firstname FROM `cuppingtherapy_review` INNER JOIN `users` ON cuppingtherapy_review.user_id=users.id WHERE cuppingtherapy_review.user_id = '$user_id' order by cuppingtherapy_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('cuppingtherapy_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('cuppingtherapy_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('cuppingtherapy_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
                $cuppingtherapy_review[] = array(
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
                    'type' => 'Cupping Therapy'
                );
            }
        } else {
            $cuppingtherapy_review = array();
        }       
        
        $review_count = $this->db->select('id')->from('dai_nanny_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT dai_nanny_review.id,dai_nanny_review.user_id,dai_nanny_review.dai_nanny_id,dai_nanny_review.rating,dai_nanny_review.review, dai_nanny_review.service,dai_nanny_review.date as review_date,users.id as user_id,users.name as firstname FROM `dai_nanny_review` INNER JOIN `users` ON dai_nanny_review.user_id=users.id WHERE dai_nanny_review.user_id = '$user_id' order by dai_nanny_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('dai_nanny_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('dai_nanny_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('dai_nanny_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
                $dai_nanny_review[] = array(
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
                    'type' => 'Dai Nany'
                );
            }
        } else {
            $dai_nanny_review = array();
        }
        
        $review_count = $this->db->select('id')->from('doctors_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT doctors_review.id,doctors_review.user_id,doctors_review.doctor_id,doctors_review.rating,doctors_review.review, doctors_review.service,doctors_review.date as review_date,users.id as user_id,users.name as firstname FROM `doctors_review` INNER JOIN `users` ON doctors_review.user_id=users.id WHERE doctors_review.user_id = '$user_id' order by doctors_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);                
                $like_count  = $this->db->select('id')->from('doctors_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('doctors_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('doctors_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
                $doctors_review[] = array(
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
                    'type' => 'Doctor'
                );
            }
        } else {
            $doctors_review = array();
        }       
        
        $review_count = $this->db->select('id')->from('fitness_center_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT fitness_center_review.id,fitness_center_review.user_id,fitness_center_review.listing_id	,fitness_center_review.rating,fitness_center_review.review, fitness_center_review.service,fitness_center_review.date as review_date,users.id as user_id,users.name as firstname FROM `fitness_center_review` INNER JOIN `users` ON fitness_center_review.user_id=users.id WHERE fitness_center_review.user_id = '$user_id' order by fitness_center_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('fitness_center_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('fitness_center_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('fitness_center_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
                $fitness_center_review[] = array(
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
                    'type' => 'Fitness Center'
                );
            }
        } else {
            $fitness_center_review = array();
        }        
        
        $review_count = $this->db->select('id')->from('healthy_food_product_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT healthy_food_product_review.id,healthy_food_product_review.user_id,healthy_food_product_review.product_id	,healthy_food_product_review.rating,healthy_food_product_review.review, healthy_food_product_review.service,healthy_food_product_review.date as review_date,users.id as user_id,users.name as firstname FROM `healthy_food_product_review` INNER JOIN `users` ON healthy_food_product_review.user_id=users.id WHERE healthy_food_product_review.user_id = '$user_id' order by healthy_food_product_review.id desc");            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('healthy_food_product_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('healthy_food_product_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('healthy_food_product_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
                $healthy_food_product_review[] = array(
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
                    'type' => 'Healthy Food Product'
                );
            }
        } else {
            $healthy_food_product_review = array();
        }        
        
        $review_count = $this->db->select('id')->from('hospital_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT hospital_review.id,hospital_review.user_id,hospital_review.hospital_id	,hospital_review.rating,hospital_review.review, hospital_review.service,hospital_review.date as review_date,users.id as user_id,users.name as firstname FROM `hospital_review` INNER JOIN `users` ON hospital_review.user_id=users.id WHERE hospital_review.user_id = '$user_id' order by hospital_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('hospital_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('hospital_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('hospital_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
                $hospital_review[] = array(
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
                    'type' => 'Hospital'
                );
            }
        } else {
            $hospital_review = array();
        }        
        
        $review_count = $this->db->select('id')->from('labcenter_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT labcenter_review.id,labcenter_review.user_id,labcenter_review.labcenter_id	,labcenter_review.rating,labcenter_review.review, labcenter_review.service,labcenter_review.date as review_date,users.id as user_id,users.name as firstname FROM `labcenter_review` INNER JOIN `users` ON labcenter_review.user_id=users.id WHERE labcenter_review.user_id = '$user_id' order by labcenter_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('labcenter_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('labcenter_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('labcenter_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
                $labcenter_review[] = array(
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
                    'type' => 'Labcenter'
                );
            }
        } else {
            $labcenter_review = array();
        }        
        
        $review_count = $this->db->select('id')->from('medical_college_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT medical_college_review.id,medical_college_review.user_id,medical_college_review.college_id	,medical_college_review.rating,medical_college_review.review, medical_college_review.service,medical_college_review.date as review_date,users.id as user_id,users.name as firstname FROM `medical_college_review` INNER JOIN `users` ON medical_college_review.user_id=users.id WHERE medical_college_review.user_id = '$user_id' order by medical_college_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('medical_college_review_like')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('medical_college_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('medical_college_review_like')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
                $medical_college_review[] = array(
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
                    'type' => 'Medical College'
                );
            }
        } else {
            $medical_college_review = array();
        }
        
        $review_count = $this->db->select('id')->from('nursing_attendant_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT nursing_attendant_review.id,nursing_attendant_review.user_id,nursing_attendant_review.nursing_attendant_id	,nursing_attendant_review.rating,nursing_attendant_review.review, nursing_attendant_review.service,nursing_attendant_review.date as review_date,users.id as user_id,users.name as firstname FROM `nursing_attendant_review` INNER JOIN `users` ON nursing_attendant_review.user_id=users.id WHERE nursing_attendant_review.user_id = '$user_id' order by nursing_attendant_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('nursing_attendant_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('nursing_attendant_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('nursing_attendant_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
                $nursing_attendant_review[] = array(
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
                    'type' => 'Nursing Attendant'
                );
            }
        } else {
            $nursing_attendant_review = array();
        }
        
        $review_count = $this->db->select('id')->from('oldagehome_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT oldagehome_review.id,oldagehome_review.user_id,oldagehome_review.oldagehome_id	,oldagehome_review.rating,oldagehome_review.review, oldagehome_review.service,oldagehome_review.date as review_date,users.id as user_id,users.name as firstname FROM `oldagehome_review` INNER JOIN `users` ON oldagehome_review.user_id=users.id WHERE oldagehome_review.user_id = '$user_id' order by oldagehome_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('oldagehome_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('oldagehome_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('oldagehome_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
                $oldagehome_review[] = array(
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
                    'type' => 'Old Age Home'
                );
            }
        } else {
            $oldagehome_review = array();
        }        
        
        $review_count = $this->db->select('id')->from('optic_store_product_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT optic_store_product_review.id,optic_store_product_review.user_id,optic_store_product_review.product_id	,optic_store_product_review.rating,optic_store_product_review.review, optic_store_product_review.service,optic_store_product_review.date as review_date,users.id as user_id,users.name as firstname FROM `optic_store_product_review` INNER JOIN `users` ON optic_store_product_review.user_id=users.id WHERE optic_store_product_review.user_id = '$user_id' order by optic_store_product_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('optic_store_product_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('optic_store_product_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('optic_store_product_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
                $optic_store_product_review[] = array(
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
                    'type' => 'Optic Store'
                );
            }
        } else {
            $optic_store_product_review = array();
        }        
        
        $review_count = $this->db->select('id')->from('organicindia_product_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT organicindia_product_review.id,organicindia_product_review.user_id,organicindia_product_review.product_id	,organicindia_product_review.rating,organicindia_product_review.review, organicindia_product_review.service,organicindia_product_review.date as review_date,users.id as user_id,users.name as firstname FROM `organicindia_product_review` INNER JOIN `users` ON organicindia_product_review.user_id=users.id WHERE organicindia_product_review.user_id = '$user_id' order by organicindia_product_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('organicindia_product_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('organicindia_product_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('organicindia_product_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
                $organicindia_product_review[] = array(
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
                    'type' => 'Organicindia Product'
                );
            }
        } else {
            $organicindia_product_review = array();
        }
        
        $review_count = $this->db->select('id')->from('organicindia_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT organicindia_review.id,organicindia_review.user_id,organicindia_review.organicindia_id	,organicindia_review.rating,organicindia_review.review, organicindia_review.service,organicindia_review.date as review_date,users.id as user_id,users.name as firstname FROM `organicindia_review` INNER JOIN `users` ON organicindia_review.user_id=users.id WHERE organicindia_review.user_id = '$user_id' order by organicindia_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('organicindia_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('organicindia_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('organicindia_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
                $organicindia_review[] = array(
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
                    'type' => 'Organicindia'
                );
            }
        } else {
            $organicindia_review = array();
        }
        
        $review_count = $this->db->select('id')->from('personal_trainers_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT personal_trainers_review.id,personal_trainers_review.user_id,personal_trainers_review.listing_id	,personal_trainers_review.rating,personal_trainers_review.review, personal_trainers_review.service,personal_trainers_review.date as review_date,users.id as user_id,users.name as firstname FROM `personal_trainers_review` INNER JOIN `users` ON personal_trainers_review.user_id=users.id WHERE personal_trainers_review.user_id = '$user_id' order by personal_trainers_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('personal_trainers_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('personal_trainers_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('personal_trainers_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
                $personal_trainers_review[] = array(
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
                    'type' => 'Personal Trainer'
                );
            }
        } else {
            $personal_trainers_review = array();
        }
        
        $review_count = $this->db->select('id')->from('pest_control_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT pest_control_review.id,pest_control_review.user_id,pest_control_review.pest_control_id	,pest_control_review.rating,pest_control_review.review, pest_control_review.service,pest_control_review.date as review_date,users.id as user_id,users.name as firstname FROM `pest_control_review` INNER JOIN `users` ON pest_control_review.user_id=users.id WHERE pest_control_review.user_id = '$user_id' order by pest_control_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('pest_control_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('pest_control_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('pest_control_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
                $pest_control_review[] = array(
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
                    'type' => 'Pest Control'
                );
            }
        } else {
            $pest_control_review = array();
        }
        
        $review_count = $this->db->select('id')->from('psychiatrist_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT psychiatrist_review.id,psychiatrist_review.user_id,psychiatrist_review.doctor_id	,psychiatrist_review.rating,psychiatrist_review.review, psychiatrist_review.service,psychiatrist_review.date as review_date,users.id as user_id,users.name as firstname FROM `psychiatrist_review` INNER JOIN `users` ON psychiatrist_review.user_id=users.id WHERE psychiatrist_review.user_id = '$user_id' order by psychiatrist_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('psychiatrist_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('psychiatrist_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('psychiatrist_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
                $psychiatrist_review[] = array(
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
                    'type' => 'Psychiatrist'
                );
            }
        } else {
            $psychiatrist_review = array();
        }
        
        $review_count = $this->db->select('id')->from('roots_herbs_product_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT roots_herbs_product_review.id,roots_herbs_product_review.user_id,roots_herbs_product_review.product_id	,roots_herbs_product_review.rating,roots_herbs_product_review.review, roots_herbs_product_review.service,roots_herbs_product_review.date as review_date,users.id as user_id,users.name as firstname FROM `roots_herbs_product_review` INNER JOIN `users` ON roots_herbs_product_review.user_id=users.id WHERE roots_herbs_product_review.user_id = '$user_id' order by roots_herbs_product_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('roots_herbs_product_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('roots_herbs_product_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('roots_herbs_product_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
                $roots_herbs_product_review[] = array(
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
                    'type' => 'Roots Herbs Product'
                );
            }
        } else {
            $roots_herbs_product_review = array();
        }
        
        $review_count = $this->db->select('id')->from('roots_herbs_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT roots_herbs_review.id,roots_herbs_review.user_id,roots_herbs_review.roots_herbs_id	,roots_herbs_review.rating,roots_herbs_review.review, roots_herbs_review.service,roots_herbs_review.date as review_date,users.id as user_id,users.name as firstname FROM `roots_herbs_review` INNER JOIN `users` ON roots_herbs_review.user_id=users.id WHERE roots_herbs_review.user_id = '$user_id' order by roots_herbs_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('roots_herbs_comment_like')->where('comment_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('roots_herbs_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('roots_herbs_comment_like')->where('user_id', $user_id)->where('comment_id', $id)->get()->num_rows();
                
                $roots_herbs_review[] = array(
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
                    'type' => 'Roots Herbs'
                );
            }
        } else {
            $roots_herbs_review = array();
        }        
        
        $review_count = $this->db->select('id')->from('sex_store_product_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT sex_store_product_review.id,sex_store_product_review.user_id,sex_store_product_review.product_id	,sex_store_product_review.rating,sex_store_product_review.review, sex_store_product_review.service,sex_store_product_review.date as review_date,users.id as user_id,users.name as firstname FROM `sex_store_product_review` INNER JOIN `users` ON sex_store_product_review.user_id=users.id WHERE sex_store_product_review.user_id = '$user_id' order by sex_store_product_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('sex_store_product_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('sex_store_product_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('sex_store_product_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
                $sex_store_product_review[] = array(
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
                    'type' => 'Sex Store Product'
                );
            }
        } else {
            $sex_store_product_review = array();
        }
        
        $review_count = $this->db->select('id')->from('sex_store_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT sex_store_review.id,sex_store_review.user_id,sex_store_review.sex_store_id	,sex_store_review.rating,sex_store_review.review, sex_store_review.service,sex_store_review.date as review_date,users.id as user_id,users.name as firstname FROM `sex_store_review` INNER JOIN `users` ON sex_store_review.user_id=users.id WHERE sex_store_review.user_id = '$user_id' order by sex_store_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('sex_store_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('sex_store_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('sex_store_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
                $sex_store_review[] = array(
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
                    'type' => 'Sex Store'
                );
            }
        } else {
            $sex_store_review = array();
        }        
        $resultpost[] = array(
            array_merge($ambulance_review, $babysitter_review, $blood_bank_review, $child_physiotherapy, $counselling_review, $cuppingtherapy_review, $dai_nanny_review, $doctors_review, $fitness_center_review, $healthy_food_product_review, $hospital_review, $labcenter_review, $medical_college_review, $nursing_attendant_review, $oldagehome_review, $optic_store_product_review, $organicindia_product_review, $organicindia_review, $personal_trainers_review, $pest_control_review, $psychiatrist_review, $roots_herbs_product_review, $roots_herbs_review, $sex_store_product_review, $sex_store_review)
        );        
        return $resultpost;
    }   
    
    public function user_lat_lng($user_id, $latitude, $longitude, $map_location)
    {
        $query = $this->db->query("UPDATE `users` SET `lat`='$latitude',`lng`='$longitude',`map_location`='$map_location' WHERE id='$user_id'");
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }    
    
    public function review_delete($user_id, $post_id, $type)
    {
        switch ($type) {
            case "Ambulance":
                $this->db->query("DELETE FROM `ambulance_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `ambulance_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Babysitter":
                $this->db->query("DELETE FROM `babysitter_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `babysitter_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Blood Bank":
                $this->db->query("DELETE FROM `blood_bank_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `blood_bank_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Child Physiotherapy":
                $this->db->query("DELETE FROM `child_physiotherapy_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `child_physiotherapy_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Counselling":
                $this->db->query("DELETE FROM `counselling_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `counselling_review_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Cupping Therapy":
                $this->db->query("DELETE FROM `cuppingtherapy_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `cuppingtherapy_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Dai Nany":
                $this->db->query("DELETE FROM `dai_nanny_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `dai_nanny_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Doctor":
                $this->db->query("DELETE FROM `doctors_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `doctors_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Fitness Center":
                $this->db->query("DELETE FROM `fitness_center_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `fitness_center_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Healthy Food Product":
                $this->db->query("DELETE FROM `healthy_food_product_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `healthy_food_product_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Hospital":
                $this->db->query("DELETE FROM `hospital_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `hospital_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Labcenter":
                $this->db->query("DELETE FROM `labcenter_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `labcenter_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Medical College":
                $this->db->query("DELETE FROM `medical_college_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `medical_college_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Nursing Attendant":
                $this->db->query("DELETE FROM `nursing_attendant_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `nursing_attendant_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Old Age Home":
                $this->db->query("DELETE FROM `oldagehome_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `oldagehome_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Optic Store":
                $this->db->query("DELETE FROM `optic_store_product_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `optic_store_product_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Organicindia Product":
                $this->db->query("DELETE FROM `organicindia_product_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `organicindia_product_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Organicindia":
                $this->db->query("DELETE FROM `organicindia_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `organicindia_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Personal Trainer":
                $this->db->query("DELETE FROM `personal_trainers_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `personal_trainers_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Pest Control":
                $this->db->query("DELETE FROM `pest_control_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `pest_control_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Psychiatrist":
                $this->db->query("DELETE FROM `psychiatrist_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `psychiatrist_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Roots Herbs Product":
                $this->db->query("DELETE FROM `roots_herbs_product_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `roots_herbs_product_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Roots Herbs":
                $this->db->query("DELETE FROM `roots_herbs_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `roots_herbs_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Sex Store Product":
                $this->db->query("DELETE FROM `sex_store_product_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `sex_store_product_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Sex Store":
                $this->db->query("DELETE FROM `sex_store_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `sex_store_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
        }        
        return array(
            'status' => 200,
            'message' => 'deleted'
        );
    }   
    
    public function app_banner()
    {
        
        $query = $this->db->query("SELECT `id`, `cat_id`, `image` FROM `app_banners`");
        $count = $query->num_rows();
        if ($count > 0) {            
            $home              = array();
            $blood_group       = array();
            $OFW               = array();
            $sex_education     = array();
            $child_care        = array();
            $mind_counseling   = array();
            $skin_hair_care    = array();
            $eye_care          = array();
            $dental_care       = array();
            $health_cover      = array();
            $pharmacy_stores   = array();
            $ayurveda          = array();
            $doctors           = array();
            $labs              = array();
            $nursing_attendant = array();
            $cupping_therapy   = array();
            $physiotherapist   = array();
            $fitness           = array();
            $pest_control      = array();
            $hospitals         = array();
            $diet_nutrition    = array();
            
            $job_placements = array();
            
            foreach ($query->result_array() as $row) {
                $cat_id = $row['cat_id'];
                $image  = $row['image'];
                $image  = 'https://d2c8oti4is0ms3.cloudfront.net/images/medicalwale_mobile_app_icons/banners/'.$image;
                
                switch ($cat_id) {
                    case "1":
                        $home[] = array(
                            'image' => $image
                        );
                        break;                    
                    case "2":
                        $blood_group[] = array(
                            'image' => $image
                        );
                        break;                    
                    case "3":
                        $OFW[] = array(
                            'image' => $image
                        );
                        break;                    
                    case "4":
                        $sex_education[] = array(
                            'image' => $image
                        );
                        break;                    
                    case "5":
                        $child_care[] = array(
                            'image' => $image
                        );
                        break;                    
                    case "6":
                        $mind_counseling[] = array(
                            'image' => $image
                        );
                        break;                    
                    case "7":
                        $skin_hair_care[] = array(
                            'image' => $image
                        );
                        break;                    
                    case "8":
                        $eye_care[] = array(
                            'image' => $image
                        );
                        break;                    
                    case "9":
                        $dental_care[] = array(
                            'image' => $image
                        );
                        break;                    
                    case "10":
                        $health_cover[] = array(
                            'image' => $image
                        );
                        break;                    
                    case "11":
                        $pharmacy_stores[] = array(
                            'image' => $image
                        );
                        break;                    
                    case "12":
                        $ayurveda[] = array(
                            'image' => $image
                        );
                        break;                    
                    case "13":
                        $doctors[] = array(
                            'image' => $image
                        );
                        break;                    
                    case "14":
                        $labs[] = array(
                            'image' => $image
                        );
                        break;                    
                    case "15":
                        $nursing_attendant[] = array(
                            'image' => $image
                        );
                        break;                    
                    case "16":
                        $cupping_therapy[] = array(
                            'image' => $image
                        );
                        break;                    
                    case "17":
                        $physiotherapist[] = array(
                            'image' => $image
                        );
                        break;                    
                    case "18":
                        $fitness[] = array(
                            'image' => $image
                        );
                        break;                    
                    case "19":
                        $pest_control[] = array(
                            'image' => $image
                        );
                        break;                    
                    case "20":
                        $hospitals[] = array(
                            'image' => $image
                        );
                        break;                    
                    case "21":
                        $diet_nutrition[] = array(
                            'image' => $image
                        );
                        break;                    
                    case "22":
                        $job_placements[] = array(
                            'image' => $image
                        );
                        break;
                }
                
                
            }
            $data[] = array(
                "home" => $home,
                "blood_group" => $blood_group,
                "OFW" => $OFW,
                "sex_education" => $sex_education,
                "child_care" => $child_care,
                "mind_counseling" => $mind_counseling,
                "skin_hair_care" => $skin_hair_care,
                "eye_care" => $eye_care,
                "dental_care" => $dental_care,
                "health_cover" => $health_cover,
                "pharmacy_stores" => $pharmacy_stores,
                "ayurveda" => $ayurveda,
                "doctors" => $doctors,
                "labs" => $labs,
                "nursing_attendant" => $nursing_attendant,
                "cupping_therapy" => $cupping_therapy,
                "physiotherapist" => $physiotherapist,
                "fitness" => $fitness,
                "pest_control" => $pest_control,
                "hospitals" => $hospitals,
                "diet_nutrition" => $diet_nutrition,
                "job_placements" => $job_placements
                
                
            );
            
            $resultpost = array(
                "status" => 200,
                "message" => 'success',
                "data" => $data
            );
        } else {
            $data = array();
            return array(
                "status" => 200,
                "message" => "success",
                "count" => 0,
                "data" => $data
            );
        }
        return $resultpost;
    }
    
    
    
}
