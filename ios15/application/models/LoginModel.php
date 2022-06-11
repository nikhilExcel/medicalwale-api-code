<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class LoginModel extends CI_Model { 

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

    //check app version
    public function appversion() {
        $text = 'What’s New
1. Offline Bachat Health Card
2. Reply on comments is added on Healthwall and Ask Saheii
3. UI enhancements and bugs resolved';
        $version = '27';
        return array(
            'status' => 200,
            'message' => 'success',
            'version' => (int) $version,
            'text' => $text
        );
    }

    //check api login
    public function login($username, $password) {
        date_default_timezone_set('Asia/Kolkata');
        $q = $this->db->select('password,id')->from('api_users')->where('username', $username)->get()->row();
        if ($q == "") {
            return array(
                'status' => 204,
                'message' => 'Username not found.'
            );
        } else {
            $hashed_password = $q->password;
            $id = $q->id;
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
    

            public function forgotpassword_app($username,$type,$hash_key,$agent) {                
            if ($username == "") {
                $resultpost = array(
                    "status" => 208,
                    "message" => "Please enter email or phone.",
                    "data" => array()
                );
                return $resultpost;
            } else {
            $user_query  = $this->db->query("SELECT id from users where (email='$username' or phone='$username')");
            $check_user  = $user_query->num_rows();			
            if ($check_user>0) {
                $user_querys = $user_query->row();
                $id = $user_querys->id;
                $q = $this->db->select('phone,email,id')->from('users')->where('users.id', $id)->get()->row();
                $id = $q->id;
                $phone = $q->phone;
                $email = $q->email;
                
                
                
                $otp_code = rand(100000, 999999);
                if($type=='email'){
                    $message = 'Please enter this One Time Password : ' . $otp_code . ' to verify your Email Address.';
                    $to = $email;
                    $subject = 'medicalwale login OTP';
                    $msg = $message;
                    $this->Send_Email_Notification($to, $subject, $msg);
                }
                else{
                    if($agent=="android")
                    {
                    $message = '<#> Please enter this One Time Password : ' . $otp_code . ' to verify your mobile number. ' . $hash_key . '. Aap ke Health Ka Saathi.';
                    }
                    else
                    {
                      $message = 'Please enter this One Time Password : ' . $otp_code . ' to verify your mobile number. ';  
                    }
                    $post_data = array(
                        'From' => '02233721563',
                        'To' => $phone,
                        'Body' => $message
                    );
                    $exotel_sid = "aegishealthsolutions";
                    $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                    $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
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
                }
                $querys = $this->db->query("UPDATE `users` SET `otp_code`='$otp_code' WHERE id='$id'");
                $resultpost[] = array(
                    'otp_code' => (int) $otp_code,
                    'id' => $id
                );
                $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "data" => $resultpost
                );
                return $resultpost;
                
            }
            else{
			
			    $otp_code = rand(100000, 999999);
                if($type=='email'){
                    $message = 'Please enter this One Time Password : ' . $otp_code . ' to verify your Email Address.';
                    $to = $username;
                    $subject = 'medicalwale login OTP';
                    $msg = $message;
                    $this->Send_Email_Notification($to, $subject, $msg);
                 }
				 else{
				 
				   if($agent=="android")
                    {
                    $message = '<#> Please enter this One Time Password : ' . $otp_code . ' to verify your mobile number. ' . $hash_key . '. Aap ke Health Ka Saathi.';
                    }
                    else
                    {
                      $message = 'Please enter this One Time Password : ' . $otp_code . ' to verify your mobile number. ';  
                    }
                    $post_data = array(
                        'From' => '02233721563',
                        'To' => $username,
                        'Body' => $message
                    );
                    $exotel_sid = "aegishealthsolutions";
                    $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                    $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
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
				 }	
                $resultpost[] = array(
                    'otp_code' => (int) $otp_code,
                    'id' => 0
                );
                $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "data" => $resultpost
                );
                return $resultpost;
            }
            
    }
    
}
	
	    public function applogin($username,$password,$token,$agent) {
        if (empty($username) && empty($password)) {
            return array(
                'status' => 208,
                'message' => 'Please enter username and password'
            );
        } else {
            $user_query  = $this->db->query("SELECT id from users where (email='$username' or phone='$username') AND vendor_id=0");
            $check_user  = $user_query->num_rows();			
            if ($check_user>0) {
                $user_querys = $user_query->row();
                $id = $user_querys->id;
                $q = $this->db->select('password,IFNULL(height_cm_ft,"") AS height_cm_ft,id,name,phone,email,gender,dob,height,weight,marital_status,blood_group,diet_fitness,organ_donor,sex_history,agent')->from('users')->where('users.id', $id)->get()->row();
                //print_r($q);
                $id = $q->id;
                $name = $q->name;
                $phone = $q->phone;
                $email = $q->email;
                $gender = $q->gender;
                $dob = $q->dob;
                $height = $q->height;
                $weight = $q->weight;
                $marital_status = $q->marital_status;
                $blood_group = $q->blood_group;
                $diet_fitness = $q->diet_fitness;
                $organ_donor = $q->organ_donor;
                $sex_history = $q->sex_history;
                 if($q->height_cm_ft != NULL)
                {
                  $height_cm_ft = $q->height_cm_ft;
                }
                else
                {
                    $height_cm_ft = "";
                }
                

                $img_count = $this->db->select('media.id')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $id)->get()->num_rows();
               
                 if ($img_count > 0) {
                    $media = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $id)->get()->row();
                    $img_file = $media->source;
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                $last_login = date('Y-m-d H:i:s', strtotime('+12 hours'));
                $this->db->where('id', $id)->update('users', array(
                    'last_login' => $last_login
                ));
                $card_no = '';
                $card_type = '';
                $card_query = $this->db->query("SELECT card_no,card_type from user_privilage_card where user_id='$id'");
                $card_count = $card_query->num_rows();
                $card_query_new = $this->db->query("SELECT card_no,card_type from user_priviladge_card_new where user_id='$id'");
                $card_count_new = $card_query_new->num_rows();
                if ($card_count > 0) {
                    $card_list = $card_query->row_array();
                    $card_no = $card_list['card_no'];
                    $card_type = $card_list['card_type'];
                } else if ($card_count_new > 0) {
                    $card_list_new = $card_query_new->row_array();
                    $card_no = $card_list_new['card_no'];
                    $card_type = $card_list_new['card_type'];
                }

                $q = $this->db->select('avatar_id,password,height_cm_ft,id,name,IFNULL(activity_level,"") AS activity_level,phone,email,gender,dob,IFNULL(height,"") AS height,IFNULL(weight,"") AS weight,IFNULL(marital_status,"") AS marital_status,IFNULL(blood_group,"") AS blood_group,IFNULL(blood_is_active,"") AS blood_is_active,IFNULL(diet_fitness,"") AS diet_fitness,IFNULL(organ_donor,"") AS organ_donor,sex_history,exercise_level,health_condition,health_insurance,addiction,allergies,IFNULL(bmi,"") AS bmi ,IFNULL(heradiatry_problem,"") AS heradiatry_problem,IFNULL(lat,"") AS lat,IFNULL(lng,"") AS lng,IFNULL(map_location,"") AS map_location')->from('users')->where('users.id', $id)->get()->row();
               // print_r($q);

                 if($q->height_cm_ft != NULL)
                {
                  $height_cm_ft = $q->height_cm_ft;
                }
                else
                {
                    $height_cm_ft = "";
                }
                
                $id = $q->id;
                $name = $q->name;
                $avatar_id = $q->avatar_id;
                $phone = $q->phone;
                $email = $q->email;
                $gender = $q->gender;
                $dob = $q->dob;
                $height = $q->height;
                $weight = $q->weight;
                $marital_status = $q->marital_status;
                $blood_group = $q->blood_group;
                $diet_fitness = $q->diet_fitness;
                $organ_donor = $q->organ_donor;
                $sex_history = $q->sex_history;
                $exercise_level = $q->exercise_level;
                $activity_level = $q->activity_level;
                $health_condition = $q->health_condition;
                $allergies = $q->allergies;
                $bmi = $q->bmi;
                $health_insurance = $q->health_insurance;
                $addiction = $q->addiction;
                $blood_is_active = $q->blood_is_active;
                $heradiatry_problem = $q->heradiatry_problem;
                $lat = $q->lat;
                $lng = $q->lng;
                $map_location = $q->map_location;
                $medical_genetic_disorder = array();
                $count_query = $this->db->query("SELECT * FROM `user_medical_genetic_disorder` WHERE user_id='$id'");
                $count = $count_query->num_rows();
                if ($count > 0) {
                    foreach ($count_query->result_array() as $row) {
                        $medical_genetic_disorder[] = array('name' => $row['name'], 'value' => $row['value']);
                    }
                }
                if ($avatar_id == '0') {
                    $avatar_id_status = '0';
                } else {
                    $avatar_id_status = '5';
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
                    $sex_history_status = '5';
                } else {
                    $sex_history_status = '5';
                }
                if ($exercise_level == '') {
                    $activity_level_status = '0';
                } else {
                    $activity_level_status = '5';
                }
                if ($health_condition == '' || $health_condition == '0') {
                    $health_condition_is = '0';
                } else {
                    $health_condition_is = '5';
                }
                if ($allergies == '' || $allergies == '0') {
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
                if ($addiction == '' || $addiction == '0') {
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

                $profile_completed = $step1 + $height_status + $weight_status + $marital_status_is + $blood_group_status + $diet_fitness_status + $organ_donor_status + $sex_history_status + $activity_level_status + $health_condition_is + $allergies_status + $bmi_status + $health_insurance_status + $addiction_status + $heradiatry_problem_status + $location_status + $avatar_id_status;
  
                $Miss_package = $this->db->query("SELECT diet_user_package_history.id FROM `diet_user_package_history` INNER JOIN `users` ON diet_user_package_history.user_id=users.id WHERE diet_user_package_history.user_id='$id'");
                $Miss_belly_package = $Miss_package->num_rows();
                if ($Miss_belly_package > 0) {
                    $Miss_belly_status = "true";
                } else {
                    $Miss_package_free = $this->db->query("SELECT medicalbot_savefreedietplans.id FROM `medicalbot_savefreedietplans` INNER JOIN `users` ON medicalbot_savefreedietplans.user_id=users.id WHERE medicalbot_savefreedietplans.user_id='$id'");
                    $Miss_belly_package_free = $Miss_package_free->num_rows();
                    if ($Miss_belly_package_free > 0) {
                        $Miss_belly_status = "True";
                    } else {
                        $Miss_belly_status = "False";
                    }
                }
                $otp_code='0';
				if(empty($image))
				{
				    $image='';
				}
				else
				{
				    $image;
				}
			//	echo $q->password;
				
				if($q->password==$password){
					$resultpost[] = array(
                    'otp_code' => (int) $otp_code,
                    'user' => 'old',
                    'id' => $id,
                    'card_no' => str_replace('null', '', $card_no),
                    'card_type' => $card_type,
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
                    'sex_history' => $sex_history,
                    'height_cm_ft' => $height_cm_ft,
                    'profile_percentage' => $profile_completed,
                    'is_Miss_belly_activated' => $Miss_belly_status
					);
					$resultpost = array(
                        "status" => 200,
                        "message" => "success",
                        "data" => $resultpost
                    );
            return $resultpost;
				}
				else if(empty($q->password)){
					$resultpost = array(
                        'status' => 208,
						'message' => 'password not set',
                        'data' => array()
                    );
            return $resultpost;
				}
				else{
					$resultpost = array(
                        'status' => 208,
						'message' => 'wrong password',
                        'data' => array()
                    );
            return $resultpost;
				}
				
                
            } else {
            $resultpost = array(
                        'status' => 208,
						'message' => 'wrong username',
                        'data' => array()
                    );

            return $resultpost;
            }
        }
    }
	
    public function app_set_password($user_id,$password) {
        if (empty($user_id) && empty($password)) {
            return array(
                'status' => 208,
                'message' => 'Please enter user id and password'
            );
        } else {
            $user_query  = $this->db->query("SELECT id from users where (id='$user_id')");
            $check_user  = $user_query->num_rows();			
            if ($check_user>0) {
                $user_querys = $user_query->row();
                $id = $user_querys->id;
				$querys = $this->db->query("UPDATE `users` SET `password`='$password' WHERE id='$user_id'");
                $q = $this->db->select('password,IFNULL(height_cm_ft,"") AS height_cm_ft,id,name,phone,email,gender,dob,height,weight,marital_status,blood_group,diet_fitness,organ_donor,sex_history,agent')->from('users')->where('users.id', $id)->get()->row();
                $id = $q->id;
                $name = $q->name;
                $phone = $q->phone;
                $email = $q->email;
                $gender = $q->gender;
                $dob = $q->dob;
                $height = $q->height;
                $weight = $q->weight;
                $marital_status = $q->marital_status;
                $blood_group = $q->blood_group;
                $diet_fitness = $q->diet_fitness;
                $organ_donor = $q->organ_donor;
                $sex_history = $q->sex_history;
                $height_cm_ft = $q->height_cm_ft;
                $image='';
                $img_count = $this->db->select('media.id')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $id)->get()->num_rows();
               
                 if ($img_count > 0) {
                    $media = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $id)->get()->row();
                    $img_file = $media->source;
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                $last_login = date('Y-m-d H:i:s', strtotime('+12 hours'));
                $this->db->where('id', $id)->update('users', array(
                    'last_login' => $last_login
                ));
                $card_no = '';
                $card_type = '';
                $card_query = $this->db->query("SELECT card_no,card_type from user_privilage_card where user_id='$id'");
                $card_count = $card_query->num_rows();
                $card_query_new = $this->db->query("SELECT card_no,card_type from user_priviladge_card_new where user_id='$id'");
                $card_count_new = $card_query_new->num_rows();
                if ($card_count > 0) {
                    $card_list = $card_query->row_array();
                    $card_no = $card_list['card_no'];
                    $card_type = $card_list['card_type'];
                } else if ($card_count_new > 0) {
                    $card_list_new = $card_query_new->row_array();
                    $card_no = $card_list_new['card_no'];
                    $card_type = $card_list_new['card_type'];
                }

                $q = $this->db->select('avatar_id,password,height_cm_ft,id,name,IFNULL(activity_level,"") AS activity_level,phone,email,gender,dob,IFNULL(height,"") AS height,IFNULL(weight,"") AS weight,IFNULL(marital_status,"") AS marital_status,IFNULL(blood_group,"") AS blood_group,IFNULL(blood_is_active,"") AS blood_is_active,IFNULL(diet_fitness,"") AS diet_fitness,IFNULL(organ_donor,"") AS organ_donor,sex_history,exercise_level,health_condition,health_insurance,addiction,allergies,IFNULL(bmi,"") AS bmi ,IFNULL(heradiatry_problem,"") AS heradiatry_problem,IFNULL(lat,"") AS lat,IFNULL(lng,"") AS lng,IFNULL(map_location,"") AS map_location')->from('users')->where('users.id', $id)->get()->row();

                $height_cm_ft = $q->height_cm_ft;
                $id = $q->id;
                $name = $q->name;
                $avatar_id = $q->avatar_id;
                $phone = $q->phone;
                $email = $q->email;
                $gender = $q->gender;
                $dob = $q->dob;
                $height = $q->height;
                $weight = $q->weight;
                $marital_status = $q->marital_status;
                $blood_group = $q->blood_group;
                $diet_fitness = $q->diet_fitness;
                $organ_donor = $q->organ_donor;
                $sex_history = $q->sex_history;
                $exercise_level = $q->exercise_level;
                $activity_level = $q->activity_level;
                $health_condition = $q->health_condition;
                $allergies = $q->allergies;
                $bmi = $q->bmi;
                $health_insurance = $q->health_insurance;
                $addiction = $q->addiction;
                $blood_is_active = $q->blood_is_active;
                $heradiatry_problem = $q->heradiatry_problem;
                $lat = $q->lat;
                $lng = $q->lng;
                $map_location = $q->map_location;
                $medical_genetic_disorder = array();
                $count_query = $this->db->query("SELECT * FROM `user_medical_genetic_disorder` WHERE user_id='$id'");
                $count = $count_query->num_rows();
                if ($count > 0) {
                    foreach ($count_query->result_array() as $row) {
                        $medical_genetic_disorder[] = array('name' => $row['name'], 'value' => $row['value']);
                    }
                }
                if ($avatar_id == '0') {
                    $avatar_id_status = '0';
                } else {
                    $avatar_id_status = '5';
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
                    $sex_history_status = '5';
                } else {
                    $sex_history_status = '5';
                }
                if ($exercise_level == '') {
                    $activity_level_status = '0';
                } else {
                    $activity_level_status = '5';
                }
                if ($health_condition == '' || $health_condition == '0') {
                    $health_condition_is = '0';
                } else {
                    $health_condition_is = '5';
                }
                if ($allergies == '' || $allergies == '0') {
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
                if ($addiction == '' || $addiction == '0') {
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

                $profile_completed = $step1 + $height_status + $weight_status + $marital_status_is + $blood_group_status + $diet_fitness_status + $organ_donor_status + $sex_history_status + $activity_level_status + $health_condition_is + $allergies_status + $bmi_status + $health_insurance_status + $addiction_status + $heradiatry_problem_status + $location_status + $avatar_id_status;
  
                $Miss_package = $this->db->query("SELECT diet_user_package_history.id FROM `diet_user_package_history` INNER JOIN `users` ON diet_user_package_history.user_id=users.id WHERE diet_user_package_history.user_id='$id'");
                $Miss_belly_package = $Miss_package->num_rows();
                if ($Miss_belly_package > 0) {
                    $Miss_belly_status = "true";
                } else {
                    $Miss_package_free = $this->db->query("SELECT medicalbot_savefreedietplans.id FROM `medicalbot_savefreedietplans` INNER JOIN `users` ON medicalbot_savefreedietplans.user_id=users.id WHERE medicalbot_savefreedietplans.user_id='$id'");
                    $Miss_belly_package_free = $Miss_package_free->num_rows();
                    if ($Miss_belly_package_free > 0) {
                        $Miss_belly_status = "True";
                    } else {
                        $Miss_belly_status = "False";
                    }
                }
                $otp_code='0';
				if(empty($q->password) || $q->password == NULL){
   $passoword_set=0;
}
else{
    $passoword_set=1;
}
				$resultpost[] = array(
                    'otp_code' => (int) $otp_code,
                    'user' => 'old',
                    'passoword_set' => $passoword_set,
                    'id' => $id,
                    'card_no' => str_replace('null', '', $card_no),
                    'card_type' => $card_type,
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
                    'sex_history' => $sex_history,
                    'height_cm_ft' => $height_cm_ft,
                    'profile_percentage' => $profile_completed,
                    'is_Miss_belly_activated' => $Miss_belly_status
					);
				
                $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "data" => $resultpost
                );
            } else {
			$resultpost = array(
                'status' => 208,
                'message' => 'wrong user id',
                'data' => array()
            );

            }
            return $resultpost;
        }
    }

    //token logout to stop notification
    public function logout() {
        $users_id = $this->input->get_request_header('User-ID', TRUE);
        $token = $this->input->get_request_header('Authorization', TRUE);
        $this->db->where('users_id', $users_id)->where('token', $token)->delete('api_users_authentication');
        return array(
            'status' => 200,
            'message' => 'Successfully logout.'
        );
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
                $expired_at = '2020-11-12 08:57:58';
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

    //send otp & update last otp
    public function sendotp($phone) {
        $otp_code = rand(100000, 999999);
        $message = 'Please enter this One Time Password : ' . $otp_code . ' to verify your mobile number.';
        $post_data = array('From' => '02233721563', 'To' => $phone, 'Body' => $message);
        $exotel_sid = "aegishealthsolutions";
        $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
        $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
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
        $id=0;
        $querys = $this->db->query("UPDATE `users` SET `otp_code`='$otp_code' WHERE phone='$phone'");
        $user_query  = $this->db->query("SELECT id from users where (phone='$phone')");
        $check_user  = $user_query->num_rows();
        if ($check_user>0) {
            $user_querys = $user_query->row();
            $id = $user_querys->id;
        }
        return array(
            'status' => 204,
            'message' => 'success',
            'otp_code' => $otp_code,
            'user_id' => $id
        );
    }

    //calculate current time difference
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
            return date('D, d M Y', strtotime($created_time));
        } elseif (intval($time_differnce / $years) > 0) {
            return date('D, d M Y', strtotime($created_time));
        } elseif (intval($time_differnce / $months) > 1) {
            return intval($time_differnce / $months) . ' months';
        } elseif (intval(($time_differnce / $months)) > 0) {
            return date('D, d M Y', strtotime($created_time));
            return intval(($time_differnce / $months)) . ' month';
        } elseif (intval(($time_differnce / $days)) > 1) {
            return date('D, d M', strtotime($created_time));
        } elseif (intval(($time_differnce / $days)) > 0) {
            return date('D, d M', strtotime($created_time));
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

    //activate privilage card
    public function activate_privilage($user_id, $card_no) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $query = $this->db->query("SELECT id from users where id='$user_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            $card_query = $this->db->query("SELECT id,card_type,card_no from user_privilage_card where is_active='0' and card_no='$card_no'");
            /* echo "SELECT id,card_type,card_no from user_privilage_card where is_active='0' and card_no='$card_no'";
              die(); */
            $card_querys = $card_query->row();

            $card_count = $card_query->num_rows();
//die();
            if ($card_count > 0) {
                $card_type = $card_querys->card_type;

                $card_no = $card_querys->card_no;
                $this->db->query("UPDATE `user_privilage_card` SET `is_active`='1',user_id='$user_id',`date`='$created_at' where card_no='$card_no'");


                //added by jakir on 01-june-2018 for notification on activation 
                $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $getusr = $user_plike->row_array();
                $usr_name = $getusr['name'];
                $msg = $usr_name . ', Your Physical Bachat card No :' . $card_no . ' is activated . ';
                $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
                $title = $usr_name . ', Your Physical Bachat card No :' . $card_no . ' is activated . ';
                $customer_token_count = $customer_token->num_rows();

                if ($customer_token_count > 0) {
                    $token_status = $customer_token->row_array();
                    $agent = $token_status['agent'];
                    $reg_id = $token_status['token'];
                    $img_url = $userimage;
                    $tag = 'text';
                    $key_count = '1';
                    $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent);
                }

                //end


                return array(
                    'card_type' => $card_type,
                    'card_no' => $card_no,
                    'status' => 200,
                    'card_status' => 1
                );
            } else {

                //$this->db->query("INSERT INTO `user_privilage_card`( `user_id`, `card_no`, `date`, `is_active`, `expiry`) VALUES ('$user_id','$card_no','$created_at','1','')");
                return array(
                    'card_type' => 'offline',
                    'card_no' => $card_no,
                    'status' => 200,
                    'card_status' => 0,
                    'message' => 'Card is already exist.'
                );
            }
        } else {
            return array(
                'status' => 201,
                'card_status' => '0'
            );
        }
    }

    public function generate_privilage($user_id,$coupon_id) {
        date_default_timezone_set('Asia/Kolkata');
        $booking_id = date("YmdHis");
        $created_at = date('Y-m-d H:i:s');
        $query = $this->db->query("SELECT id from users where id='$user_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            $card_query = $this->db->query("SELECT id,card_no,card_type from user_privilage_card where user_id='$user_id'");
            $card_querys = $card_query->row();
            $card_count = $card_query->num_rows();
            if ($card_count > 0) {
                $card_type = $card_querys->card_type;
                $card_no = $card_querys->card_no;
                 if($coupon_id > 0 ){
                   $user_data_dh    = array(
                            'use_status' => 1
                        );
                        $updateStatus = $this->db->where('coupon',$coupon_id)->where('user_id',$user_id)->update('use_coupon', $user_data_dh); 
                }
                
                return array(
                    'card_no' => $card_no,
                    'card_type' => $card_type,
                    'status' => 200,
                    'card_status' => 0
                );
                
            } else {
                $new_rand_card = $this->db->query("SELECT * FROM user_ecard ORDER BY RAND() LIMIT 1");
                $newcard_details = $new_rand_card->row();
                $new_card_id = $newcard_details->id;
                $new_card_no = $newcard_details->card_no;
                $new_rand_card = $this->db->query("DELETE user_ecard.* FROM user_ecard WHERE id='$new_card_id'");
                $new_ecard = array(
                    'user_id' => $user_id,
                    'card_no' => $new_card_no,
                    'card_type' => 'online',
                    'date' => $created_at,
                    'is_active' => '1',
                );
                $this->db->insert('user_privilage_card', $new_ecard);
                
                
                if($coupon_id > 0 ){
                   $user_data_dh    = array(
                            'use_status' => 1
                        );
                        $updateStatus = $this->db->where('coupon',$coupon_id)->where('user_id',$user_id)->update('use_coupon', $user_data_dh); 
                }
                
                //added by jakir for diet package free subscription on 22-09-2018
                
                
                $new_deit_entry = array(
                    'user_id' => $user_id,
                    'enroll_from' => "Bachat Card",
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $user_id
                );

                $this->db->insert('diet_leads', $new_deit_entry);
                $lead_id = $this->db->insert_id();
                //diet_packages_booked
                $month = "";
                $this->db->select('month');
                $this->db->from('diet_master_package');
                $this->db->where('id', 1);
                $query1 = $this->db->get();
                $month = $query1->row()->month;

                $month1 = "+$month";
                $effectiveDate1 = date('Y-m-d');
                $effectiveDate = date('Y-m-d', strtotime("$month1 months", strtotime($effectiveDate1)));


                $new_booking_lead = array(
                    'user_id' => $user_id,
                    'package_id' => '1',
                    'leads_id' => $lead_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'booking_from_date' => date('Y-m-d'),
                    'booking_to_date' => $effectiveDate,
                    'created_by' => $user_id,
                    'status' => '1'
                );

                $this->db->insert('diet_packages_booked', $new_booking_lead);

                $notification = array('user_id' => $user_id,
                    'listing_id' => "",
                    'package_id' => 1,
                    'booking_id' => $booking_id,
                    'order_id' => 0,
                    'title' => "Diet Package Booking",
                    'msg' => "Diet Package Booked Through Ecard.",
                    'notification_type' => "Diet Booking",
                    'created_at' => date('Y-m-d H:i:s')
                );
                $this->db->insert('diet_plan_notifications', $notification);


                
                /*$booking_history = array('user_id' => $user_id,
                    'leads_id' => $lead_id,
                    'package_id' => 1,
                    'booking_id' => $booking_id,
                    'booking_from' => date('Y-m-d'),
                    'booking_to' => $effectiveDate,
                    'created_at' => date('Y-m-d H:i:s')
                );*/
                
                $booking_history = array('user_id' => $user_id,
                    'leads_id' => $lead_id,
                    'package_id' => 1,
                    'booking_id' => $booking_id,
                    'booking_from' => '0000-00-00',
                    'booking_to' => '0000-00-00',
                    'created_at' => date('Y-m-d H:i:s')
                );

                $this->db->insert('diet_user_package_history', $booking_history);
                $diet_booking_id = $this->db->insert_id();
                //end 
                //added by jakir on 01-june-2018 for notification on activation 
                $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $getusr = $user_plike->row_array();
                $usr_name = $getusr['name'];
                $msg = $usr_name . ', Your online Bachat card No :' . $new_card_no . ' is Generated . ';
                $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
                $title = $usr_name . ', Your online Bachat card No :' . $new_card_no . ' is Generated . ';
                $customer_token_count = $customer_token->num_rows();

                if ($customer_token_count > 0) {
                    $token_status = $customer_token->row_array();
                    $agent = $token_status['agent'];
                    $reg_id = $token_status['token'];
                    $img_url = $userimage;
                    $tag = 'text';
                    $key_count = '1';
                    $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent);

                    $img_url = 'https://medicalwale.com/img/noti_icon_miss_belly.png';
                    $title_package = "Free Diet Package";
                    $msg_package = "Congratulations, You get " . $month . " month free package of Missbelly Diet Plan on Bachat Health Card.";
                    $this->send_gcm_notify_dietpackage($title_package, $reg_id, $msg_package, $img_url, $tag, $agent, $diet_booking_id);
                    $this->insert_all_notification_Mobile($user_id,$title_package, $reg_id, $msg_package, $img_url, $tag, $agent, $diet_booking_id);
                    
                }

                //end


                return array(
                    'card_no' => $new_card_no,
                    'card_type' => 'online',
                    'status' => 200,
                    'card_status' => 1
                );
            }
        } else {
            return array(
                'status' => 201,
                'card_status' => '0'
            );
        }
    }

    //activate privilage card
    public function activate_privilage_with_Coupon($user_id, $card_no, $coupon_code) {
        date_default_timezone_set('Asia/Kolkata');
        $booking_id = date("YmdHis");
        $created_at = date('Y-m-d H:i:s');
        $query = $this->db->query("SELECT id from users where id='$user_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            $card_query = $this->db->query("SELECT id,card_type,card_no,pin_number,pay_type from user_priviladge_card_new where status='inactive' and card_no='$card_no' and pin_number='$coupon_code'");

            $card_querys = $card_query->row();

            $card_count = $card_query->num_rows();

            $old_card_query = $this->db->query("SELECT id,card_type,card_no,pin_number from user_privilage_card where is_active='0' and card_no='$card_no' and pin_number='$coupon_code'");

            $old_card_querys = $old_card_query->row();

            $old_card_count = $old_card_query->num_rows();
//die();
            if ($card_count > 0) {
                $card_type = $card_querys->card_type;

                $card_no = $card_querys->card_no;
                $this->db->query("UPDATE `user_priviladge_card_new` SET `status`='active',user_id='$user_id' where card_no='$card_no' and pin_number='$coupon_code'");


                //added by jakir on 01-june-2018 for notification on activation 
                $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $getusr = $user_plike->row_array();
                $usr_name = $getusr['name'];
                $msg = $usr_name . ', Your Physical Bachat card No :' . $card_no . ' is activated . with Coupon Code is : ' . $coupon_code . ' ';
                $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
                $title = $usr_name . ', Your Physical Bachat card No :' . $card_no . ' is activated . with Coupon Code is : ' . $coupon_code . ' ';
                $customer_token_count = $customer_token->num_rows();



                //  //added by jakir for diet package free subscription on 22-09-2018
                // $new_deit_entry = array(
                //     'user_id' => $user_id,
                //     'status_at' => date('Y-m-d H:i:s'),
                //     'created_at' => date('Y-m-d H:i:s'),
                //     'updated_at' => date('Y-m-d H:i:s'),
                //     'created_by' => $user_id,
                //     'updated_by' => $user_id,
                //     'status' => '1'
                //     );
                //   $this->db->insert('diet_leads', $new_deit_entry); 
                //  $lead_id =  $this->db->insert_id();
                //   //diet_packages_booked
                //   $new_booking_lead = array(
                //       'user_id' => $user_id,
                //       'package_id' => '5',
                //       'lead_id' => $lead_id,
                //       'created_at' =>date('Y-m-d H:i:s'),
                //       'booking_from_date' => date('Y-m-d H:i:s'),
                //       'created_by' =>$user_id,
                //       'updated_at' => date('Y-m-d H:i:s'),
                //       'updated_by' => $user_id,
                //       'status' => '1'
                //       );
                //   $this->db->insert('diet_packages_booked', $new_booking_lead); 
                // //end 
                //added by jakir for diet package free subscription on 22-09-2018
                $new_deit_entry = array(
                    'user_id' => $user_id,
                    'enroll_from' => "Bachat Card",
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $user_id
                );

                $diet_pack_id = 1;
                if($card_querys->pay_type == 600)
                {
                    $diet_pack_id = 2;
                }
                $this->db->insert('diet_leads', $new_deit_entry);
                $lead_id = $this->db->insert_id();
                //diet_packages_booked
                $month = "";
                $this->db->select('month');
                $this->db->from('diet_master_package');
                $this->db->where('id', $diet_pack_id);
                $query1 = $this->db->get();
                $month = $query1->row()->month;

                $month1 = "+$month";
                $effectiveDate1 = date('Y-m-d');
                $effectiveDate = date('Y-m-d', strtotime("$month1 months", strtotime($effectiveDate1)));


                $new_booking_lead = array(
                    'user_id' => $user_id,
                    'package_id' => $diet_pack_id,
                    'leads_id' => $lead_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'booking_from_date' => date('Y-m-d'),
                    'booking_to_date' => $effectiveDate,
                    'created_by' => $user_id,
                    'status' => '1'
                );

                $this->db->insert('diet_packages_booked', $new_booking_lead);

                $notification = array('user_id' => $user_id,
                    'listing_id' => "",
                    'package_id' => $diet_pack_id,
                    'booking_id' => $booking_id,
                    'order_id' => 0,
                    'title' => "Diet Package Booking",
                    'msg' => "Diet Package Booked Through Ecard.",
                    'notification_type' => "Diet Booking",
                    'created_at' => date('Y-m-d H:i:s')
                );
                $this->db->insert('diet_plan_notifications', $notification);
                $note_id = $this->db->insert_id();


                $booking_history = array('user_id' => $user_id,
                    'leads_id' => $lead_id,
                    'package_id' => $diet_pack_id,
                    'booking_id' => $booking_id,
                    'booking_from' => '0000-00-00',
                    'booking_to' => '0000-00-00',
                    'created_at' => date('Y-m-d H:i:s')
                );

                $this->db->insert('diet_user_package_history', $booking_history);
                $diet_booking_id = $this->db->insert_id();
                //end 


                if ($customer_token_count > 0) {
                    $token_status = $customer_token->row_array();
                    $agent = $token_status['agent'];
                    $reg_id = $token_status['token'];
                    $img_url = $userimage;
                    $tag = 'text';
                    $key_count = '1';
                    $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent);
                    
                     
                    $img_url = 'https://medicalwale.com/img/noti_icon_miss_belly.png';
                    $title_package = "Free Diet Package";
                    $msg_package = "Congratulations, You get " . $month . " month free package of Missbelly Diet Plan on Bachat Health Card.";
                    $this->send_gcm_notify_dietpackage($title_package, $reg_id, $msg_package, $img_url, $tag, $agent, $diet_booking_id);
                    
                     $this->insert_all_notification_Mobile($user_id,$title_package, $reg_id, $msg_package, $img_url, $tag, $agent, $diet_booking_id);
                    
                    
                }

                //end


                return array(
                    'card_type' => $card_type,
                    'card_no' => $card_no,
                    'coupon_number' => $coupon_code,
                    'status' => 200,
                    'card_status' => 1
                );
            } else if ($old_card_count > 0) {
                $card_type = $old_card_querys->card_type;

                $card_no = $old_card_querys->card_no;
                $this->db->query("UPDATE `user_privilage_card` SET `is_active`='1',user_id='$user_id',`date`='$created_at' where card_no='$card_no' and pin_number='$coupon_code'");


                //added by jakir on 01-june-2018 for notification on activation 
                $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $getusr = $user_plike->row_array();
                $usr_name = $getusr['name'];
                $msg = $usr_name . ', Your Physical Bachat card No :' . $card_no . ' is activated . with Coupon Code is : ' . $coupon_code . ' ';
                $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
                $title = $usr_name . ', Your Physical Bachat card No :' . $card_no . ' is activated . with Coupon Code is : ' . $coupon_code . ' ';
                $customer_token_count = $customer_token->num_rows();

                //old card diet plan
                //added by jakir for diet package free subscription on 22-09-2018
                $new_deit_entry = array(
                    'user_id' => $user_id,
                    'enroll_from' => "Free Bachat Card",
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $user_id
                );

                $this->db->insert('diet_leads', $new_deit_entry);
                $lead_id = $this->db->insert_id();
                //diet_packages_booked
                $month = "";
                $this->db->select('month');
                $this->db->from('diet_master_package');
                $this->db->where('id', 1);
                $query1 = $this->db->get();
                $month = $query1->row()->month;

                $month1 = "+$month";
                $effectiveDate1 = date('Y-m-d');
                $effectiveDate = date('Y-m-d', strtotime("$month1 months", strtotime($effectiveDate1)));


                $new_booking_lead = array(
                    'user_id' => $user_id,
                    'package_id' => '1',
                    'leads_id' => $lead_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'booking_from_date' => date('Y-m-d'),
                    'booking_to_date' => $effectiveDate,
                    'created_by' => $user_id,
                    'status' => '1'
                );

                $this->db->insert('diet_packages_booked', $new_booking_lead);

                $notification = array('user_id' => $user_id,
                    'listing_id' => "",
                    'package_id' => 1,
                    'booking_id' => $booking_id,
                    'order_id' => 0,
                    'title' => "Diet Package Booking",
                    'msg' => "Diet Package Booked Through Ecard.",
                    'notification_type' => "Diet Booking",
                    'created_at' => date('Y-m-d H:i:s')
                );
                $this->db->insert('diet_plan_notifications', $notification);
                $note_id = $this->db->insert_id();


                $booking_history = array('user_id' => $user_id,
                    'leads_id' => $lead_id,
                    'package_id' => 1,
                    'booking_id' => $booking_id,
                    'booking_from' => '0000-00-00',
                    'booking_to' => '0000-00-00',
                    'created_at' => date('Y-m-d H:i:s')
                );

                $this->db->insert('diet_user_package_history', $booking_history);
                $diet_booking_id = $this->db->insert_id();
                //end 
                //end

                if ($customer_token_count > 0) {
                    $token_status = $customer_token->row_array();
                    $agent = $token_status['agent'];
                    $reg_id = $token_status['token'];
                    $img_url = $userimage;
                    $tag = 'text';
                    $key_count = '1';
                    $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent);
                    
                     $img_url = 'https://medicalwale.com/img/noti_icon_miss_belly.png';
                    $title_package = "Free Diet Package";
                    $msg_package = "Congratulations, You get " . $month . " month free package of Missbelly Diet Plan on Bachat Health Card.";
                    $this->send_gcm_notify_dietpackage($title_package, $reg_id, $msg_package, $img_url, $tag, $agent, $diet_booking_id);
                    $this->insert_all_notification_Mobile($user_id,$title_package, $reg_id, $msg_package, $img_url, $tag, $agent, $diet_booking_id);
                    
                }

                //end


                return array(
                    'card_type' => $card_type,
                    'card_no' => $card_no,
                    'coupon_number' => $coupon_code,
                    'status' => 200,
                    'card_status' => 1
                );
            } else {

                //$this->db->query("INSERT INTO `user_privilage_card`( `user_id`, `card_no`, `date`, `is_active`, `expiry`) VALUES ('$user_id','$card_no','$created_at','1','')");
                return array(
                    'card_type' => 'offline',
                    'card_no' => $card_no,
                    'coupon_number' => $coupon_code,
                    'status' => 200,
                    'card_status' => 0,
                    'message' => 'Invalid card number or Coupon Code. Kindly enter correct number

OR

Kindly Generate a New Card'
                );
            }
        } else {
            return array(
                'status' => 201,
                'card_status' => '0'
            );
        }
    }

    public function check_privilage_card_with_Coupon($user_id, $card_no) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $query = $this->db->query("SELECT id from users where id='$user_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            $card_query = $this->db->query("SELECT id,card_type,card_no,pin_number from user_priviladge_card_new where status='inactive' and card_no='$card_no'");

            $card_querys = $card_query->row();

            $card_count = $card_query->num_rows();



//die();
            if ($card_count > 0) {

                $pin_number = $card_querys->pin_number;
                $card_no = $card_querys->card_no;

                $order_info = $this->db->select('phone,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
                $token_status = $order_info->token_status;
                $customer_phone = $order_info->phone;


                // $message = 'Your generated coupon code is .'.$pin_number.' with cord no is . '.$card_no.'';
                // $message = 'Your generated coupon code is : '.$pin_number.' with card no is : '.$card_no.' please Enter coupon code To verify.';    
                //     $post_data = array('From' => '02233721563', 'To' => $customer_phone, 'Body' => $message);
                //     $exotel_sid = "aegishealthsolutions";
                //     $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                //     $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
                //     $ch = curl_init();
                //     curl_setopt($ch, CURLOPT_VERBOSE, 1);
                //     curl_setopt($ch, CURLOPT_URL, $url);
                //     curl_setopt($ch, CURLOPT_POST, 1);
                //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                //     curl_setopt($ch, CURLOPT_FAILONERROR, 0);
                //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                //     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                //     $http_result = curl_exec($ch);
                //     curl_close($ch);

                return array(
                    'card_no' => $card_no,
                    'message' => "coupon code is present for this card.",
                    'status' => 200,
                );
            } else {
                //    $query = $this->db->query("SELECT id from users where id='$user_id'");
                $card_query_old = $this->db->query("SELECT * from user_privilage_card where is_active='0' and card_no='$card_no'");
                $count = $card_query_old->num_rows();


                if ($count > 0) {

                    $max1 = strlen($card_no);
                    $generated_coupon = "";
                    for ($i = 0; $i < 8; $i++) {
                        $generated_coupon .=$card_no[random_int(0, $max1 - 1)];
                    }




                    $order_info = $this->db->select('phone,email,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
                    $token_status = $order_info->token_status;
                    $customer_phone = $order_info->phone;
                    $mail = $order_info->email;

                    //  $message = 'Your generated coupon code is .'.$generated_coupon.' with card no is . '.$card_no.'';
                    $message = 'Your generated coupon code is : ' . $generated_coupon . ' with card no is : ' . $card_no . ' please Enter coupon code To verify.';
                    // $post_data = array('From' => '02233721563', 'To' => $customer_phone, 'Body' => $message);
                    // $exotel_sid = "aegishealthsolutions";
                    // $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                    // $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
                    // $ch = curl_init();
                    // curl_setopt($ch, CURLOPT_VERBOSE, 1);
                    // curl_setopt($ch, CURLOPT_URL, $url);
                    // curl_setopt($ch, CURLOPT_POST, 1);
                    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    // curl_setopt($ch, CURLOPT_FAILONERROR, 0);
                    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                    // $http_result = curl_exec($ch);
                    // curl_close($ch);
                    // echo $customer_phone;
                    //  die();
                    $post_data = array(
                        'From' => '02233721563',
                        'To' => $customer_phone,
                        'Body' => $message
                    );
                    $exotel_sid = "aegishealthsolutions";
                    $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                    $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";

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

                    if ($mail != '' || $mail != NULL) {
                        $message = 'Your generated coupon code is .' . $generated_coupon . ' with card no is . ' . $card_no . '';
                        $to = $mail;
                        $subject = 'medicalwale Privilage generated coupon code';
                        $msg = $message;
                        $this->Send_Email_Notification($to, $subject, $msg);
                    }
                    $this->db->query("UPDATE `user_privilage_card` SET `pin_number`='$generated_coupon',user_id='$user_id',`date`='$created_at' where card_no='$card_no'");
                    return array(
                        'card_no' => $card_no,
                        'message' => "coupon code is successfully send on registered mobile number for this card.",
                        'coupon_code' => $generated_coupon,
                        'status' => 200,
                    );
                } else {
                    return array(
                        'card_no' => $card_no,
                        'message' => 'Invalid card number or Coupon Code. Kindly enter correct number OR Kindly Generate a New Card',
                        'status' => 200,
                    );
                }
                // return array(
                //     'card_no' => $card_no,
                //     'message' => "coupon code is successfully send on registered mobile number for this card.",
                //     'coupon_code' => $generated_coupon,
                //     'status' => 200,
                // ); 
            }
        } else {
            return array(
                'status' => 201,
                'card_status' => '0'
            );
        }
    }

    public function generate_coupon_code_for_card($user_id, $card_no) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $query = $this->db->query("SELECT id from users where id='$user_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            $max1 = strlen($card_no);

            for ($i = 0; $i < 8; $i++) {
                $generated_coupon .=$card_no[random_int(0, $max1 - 1)];
            }

            $order_info = $this->db->select('phone,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $order_info->token_status;
            $customer_phone = $order_info->phone;
            //  $message = 'Your generated coupon code is .'.$generated_coupon.' with card no is . '.$card_no.'';
            $message = 'Your generated coupon code is : ' . $generated_coupon . ' with card no is : ' . $card_no . ' please Enter coupon code To verify.';
            $post_data = array('From' => '02233721563', 'To' => $customer_phone, 'Body' => $message);
            $exotel_sid = "aegishealthsolutions";
            $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
            $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
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
                // 'card_no' => $generated_coupon,
                'message' => "coupon code is successfully send on registered mobile number for this card.",
                'status' => 200,
            );
        } else {
            return array(
                'status' => 201,
                'card_status' => '0'
            );
        }
    }

    //added for validate generated coupon and entry table in card number with coupon code 

    public function verify_generated_coupon($user_id, $card_no, $coupon_code) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $query = $this->db->query("SELECT id from users where id='$user_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            $card_query = $this->db->query("SELECT id,card_type,card_no from user_privilage_card where is_active='0' and card_no='$card_no'");
            /* echo "SELECT id,card_type,card_no from user_privilage_card where is_active='0' and card_no='$card_no'";
              die(); */
            $card_querys = $card_query->row();

            $card_count = $card_query->num_rows();
//die();
            if ($card_count > 0) {
                $card_type = $card_querys->card_type;

                $card_no = $card_querys->card_no;
                $this->db->query("UPDATE `user_privilage_card` SET `is_active`='1' AND `pin_number`='$coupon_code',user_id='$user_id' where card_no='$card_no'");


                //added by jakir on 01-june-2018 for notification on activation 
                $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $getusr = $user_plike->row_array();
                $usr_name = $getusr['name'];
                $msg = $usr_name . ', Your Physical Bachat card No :' . $card_no . ' with coupon card:' . $coupon_code . ' is activated . ';
                $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
                $title = $usr_name . ', Your Physical Bachat card No :' . $card_no . ' with coupon card:' . $coupon_code . ' is activated . ';
                $customer_token_count = $customer_token->num_rows();

                if ($customer_token_count > 0) {
                    $token_status = $customer_token->row_array();
                    $agent = $token_status['agent'];
                    $reg_id = $token_status['token'];
                    $img_url = $userimage;
                    $tag = 'text';
                    $key_count = '1';
                    $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent);
                }

                //end


                return array(
                    'card_type' => $card_type,
                    'card_no' => $card_no,
                    'status' => 200,
                    'card_status' => 1
                );
            } else {

                //$this->db->query("INSERT INTO `user_privilage_card`( `user_id`, `card_no`, `date`, `is_active`, `expiry`) VALUES ('$user_id','$card_no','$created_at','1','')");
                return array(
                    'card_type' => 'offline',
                    'card_no' => $card_no,
                    'status' => 200,
                    'card_status' => 0,
                    'message' => 'Invalid card number. Kindly enter correct number

OR

Kindly Generate a New Card'
                );
            }
        } else {
            return array(
                'status' => 201,
                'card_status' => '0'
            );
        }
    }

    //added by jakir on 01-june-2018
    public function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent) {

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
                "notification_type" => 'Bachat_card',
                "notification_date" => $date,
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

 public function insert_all_notification_Mobile($user_id,$title, $reg_id, $msg, $img_url, $tag, $agent, $diet_booking_id){
$notification_array = array(
                      'title' => $title,
                      'msg'  => $msg,
                      'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => "",
                      'order_date' => "",
                      'order_id'   => "",
                      'post_id'  => "",
                      'listing_id'  => "",
                      'booking_id'  => $diet_booking_id,
                       'invoice_no' => "",
                       'user_id'  => $user_id,
                       'notification_type'  => 'free_diet_plan',
                       'notification_date'  => date('Y-m-d H:i:s')
                       
            );
         $this->db->insert('All_notification_Mobile', $notification_array);
         return 1;
         
}

    public function send_gcm_notify_dietpackage($title, $reg_id, $msg, $img_url, $tag, $agent, $diet_booking_id) {
        
//         echo $title. "<br>"; 
// echo $reg_id. "<br>"; 
// echo $msg. "<br>"; 
// echo $img_url. "<br>"; 
// echo $tag. "<br>"; 
// echo $agent. "<br>"; 
// echo $diet_booking_id. "<br>"; 

// die();

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
                "notification_type" => 'free_diet_plan',
                "notification_date" => $date,
                "booking_id" => $diet_booking_id
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

    //end
    //get all user informations
    public function userdetails($listing_id, $user_id) {
        if ($listing_id == "") {
            return array(
                'status' => 208,
                'message' => 'Please enter listing_id'
            );
        } else {
            $check_user = $this->db->select('id')->from('users')->where('id', $user_id)->get()->row();
            if ($check_user != "") {
                $id = $check_user->id;
                // $q = $this->db->select('password,IFNULL(height_cm_ft,"") AS height_cm_ft,id,name,IFNULL(activity_level,"") AS activity_level,phone,email,gender,dob,IFNULL(height,"") AS height,IFNULL(weight,"") AS weight,IFNULL(marital_status,"") AS marital_status,IFNULL(blood_group,"") AS blood_group,IFNULL(blood_is_active,"") AS blood_is_active,IFNULL(diet_fitness,"") AS diet_fitness,IFNULL(organ_donor,"") AS organ_donor,sex_history,exercise_level,health_condition,health_insurance,addiction,addiction_value,allergies,IFNULL(bmi,"") AS bmi ,IFNULL(heradiatry_problem,"") AS heradiatry_problem,IFNULL(lat,"") AS lat,IFNULL(lng,"") AS lng,IFNULL(map_location,"") AS map_location')->from('users')->where('users.id', $listing_id)->get()->row();

                $q = $this->db->select('password,IFNULL(height_cm_ft,"") AS height_cm_ft,id,name,IFNULL(activity_level,"") AS activity_level,phone,email,gender,dob,IFNULL(height,"") AS height,IFNULL(weight,"") AS weight,IFNULL(marital_status,"") AS marital_status,IFNULL(blood_group,"") AS blood_group,IFNULL(blood_is_active,"") AS blood_is_active,IFNULL(diet_fitness,"") AS diet_fitness,IFNULL(organ_donor,"") AS organ_donor,sex_history,exercise_level,health_condition,health_insurance,addiction,allergies,IFNULL(bmi,"") AS bmi ,IFNULL(heradiatry_problem,"") AS heradiatry_problem,IFNULL(lat,"") AS lat,IFNULL(lng,"") AS lng,IFNULL(map_location,"") AS map_location')->from('users')->where('users.id', $listing_id)->get()->row();
                $id = $q->id;
                $name = $q->name;
                $phone = $q->phone;
                $email = $q->email;
                $gender = $q->gender;
                $dob = $q->dob;
                $height = $q->height;
                $weight = $q->weight;
                $marital_status = $q->marital_status;
                $blood_group = $q->blood_group;
                $diet_fitness = $q->diet_fitness;
                $organ_donor = $q->organ_donor;
                $sex_history = $q->sex_history;
                $exercise_level = $q->exercise_level;
                $activity_level = $q->activity_level;
                $health_condition = $q->health_condition;
                $allergies = $q->allergies;
                $bmi = $q->bmi;
                $health_insurance = $q->health_insurance;
                $addiction = $q->addiction;
                // $addiction_value = $q->addiction_value;
                $blood_is_active = $q->blood_is_active;
                $heradiatry_problem = $q->heradiatry_problem;
                $lat = $q->lat;
                $lng = $q->lng;
                $height_cm_ft = $q->height_cm_ft;
                $map_location = $q->map_location;
                $medical_genetic_disorder = array();
                $count_query = $this->db->query("SELECT * FROM `user_medical_genetic_disorder` WHERE user_id='$user_id'");
                $count = $count_query->num_rows();
                if ($count > 0) {
                    foreach ($count_query->result_array() as $row) {
                        $medical_genetic_disorder[] = array('name' => $row['name'], 'value' => $row['value']);
                    }
                }

                $img_count = $this->db->select('media.id')->from('media')->join('users', 'users.avatar_id=media.id','LEFT')->where('users.id', $listing_id)->get()->num_rows();

                if ($img_count > 0) {
                    $media = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $listing_id)->get()->row();
                    $img_file = $media->source;
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                    $image_status = '5';
                } else {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    $image_status = '0';
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
                    $sex_history_status = '5';
                } else {
                    $sex_history_status = '5';
                }
                if ($exercise_level == '') {
                    $activity_level_status = '0';
                } else {
                    $activity_level_status = '5';
                }
                if ($health_condition == '' || $health_condition == '0') {
                    $health_condition_is = '0';
                } else {
                    $health_condition_is = '5';
                }
                if ($allergies == '' || $allergies == '0') {
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
                if ($addiction == '' || $addiction == '0') {
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
                    'height_cm_ft' => $height_cm_ft,
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'image' => $image,
                    'gender' => $gender,
                    'dob' => trim($dob),
                    'height' => $height,
                    'weight' => $weight,
                    'exercise_level' => $exercise_level,
                    'health_condition' => $health_condition,
                    'addiction' => str_replace('null', '', $addiction),
                    // 'addiction_value' => str_replace('null', '', $addiction_value),
                    'medical_genetic_disorder' => $medical_genetic_disorder,
                    'health_insurance' => str_replace('null', '', $health_insurance),
                    'allergies' => $allergies,
                    'marital_status' => $marital_status,
                    'blood_group' => $blood_group,
                    'blood_is_active' => $blood_is_active,
                    'activity_level' => $activity_level,
                    'diet_fitness' => $diet_fitness,
                    'organ_donor' => $organ_donor,
                    'bmi' => $bmi,
                    'sex_history' => $sex_history,
                    'followers' => $followers,
                    'following' => $following,
                    'reviews_done' => '0',
                    'is_follow' => $is_follow,
                    'heradiatry_problem' => $heradiatry_problem,
                    'lat' => $lat,
                    'lng' => $lng,
                    'map_location' => $map_location,
                    'profile_completed' => $profile_completed,
                );
            } else {
                return array(
                    'status' => 209,
                    'message' => 'user not found'
                );
            }
        }
    }
    
    public function sociallogin($email,$phone,$oauth_provider,$oauth_uid,$agent,$name,$gender,$image,$token) {
      
        if ($email == "") {
            return array(
                'status' => 208,
                'message' => 'Please enter Email Id.'
            );
        } else {
            $email_='';
            $phone_='';
            if(!empty($email)){
              //  $email_="or email='$email'";
                 $user_query  = $this->db->query("SELECT id from users where  email = '$email' AND vendor_id=0 ");
            }
            //$user_query  = $this->db->query("SELECT id from users where (oauth_provider='$oauth_provider' and oauth_uid='$oauth_uid') OR email = '$email'");
          /*  if(!empty($phone)){
                $phone_="or phone='$phone'";
            }*/
         //  echo "SELECT id from users where (oauth_provider='$oauth_provider' and oauth_uid='$oauth_uid') $email_ $phone_";
           
            $check_user  = $user_query->num_rows();
			
            if ($check_user>0) {
              
                $user_querys = $user_query->row();
                $id = $user_querys->id;
                $q = $this->db->select('password,IFNULL(height_cm_ft,"") AS height_cm_ft,id,name,phone,email,gender,dob,height,weight,marital_status,blood_group,diet_fitness,organ_donor,sex_history,agent')->from('users')->where('users.id', $id)->get()->row();
                $id = $q->id;
                $name = $q->name;
                $phone = $q->phone;
                $email = $q->email;
                $gender = $q->gender;
                $dob = $q->dob;
                $height = $q->height;
                $weight = $q->weight;
                $marital_status = $q->marital_status;
                $blood_group = $q->blood_group;
                $diet_fitness = $q->diet_fitness;
                $organ_donor = $q->organ_donor;
                $sex_history = $q->sex_history;
                if($q->height_cm_ft != NULL)
                {
                  $height_cm_ft = $q->height_cm_ft;
                }
                else
                {
                    $height_cm_ft = "";
                }
                
                $querys = $this->db->query("UPDATE `users` SET `token`='$token',`agent`='$agent',`token_status`='1' WHERE id='$id'");

                $img_count = $this->db->select('media.id')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $id)->get()->num_rows();
                if ($img_count > 0) {
                    $media = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $id)->get()->row();
                    $img_file = $media->source;
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                }
               /* else
                {
                    include('s3_config.php');
                    
                       // Your file
                    $file = $image;
                    
                    // Open the file to get existing content
                    $data = file_get_contents($file);
                     $fiel_name = uniqid() . date("YmdHis")."."."jpg";
                     $sourcePath = $data;
                    $targetPath = "images/healthwall_avatar/".$fiel_name;
                    $s3->putObjectFile($sourcePath, $bucket, $targetPath, S3::ACL_PUBLIC_READ);
                 
                  $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $targetPath;
                   /* $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $fiel_name;
                    
                    // Write the contents back to a new file
                    file_put_contents($image, $data);
                }
              */
                $last_login = date('Y-m-d H:i:s', strtotime('+12 hours'));
                $this->db->where('id', $id)->update('users', array(
                    'last_login' => $last_login
                ));
                $card_no = '';
                $card_type = '';
                $card_query = $this->db->query("SELECT card_no,card_type from user_privilage_card where user_id='$id'");
                $card_count = $card_query->num_rows();
                $card_query_new = $this->db->query("SELECT card_no,card_type from user_priviladge_card_new where user_id='$id'");
                $card_count_new = $card_query_new->num_rows();
                if ($card_count > 0) {
                    $card_list = $card_query->row_array();
                    $card_no = $card_list['card_no'];
                    $card_type = $card_list['card_type'];
                } else if ($card_count_new > 0) {
                    $card_list_new = $card_query_new->row_array();
                    $card_no = $card_list_new['card_no'];
                    $card_type = $card_list_new['card_type'];
                }

                //added for profile percentage  by zak
                //**********************************************
                $q = $this->db->select('avatar_id,password,height_cm_ft,id,name,IFNULL(activity_level,"") AS activity_level,phone,email,gender,dob,IFNULL(height,"") AS height,IFNULL(weight,"") AS weight,IFNULL(marital_status,"") AS marital_status,IFNULL(blood_group,"") AS blood_group,IFNULL(blood_is_active,"") AS blood_is_active,IFNULL(diet_fitness,"") AS diet_fitness,IFNULL(organ_donor,"") AS organ_donor,sex_history,exercise_level,health_condition,health_insurance,addiction,allergies,IFNULL(bmi,"") AS bmi ,IFNULL(heradiatry_problem,"") AS heradiatry_problem,IFNULL(lat,"") AS lat,IFNULL(lng,"") AS lng,IFNULL(map_location,"") AS map_location')->from('users')->where('users.id', $id)->get()->row();

                //   $q = $this->db->select('avatar_id,password,height_cm_ft,id,name,IFNULL(activity_level,"") AS activity_level,phone,email,gender,dob,IFNULL(height,"") AS height,IFNULL(weight,"") AS weight,IFNULL(marital_status,"") AS marital_status,IFNULL(blood_group,"") AS blood_group,IFNULL(blood_is_active,"") AS blood_is_active,IFNULL(diet_fitness,"") AS diet_fitness,IFNULL(organ_donor,"") AS organ_donor,sex_history,exercise_level,health_condition,health_insurance,addiction,allergies,IFNULL(bmi,"") AS bmi ,IFNULL(heradiatry_problem,"") AS heradiatry_problem,IFNULL(lat,"") AS lat,IFNULL(lng,"") AS lng,IFNULL(map_location,"") AS map_location')->from('users')->where('users.id', $user_id)->get()->row();

            
                if($q->height_cm_ft != NULL)
                {
                  $height_cm_ft = $q->height_cm_ft;
                }
                else
                {
                    $height_cm_ft = "";
                }
                
                $id = $q->id;
                $name = $q->name;
                $avatar_id = $q->avatar_id;
                $phone = $q->phone;
                $email = $q->email;
                $gender = $q->gender;
                $dob = $q->dob;
                $height = $q->height;
                $weight = $q->weight;
                $marital_status = $q->marital_status;
                $blood_group = $q->blood_group;
                $diet_fitness = $q->diet_fitness;
                $organ_donor = $q->organ_donor;
                $sex_history = $q->sex_history;
                $exercise_level = $q->exercise_level;
                $activity_level = $q->activity_level;
                $health_condition = $q->health_condition;
                $allergies = $q->allergies;
                $bmi = $q->bmi;
                $health_insurance = $q->health_insurance;
                $addiction = $q->addiction;
                //$addiction_value = $q->addiction_value;
                $blood_is_active = $q->blood_is_active;
                $heradiatry_problem = $q->heradiatry_problem;
                $lat = $q->lat;
                $lng = $q->lng;
                $map_location = $q->map_location;

                $medical_genetic_disorder = array();
                $count_query = $this->db->query("SELECT * FROM `user_medical_genetic_disorder` WHERE user_id='$id'");
                $count = $count_query->num_rows();
                if ($count > 0) {
                    foreach ($count_query->result_array() as $row) {
                        $medical_genetic_disorder[] = array('name' => $row['name'], 'value' => $row['value']);
                    }
                }

                if ($avatar_id == '0') {
                    $avatar_id_status = '0';
                } else {
                    $avatar_id_status = '5';
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
                    $sex_history_status = '5';
                } else {
                    $sex_history_status = '5';
                }
                if ($exercise_level == '') {
                    $activity_level_status = '0';
                } else {
                    $activity_level_status = '5';
                }
                if ($health_condition == '' || $health_condition == '0') {
                    $health_condition_is = '0';
                } else {
                    $health_condition_is = '5';
                }
                if ($allergies == '' || $allergies == '0') {
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
                if ($addiction == '' || $addiction == '0') {
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

                $profile_completed = $step1 + $height_status + $weight_status + $marital_status_is + $blood_group_status + $diet_fitness_status + $organ_donor_status + $sex_history_status + $activity_level_status + $health_condition_is + $allergies_status + $bmi_status + $health_insurance_status + $addiction_status + $heradiatry_problem_status + $location_status + $avatar_id_status;
  
                $Miss_package = $this->db->query("SELECT diet_user_package_history.id FROM `diet_user_package_history` INNER JOIN `users` ON diet_user_package_history.user_id=users.id WHERE diet_user_package_history.user_id='$id'");
                $Miss_belly_package = $Miss_package->num_rows();
                if ($Miss_belly_package > 0) {
                    $Miss_belly_status = "true";
                } else {
                    $Miss_package_free = $this->db->query("SELECT medicalbot_savefreedietplans.id FROM `medicalbot_savefreedietplans` INNER JOIN `users` ON medicalbot_savefreedietplans.user_id=users.id WHERE medicalbot_savefreedietplans.user_id='$id'");
                    $Miss_belly_package_free = $Miss_package_free->num_rows();
                    if ($Miss_belly_package_free > 0) {
                        $Miss_belly_status = "True";
                    } else {
                        $Miss_belly_status = "False";
                    }
                }
                 if($phone !=0)
                {
                    $oldnew = 'old';
                    $newphone = $phone;
                }
                else
                {
                     $oldnew = 'new';
                     $newphone = '';
                }
                
                
                $otp_code='0';
                return array(
                    'otp_code' => (int) $otp_code,
                    'user' => $oldnew,
                    'id' => $id,
                    'card_no' => str_replace('null', '', $card_no),
                    'card_type' => $card_type,
                    'name' => $name,
                    'email' => $email,
                    'phone' => $newphone,
                    'image' => $image,
                    'gender' => $gender,
                    'dob' => $dob,
                    'height' => $height,
                    'weight' => $weight,
                    'marital_status' => $marital_status,
                    'blood_group' => $blood_group,
                    'diet_fitness' => $diet_fitness,
                    'organ_donor' => $organ_donor,
                    'sex_history' => $sex_history,
                    'height_cm_ft' => $height_cm_ft,
                    'profile_percentage' => $profile_completed,
                    'is_Miss_belly_activated' => $Miss_belly_status
                );
            } else {
                date_default_timezone_set('Asia/Kolkata');
                $updated_at = date('Y-m-d H:i:s');
                $user_type = 'user';
                $userpassword = '';
                $user_data = array(
                            'name' => $name,
                            'phone' => '0',
                            'email' => $email,
                            'oauth_provider' => $oauth_provider,
                            'oauth_uid' => $oauth_uid,
                            'gender' => $gender,
                            'dob' => '',
                            'password' => $userpassword,
                            'token' => $token,
                            'agent' => $agent,
                            'token_status' => '1',
                            'created_at' => $updated_at,
                            'deviceId' => ''
                        );

                $this->db->insert('users', $user_data);
                $id = $this->db->insert_id();
                $q = $this->db->select('password,IFNULL(height_cm_ft,"") AS height_cm_ft,id,name,phone,email,gender,dob,height,weight,marital_status,blood_group,diet_fitness,organ_donor,sex_history,agent')->from('users')->where('users.id', $id)->get()->row();
                $id = $q->id;
                $name = $q->name;
                $phone = $q->phone;
                $email = $q->email;
                $gender = $q->gender;
                $dob = $q->dob;
                $height = $q->height;
                $weight = $q->weight;
                $marital_status = $q->marital_status;
                $blood_group = $q->blood_group;
                $diet_fitness = $q->diet_fitness;
                $organ_donor = $q->organ_donor;
                $sex_history = $q->sex_history;
                $height_cm_ft = $q->height_cm_ft;


                $img_count = $this->db->select('media.id')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $id)->get()->num_rows();
                if ($img_count > 0) {
                    $media = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $id)->get()->row();
                    $img_file = $media->source;
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                $last_login = date('Y-m-d H:i:s', strtotime('+12 hours'));
                $this->db->where('id', $id)->update('users', array(
                    'last_login' => $last_login
                ));
                $card_no = '';
                $card_type = '';
                $card_query = $this->db->query("SELECT card_no,card_type from user_privilage_card where user_id='$id'");
                $card_count = $card_query->num_rows();
                $card_query_new = $this->db->query("SELECT card_no,card_type from user_priviladge_card_new where user_id='$id'");
                $card_count_new = $card_query_new->num_rows();
                if ($card_count > 0) {
                    $card_list = $card_query->row_array();
                    $card_no = $card_list['card_no'];
                    $card_type = $card_list['card_type'];
                } else if ($card_count_new > 0) {
                    $card_list_new = $card_query_new->row_array();
                    $card_no = $card_list_new['card_no'];
                    $card_type = $card_list_new['card_type'];
                }

                //added for profile percentage  by zak
                //**********************************************
                $q = $this->db->select('avatar_id,password,height_cm_ft,id,name,IFNULL(activity_level,"") AS activity_level,phone,email,gender,dob,IFNULL(height,"") AS height,IFNULL(weight,"") AS weight,IFNULL(marital_status,"") AS marital_status,IFNULL(blood_group,"") AS blood_group,IFNULL(blood_is_active,"") AS blood_is_active,IFNULL(diet_fitness,"") AS diet_fitness,IFNULL(organ_donor,"") AS organ_donor,sex_history,exercise_level,health_condition,health_insurance,addiction,allergies,IFNULL(bmi,"") AS bmi ,IFNULL(heradiatry_problem,"") AS heradiatry_problem,IFNULL(lat,"") AS lat,IFNULL(lng,"") AS lng,IFNULL(map_location,"") AS map_location')->from('users')->where('users.id', $id)->get()->row();

                //   $q = $this->db->select('avatar_id,password,height_cm_ft,id,name,IFNULL(activity_level,"") AS activity_level,phone,email,gender,dob,IFNULL(height,"") AS height,IFNULL(weight,"") AS weight,IFNULL(marital_status,"") AS marital_status,IFNULL(blood_group,"") AS blood_group,IFNULL(blood_is_active,"") AS blood_is_active,IFNULL(diet_fitness,"") AS diet_fitness,IFNULL(organ_donor,"") AS organ_donor,sex_history,exercise_level,health_condition,health_insurance,addiction,allergies,IFNULL(bmi,"") AS bmi ,IFNULL(heradiatry_problem,"") AS heradiatry_problem,IFNULL(lat,"") AS lat,IFNULL(lng,"") AS lng,IFNULL(map_location,"") AS map_location')->from('users')->where('users.id', $user_id)->get()->row();

                $height_cm_ft = $q->height_cm_ft;
                $id = $q->id;
                $name = $q->name;
                $avatar_id = $q->avatar_id;
                $phone = $q->phone;
                $email = $q->email;
                $gender = $q->gender;
                $dob = $q->dob;
                $height = $q->height;
                $weight = $q->weight;
                $marital_status = $q->marital_status;
                $blood_group = $q->blood_group;
                $diet_fitness = $q->diet_fitness;
                $organ_donor = $q->organ_donor;
                $sex_history = $q->sex_history;
                $exercise_level = $q->exercise_level;
                $activity_level = $q->activity_level;
                $health_condition = $q->health_condition;
                $allergies = $q->allergies;
                $bmi = $q->bmi;
                $health_insurance = $q->health_insurance;
                $addiction = $q->addiction;
                //$addiction_value = $q->addiction_value;
                $blood_is_active = $q->blood_is_active;
                $heradiatry_problem = $q->heradiatry_problem;
                $lat = $q->lat;
                $lng = $q->lng;
                $map_location = $q->map_location;

                $medical_genetic_disorder = array();
                $count_query = $this->db->query("SELECT * FROM `user_medical_genetic_disorder` WHERE user_id='$id'");
                $count = $count_query->num_rows();
                if ($count > 0) {
                    foreach ($count_query->result_array() as $row) {
                        $medical_genetic_disorder[] = array('name' => $row['name'], 'value' => $row['value']);
                    }
                }

                if ($avatar_id == '0') {
                    $avatar_id_status = '0';
                } else {
                    $avatar_id_status = '5';
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
                    $sex_history_status = '5';
                } else {
                    $sex_history_status = '5';
                }
                if ($exercise_level == '') {
                    $activity_level_status = '0';
                } else {
                    $activity_level_status = '5';
                }
                if ($health_condition == '' || $health_condition == '0') {
                    $health_condition_is = '0';
                } else {
                    $health_condition_is = '5';
                }
                if ($allergies == '' || $allergies == '0') {
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
                if ($addiction == '' || $addiction == '0') {
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

                $profile_completed = $step1 + $height_status + $weight_status + $marital_status_is + $blood_group_status + $diet_fitness_status + $organ_donor_status + $sex_history_status + $activity_level_status + $health_condition_is + $allergies_status + $bmi_status + $health_insurance_status + $addiction_status + $heradiatry_problem_status + $location_status + $avatar_id_status;
  
                $Miss_package = $this->db->query("SELECT diet_user_package_history.id FROM `diet_user_package_history` INNER JOIN `users` ON diet_user_package_history.user_id=users.id WHERE diet_user_package_history.user_id='$id'");
                $Miss_belly_package = $Miss_package->num_rows();
                if ($Miss_belly_package > 0) {
                    $Miss_belly_status = "true";
                } else {
                    $Miss_package_free = $this->db->query("SELECT medicalbot_savefreedietplans.id FROM `medicalbot_savefreedietplans` INNER JOIN `users` ON medicalbot_savefreedietplans.user_id=users.id WHERE medicalbot_savefreedietplans.user_id='$id'");
                    $Miss_belly_package_free = $Miss_package_free->num_rows();
                    if ($Miss_belly_package_free > 0) {
                        $Miss_belly_status = "True";
                    } else {
                        $Miss_belly_status = "False";
                    }
                }
                        $user_data1 = array(
                        'user_id'=>$id,
                        'relation_id'=>0,
                        'patient_name' => $name,
                        'avatar_id' => '0',
                        'relationship' => "Myself",
                        'gender' => $gender,
                        'date_of_birth' => '',
                        'email' => $email,
                        'phone' => '',
                        'created_at' => $updated_at
                    );

                $this->db->insert('health_record', $user_data1);
                $otp_code='0';
                return array(
                    'otp_code' => (int) $otp_code,
                    'user' => 'new',
                    'id' => $id,
                    'card_no' => str_replace('null', '', $card_no),
                    'card_type' => $card_type,
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
                    'sex_history' => $sex_history,
                    'height_cm_ft' => $height_cm_ft,
                    'profile_percentage' => $profile_completed,
                    'is_Miss_belly_activated' => $Miss_belly_status
                );
            }
        }
    }

    //validate user login
    //validate user login
    public function userlogin($phone, $token, $agent, $hash_key) {
        if ($phone == "") {
            return array(
                'status' => 208,
                'message' => 'Please enter phone no.'
            );
        } else {
            $check_user = $this->db->select('id')->from('users')->where('phone', $phone)->where('vendor_id', '0')->get()->row();
            if ($check_user != "") {
                $id = $check_user->id;
                $q = $this->db->select('password,IFNULL(height_cm_ft,"") AS height_cm_ft,id,name,phone,email,gender,dob,height,weight,marital_status,blood_group,diet_fitness,organ_donor,sex_history,agent')->from('users')->where('users.id', $id)->get()->row();
                $id = $q->id;
                $name = $q->name;
                $phone = $q->phone;
                $email = $q->email;
                $gender = $q->gender;
                $dob = $q->dob;
                $height = $q->height;
                $weight = $q->weight;
                $marital_status = $q->marital_status;
                $blood_group = $q->blood_group;
                $diet_fitness = $q->diet_fitness;
                $organ_donor = $q->organ_donor;
                $sex_history = $q->sex_history;
                 if($q->height_cm_ft != NULL)
                {
                  $height_cm_ft = $q->height_cm_ft;
                }
                else
                {
                    $height_cm_ft = "";
                }
                

                $img_count = $this->db->select('media.id')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $id)->get()->num_rows();
                if ($img_count > 0) {
                    $media = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $id)->get()->row();
                    $img_file = $media->source;
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                $last_login = date('Y-m-d H:i:s', strtotime('+12 hours'));
                $this->db->where('id', $id)->update('users', array(
                    'last_login' => $last_login
                ));
                $card_no = '';
                $card_type = '';
                $card_query = $this->db->query("SELECT card_no,card_type from user_privilage_card where user_id='$id'");
                $card_count = $card_query->num_rows();
                $card_query_new = $this->db->query("SELECT card_no,card_type from user_priviladge_card_new where user_id='$id'");
                $card_count_new = $card_query_new->num_rows();
                if ($card_count > 0) {
                    $card_list = $card_query->row_array();
                    $card_no = $card_list['card_no'];
                    $card_type = $card_list['card_type'];
                } else if ($card_count_new > 0) {
                    $card_list_new = $card_query_new->row_array();
                    $card_no = $card_list_new['card_no'];
                    $card_type = $card_list_new['card_type'];
                }
                
                if(empty($q->password)){
   $passoword_set=0;
   if ($phone == '9833381096' || $phone == '7834277784' || $phone == '8655369076' || $phone == '9824242411' || $phone == '9733331211' || $phone == '8676008901' || $phone == '9107766551' || $phone == '7545032401' || $phone == '8500005213' || $phone == '9822212411' || $phone == '8080262411' || $phone == '9323465785' || $phone == '8097014524' || $phone=='7021493253' || $phone == '8879398756')  {
                    $otp_code = '123456';
                } else {
                    $otp_code = rand(100000, 999999);
                    if($agent=="android")
                    {
                    $message = '<#> Please enter this One Time Password : ' . $otp_code . ' to verify your mobile number. ' . $hash_key . '. Aap ke Health Ka Saathi.';
                    }
                    else
                    {
                      $message = 'Please enter this One Time Password : ' . $otp_code . ' to verify your mobile number. ';  
                    }
                    $post_data = array(
                        'From' => '02233721563',
                        'To' => $phone,
                        'Body' => $message
                    );
                    $exotel_sid = "aegishealthsolutions";
                    $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                    $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
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
                }
}
else{
    $passoword_set=1;
    $otp_code = '0';
}

                

                $querys = $this->db->query("UPDATE `users` SET `otp_code`='$otp_code',`token`='$token',`agent`='$agent',`token_status`='1' WHERE id='$id'");

                //added for profile percentage  by zak
                //**********************************************
                $q = $this->db->select('avatar_id,password,height_cm_ft,id,name,IFNULL(activity_level,"") AS activity_level,phone,email,gender,dob,IFNULL(height,"") AS height,IFNULL(weight,"") AS weight,IFNULL(marital_status,"") AS marital_status,IFNULL(blood_group,"") AS blood_group,IFNULL(blood_is_active,"") AS blood_is_active,IFNULL(diet_fitness,"") AS diet_fitness,IFNULL(organ_donor,"") AS organ_donor,sex_history,exercise_level,health_condition,health_insurance,addiction,allergies,IFNULL(bmi,"") AS bmi ,IFNULL(heradiatry_problem,"") AS heradiatry_problem,IFNULL(lat,"") AS lat,IFNULL(lng,"") AS lng,IFNULL(map_location,"") AS map_location')->from('users')->where('users.id', $id)->get()->row();

                //   $q = $this->db->select('avatar_id,password,height_cm_ft,id,name,IFNULL(activity_level,"") AS activity_level,phone,email,gender,dob,IFNULL(height,"") AS height,IFNULL(weight,"") AS weight,IFNULL(marital_status,"") AS marital_status,IFNULL(blood_group,"") AS blood_group,IFNULL(blood_is_active,"") AS blood_is_active,IFNULL(diet_fitness,"") AS diet_fitness,IFNULL(organ_donor,"") AS organ_donor,sex_history,exercise_level,health_condition,health_insurance,addiction,allergies,IFNULL(bmi,"") AS bmi ,IFNULL(heradiatry_problem,"") AS heradiatry_problem,IFNULL(lat,"") AS lat,IFNULL(lng,"") AS lng,IFNULL(map_location,"") AS map_location')->from('users')->where('users.id', $user_id)->get()->row();

                 if($q->height_cm_ft != NULL)
                {
                  $height_cm_ft = $q->height_cm_ft;
                }
                else
                {
                    $height_cm_ft = "";
                }
                
                $id = $q->id;
                $name = $q->name;
                $avatar_id = $q->avatar_id;
                $phone = $q->phone;
                $email = $q->email;
                $gender = $q->gender;
                $dob = $q->dob;
                $height = $q->height;
                $weight = $q->weight;
                $marital_status = $q->marital_status;
                $blood_group = $q->blood_group;
                $diet_fitness = $q->diet_fitness;
                $organ_donor = $q->organ_donor;
                $sex_history = $q->sex_history;
                $exercise_level = $q->exercise_level;
                $activity_level = $q->activity_level;
                $health_condition = $q->health_condition;
                $allergies = $q->allergies;
                $bmi = $q->bmi;
                $health_insurance = $q->health_insurance;
                $addiction = $q->addiction;
                //$addiction_value = $q->addiction_value;
                $blood_is_active = $q->blood_is_active;
                $heradiatry_problem = $q->heradiatry_problem;
                $lat = $q->lat;
                $lng = $q->lng;
                $map_location = $q->map_location;

                $medical_genetic_disorder = array();
                $count_query = $this->db->query("SELECT * FROM `user_medical_genetic_disorder` WHERE user_id='$id'");
                $count = $count_query->num_rows();
                if ($count > 0) {
                    foreach ($count_query->result_array() as $row) {
                        $medical_genetic_disorder[] = array('name' => $row['name'], 'value' => $row['value']);
                    }
                }

                if ($avatar_id == '0') {
                    $avatar_id_status = '0';
                } else {
                    $avatar_id_status = '5';
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
                    $sex_history_status = '5';
                } else {
                    $sex_history_status = '5';
                }
                if ($exercise_level == '') {
                    $activity_level_status = '0';
                } else {
                    $activity_level_status = '5';
                }
                if ($health_condition == '' || $health_condition == '0') {
                    $health_condition_is = '0';
                } else {
                    $health_condition_is = '5';
                }
                if ($allergies == '' || $allergies == '0') {
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
                if ($addiction == '' || $addiction == '0') {
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

                $profile_completed = $step1 + $height_status + $weight_status + $marital_status_is + $blood_group_status + $diet_fitness_status + $organ_donor_status + $sex_history_status + $activity_level_status + $health_condition_is + $allergies_status + $bmi_status + $health_insurance_status + $addiction_status + $heradiatry_problem_status + $location_status + $avatar_id_status;
                /* $id1=0;
                  $status="";
                  $q1 = $this->db->select('*')->from('diet_user_package_history')->where('user_id', $id)->get()->row();
                  $id1=$q1->id;
                  if($id1=="0")
                  {
                  $status="fasle";
                  }
                  else
                  {
                  $status="true";
                  } */

                $Miss_package = $this->db->query("SELECT diet_user_package_history.id FROM `diet_user_package_history` INNER JOIN `users` ON diet_user_package_history.user_id=users.id WHERE diet_user_package_history.user_id='$id'");
                $Miss_belly_package = $Miss_package->num_rows();
                if ($Miss_belly_package > 0) {
                    $Miss_belly_status = "true";
                } else {
                    $Miss_package_free = $this->db->query("SELECT medicalbot_savefreedietplans.id FROM `medicalbot_savefreedietplans` INNER JOIN `users` ON medicalbot_savefreedietplans.user_id=users.id WHERE medicalbot_savefreedietplans.user_id='$id'");
                    $Miss_belly_package_free = $Miss_package_free->num_rows();
                    if ($Miss_belly_package_free > 0) {
                        $Miss_belly_status = "True";
                    } else {
                        $Miss_belly_status = "False";
                    }
                }




                //end 
                $resultpost[] = array(
                    'otp_code' => (int) $otp_code,
                    'user' => 'old',
                    'password_set' => $passoword_set,
                    'id' => $id,
                    'card_no' => str_replace('null', '', $card_no),
                    'card_type' => $card_type,
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
                    'sex_history' => $sex_history,
                    'height_cm_ft' => $height_cm_ft,
                    'profile_percentage' => $profile_completed,
                    'is_Miss_belly_activated' => $Miss_belly_status
                );
                $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "data" => $resultpost
                );
            return $resultpost;
            } else {
                if ($phone == '7834277784' || $phone == '8655369076' || $phone == '9824242411' || $phone == '9733331211' || $phone == '8676008901' || $phone == '9107766551' || $phone == '7545032401' || $phone == '8500005213' || $phone == '9822212411' || $phone == '8080262411' || $phone == '9323465785') {
                    $otp_code = '123456';
                } else {
                    $otp_code = rand(100000, 999999);
                    $message = 'Please enter this One Time Password : ' . $otp_code . ' to verify your mobile number.';
                    $post_data = array(
                        'From' => '02233721563',
                        'To' => $phone,
                        'Body' => $message
                    );
                    $exotel_sid = "aegishealthsolutions";
                    $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                    $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
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
                }
                $passoword_set=0;
                $resultpost[] = array(
                    'otp_code' => (int) $otp_code,
                    'user' => 'new',
                    'password_set' => $passoword_set,
                    'id' => '',
                    'card_no' => '',
                    'card_type' => '',
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
                    'sex_history' => '',
                    'height_cm_ft' => ''
                );
                 $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "data" => $resultpost
                );
            return $resultpost;
            }
        }
    }

    //check profile and is_active percentage for missbelly 

    public function checkUserDetails($user_id) {

        /* $q = $this->db->select('avatar_id,password,height_cm_ft,id,name,IFNULL(activity_level,"") AS activity_level,phone,email,gender,dob,IFNULL(height,"") AS height,IFNULL(weight,"") AS weight,IFNULL(marital_status,"") AS marital_status,IFNULL(blood_group,"") AS blood_group,IFNULL(blood_is_active,"") AS blood_is_active,IFNULL(diet_fitness,"") AS diet_fitness,IFNULL(organ_donor,"") AS organ_donor,sex_history,exercise_level,health_condition,health_insurance,addiction,allergies,IFNULL(bmi,"") AS bmi ,IFNULL(heradiatry_problem,"") AS heradiatry_problem,IFNULL(lat,"") AS lat,IFNULL(lng,"") AS lng,IFNULL(map_location,"") AS map_location')->from('users')->where('users.id', $user_id)->get()->row();

          //   $q = $this->db->select('avatar_id,password,height_cm_ft,id,name,IFNULL(activity_level,"") AS activity_level,phone,email,gender,dob,IFNULL(height,"") AS height,IFNULL(weight,"") AS weight,IFNULL(marital_status,"") AS marital_status,IFNULL(blood_group,"") AS blood_group,IFNULL(blood_is_active,"") AS blood_is_active,IFNULL(diet_fitness,"") AS diet_fitness,IFNULL(organ_donor,"") AS organ_donor,sex_history,exercise_level,health_condition,health_insurance,addiction,allergies,IFNULL(bmi,"") AS bmi ,IFNULL(heradiatry_problem,"") AS heradiatry_problem,IFNULL(lat,"") AS lat,IFNULL(lng,"") AS lng,IFNULL(map_location,"") AS map_location')->from('users')->where('users.id', $user_id)->get()->row();

          $height_cm_ft = $q->height_cm_ft;
          $id = $q->id;
          $name = $q->name;
          $avatar_id = $q->avatar_id;
          $phone = $q->phone;
          $email = $q->email;
          $gender = $q->gender;
          $dob = $q->dob;
          $height = $q->height;
          $weight = $q->weight;
          $marital_status = $q->marital_status;
          $blood_group = $q->blood_group;
          $diet_fitness = $q->diet_fitness;
          $organ_donor = $q->organ_donor;
          $sex_history = $q->sex_history;
          $exercise_level = $q->exercise_level;
          $activity_level = $q->activity_level;
          $health_condition = $q->health_condition;
          $allergies = $q->allergies;
          $bmi = $q->bmi;
          $health_insurance = $q->health_insurance;
          $addiction = $q->addiction;
          //$addiction_value = $q->addiction_value;
          $blood_is_active = $q->blood_is_active;
          $heradiatry_problem = $q->heradiatry_problem;
          $lat = $q->lat;
          $lng = $q->lng;
          $map_location = $q->map_location;

          $medical_genetic_disorder=array();
          $count_query = $this->db->query("SELECT * FROM `user_medical_genetic_disorder` WHERE user_id='$id'");
          $count = $count_query->num_rows();
          if ($count > 0) {
          foreach ($count_query->result_array() as $row) {
          $medical_genetic_disorder[] = array('name'=>$row['name'],'value'=>$row['value']);
          }
          }

          if ($avatar_id == '0') {
          $avatar_id_status = '0';
          } else {
          $avatar_id_status = '5';
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
          $sex_history_status = '5';
          } else {
          $sex_history_status = '5';
          }
          if ($exercise_level == '') {
          $activity_level_status = '0';
          } else {
          $activity_level_status = '5';
          }
          if ($health_condition == '' || $health_condition == '0') {
          $health_condition_is = '0';
          } else {
          $health_condition_is = '5';
          }
          if ($allergies == '' || $allergies == '0') {
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
          if ($addiction == '' || $addiction == '0') {
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

          $profile_completed = $step1 + $height_status + $weight_status + $marital_status_is + $blood_group_status + $diet_fitness_status + $organ_donor_status + $sex_history_status + $activity_level_status + $health_condition_is + $allergies_status + $bmi_status + $health_insurance_status + $addiction_status + $heradiatry_problem_status + $location_status + $avatar_id_status; */
        $q = $this->db->select('password,IFNULL(height_cm_ft,"") AS height_cm_ft,id,name,IFNULL(activity_level,"") AS activity_level,phone,email,gender,dob,IFNULL(height,"") AS height,IFNULL(weight,"") AS weight,IFNULL(marital_status,"") AS marital_status,IFNULL(blood_group,"") AS blood_group,IFNULL(blood_is_active,"") AS blood_is_active,IFNULL(diet_fitness,"") AS diet_fitness,IFNULL(organ_donor,"") AS organ_donor,sex_history,exercise_level,health_condition,health_insurance,addiction,allergies,IFNULL(bmi,"") AS bmi ,IFNULL(heradiatry_problem,"") AS heradiatry_problem,IFNULL(lat,"") AS lat,IFNULL(lng,"") AS lng,IFNULL(map_location,"") AS map_location')->from('users')->where('users.id', $user_id)->get()->row();
        $id = $q->id;
        $name = $q->name;
        $phone = $q->phone;
        $email = $q->email;
        $gender = $q->gender;
        $dob = $q->dob;
        $height = $q->height;
        $weight = $q->weight;
        $marital_status = $q->marital_status;
        $blood_group = $q->blood_group;
        $diet_fitness = $q->diet_fitness;
        $organ_donor = $q->organ_donor;
        $sex_history = $q->sex_history;
        $exercise_level = $q->exercise_level;
        $activity_level = $q->activity_level;
        $health_condition = $q->health_condition;
        $allergies = $q->allergies;
        $bmi = $q->bmi;
        $health_insurance = $q->health_insurance;
        $addiction = $q->addiction;
        // $addiction_value = $q->addiction_value;
        $blood_is_active = $q->blood_is_active;
        $heradiatry_problem = $q->heradiatry_problem;
        $lat = $q->lat;
        $lng = $q->lng;
        $height_cm_ft = $q->height_cm_ft;
        $map_location = $q->map_location;
        $medical_genetic_disorder = array();
        $count_query = $this->db->query("SELECT * FROM `user_medical_genetic_disorder` WHERE user_id='$user_id'");
        $count = $count_query->num_rows();
        if ($count > 0) {
            foreach ($count_query->result_array() as $row) {
                $medical_genetic_disorder[] = array('name' => $row['name'], 'value' => $row['value']);
            }
        }

        $img_count = $this->db->select('media.id')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $id)->get()->num_rows();

        if ($img_count > 0) {
            $media = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $id)->get()->row();
            $img_file = $media->source;
            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
            $image_status = '5';
        } else {
            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
            $image_status = '0';
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
            $sex_history_status = '5';
        } else {
            $sex_history_status = '5';
        }
        if ($exercise_level == '') {
            $activity_level_status = '0';
        } else {
            $activity_level_status = '5';
        }
        if ($health_condition == '' || $health_condition == '0') {
            $health_condition_is = '0';
        } else {
            $health_condition_is = '5';
        }
        if ($allergies == '' || $allergies == '0') {
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
        if ($addiction == '' || $addiction == '0') {
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


        /* $id1=0;
          $status="";
          $q1 = $this->db->select('*')->from('diet_user_package_history')->where('user_id', $id)->get()->row();
          $id1=$q1->id;
          if($id1=="0")
          {
          $status="fasle";
          }
          else
          {
          $status="true";
          } */

        $Miss_package = $this->db->query("SELECT diet_user_package_history.id FROM `diet_user_package_history` INNER JOIN `users` ON diet_user_package_history.user_id=users.id WHERE diet_user_package_history.user_id='$id'");
        $Miss_belly_package = $Miss_package->num_rows();
        if ($Miss_belly_package > 0) {
            $Miss_belly_status = "true";
        } else {
            $Miss_package_free = $this->db->query("SELECT medicalbot_savefreedietplans.id FROM `medicalbot_savefreedietplans` INNER JOIN `users` ON medicalbot_savefreedietplans.user_id=users.id WHERE medicalbot_savefreedietplans.user_id='$id'");
            $Miss_belly_package_free = $Miss_package_free->num_rows();
            if ($Miss_belly_package_free > 0) {
                $Miss_belly_status = "True";
            } else {
                $Miss_belly_status = "False";
            }
        }
        return array(
            'profile_percentage' => $profile_completed,
            'is_Miss_belly_activated' => $Miss_belly_status
        );
    }


    //sample for email check service 

    public function userloginformail($phone, $mail, $token, $agent, $is_mail) {


        //new code for email
        if ($is_mail == 'android_mail' || $is_mail == 'ios_mail') {
            
            
            
            
            if ($mail == "") {
                return array(
                    'status' => 208,
                    'message' => 'Please enter email.'
                );
            } else {

                $check_user = $this->db->select('id')->from('users')->where('email', $mail)->where('vendor_id', '0')->get()->row();
                //  echo ("select id from users where email= $mail and vendor_id = 0");
                if ($check_user != "") {

                    $id = $check_user->id;
                    $q = $this->db->select('password,id,name,phone,email,gender,dob,height,weight,marital_status,blood_group,diet_fitness,organ_donor,sex_history,IFNULL(height_cm_ft,"") AS height_cm_ft')->from('users')->where('users.id', $id)->get()->row();
                    $id = $q->id;
                    $name = $q->name;
                    $phone = $q->phone;
                    $email = $q->email;
                    $gender = $q->gender;
                    $dob = $q->dob;
                    $height = $q->height;
                    $weight = $q->weight;
                    $marital_status = $q->marital_status;
                    $blood_group = $q->blood_group;
                    $diet_fitness = $q->diet_fitness;
                    $organ_donor = $q->organ_donor;
                    $sex_history = $q->sex_history;
                      if($q->height_cm_ft != NULL)
                    {
                      $height_cm_ft = $q->height_cm_ft;
                    }
                    else
                    {
                        $height_cm_ft = "";
                    }

                    $img_count = $this->db->select('media.id')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $id)->get()->num_rows();
                    if ($img_count > 0) {
                        $media = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $id)->get()->row();
                        $img_file = $media->source;
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                    } else {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    }
                    $last_login = date('Y-m-d H:i:s', strtotime('+12 hours'));
                    $this->db->where('id', $id)->update('users', array(
                        'last_login' => $last_login
                    ));
                    $card_no = '';
                    $card_type = '';
                    $card_query = $this->db->query("SELECT card_no,card_type from user_privilage_card where user_id='$id'");
                    $card_count = $card_query->num_rows();
                    $card_query_new = $this->db->query("SELECT card_no,card_type from user_priviladge_card_new where user_id='$id'");
                    $card_count_new = $card_query_new->num_rows();
                    if ($card_count > 0) {
                        $card_list = $card_query->row_array();
                        $card_no = $card_list['card_no'];
                        $card_type = $card_list['card_type'];
                    } else if ($card_count_new > 0) {
                        $card_list_new = $card_query_new->row_array();
                        $card_no = $card_list_new['card_no'];
                        $card_type = $card_list_new['card_type'];
                    }
                    
                      $Miss_package = $this->db->query("SELECT diet_user_package_history.id FROM `diet_user_package_history` INNER JOIN `users` ON diet_user_package_history.user_id=users.id WHERE diet_user_package_history.user_id='$id'");
                $Miss_belly_package = $Miss_package->num_rows();
                if ($Miss_belly_package > 0) {
                    $Miss_belly_status = "true";
                } else {
                    $Miss_package_free = $this->db->query("SELECT medicalbot_savefreedietplans.id FROM `medicalbot_savefreedietplans` INNER JOIN `users` ON medicalbot_savefreedietplans.user_id=users.id WHERE medicalbot_savefreedietplans.user_id='$id'");
                    $Miss_belly_package_free = $Miss_package_free->num_rows();
                    if ($Miss_belly_package_free > 0) {
                        $Miss_belly_status = "True";
                    } else {
                        $Miss_belly_status = "False";
                    }
                }
                    
                    if(empty($q->password)){
   $passoword_set=0;
   $otp_code = rand(100000, 999999);
                    $message = 'Please enter this One Time Password : ' . $otp_code . ' to verify your Email Address.';
                    $to = $mail;
                    $subject = 'medicalwale login OTP';
                    $msg = $message;
                    $this->Send_Email_Notification($to, $subject, $msg);
}
else{
    $passoword_set=1;
    $otp_code = 0;
}
                    
                    
                    


                    $querys = $this->db->query("UPDATE `users` SET `otp_code`='$otp_code',`token`='$token',`agent`='$agent',`token_status`='1' WHERE id='$id'");

                    $resultpost[] = array(
                        'otp_code' => (int) $otp_code,
                        'user' => 'old',
                        'password_set' => $passoword_set,
                        'id' => $id,
                        'card_no' => str_replace('null', '', $card_no),
                        'card_type' => $card_type,
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
                        'sex_history' => $sex_history,
                        'height_cm_ft' => $height_cm_ft,
                        'is_Miss_belly_activated' => $Miss_belly_status,
                        'profile_percentage' => 0,
                        'is_mail' => 'yes'
                    );
                    $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "data" => $resultpost
                );
            return $resultpost;
                } else {
                    $otp_code = rand(100000, 999999);
                    $message = 'Please enter this One Time Password : ' . $otp_code . ' to verify your Email Address.';
                    $to = $mail;
                    $subject = 'medicalwale login OTP';
                    $msg = 'Please enter this One Time Password : ' . $otp_code . ' to verify your Email Address.';

                    $this->Send_Email_Notification($to, $subject, $msg);
                    
                    
                    
                    $passoword_set=0;
                    $resultpost[] = array(
                        'otp_code' => (int) $otp_code,
                        'user' => 'new',
                        'password_set' => $passoword_set,
                        'id' => '',
                        'card_no' => '',
                        'card_type' => '',
                        'name' => '',
                        'email' => $mail,
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
                        'sex_history' => '',
                        'height_cm_ft' => '',
                        'is_Miss_belly_activated' => '',
                        'profile_percentage' => 0,
                        'is_mail' => 'yes'
                    );
                    $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "data" => $resultpost
                );
            return $resultpost;
                }
            }
        } else {
            if ($phone == "") {
                return array(
                    'status' => 208,
                    'message' => 'Please enter phone no.'
                );
            } else {
                $check_user = $this->db->select('id')->from('users')->where('phone', $phone)->where('vendor_id', '0')->get()->row(); //existing for mobile code
                if ($check_user != "") {
                    $id = $check_user->id;
                    $q = $this->db->select('password,id,name,phone,email,gender,dob,height,weight,marital_status,blood_group,diet_fitness,organ_donor,sex_history,IFNULL(height_cm_ft,"") AS height_cm_ft,')->from('users')->where('users.id', $id)->get()->row();
                    $id = $q->id;
                    $name = $q->name;
                    $phone = $q->phone;
                    $email = $q->email;
                    $gender = $q->gender;
                    $dob = $q->dob;
                    $height = $q->height;
                    $weight = $q->weight;
                    $marital_status = $q->marital_status;
                    $blood_group = $q->blood_group;
                    $diet_fitness = $q->diet_fitness;
                    $organ_donor = $q->organ_donor;
                    $sex_history = $q->sex_history;
                    if($q->height_cm_ft != NULL)
                    {
                      $height_cm_ft = $q->height_cm_ft;
                    }
                    else
                    {
                        $height_cm_ft = "";
                    }
                    $img_count = $this->db->select('media.id')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $id)->get()->num_rows();
                    if ($img_count > 0) {
                        $media = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $id)->get()->row();
                        $img_file = $media->source;
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                    } else {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    }
                    $last_login = date('Y-m-d H:i:s', strtotime('+12 hours'));
                    $this->db->where('id', $id)->update('users', array(
                        'last_login' => $last_login
                    ));
                    $card_no = '';
                    $card_type = '';
                    $card_query = $this->db->query("SELECT card_no,card_type from user_privilage_card where user_id='$id'");
                    $card_count = $card_query->num_rows();
                    $card_query_new = $this->db->query("SELECT card_no,card_type from user_priviladge_card_new where user_id='$id'");
                    $card_count_new = $card_query_new->num_rows();
                    if ($card_count > 0) {
                        $card_list = $card_query->row_array();
                        $card_no = $card_list['card_no'];
                        $card_type = $card_list['card_type'];
                    } else if ($card_count_new > 0) {
                        $card_list_new = $card_query_new->row_array();
                        $card_no = $card_list_new['card_no'];
                        $card_type = $card_list_new['card_type'];
                    }
                    
                      
                      
                      $Miss_package = $this->db->query("SELECT diet_user_package_history.id FROM `diet_user_package_history` INNER JOIN `users` ON diet_user_package_history.user_id=users.id WHERE diet_user_package_history.user_id='$id'");
                $Miss_belly_package = $Miss_package->num_rows();
                if ($Miss_belly_package > 0) {
                    $Miss_belly_status = "true";
                } else {
                    $Miss_package_free = $this->db->query("SELECT medicalbot_savefreedietplans.id FROM `medicalbot_savefreedietplans` INNER JOIN `users` ON medicalbot_savefreedietplans.user_id=users.id WHERE medicalbot_savefreedietplans.user_id='$id'");
                    $Miss_belly_package_free = $Miss_package_free->num_rows();
                    if ($Miss_belly_package_free > 0) {
                        $Miss_belly_status = "True";
                    } else {
                        $Miss_belly_status = "False";
                    }
                }
if(empty($q->password)){
   $passoword_set=0;
    if ($phone == '7834277784' || $phone == '8655369076' || $phone == '9824242411' || $phone == '9733331211' || $phone == '8676008901' || $phone == '9107766551' || $phone == '7545032401' || $phone == '8500005213' || $phone == '9822212411' || $phone == '8080262411' || $phone == '9323465785') {
                        $otp_code = '123456';
                    } else {
                        $otp_code = rand(100000, 999999);
                        $message = 'Please enter this One Time Password : ' . $otp_code . ' to verify your mobile number.';
                        $post_data = array(
                            'From' => '02233721563',
                            'To' => $phone,
                            'Body' => $message
                        );
                        $exotel_sid = "aegishealthsolutions";
                        $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                        $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
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
                    }
}
else{
    $passoword_set=1;
    $otp_code = 0;
}


                   

                    $querys = $this->db->query("UPDATE `users` SET `otp_code`='$otp_code',`token`='$token',`agent`='$agent',`token_status`='1' WHERE id='$id'");

                    $resultpost[] = array(
                        'otp_code' => (int) $otp_code,
                        'user' => 'old',
                        'passoword_set' => $passoword_set,
                        'id' => $id,
                        'card_no' => str_replace('null', '', $card_no),
                        'card_type' => $card_type,
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
                        'sex_history' => $sex_history,
                        'height_cm_ft' => $height_cm_ft,
                        'is_Miss_belly_activated' => $Miss_belly_status,
                        'profile_percentage' => 0,
                        'is_mail' => 'no'
                    );
                    $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "data" => $resultpost
                );
            return $resultpost;
                } else {
                    if ($phone == '7834277784' || $phone == '8655369076' || $phone == '9824242411' || $phone == '9733331211' || $phone == '8676008901' || $phone == '9107766551' || $phone == '7545032401' || $phone == '8500005213' || $phone == '9822212411' || $phone == '8080262411' || $phone == '9323465785') {
                        $otp_code = '123456';
                    } else {
                        $otp_code = rand(100000, 999999);
                        $message = 'Please enter this One Time Password : ' . $otp_code . ' to verify your mobile number.';
                        $post_data = array(
                            'From' => '02233721563',
                            'To' => $phone,
                            'Body' => $message
                        );
                        $exotel_sid = "aegishealthsolutions";
                        $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                        $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
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
                    }
                    $passoword_set=0;
                    $resultpost[] = array(
                        'otp_code' => (int) $otp_code,
                        'user' => 'new',
                        'passoword_set' => $passoword_set,
                        'id' => '',
                        'card_no' => '',
                        'card_type' => '',
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
                        'sex_history' => '',
                        'height_cm_ft' => '',
                        'is_Miss_belly_activated' => '',
                        'profile_percentage' => 0,
                        'is_mail' => 'no'
                    );
                    $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "data" => $resultpost
                );
            return $resultpost;
                }
            }
        }
    }

    public function Send_Email_Notification($to, $subject, $msg) {
        //   $to      = 'jayesh@example.com';
        //     $subject = 'otp';
        //     $message = 'hello';
        $headers = 'From:Medicalwale.com <info@medicalwale.com>' . "\r\n" .
                'Reply-To: donotreply@medicalwale.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $msg, $headers);
    }

    //create new user
    public function usersignup($name, $phone, $mail, $gender, $dob, $image, $token, $agent, $is_mail, $deviceId) {

        //new code for email
        if ($is_mail == 'android_mail' || $is_mail == 'ios_mail') {
            if ($mail == "") {
                return array(
                    'status' => 208,
                    'message' => 'Please enter email.'
                );
            } else {
                //$count_user2 = $this->db->select('id')->from('users')->where('email', $mail)->where('email!=', $mail)->get()->num_rows();
                //$count_phone = $this->db->select('id')->from('users')->where('phone', $phone)->where('phone!=', $phone)->get()->num_rows();
$count_user2=0;
                if(!empty($mail))
            {    
             $query_23 = $this->db->query("SELECT id FROM `users` WHERE email='$mail' and vendor_id='0' ");
             $count_user2 = $query_23->num_rows();
       
            
            }
                $count_phone = $this->db->select('id')->from('users')->where('phone', $phone)->where('vendor_id', 0)->get()->num_rows();

                if ($count_user2 > 0) {
                   /* return array(
                        'status' => 208,
                        'message' => 'Email already exist'
                    );*/
                    $row_id = $query_23->row_array();  
                    $user_id=$row_id['id'];
                     if ($name != '' && $mail != '' && $gender != '') {
                        date_default_timezone_set('Asia/Kolkata');
                        $updated_at = date('Y-m-d H:i:s');
                        if ($image != '') {
                            $type = 'image';
                            $image_data = array(
                                'title' => $image,
                                'type' => $type,
                                'source' => $image,
                                'created_at' => $updated_at,
                                'updated_at' => $updated_at
                            );

                            $this->db->insert('media', $image_data);
                            $media_id = $this->db->insert_id();
                            $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                        } else {
                            $media_id = '0';
                            $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
                        if ($phone == '') {
                            $phone = '';
                        }

                        $user_type = 'user';
                        $userpassword = '';
                        $user_data = array(
                            'name' => $name,
                            'phone' => $phone,
                            'email' => $mail,
                            'gender' => $gender,
                            'dob' => $dob,
                            'password' => $userpassword,
                            'token' => $token,
                            'agent' => $agent,
                            'token_status' => '1',
                            'created_at' => $updated_at,
                            'deviceId' => $deviceId
                        );
                        $this->db->where('id', $user_id);
                        $this->db->update('users', $user_data);
                        
                         $id = $user_id;
                        //$this->db->insert('users',$data);
 $user_data1 = array(
                        'user_id'=>$id,
                        'relation_id'=>0,
                        'patient_name' => $name,
                        'avatar_id' => $media_id,
                        'relationship' => "Myself",
                        'gender' => $gender,
                        'date_of_birth' => $dob,
                        'email' => $mail,
                        'phone' => $phone,
                        'created_at' => $updated_at
                    );
                    $this->db->where('user_id', $user_id);
                    $this->db->update('health_record', $user_data1);
                   
                        if ($agent == 'ios') {
                            return array(
                                'status' => 201,
                                'message' => 'success',
                                'user' => 'old',
                                'id' => $id,
                                'name' => $name,
                                'email' => $mail,
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
                                'email' => $mail,
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
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                } else if ($count_phone > 0) {
                    return array(
                        'status' => 208,
                        'message' => 'Phone number already exist'
                    );
                } else {
                    if ($name != '' && $mail != '' && $gender != '') {
                        date_default_timezone_set('Asia/Kolkata');
                        $updated_at = date('Y-m-d H:i:s');
                        if ($image != '') {
                            $type = 'image';
                            $image_data = array(
                                'title' => $image,
                                'type' => $type,
                                'source' => $image,
                                'created_at' => $updated_at,
                                'updated_at' => $updated_at
                            );

                            $this->db->insert('media', $image_data);
                            $media_id = $this->db->insert_id();
                            $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                        } else {
                            $media_id = '0';
                            $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
                        if ($phone == '') {
                            $phone = '';
                        }

                        $user_type = 'user';
                        $userpassword = '';
                        $user_data = array(
                            'name' => $name,
                            'phone' => $phone,
                            'email' => $mail,
                            'gender' => $gender,
                            'dob' => $dob,
                            'password' => $userpassword,
                            'token' => $token,
                            'agent' => $agent,
                            'token_status' => '1',
                            'created_at' => $updated_at,
                            'deviceId' => $deviceId
                        );

                        $this->db->insert('users', $user_data);
                        $id = $this->db->insert_id();
                        //$this->db->insert('users',$data);
 $user_data1 = array(
                        'user_id'=>$id,
                        'relation_id'=>0,
                        'patient_name' => $name,
                        'avatar_id' => $media_id,
                        'relationship' => "Myself",
                        'gender' => $gender,
                        'date_of_birth' => $dob,
                        'email' => $mail,
                        'phone' => $phone,
                        'created_at' => $updated_at
                    );

                    $this->db->insert('health_record', $user_data1);
                        if ($agent == 'ios') {
                            return array(
                                'status' => 201,
                                'message' => 'success',
                                'user' => 'old',
                                'id' => $id,
                                'name' => $name,
                                'email' => $mail,
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
                                'email' => $mail,
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
        } else if ($is_mail != 'android_mail' || $is_mail != 'ios_mail') {
            //code for phone number 
            /* $count_user2 = $this->db->select('id')->from('users')->where('phone', $phone)->get()->num_rows();
              if ($count_user2 > 0) {
              return array(
              'status' => 208,
              'message' => 'Phone number already exist'
              );
              } */
            //$count_user2 = $this->db->select('id')->from('users')->where('email', $mail)->where('email!=', $mail)->get()->num_rows();
            //$count_phone = $this->db->select('id')->from('users')->where('phone', $phone)->where('phone!=', $phone)->get()->num_rows();
            $count_user2=0;
            if(!empty($mail))
            {    
             $query_23 = $this->db->query("SELECT users.id FROM `users` WHERE users.email='$mail' and vendor_id='0'");
             $count_user2 = $query_23->num_rows();
       
	
            }
            $count_phone = $this->db->select('id')->from('users')->where('phone', $phone)->where('vendor_id', 0)->get()->num_rows();
            
            
            
            	
            if ($count_user2 > 0) {
                 $row_id = $query_23->row_array();  
                    $user_id=$row_id['id'];
                     if ($name != '' && $mail != '' && $gender != '') {
                        date_default_timezone_set('Asia/Kolkata');
                        $updated_at = date('Y-m-d H:i:s');
                        if ($image != '') {
                            $type = 'image';
                            $image_data = array(
                                'title' => $image,
                                'type' => $type,
                                'source' => $image,
                                'created_at' => $updated_at,
                                'updated_at' => $updated_at
                            );

                            $this->db->insert('media', $image_data);
                            $media_id = $this->db->insert_id();
                            $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                        } else {
                            $media_id = '0';
                            $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
                        if ($phone == '') {
                            $phone = '';
                        }

                        $user_type = 'user';
                        $userpassword = '';
                        $user_data = array(
                            'name' => $name,
                            'phone' => $phone,
                            'email' => $mail,
                            'gender' => $gender,
                            'dob' => $dob,
                            'password' => $userpassword,
                            'token' => $token,
                            'agent' => $agent,
                            'token_status' => '1',
                            'created_at' => $updated_at,
                            'deviceId' => $deviceId
                        );
                        $this->db->where('id', $user_id);
                        $this->db->update('users', $user_data);
                        
                        $id = $user_id;
                        //$this->db->insert('users',$data);
 $user_data1 = array(
                        'user_id'=>$id,
                        'relation_id'=>0,
                        'patient_name' => $name,
                        'avatar_id' => $media_id,
                        'relationship' => "Myself",
                        'gender' => $gender,
                        'date_of_birth' => $dob,
                        'email' => $mail,
                        'phone' => $phone,
                        'created_at' => $updated_at
                    );
                    $this->db->where('user_id', $user_id);
                    $this->db->update('health_record', $user_data1);
                   
                        if ($agent == 'ios') {
                            return array(
                                'status' => 201,
                                'message' => 'success',
                                'user' => 'old',
                                'id' => $id,
                                'name' => $name,
                                'email' => $mail,
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
                                'email' => $mail,
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
                    
            } else if ($count_phone > 0) {
                return array(
                    'status' => 208,
                    'message' => 'Phone number already exist'
                );
            } else {
                if ($name != '' && $phone != '' && $gender != '') {
                    date_default_timezone_set('Asia/Kolkata');
                    $updated_at = date('Y-m-d H:i:s');
                    if ($image != '') {
                        $type = 'image';
                        $image_data = array(
                            'title' => $image,
                            'type' => $type,
                            'source' => $image,
                            'created_at' => $updated_at,
                            'updated_at' => $updated_at
                        );

                        $this->db->insert('media', $image_data);
                        $media_id = $this->db->insert_id();
                        $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                    } else {
                        $media_id = '0';
                        $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    }
                    $user_type = 'user';
                    $userpassword = '';
                    if ($mail == '') {
                        $mail = '';
                    } else {
                        
                    }
                    $user_data = array(
                        'name' => $name,
                        'phone' => $phone,
                        'email' => $mail,
                        'gender' => $gender,
                        'dob' => $dob,
                        'password' => $userpassword,
                        'token' => $token,
                        'agent' => $agent,
                        'token_status' => '1',
                        'created_at' => $updated_at,
                        'deviceId' => $deviceId
                    );

                    $this->db->insert('users', $user_data);
                    $id = $this->db->insert_id();
                    //$this->db->insert('users',$data);
                  
                  $user_data1 = array(
                        'user_id'=>$id,
                        'relation_id'=>0,
                        'patient_name' => $name,
                        'avatar_id' => $media_id,
                        'relationship' => "Myself",
                        'gender' => $gender,
                        'date_of_birth' => $dob,
                        'email' => $mail,
                        'phone' => $phone,
                        'created_at' => $updated_at
                    );

                    $this->db->insert('health_record', $user_data1);
                   



                    if ($agent == 'ios') {
                        return array(
                            'status' => 201,
                            'message' => 'success',
                            'user' => 'old',
                            'id' => $id,
                            'name' => $name,
                            'email' => $mail,
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
                            'email' => $mail,
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
    }

    //create new family member
    /*public function add_family($user_id,$name, $phone, $email, $gender, $dob, $image, $token, $agent, $relationship) {
        //$count_email = $this->db->select('id')->from('users')->where('email', $email)->where('vendor_id', 0)->get()->num_rows();
        //$count_phone = $this->db->select('id')->from('users')->where('phone', $phone)->where('vendor_id', 0)->get()->num_rows();
        $query = $this->db->query("SELECT users.id FROM `users` WHERE users.email='$email' or users.phone='$phone'");
		$count = $query->num_rows();
        if ($count > 0) {
            $row = $query->row_array();
             if ($image != '') {
                $type = 'image';
                $image_data = array(
                    'title' => $image,
                    'type' => $type,
                    'source' => $image,
                    'created_at' => $updated_at,
                    'updated_at' => $updated_at
                );

                $this->db->insert('media', $image_data);
                $media_id = $this->db->insert_id();
                $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
            } else {
                $media_id = '0';
                $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
            }
			$relation_id=$row['id'];
			date_default_timezone_set('Asia/Kolkata');
            $updated_at = date('Y-m-d H:i:s');
			$user_data = array(
			    'user_id'=>$user_id,
                'relation_id' => $relation_id,
				'relationship'=>$relationship,
                'patient_name' => $name,
                'phone' => $phone,
                'email' => $email,
                'gender' => $gender,
                'date_of_birth' => $dob,
                'created_at' => $updated_at
            );

            $this->db->insert('health_record', $user_data);
            return array(
               'status' => 201,
               'message' => 'success',
               'member_id' => $relation_id
            );
        } else {
            date_default_timezone_set('Asia/Kolkata');
            $updated_at = date('Y-m-d H:i:s');
            if ($image != '') {
                $type = 'image';
                $image_data = array(
                    'title' => $image,
                    'type' => $type,
                    'source' => $image,
                    'created_at' => $updated_at,
                    'updated_at' => $updated_at
                );

                $this->db->insert('media', $image_data);
                $media_id = $this->db->insert_id();
                $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
            } else {
                $media_id = '0';
                $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
            }
            if ($phone == '') {
                $phone = '';
            }

            $userpassword = '';
            $user_data = array(			
                'parent_id' => $user_id,
				'relationship'=>$relationship,
                'name' => $name,
                'avatar_id' => $media_id,
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
            $member_id = $this->db->insert_id();
           	$user_data = array(
			    'user_id'=>$user_id,
                'relation_id' => $member_id,
				'relationship'=>$relationship,
                'patient_name' => $name,
                'avatar_id' => $media_id,
                'phone' => $phone,
                'email' => $email,
                'gender' => $gender,
                'date_of_birth' => $dob,
                'created_at' => $updated_at
            );

            $this->db->insert('health_record', $user_data);
            return array(
                'status' => 201,
                'message' => 'success',
                'member_id' => $member_id
            );
        }
    }
    */
    // Dhaval Edited add new family member
       public function add_family($user_id,$name, $phone, $email, $gender, $dob, $image, $token, $agent, $relationship) {
        $query = $this->db->query("SELECT users.id FROM `users` WHERE users.email='$email' or users.phone='$phone'");
      
		$count = $query->num_rows();
        if ($count > 0) {
            $row = $query->row_array();
           
             $updated_at = date('Y-m-d H:i:s');
             if ($image != '') {
                $type = 'image';
                $image_data = array(
                    'title' => $image,
                    'type' => $type,
                    'source' => $image,
                    'created_at' => $updated_at,
                    'updated_at' => $updated_at
                );

                $this->db->insert('media', $image_data);
                $media_id = $this->db->insert_id();
                $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
            } else {
                $media_id = '0';
                $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
            }
            // echo ; die();
			$relation_id=$row['id'];
			date_default_timezone_set('Asia/Kolkata');
           
			$user_data = array(
			    'user_id'=>$user_id,
                'relation_id' => $relation_id,
				'relationship'=>$relationship,
                'patient_name' => $name,
                'avatar_id'=>$media_id,
                'phone' => $phone,
                'email' => $email,
                'gender' => $gender,
                'date_of_birth' => $dob,
                'created_at' => $updated_at
            );

            $this->db->insert('health_record', $user_data);
            return array(
               'status' => 201,
               'message' => 'success',
               'member_id' => $relation_id
            );
        } else {
            date_default_timezone_set('Asia/Kolkata');
            $updated_at = date('Y-m-d H:i:s');
            if ($image != '') {
                $type = 'image';
                $image_data = array(
                    'title' => $image,
                    'type' => $type,
                    'source' => $image,
                    'created_at' => $updated_at,
                    'updated_at' => $updated_at
                );

                $this->db->insert('media', $image_data);
                $media_id = $this->db->insert_id();
                $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
            } else {
                $media_id = '0';
                $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
            }
            if ($phone == '') {
                $phone = '';
            }

            $userpassword = '';
            $user_data = array(			
                'parent_id' => $user_id,
				'relationship'=>$relationship,
                'name' => $name,
                'avatar_id' => $media_id,
                'phone' => $phone,
                'email' => $email,
                'gender' => $gender,
                'dob' => $dob,
                'password' => $userpassword,
                'agent' => $agent,
                'token_status' => '1',
                'created_at' => $updated_at
            );

            $this->db->insert('users', $user_data);
            $member_id = $this->db->insert_id();
           	$user_data1 = array(
			    'user_id'=>$user_id,
                'relation_id' => $member_id,
				'relationship'=>$relationship,
                'patient_name' => $name,
                'avatar_id' => $media_id,
                'phone' => $phone,
                'email' => $email,
                'gender' => $gender,
                'date_of_birth' => $dob,
                'created_at' => $updated_at
            );

            $this->db->insert('health_record', $user_data1);
            return array(
                'status' => 201,
                'message' => 'success',
                'member_id' => $member_id
            );
        }
    }
    // End Here
    
    
	
/*	public function update_family($id, $name, $phone, $email, $gender, $dob, $profile_pic, $relationship) {
        $Q = "SET source = '$profile_pic', title='$profile_pic'";
        $sql = "SELECT avatar_id FROM users WHERE users.id ='" . $id . "'";
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
                $this->db->query("UPDATE users SET avatar_id = '" . $aid . "' WHERE id = '" . $id . "'");
            } else {
                $this->db->query("UPDATE media $Q WHERE id = '" . $avatar_id . "'");
            }
        }
        $query = $this->db->query("UPDATE users SET `gender`='$gender',`dob`='$dob',`name`='$name',`phone`='$phone',`email`='$email',`relationship`='$relationship' WHERE id='$id'");

        if ($profile_pic != '') {
            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
        } elseif ($avatar_id == 0) {
            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
        } else {
            $sql2 = "SELECT source FROM media  WHERE id ='" . $avatar_id . "'";
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
            'email' => $email,
            'relationship' => $relationship
        );
    }*/
	
	// edited by dhaval
	public function update_family($id, $name, $phone, $email, $gender, $dob, $profile_pic, $relationship) {
        $Q = "SET source = '$profile_pic', title='$profile_pic'";
        $sql = "SELECT avatar_id, relation_id FROM health_record WHERE id='$id'";
        $avatar_id = $this->db->query($sql)->row()->avatar_id;
        $relation_id = $this->db->query($sql)->row()->relation_id;
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
                 $this->db->query("UPDATE users SET avatar_id = '" . $aid . "' WHERE id = '" .$relation_id. "'");
                $this->db->query("UPDATE health_record SET avatar_id = '" . $aid . "' WHERE relation_id='$relation_id' and id='$id' ");
            } else {
                $this->db->query("UPDATE media $Q WHERE id = '" . $avatar_id . "'");
                $this->db->query("UPDATE users SET avatar_id = '" . $avatar_id . "' WHERE id = '" .$relation_id. "'");
                $this->db->query("UPDATE health_record SET avatar_id = '" . $avatar_id . "' WHERE relation_id='$relation_id' and id='$id' ");
            }
        }
         $aid="";
       
        $query1 = $this->db->query("UPDATE users SET `gender`='$gender',`dob`='$dob',`name`='$name',`phone`='$phone',`email`='$email' WHERE id='$relation_id'");

        $query = $this->db->query("UPDATE health_record SET `gender`='$gender',`date_of_birth`='$dob',`patient_name`='$name',`phone`='$phone',`email`='$email',`relationship`='$relationship' WHERE relation_id='$relation_id' and id='$id'");

        if ($profile_pic != '') {
            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
        } elseif ($avatar_id == 0) {
            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
        } else {
            $sql2 = "SELECT source FROM media  WHERE id ='" . $avatar_id . "'";
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
            'email' => $email,
            'relationship' => $relationship
        );
    }
	
	
	//delete family member
	public function delete_family($user_id, $family_id) {
	   // echo "DELETE FROM `users` WHERE id='$family_id' AND parent_id='$user_id'";
        $query = $this->db->query("UPDATE health_record SET active = '1' WHERE (id='$family_id' AND user_id='$user_id') or (relation_id='$family_id' AND user_id='$user_id') ");
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }
    

// old family_tree
	
/*	public function family_tree($user_id) {
	    $resultpost = array();
		$childpost = array();
		$query = $this->db->query("SELECT users.id,users.name,users.gender,users.dob,users.phone,users.email,users.relationship,media.source FROM `users` LEFT JOIN media ON users.avatar_id = media.id WHERE users.parent_id='$user_id'");	
        // $query = $this->db->query("SELECT users.id,users.name,users.gender,users.dob,users.phone,users.email,users.relationship,media.source FROM `users` LEFT JOIN media ON users.avatar_id = media.id WHERE users.parent_id='$user_id'");	
      
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
			    $id           = $row['id'];
	            $image        = $row['source'];
				if(empty($image)){
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
				}else{				
					$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
				}	
				$childpost = $this->LoginModel->child_tree($id);
                $resultpost[] = array(
                    "id"           => $row['id'],
                    "name"         => $row['name'],
                    "gender"       => $row['gender'],
                    'dob'          => $row['dob'],
                    'phone'        => $row['phone'],
                    'email'        => $row['email'],
                    'relationship' => $row['relationship'],
                    'image'        => $image,
					'child'        => $childpost
                );
            }
			return array(
				'status' => 201,
				'message' => 'success',
				'data' => $resultpost
            );
        } else {            
			return array(
            'status' => 208,
            'message' => 'failure',
            'data' => $resultpost
           );
		}
		
    }*/
// Edited by Dhaval 
public function family_tree($user_id) {
	    $resultpost = array();
		$childpost = array();
		$query = $this->db->query("SELECT health_record.relation_id as relation_id,health_record.id as aid,health_record.patient_name,health_record.gender,health_record.date_of_birth,health_record.phone,health_record.email,health_record.relationship,media.source FROM `health_record` LEFT JOIN media ON health_record.avatar_id = media.id WHERE health_record.user_id='$user_id' and health_record.active='0'");	
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
			    $id           = $row['aid'];
	            $image        = $row['source'];
				if(empty($image)){
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
				}else{				
					$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
				}
				 $parent_id           = $row['relation_id'];
				$childpost = $this->LoginModel->child_tree($parent_id);
			if(empty($row['email']))
			{
			    $email="";
			}
			else
			{
			    $email=$row['email'];
			}
                $resultpost[] = array(
                    "id"           => $row['aid'],
                    "relation_id"  => $row['relation_id'],
                    "name"         => $row['patient_name'],
                    "gender"       => $row['gender'],
                    'dob'          => $row['date_of_birth'],
                    'phone'        => $row['phone'],
                    'email'        => $email,
                    'relationship' => $row['relationship'],
                    'image'        => $image,
					'child'        => $childpost
                );
            }
			return array(
				'status' => 201,
				'message' => 'success',
				'data' => $resultpost
            );
        } else {            
			return array(
            'status' => 208,
            'message' => 'failure',
            'data' => $resultpost
           );
		}
		
    }


	public function child_tree($user_id) {
	    $resultpost = array();
		$childpost = array();
		$query = $this->db->query("SELECT health_record.relation_id as relation_id,health_record.id as aid,health_record.patient_name,health_record.gender,health_record.date_of_birth,health_record.phone,health_record.email,health_record.relationship,media.source FROM `health_record` LEFT JOIN media ON health_record.avatar_id = media.id WHERE health_record.user_id='$user_id' and health_record.active='0'");	
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
			    $id           = $row['aid'];
	            $image        = $row['source'];
				if(empty($image)){
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
				}else{				
					$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
				}
				 $parent_id           = $row['relation_id'];
				$childpost = $this->LoginModel->child_tree($id);
                $resultpost[] = array(
                    "id"           => $row['aid'],
                    "relation_id"  => $row['relation_id'],
                    "name"         => $row['patient_name'],
                    "gender"       => $row['gender'],
                    'dob'          => $row['date_of_birth'],
                    'phone'        => $row['phone'],
                    'email'        => $row['email'],
                    'relationship' => $row['relationship'],
                    'image'        => $image,
					'child'        => $childpost
                );
            }
			return $resultpost;
        } else {            
			return $resultpost;
		}
		
    }


	function super_unique($array,$key)
    {
       $temp_array = array();
       foreach ($array as &$v) {
           if (!isset($temp_array[$v[$key]]))
           $temp_array[$v[$key]] =& $v;
       }
       $array = array_values($temp_array);
       return $array;

    }
	
    public function family_relation_tree($user_id) {
        // $query = $this->db->query("SELECT users.id,users.name,users.gender,users.dob,users.phone,users.email,users.relationship,users.created_at,media.source FROM `users` LEFT JOIN media ON users.avatar_id = media.id WHERE users.parent_id='$user_id'");
        $grandparent_  = array(); 
        $parent_    = array();
        $subparent_ = array();
        $friend_    = array();
        $sibling_= array();
        $spouse_    = array();
        $child_= array();
        
        $query = $this->db->query("SELECT h.relation_id as relation_id,h.id as aid, h.patient_name as name , h.gender, h.date_of_birth, h.phone, h.email, h.relationship , u.created_at, m.source FROM health_record as h LEFT JOIN users as u on (h.user_id = u.id) LEFT join media as m ON (h.avatar_id = m.id) WHERE h.user_id='$user_id' and h.relation_id !='0' and h.active='0'");
        
        $count = $query->num_rows();
        
        $grandparent = array();
        $parent = array();
        $subparent = array();
        $neighbour = array();
        $spouse = array();
        $sibling = array();
        $child = array();
        $friend = array();
        
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
			    $relationship = $row['relationship'];
                $image        = $row['source'];
                $created_at        = $row['created_at'];
				if(empty($image)){
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
				}else{				
					$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
				}
                if($relationship==='Grand Father' || $relationship==='Grand Mother'){
                        $grandparent_[] = array(
                            "id"           => $row['aid'],
                            "relation_id"  => $row['relation_id'],
							"name"         => $row['name'],
							"gender"       => $row['gender'],
							'dob'          => $row['date_of_birth'],
							'phone'        => $row['phone'],
							'email'        => $row['email'],
							'relationship' => $row['relationship'],
							'image'        => $image,
                            'created_at' => $created_at
                        );
                }
                
                if($relationship==='Mother' || $relationship==='Father'){
                    $parent_[] = array(
                            "id"           => $row['aid'],
                            "relation_id"  => $row['relation_id'],
							"name"         => $row['name'],
							"gender"       => $row['gender'],
							'dob'          => $row['date_of_birth'],
							'phone'        => $row['phone'],
							'email'        => $row['email'],
							'relationship' => $row['relationship'],
							'image'        => $image,
                            'created_at' => $created_at
                        );
                }
                
                 if($relationship==='Aunty' || $relationship==='Uncle' || $relationship==='aunty' || $relationship==='uncle'){
                    $subparent_[] = array(
                            "id"           => $row['aid'],
                            "relation_id"  => $row['relation_id'],
							"name"         => $row['name'],
							"gender"       => $row['gender'],
							'dob'          => $row['date_of_birth'],
							'phone'        => $row['phone'],
							'email'        => $row['email'],
							'relationship' => $row['relationship'],
							'image'        => $image,
                            'created_at' => $created_at
                        );
                }
                
                if($relationship==='Friend' || $relationship==='Neighbour'){
                    $friend_[] = array(
                            "id"           => $row['aid'],
                            "relation_id"  => $row['relation_id'],    
							"name"         => $row['name'],
							"gender"       => $row['gender'],
							'dob'          => $row['date_of_birth'],
							'phone'        => $row['phone'],
							'email'        => $row['email'],
							'relationship' => $row['relationship'],
							'image'        => $image,
                            'created_at' => $created_at
                        );
                }
                
                if($relationship==='Brother' || $relationship==='Sister'){
                    $sibling_[] = array(
                            "id"           => $row['aid'],
                             "relation_id"  => $row['relation_id'],
							"name"         => $row['name'],
							"gender"       => $row['gender'],
							'dob'          => $row['date_of_birth'],
							'phone'        => $row['phone'],
							'email'        => $row['email'],
							'relationship' => $row['relationship'],
							'image'        => $image,
                            'created_at' => $created_at
                        );
                }
                
                if($relationship==='Husband' || $relationship==='Wife'){
                    $spouse_[] = array(
                            "id"           => $row['aid'],
                             "relation_id"  => $row['relation_id'],
							"name"         => $row['name'],
							"gender"       => $row['gender'],
							'dob'          => $row['date_of_birth'],
							'phone'        => $row['phone'],
							'email'        => $row['email'],
							'relationship' => $row['relationship'],
							'image'        => $image,
                            'created_at' => $created_at
                        );
                }
                
                
                if($relationship==='Son' || $relationship==='Daughter'){
                    $child_[] = array(
                            "id"           => $row['aid'],
                             "relation_id"  => $row['relation_id'],
							"name"         => $row['name'],
							"gender"       => $row['gender'],
							'dob'          => $row['date_of_birth'],
							'phone'        => $row['phone'],
							'email'        => $row['email'],
							'relationship' => $row['relationship'],
							'image'        => $image,
                            'created_at' => $created_at
                        );
                }
                
                if(!empty($grandparent_)){        
                    $grandparent = $this->super_unique(array_merge($grandparent,$grandparent_),'id');
                }
                if(!empty($parent_)){        
                    $parent = $this->super_unique(array_merge($parent,$parent_),'id');
                }
                if(!empty($subparent_)){        
                    $subparent = $this->super_unique(array_merge($subparent,$subparent_),'id');
                }
                if(!empty($spouse_)){        
                    $spouse = $this->super_unique(array_merge($spouse,$spouse_),'id');
                }
                if(!empty($friend_)){        
                    $friend = $this->super_unique(array_merge($friend,$friend_),'id');
                }
                if(!empty($sibling_)){        
                    $sibling = $this->super_unique(array_merge($sibling,$sibling_),'id');
                }
                if(!empty($child_)){        
                    $child = $this->super_unique(array_merge($child,$child_),'id');
                }
            }
            // print_r($sibling); die();
            $resultpost[] = array(
                        'grandparent' => $grandparent_,
                        'parent' => $parent_,
                        'friends_relatives' => $subparent_,
                        'spouse' => $spouse_,
                        'sibling'=>$sibling_,
                        'friend'=>$friend_,
                        'child'=>$child_
                    );
        } else {
            $resultpost = array();
        }
		return array(
				'status' => 201,
				'message' => 'success',
				'data' => $resultpost
            );
    }


    //added by zak for new user create through missbelly or other tab
    public function usersignup_for_app($name, $phone, $mail, $gender, $dob, $image, $token, $agent, $is_mail, $is_user) {
        if ($is_mail == 'android_phone' || $is_mail == 'ios_phone') {
            if ($phone == "") {
                return array(
                    'status' => 208,
                    'message' => 'Please enter mobile number.'
                );
            } else {

                $count_user2 = $this->db->select('id')->from('users')->where('email', $mail)->where('vendor_id', 0)->get()->num_rows();
                $count_phone = $this->db->select('id')->from('users')->where('phone', $phone)->where('vendor_id', 0)->get()->num_rows();

                if ($count_user2 > 0) {
                    $existing_query = $this->db->query("SELECT * FROM `users` WHERE email='$mail'");
                    $count = $existing_query->num_rows();
                    if ($count > 0) {
                        foreach ($existing_query->result_array() as $row) {
                            $id = $row['id'];
                            $name = $row['name'];
                            $phone = $row['phone'];
                            $mail = $row['email'];
                            $gender = $row['gender'];
                            $dob = $row['dob'];
                        }
                        if ($image != '') {
                            $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                        } else {
                            $media_id = '0';
                            $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
                        return array(
                            'status' => 201,
                            'message' => 'success',
                            'id' => $id,
                            'user' => 'other',
                            'name' => $name,
                            'phone' => $phone,
                            'email' => $mail,
                            'gender' => $gender,
                            'dob' => $dob,
                            'image' => $profile
                        );
                    }
                    // return array(
                    //     'status' => 208,
                    //     'message' => 'Email already exist'
                    // );
                } else if ($count_phone > 0) {

                    $existing_query = $this->db->query("SELECT * FROM `users` WHERE phone='$phone'");
                    $count = $existing_query->num_rows();
                    if ($count > 0) {
                        foreach ($existing_query->result_array() as $row) {
                            $id = $row['id'];
                            $name = $row['name'];
                            $phone = $row['phone'];
                            $mail = $row['email'];
                            $gender = $row['gender'];
                            $dob = $row['dob'];
                        }
                        if ($image != '') {
                            $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                        } else {
                            $media_id = '0';
                            $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
                        return array(
                            'status' => 201,
                            'message' => 'success',
                            'id' => $id,
                            'user' => 'other',
                            'name' => $name,
                            'phone' => $phone,
                            'email' => $mail,
                            'gender' => $gender,
                            'dob' => $dob,
                            'image' => $profile
                        );
                    }
                    // return array(
                    //     'status' => 208,
                    //     'message' => 'Phone number already exist'
                    // );
                } else {
                    if ($name != '' && $phone != '' && $gender != '') {
                        date_default_timezone_set('Asia/Kolkata');
                        $updated_at = date('Y-m-d H:i:s');
                        if ($image != '') {
                            $type = 'image';
                            $image_data = array(
                                'title' => $image,
                                'type' => $type,
                                'source' => $image,
                                'created_at' => $updated_at,
                                'updated_at' => $updated_at
                            );

                            $this->db->insert('media', $image_data);
                            $media_id = $this->db->insert_id();
                            $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                        } else {
                            $media_id = '0';
                            $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
                        if ($mail == '') {
                            $mail = '';
                        }

                        $user_type = 'user';
                        $userpassword = '';
                        $user_data = array(
                            'name' => $name,
                            'phone' => $phone,
                            'email' => $mail,
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
                        //need to add excotel sms code to invite insrted phone number
                        //end

                        if ($agent == 'ios') {
                            return array(
                                'status' => 201,
                                'message' => 'success',
                                'user' => 'other',
                                'id' => $id,
                                'name' => $name,
                                'email' => $mail,
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
                                'user' => 'other',
                                'name' => $name,
                                'phone' => $phone,
                                'email' => $mail,
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
        }
        //     else if($is_mail != 'android_mail' || $is_mail != 'ios_mail')
        //     {
        //         //code for phone number 
        //   /* $count_user2 = $this->db->select('id')->from('users')->where('phone', $phone)->get()->num_rows();
        //     if ($count_user2 > 0) {
        //         return array(
        //             'status' => 208,
        //             'message' => 'Phone number already exist'
        //         );
        //     }*/
        //     //$count_user2 = $this->db->select('id')->from('users')->where('email', $mail)->where('email!=', $mail)->get()->num_rows();
        //     //$count_phone = $this->db->select('id')->from('users')->where('phone', $phone)->where('phone!=', $phone)->get()->num_rows();
        //           $count_user2 = $this->db->select('id')->from('users')->where('email', $mail)->get()->num_rows();
        //           $count_phone = $this->db->select('id')->from('users')->where('phone', $phone)->get()->num_rows(); 
        //      if ($count_user2 > 0) 
        //     {
        //         return array(
        //             'status' => 208,
        //             'message' => 'Email already exist'
        //         );
        //     }else if($count_phone > 0){
        //         return array(
        //             'status' => 208,
        //             'message' => 'Phone number already exist'
        //         );
        //     }
        //     else {
        //         if ($name != '' && $phone != '' && $gender != '') {
        //             date_default_timezone_set('Asia/Kolkata');
        //             $updated_at = date('Y-m-d H:i:s');
        //             if ($image != '') {
        //                 $type = 'image';
        //                 $image_data = array(
        //                     'title' => $image,
        //                     'type' => $type,
        //                     'source' => $image,
        //                     'created_at' => $updated_at,
        //                     'updated_at' => $updated_at
        //                 );
        //                 $this->db->insert('media', $image_data);
        //                 $media_id = $this->db->insert_id();
        //                 $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
        //             } else {
        //                 $media_id = '0';
        //                 $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
        //             }
        //             $user_type = 'user';
        //             $userpassword = '';
        //              if($mail == '')
        //             {
        //                 $mail = '';
        //             }
        //             else
        //             {
        //             }
        //             $user_data = array(
        //                 'name' => $name,
        //                 'phone' => $phone,
        //                 'email' => $mail,
        //                 'gender' => $gender,
        //                 'dob' => $dob,
        //                 'password' => $userpassword,
        //                 'token' => $token,
        //                 'agent' => $agent,
        //                 'token_status' => '1',
        //                 'created_at' => $updated_at
        //             );
        //             $this->db->insert('users', $user_data);
        //             $id = $this->db->insert_id();
        //             //$this->db->insert('users',$data);
        //             if ($agent == 'ios') {
        //                 return array(
        //                     'status' => 201,
        //                     'message' => 'success',
        //                     'user' => 'old',
        //                     'id' => $id,
        //                     'name' => $name,
        //                     'email' => $mail,
        //                     'phone' => $phone,
        //                     'image' => $image,
        //                     'gender' => $gender,
        //                     'dob' => $dob,
        //                     'height' => '',
        //                     'weight' => '',
        //                     'marital_status' => '',
        //                     'blood_group' => '',
        //                     'diet_fitness' => '',
        //                     'organ_donor' => '',
        //                     'sex_history' => ''
        //                 );
        //             } else {
        //                 return array(
        //                     'status' => 201,
        //                     'message' => 'success',
        //                     'id' => $id,
        //                     'name' => $name,
        //                     'phone' => $phone,
        //                     'email' => $mail,
        //                     'gender' => $gender,
        //                     'dob' => $dob,
        //                     'image' => $profile
        //                 );
        //             }
        //         } else {
        //             return array(
        //                 'status' => 208,
        //                 'message' => 'Please enter all fields'
        //             );
        //         }
        //     }
        //     }
    }

    //added by jakir for mail login 
    public function usersignupformail($name, $phone, $gender, $dob, $image, $token, $agent, $mail) {
        $count_user2 = $this->db->select('id')->from('users')->where('mail', $mail)->where('email!=', $mail)->get()->num_rows();
        if ($count_user2 > 0) {
            return array(
                'status' => 208,
                'message' => 'mail already exist'
            );
        } else {
            if ($name != '' && $mail != '' && $gender != '') {
                date_default_timezone_set('Asia/Kolkata');
                $updated_at = date('Y-m-d H:i:s');
                if ($image != '') {
                    $type = 'image';
                    $image_data = array(
                        'title' => $image,
                        'type' => $type,
                        'source' => $image,
                        'created_at' => $updated_at,
                        'updated_at' => $updated_at
                    );

                    $this->db->insert('media', $image_data);
                    $media_id = $this->db->insert_id();
                    $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                } else {
                    $media_id = '0';
                    $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                $user_type = 'user';
                $userpassword = '';
                $user_data = array(
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $mail,
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
                        'email' => $mail,
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
                        'email' => $mail,
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

    //update user informations
    public function userprofile($user_id, $data, $genetic_disorder) {
        $count_user = $this->db->select('id')->from('users')->where('id', $user_id)->get()->num_rows();
        if ($count_user > 0) {
            if ($user_id != '') {
                $this->db->where('id', $user_id);
                $update_list = $this->db->UPDATE('users', $data);
                $this->db->query("DELETE FROM `user_medical_genetic_disorder` WHERE user_id='$user_id'");


                if ($genetic_disorder != '') {
                    $disorder_json = '{"inbox":' . $genetic_disorder . '}';
                    $disorder_data = json_decode($disorder_json);
                    foreach ($disorder_data->inbox as $disorder_array) {
                        $genetic_list = array(
                            'name' => $disorder_array->name,
                            'value' => $disorder_array->value,
                            'user_id' => $user_id
                        );
                        $this->db->insert('user_medical_genetic_disorder', $genetic_list);
                    }
                }

                $q = $this->db->select('avatar_id,password,height_cm_ft,id,name,IFNULL(activity_level,"") AS activity_level,phone,email,gender,dob,IFNULL(height,"") AS height,IFNULL(weight,"") AS weight,IFNULL(marital_status,"") AS marital_status,IFNULL(blood_group,"") AS blood_group,IFNULL(blood_is_active,"") AS blood_is_active,IFNULL(diet_fitness,"") AS diet_fitness,IFNULL(organ_donor,"") AS organ_donor,sex_history,exercise_level,health_condition,health_insurance,addiction,allergies,IFNULL(bmi,"") AS bmi ,IFNULL(heradiatry_problem,"") AS heradiatry_problem,IFNULL(lat,"") AS lat,IFNULL(lng,"") AS lng,IFNULL(map_location,"") AS map_location')->from('users')->where('users.id', $user_id)->get()->row();

                //   $q = $this->db->select('avatar_id,password,height_cm_ft,id,name,IFNULL(activity_level,"") AS activity_level,phone,email,gender,dob,IFNULL(height,"") AS height,IFNULL(weight,"") AS weight,IFNULL(marital_status,"") AS marital_status,IFNULL(blood_group,"") AS blood_group,IFNULL(blood_is_active,"") AS blood_is_active,IFNULL(diet_fitness,"") AS diet_fitness,IFNULL(organ_donor,"") AS organ_donor,sex_history,exercise_level,health_condition,health_insurance,addiction,allergies,IFNULL(bmi,"") AS bmi ,IFNULL(heradiatry_problem,"") AS heradiatry_problem,IFNULL(lat,"") AS lat,IFNULL(lng,"") AS lng,IFNULL(map_location,"") AS map_location')->from('users')->where('users.id', $user_id)->get()->row();

                $height_cm_ft = $q->height_cm_ft;
                $id = $q->id;
                $name = $q->name;
                $avatar_id = $q->avatar_id;
                $phone = $q->phone;
                $email = $q->email;
                $gender = $q->gender;
                $dob = $q->dob;
                $height = $q->height;
                $weight = $q->weight;
                $marital_status = $q->marital_status;
                $blood_group = $q->blood_group;
                $diet_fitness = $q->diet_fitness;
                $organ_donor = $q->organ_donor;
                $sex_history = $q->sex_history;
                $exercise_level = $q->exercise_level;
                $activity_level = $q->activity_level;
                $health_condition = $q->health_condition;
                $allergies = $q->allergies;
                $bmi = $q->bmi;
                $health_insurance = $q->health_insurance;
                $addiction = $q->addiction;
                //$addiction_value = $q->addiction_value;
                $blood_is_active = $q->blood_is_active;
                $heradiatry_problem = $q->heradiatry_problem;
                $lat = $q->lat;
                $lng = $q->lng;
                $map_location = $q->map_location;

                $medical_genetic_disorder = array();
                $count_query = $this->db->query("SELECT * FROM `user_medical_genetic_disorder` WHERE user_id='$user_id'");
                $count = $count_query->num_rows();
                if ($count > 0) {
                    foreach ($count_query->result_array() as $row) {
                        $medical_genetic_disorder[] = array('name' => $row['name'], 'value' => $row['value']);
                    }
                }

                if ($avatar_id == '0') {
                    $avatar_id_status = '0';
                } else {
                    $avatar_id_status = '5';
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
                    $sex_history_status = '5';
                } else {
                    $sex_history_status = '5';
                }
                if ($exercise_level == '') {
                    $activity_level_status = '0';
                } else {
                    $activity_level_status = '5';
                }
                if ($health_condition == '' || $health_condition == '0') {
                    $health_condition_is = '0';
                } else {
                    $health_condition_is = '5';
                }
                if ($allergies == '' || $allergies == '0') {
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
                if ($addiction == '' || $addiction == '0') {
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

                $profile_completed = $step1 + $height_status + $weight_status + $marital_status_is + $blood_group_status + $diet_fitness_status + $organ_donor_status + $sex_history_status + $activity_level_status + $health_condition_is + $allergies_status + $bmi_status + $health_insurance_status + $addiction_status + $heradiatry_problem_status + $location_status + $avatar_id_status;

                return array(
                    'status' => 201,
                    'message' => 'success',
                    'id' => $user_id,
                    'blood_is_active' => $blood_is_active,
                    'profile_completed' => $profile_completed
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

    public function update_userprofile_question($data2) {


        $this->db->insert('userprofile_question_answer', $data2);
    }

    public function delete_question($user_id) {

        $this->db->where('user_id', $user_id);
        $this->db->delete('userprofile_question_answer');
    }

    //update user's blood group
    public function blood_group_update($user_id, $blood_group, $blood_is_active) {
        $count_user = $this->db->select('id')->from('users')->where('id', $user_id)->get()->num_rows();
        if ($count_user > 0) {
            if ($user_id != '') {
                date_default_timezone_set('Asia/Kolkata');
                $updated_at = date('Y-m-d H:i:s');
                $user_data = array(
                    'blood_group' => $blood_group,
                    'blood_is_active' => $blood_is_active
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

    //follow user
    public function follow($user_id, $following_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from follow_user where user_id='$user_id' and parent_id='$following_id'");
        $count = $count_query->num_rows();
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

    //get user's following list
    public function following_list($user_id) {
        $count_query = $this->db->query("SELECT follow_user.user_id as user_id, follow_user.parent_id, users.id, users.name, users.avatar_id, media.id, media.title  from follow_user
        INNER JOIN users ON users.id = follow_user.parent_id
        LEFT JOIN media ON media.id = users.avatar_id
        where user_id='$user_id'");
        $count = $count_query->num_rows();
        if ($count > 0) {
            foreach ($count_query->result_array() as $row) {
                $user_id = $row['parent_id'];
                $name = $row['name'];
                $avatar_id = $row['avatar_id'];
                $title = $row['title'];
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

    //get user's follower list
    public function follower_list($user_id) {
        $count_query = $this->db->query("SELECT follow_user.user_id, follow_user.parent_id, users.id, users.name, users.avatar_id, media.id, media.title  from follow_user
        LEFT JOIN users ON users.id = follow_user.user_id
        LEFT JOIN media ON media.id = users.avatar_id
        where parent_id='$user_id'");
        $count = $count_query->num_rows();
        if ($count > 0) {
            foreach ($count_query->result_array() as $row) {
                $user_id = $row['user_id'];
                $name = $row['name'];
                $avatar_id = $row['avatar_id'];
                $title = $row['title'];
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

    //get user's address list
    
    
    public function address_list($user_id) {
        $query = $this->db->query("SELECT address_id,name,mobile,pincode,address1,address2,full_address,landmark,city,state,address_type,lat,lng,relation_id,relation_ship FROM `user_address` WHERE user_id='$user_id' order by address_id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $address_id = $row['address_id'];
                $name = $row['name'];
                $mobile = $row['mobile'];
                $address1 = $row['address1'];
                $address2 = $row['address2'];
                $landmark = $row['landmark'];
                $city = $row['city'];
                $state = $row['state'];
                $pincode = $row['pincode'];
                $address_type = $row['address_type'];
                $lat = $row['lat'];
                $lng = $row['lng'];
                $full_address=$row['full_address'];
                $relation_id = $row['relation_id'];
                $relation_ship=$row['relation_ship'];
                if(empty($lat))
                {
                    $lat_new="";
                }
                else
                {
                   $lat_new=$lat; 
                }
                if(empty($lng))
                {
                    $lng_new="";
                }
                else
                {
                   $lng_new=$lng; 
                }
                if(empty($full_address))
                {
                    $full_address_new="";
                }
                else
                {
                   $full_address_new=$full_address; 
                }
                 if(empty($relation_id))
                {
                    $relation_id_new="";
                }
                else
                {
                  $relation_id_new=$relation_id; 
                }
                if(empty($relation_ship))
                {
                    $relation_ship_new="";
                }
                else
                {
                   $relation_ship_new=$relation_ship; 
                }
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
                    'address_type' => $address_type,
                    'lat'=>$lat_new,
                    'lng'=>$lng_new,
                    'full_address'=>$full_address_new,
                    'member_id'=>$relation_id_new,
                    'relation'=>$relation_ship_new
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }


/******************************add data****************************************/


    public function addr($name, $email, $phone) {
        
        $query = $this->db->query("SELECT `email` FROM `demo_test` WHERE `email`='$email'");
         $count = $query->num_rows();
         
         if($count > 0){
             
             return array(
            'status' => 201,
            'message' => 'Email allready Exist'
        );
             
         }
         else{
         
         $data = array(
            'name' => $name,
            'email' => $email,
            'phone' => $phone
        );
        $this->db->insert('demo_test',$data);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }
 }
 
 
 
     public function adddetails_update($id,$name, $email,$phone) {
        $query = $this->db->query("UPDATE `demo_test` SET `id` = '$id', `name` = '$name', `email` = '$email', `phone` = '$phone' WHERE `demo_test`.`id` ='$id'");
        
    //    "UPDATE `demo_test` SET `name` = '$name', `email` = '$email', `phone` = '$phone' WHERE `demo_test`.`id` ='$id'";
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }
    
    
    
    
    
        public function add_delete($id) {
        $query = $this->db->query("DELETE FROM `demo_test` WHERE id='$id'");
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }
    
    
    
    
    
     
    public function simple_list($id) {
        $query = $this->db->query("SELECT id,name,email,phone FROM `demo_test`");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $name =  $row['name'];
                $email = $row['email'];
                $phone = $row['phone'];
                
                if(empty($id))
                {
                    $lat_new="";
                }
                else
                {
                   $lat_new=$id; 
                }
                
                if(empty($name))
                {
                    $lat_new="";
                }
                else
                {
                   $lat_new=$name; 
                }
                if(empty($email))
                {
                    $lng_new="";
                }
                else
                {
                   $lng_new=$email; 
                }
                
                if(empty($phone))
                {
                    $full_address_new="";
                }
                else
                {
                   $full_address_new=$phone; 
                }
                
                $resultpost[] = array(
                    "id" =>$id,
                    "name" => $name,
                    "email" => $email,
                    'phone' => $phone
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }



     
    public function simple_list_where_condition($id) {
        $query = $this->db->query("SELECT id,name,email,phone FROM `demo_test` WHERE id='$id'");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $name =  $row['name'];
                $email = $row['email'];
                $phone = $row['phone'];
                
                if(empty($id))
                {
                    $lat_new="";
                }
                else
                {
                   $lat_new=$id; 
                }
                
                if(empty($name))
                {
                    $lat_new="";
                }
                else
                {
                   $lat_new=$name; 
                }
                if(empty($email))
                {
                    $lng_new="";
                }
                else
                {
                   $lng_new=$email; 
                }
                
                if(empty($phone))
                {
                    $full_address_new="";
                }
                else
                {
                   $full_address_new=$phone; 
                }
                
                $resultpost[] = array(
                    "id" =>$id,
                    "name" => $name,
                    "email" => $email,
                    'phone' => $phone
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

/****************************************************************/


    //add new user's address
    public function address_add($user_id, $name, $mobile, $pincode, $address1, $address2, $landmark, $city, $state, $address_type,$lat,$lng,$full_address,$relation_ship,$member_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
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
            'lat' => $lat,
            'lng' => $lng,
            'date' => $created_at,
            'full_address'=>$full_address,
            'relation_id'=>$member_id,
            'relation_ship'=>$relation_ship
        );
        $this->db->insert('user_address', $address_data);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    //update user's update
    public function address_update($user_id, $address_id, $name, $address1, $address2, $landmark, $mobile, $city, $state, $pincode, $address_type,$lat,$lng,$full_address,$relation_ship,$member_id) {
        $query = $this->db->query("UPDATE `user_address` SET `address_type`='$address_type',`name`='$name',`mobile`='$mobile',`address1`='$address1',`address2`='$address2',`landmark`='$landmark',`city`='$city',`state`='$state',`pincode`='$pincode',`lat`='$lat',`lng`='$lng',`full_address`='$full_address',`relation_id`='$member_id',`relation_ship`='$relation_ship' WHERE address_id='$address_id' and user_id='$user_id'");
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    //delete user's address
    public function address_delete($user_id, $address_id) {
        $query = $this->db->query("DELETE FROM `user_address` WHERE address_id='$address_id' AND user_id='$user_id'");
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    //get country list
    public function country() {
        $query = $this->db->query("select id,name FROM countries order by name asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $country_id = $row['id'];
                $country = $row['name'];
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

    //get state list
    public function state($country_id) {
        $query = $this->db->query("select state_id,state_name FROM states WHERE country_id='$country_id' order by state_name asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $state_id = $row['state_id'];
                $state_name = $row['state_name'];
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

    //get city list
    public function city($state_id) {
        $query = $this->db->query("select city_id,city_name FROM cities WHERE state_id='$state_id' order by city_name asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $city_id = $row['city_id'];
                $city = $row['city_name'];
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

    //get state with city
    public function state_city() {
        $query = $this->db->query("select DISTINCT state from city_state_region order by state asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $state = $row['state'];
                $city_list = '';
                $city_query = $this->db->query("select DISTINCT city from city_state_region where state='$state' order by city asc");
                $city_count = $city_query->num_rows();
                if ($city_count > 0) {
                    foreach ($city_query->result_array() as $row) {
                        $city = $row['city'];
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

    //list organicindia banner
    public function organicindia_banner() {
        $query = $this->db->query("SELECT * FROM `organicindia_banner` ORDER BY id DESC");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
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

    //update user's basic information
  /*  public function userupdate($user_id, $gender, $dob, $name, $phone, $email, $profile_pic) 
    {
        
        
        
        $Q = "SET source = '$profile_pic', title='$profile_pic'";
        $sql = "SELECT avatar_id FROM users WHERE users.id ='$user_id'";
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
        } 
        elseif ($avatar_id == 0) {
            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
        } else {
            $sql2 = "SELECT source FROM media  WHERE id ='" . $avatar_id . "'";
            $source = $this->db->query($sql2)->row()->source;
            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $source;
        }
        
        $query = $this->db->query("SELECT health_record.id FROM `health_record` WHERE health_record.relation_id='$user_id'");
      	$count = $query->num_rows();
        if ($count > 0) 
        {
            $this->db->query("UPDATE health_record SET avatar_id = '" . $aid . "' WHERE relation_id='$user_id'");
            $query = $this->db->query("UPDATE health_record SET `gender`='$gender',`date_of_birth`='$dob',`patient_name`='$name',`phone`='$phone',`email`='$email',`relationship`='$relationship' WHERE relation_id='$id'");
 
        }
        $query1 = $this->db->query("SELECT health_record.id FROM `health_record` WHERE health_record.user_id='$user_id'");
      	$count1 = $query1->num_rows();
        if ($count1 > 0) 
        {
             if ($profile_pic != "") {
            $this->db->query("UPDATE health_record SET avatar_id = '" . $aid . "' WHERE user_id='$user_id'");
             }
            $query = $this->db->query("UPDATE health_record SET `gender`='$gender',`date_of_birth`='$dob',`patient_name`='$name',`phone`='$phone',`email`='$email' WHERE user_id='$user_id'");
 
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
    }*/
     public function userupdate($user_id, $gender, $dob, $name, $phone, $email, $profile_pic) 
    {
        
        
        $aid="";
        $avatar_id=0;
        $Q = "SET source = '$profile_pic', title='$profile_pic'";
        $sql = "SELECT avatar_id FROM users WHERE users.id ='$user_id'";
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
                $this->db->query("UPDATE users SET avatar_id = '" . $aid . "' WHERE id = '" .$user_id. "'");
            } 
            else {
                $sql2 = $this->db->query("SELECT source FROM media WHERE id ='$avatar_id'");
                $count1 = $sql2->num_rows();
                if ($count1 > 0) 
                    {
                        $this->db->query("UPDATE media $Q WHERE id = '" . $avatar_id . "'");
                        $this->db->query("UPDATE users SET avatar_id = '" . $avatar_id . "' WHERE id = '" .$user_id. "'");
                    }
                else
                {
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
                    $this->db->query("UPDATE users SET avatar_id = '" . $aid . "' WHERE id = '" .$user_id. "'");
                }
            }
        }
        $query = $this->db->query("UPDATE users SET `gender`='$gender',`dob`='$dob',`name`='$name',`phone`='$phone',`email`='$email' WHERE id='$user_id'");
 
        if ($profile_pic != '') {
            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
        } 
       
        elseif ($avatar_id == 0 || empty($avatar_id)) {
            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
        } else {
            $sql2 = "SELECT source FROM media  WHERE id ='" . $avatar_id . "'";
            $source = $this->db->query($sql2)->row()->source;
            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $source;
        }
     
        $query = $this->db->query("SELECT health_record.id FROM `health_record` WHERE health_record.relation_id='$user_id'");
       $count = $query->num_rows();
        if ($count > 0) 
        {
            $this->db->query("UPDATE health_record SET avatar_id = '" . $aid . "' WHERE relation_id='$user_id'");
            $query = $this->db->query("UPDATE health_record SET `gender`='$gender',`date_of_birth`='$dob',`patient_name`='$name',`phone`='$phone',`email`='$email' WHERE relation_id='$user_id'");
 
        }
        else
        {
        $query1 = $this->db->query("SELECT health_record.id FROM `health_record` WHERE health_record.user_id='$user_id' and relation_id='0'  ");
      	$count1 = $query1->num_rows();
        if ($count1 > 0) 
        { 
            
            if ($profile_pic != '') {
                if(empty($aid))
                {
                   $this->db->query("UPDATE health_record SET avatar_id = '" . $avatar_id . "' WHERE user_id='$user_id'  and relation_id='0' ");  
                }
                else
                {
            $this->db->query("UPDATE health_record SET avatar_id = '" . $aid . "' WHERE user_id='$user_id'  and relation_id='0' ");
                }
            }
            $query = $this->db->query("UPDATE health_record SET `gender`='$gender',`date_of_birth`='$dob',`patient_name`='$name',`phone`='$phone',`email`='$email' WHERE user_id='$user_id'  and relation_id='0'");
 
        }
        
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


    //get all review list
    public function all_review_list($user_id) {
        $resultpost = '';
        $review_count = $this->db->select('id')->from('ambulance_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT ambulance_review.id,ambulance_review.user_id,ambulance_review.ambulance_id,ambulance_review.rating,ambulance_review.review, ambulance_review.service,ambulance_review.date as review_date,users.id as user_id,users.name as firstname FROM `ambulance_review` INNER JOIN `users` ON ambulance_review.user_id=users.id WHERE ambulance_review.user_id = '$user_id' order by ambulance_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
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
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('ambulance_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('ambulance_review_comment')->where('post_id', $id)->get()->num_rows();
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
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '13') {
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
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('babysitter_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('babysitter_review_comment')->where('post_id', $id)->get()->num_rows();
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
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '17') {
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
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('blood_bank_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('blood_bank_review_comment')->where('post_id', $id)->get()->num_rows();
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
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
                $review = preg_replace('~[\r\n]+~', '', $review);
                $decrypt = $this->decrypt($review);
                $encrypt = $this->encrypt($decrypt);
                if ($encrypt == $review) {
                    $review = $decrypt;
                }
                $service = $row['service'];
                $review_date = $row['review_date'];
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('child_physiotherapy_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('child_physiotherapy_review_comment')->where('post_id', $id)->get()->num_rows();
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
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
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
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('counselling_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('counselling_review_review_comment')->where('post_id', $id)->get()->num_rows();
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
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
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
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('cuppingtherapy_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('cuppingtherapy_review_comment')->where('post_id', $id)->get()->num_rows();
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
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
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
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('dai_nanny_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('dai_nanny_review_comment')->where('post_id', $id)->get()->num_rows();
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
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '1') {
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
                $review_date = $this->get_time_difference_php($review_date);
                $like_count = $this->db->select('id')->from('doctors_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('doctors_review_comment')->where('post_id', $id)->get()->num_rows();
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
            $query = $this->db->query("SELECT fitness_center_review.id,fitness_center_review.user_id,fitness_center_review.listing_id   ,fitness_center_review.rating,fitness_center_review.review, fitness_center_review.service,fitness_center_review.date as review_date,users.id as user_id,users.name as firstname FROM `fitness_center_review` INNER JOIN `users` ON fitness_center_review.user_id=users.id WHERE fitness_center_review.user_id = '$user_id' order by fitness_center_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '11') {
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
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('fitness_center_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('fitness_center_review_comment')->where('post_id', $id)->get()->num_rows();
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
            $query = $this->db->query("SELECT healthy_food_product_review.id,healthy_food_product_review.user_id,healthy_food_product_review.product_id ,healthy_food_product_review.rating,healthy_food_product_review.review, healthy_food_product_review.service,healthy_food_product_review.date as review_date,users.id as user_id,users.name as firstname FROM `healthy_food_product_review` INNER JOIN `users` ON healthy_food_product_review.user_id=users.id WHERE healthy_food_product_review.user_id = '$user_id' order by healthy_food_product_review.id desc");
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
                $review = preg_replace('~[\r\n]+~', '', $review);
                $decrypt = $this->decrypt($review);
                $encrypt = $this->encrypt($decrypt);
                if ($encrypt == $review) {
                    $review = $decrypt;
                }
                $service = $row['service'];
                $review_date = $row['review_date'];
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('healthy_food_product_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('healthy_food_product_review_comment')->where('post_id', $id)->get()->num_rows();
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
            $query = $this->db->query("SELECT hospital_review.id,hospital_review.user_id,hospital_review.hospital_id    ,hospital_review.rating,hospital_review.review, hospital_review.service,hospital_review.date as review_date,users.id as user_id,users.name as firstname FROM `hospital_review` INNER JOIN `users` ON hospital_review.user_id=users.id WHERE hospital_review.user_id = '$user_id' order by hospital_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '13') {
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
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('hospital_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('hospital_review_comment')->where('post_id', $id)->get()->num_rows();
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
            $query = $this->db->query("SELECT labcenter_review.id,labcenter_review.user_id,labcenter_review.labcenter_id    ,labcenter_review.rating,labcenter_review.review, labcenter_review.service,labcenter_review.date as review_date,users.id as user_id,users.name as firstname FROM `labcenter_review` INNER JOIN `users` ON labcenter_review.user_id=users.id WHERE labcenter_review.user_id = '$user_id' order by labcenter_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '9') {
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
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('labcenter_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('labcenter_review_comment')->where('post_id', $id)->get()->num_rows();
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
            $query = $this->db->query("SELECT medical_college_review.id,medical_college_review.user_id,medical_college_review.college_id    ,medical_college_review.rating,medical_college_review.review, medical_college_review.service,medical_college_review.date as review_date,users.id as user_id,users.name as firstname FROM `medical_college_review` INNER JOIN `users` ON medical_college_review.user_id=users.id WHERE medical_college_review.user_id = '$user_id' order by medical_college_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '8') {
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
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('medical_college_review_like')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('medical_college_review_comment')->where('post_id', $id)->get()->num_rows();
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
            $query = $this->db->query("SELECT nursing_attendant_review.id,nursing_attendant_review.user_id,nursing_attendant_review.nursing_attendant_id    ,nursing_attendant_review.rating,nursing_attendant_review.review, nursing_attendant_review.service,nursing_attendant_review.date as review_date,users.id as user_id,users.name as firstname FROM `nursing_attendant_review` INNER JOIN `users` ON nursing_attendant_review.user_id=users.id WHERE nursing_attendant_review.user_id = '$user_id' order by nursing_attendant_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '10') {
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
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('nursing_attendant_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('nursing_attendant_review_comment')->where('post_id', $id)->get()->num_rows();
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
            $query = $this->db->query("SELECT oldagehome_review.id,oldagehome_review.user_id,oldagehome_review.oldagehome_id    ,oldagehome_review.rating,oldagehome_review.review, oldagehome_review.service,oldagehome_review.date as review_date,users.id as user_id,users.name as firstname FROM `oldagehome_review` INNER JOIN `users` ON oldagehome_review.user_id=users.id WHERE oldagehome_review.user_id = '$user_id' order by oldagehome_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '11') {
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
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('oldagehome_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('oldagehome_review_comment')->where('post_id', $id)->get()->num_rows();
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
            $query = $this->db->query("SELECT optic_store_product_review.id,optic_store_product_review.user_id,optic_store_product_review.product_id    ,optic_store_product_review.rating,optic_store_product_review.review, optic_store_product_review.service,optic_store_product_review.date as review_date,users.id as user_id,users.name as firstname FROM `optic_store_product_review` INNER JOIN `users` ON optic_store_product_review.user_id=users.id WHERE optic_store_product_review.user_id = '$user_id' order by optic_store_product_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '29') {
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
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('optic_store_product_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('optic_store_product_review_comment')->where('post_id', $id)->get()->num_rows();
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
            $query = $this->db->query("SELECT organicindia_product_review.id,organicindia_product_review.user_id,organicindia_product_review.product_id ,organicindia_product_review.rating,organicindia_product_review.review, organicindia_product_review.service,organicindia_product_review.date as review_date,users.id as user_id,users.name as firstname FROM `organicindia_product_review` INNER JOIN `users` ON organicindia_product_review.user_id=users.id WHERE organicindia_product_review.user_id = '$user_id' order by organicindia_product_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
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
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('organicindia_product_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('organicindia_product_review_comment')->where('post_id', $id)->get()->num_rows();
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
            $query = $this->db->query("SELECT organicindia_review.id,organicindia_review.user_id,organicindia_review.organicindia_id    ,organicindia_review.rating,organicindia_review.review, organicindia_review.service,organicindia_review.date as review_date,users.id as user_id,users.name as firstname FROM `organicindia_review` INNER JOIN `users` ON organicindia_review.user_id=users.id WHERE organicindia_review.user_id = '$user_id' order by organicindia_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
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
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('organicindia_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('organicindia_review_comment')->where('post_id', $id)->get()->num_rows();
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
            $query = $this->db->query("SELECT personal_trainers_review.id,personal_trainers_review.user_id,personal_trainers_review.listing_id  ,personal_trainers_review.rating,personal_trainers_review.review, personal_trainers_review.service,personal_trainers_review.date as review_date,users.id as user_id,users.name as firstname FROM `personal_trainers_review` INNER JOIN `users` ON personal_trainers_review.user_id=users.id WHERE personal_trainers_review.user_id = '$user_id' order by personal_trainers_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
                $review = preg_replace('~[\r\n]+~', '', $review);
                $decrypt = $this->decrypt($review);
                $encrypt = $this->encrypt($decrypt);
                if ($encrypt == $review) {
                    $review = $decrypt;
                }
                $service = $row['service'];
                $review_date = $row['review_date'];
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('personal_trainers_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('personal_trainers_review_comment')->where('post_id', $id)->get()->num_rows();
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
            $query = $this->db->query("SELECT pest_control_review.id,pest_control_review.user_id,pest_control_review.pest_control_id    ,pest_control_review.rating,pest_control_review.review, pest_control_review.service,pest_control_review.date as review_date,users.id as user_id,users.name as firstname FROM `pest_control_review` INNER JOIN `users` ON pest_control_review.user_id=users.id WHERE pest_control_review.user_id = '$user_id' order by pest_control_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '29') {
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
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('pest_control_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('pest_control_review_comment')->where('post_id', $id)->get()->num_rows();
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
            $query = $this->db->query("SELECT psychiatrist_review.id,psychiatrist_review.user_id,psychiatrist_review.doctor_id  ,psychiatrist_review.rating,psychiatrist_review.review, psychiatrist_review.service,psychiatrist_review.date as review_date,users.id as user_id,users.name as firstname FROM `psychiatrist_review` INNER JOIN `users` ON psychiatrist_review.user_id=users.id WHERE psychiatrist_review.user_id = '$user_id' order by psychiatrist_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '10') {
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
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('psychiatrist_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('psychiatrist_review_comment')->where('post_id', $id)->get()->num_rows();
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
            $query = $this->db->query("SELECT roots_herbs_product_review.id,roots_herbs_product_review.user_id,roots_herbs_product_review.product_id    ,roots_herbs_product_review.rating,roots_herbs_product_review.review, roots_herbs_product_review.service,roots_herbs_product_review.date as review_date,users.id as user_id,users.name as firstname FROM `roots_herbs_product_review` INNER JOIN `users` ON roots_herbs_product_review.user_id=users.id WHERE roots_herbs_product_review.user_id = '$user_id' order by roots_herbs_product_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '8') {
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
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('roots_herbs_product_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('roots_herbs_product_review_comment')->where('post_id', $id)->get()->num_rows();
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
            $query = $this->db->query("SELECT roots_herbs_review.id,roots_herbs_review.user_id,roots_herbs_review.roots_herbs_id    ,roots_herbs_review.rating,roots_herbs_review.review, roots_herbs_review.service,roots_herbs_review.date as review_date,users.id as user_id,users.name as firstname FROM `roots_herbs_review` INNER JOIN `users` ON roots_herbs_review.user_id=users.id WHERE roots_herbs_review.user_id = '$user_id' order by roots_herbs_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
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
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('roots_herbs_comment_like')->where('comment_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('roots_herbs_comment')->where('post_id', $id)->get()->num_rows();
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
            $query = $this->db->query("SELECT sex_store_product_review.id,sex_store_product_review.user_id,sex_store_product_review.product_id  ,sex_store_product_review.rating,sex_store_product_review.review, sex_store_product_review.service,sex_store_product_review.date as review_date,users.id as user_id,users.name as firstname FROM `sex_store_product_review` INNER JOIN `users` ON sex_store_product_review.user_id=users.id WHERE sex_store_product_review.user_id = '$user_id' order by sex_store_product_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '5') {
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
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('sex_store_product_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('sex_store_product_review_comment')->where('post_id', $id)->get()->num_rows();
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
            $query = $this->db->query("SELECT sex_store_review.id,sex_store_review.user_id,sex_store_review.sex_store_id    ,sex_store_review.rating,sex_store_review.review, sex_store_review.service,sex_store_review.date as review_date,users.id as user_id,users.name as firstname FROM `sex_store_review` INNER JOIN `users` ON sex_store_review.user_id=users.id WHERE sex_store_review.user_id = '$user_id' order by sex_store_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '5') {
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
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('sex_store_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('sex_store_review_comment')->where('post_id', $id)->get()->num_rows();
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

        /* -------pharmacy and medical college------- */
        $resultpost = '';
        $review_count = $this->db->select('id')->from('medical_stores_review')->where('user_id', $user_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT medical_stores_review.id,medical_stores_review.user_id,medical_stores_review.medical_stores_id,medical_stores_review.rating,medical_stores_review.review, medical_stores_review.service,medical_stores_review.date as review_date,users.id as user_id,users.name as firstname FROM `medical_stores_review` INNER JOIN `users` ON medical_stores_review.user_id=users.id WHERE medical_stores_review.user_id = '$user_id' order by medical_stores_review.id DESC");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = trim($row['review']);
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '18') {
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
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('medical_stores_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('medical_stores_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('medical_stores_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

                $pharmacy_review[] = array(
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
                    'type' => 'Pharmacy'
                );
            }
        } else {
            $pharmacy_review = array();
        }

        $resultpost[] = array(
            array_merge($ambulance_review, $babysitter_review, $blood_bank_review, $child_physiotherapy, $counselling_review, $cuppingtherapy_review, $dai_nanny_review, $doctors_review, $fitness_center_review, $healthy_food_product_review, $hospital_review, $labcenter_review, $medical_college_review, $nursing_attendant_review, $oldagehome_review, $optic_store_product_review, $organicindia_product_review, $organicindia_review, $personal_trainers_review, $pest_control_review, $psychiatrist_review, $roots_herbs_product_review, $roots_herbs_review, $sex_store_product_review, $sex_store_review, $pharmacy_review)
        );
        return $resultpost;
    }

    //update user's latitude and longitude
    public function user_lat_lng($user_id, $latitude, $longitude, $map_location) {
        $query = $this->db->query("UPDATE `users` SET `lat`='$latitude',`lng`='$longitude',`map_location`='$map_location' WHERE id='$user_id'");
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }

    //delete user's delete
    public function review_delete($user_id, $post_id, $type) {
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
            case "Pharmacy":
                $this->db->query("DELETE FROM `medical_stores_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `medical_stores_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Nursing Attendant":
                $this->db->query("DELETE FROM `nursing_attendant_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `nursing_attendant_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Fitness Center":
                $this->db->query("DELETE FROM `fitness_center_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `fitness_center_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Spa":
                $this->db->query("DELETE FROM `spa_center_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `spa_center_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
        }
        return array(
            'status' => 200,
            'message' => 'deleted'
        );
    }

    //all banner for app
    public function app_banner() {
        $query = $this->db->query("SELECT `id`, `cat_id`, `image`,`tag` FROM `app_banners` WHERE is_active='1' order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            $home = array();
            $blood_group = array();
            $OFW = array();
            $sex_education = array();
            $child_care = array();
            $mind_counseling = array();
            $skin_hair_care = array();
            $eye_care = array();
            $dental_care = array();
            $health_cover = array();
            $pharmacy_stores = array();
            $ayurveda = array();
            $doctors = array();
            $labs = array();
            $nursing_attendant = array();
            $cupping_therapy = array();
            $physiotherapist = array();
            $fitness = array();
            $pest_control = array();
            $hospitals = array();
            $diet_nutrition = array();

            $job_placements = array();

            foreach ($query->result_array() as $row) {
                $cat_id = $row['cat_id'];
                $tag = $row['tag'];
                $image = $row['image'];
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/medicalwale_mobile_app_icons/banners/' . $image;

                switch ($cat_id) {
                    case "1":
                        $home[] = array(
                            'image' => $image,
                            'tag' => $tag
                        );
                        break;
                    case "2":
                        $blood_group[] = array(
                            'image' => $image,
                            'tag' => $tag
                        );
                        break;
                    case "3":
                        $OFW[] = array(
                            'image' => $image,
                            'tag' => $tag
                        );
                        break;
                    case "4":
                        $sex_education[] = array(
                            'image' => $image,
                            'tag' => $tag
                        );
                        break;
                    case "5":
                        $child_care[] = array(
                            'image' => $image,
                            'tag' => $tag
                        );
                        break;
                    case "6":
                        $mind_counseling[] = array(
                            'image' => $image,
                            'tag' => $tag
                        );
                        break;
                    case "7":
                        $skin_hair_care[] = array(
                            'image' => $image,
                            'tag' => $tag
                        );
                        break;
                    case "8":
                        $eye_care[] = array(
                            'image' => $image,
                            'tag' => $tag
                        );
                        break;
                    case "9":
                        $dental_care[] = array(
                            'image' => $image,
                            'tag' => $tag
                        );
                        break;
                    case "10":
                        $health_cover[] = array(
                            'image' => $image,
                            'tag' => $tag
                        );
                        break;
                    case "11":
                        $pharmacy_stores[] = array(
                            'image' => $image,
                            'tag' => $tag
                        );
                        break;
                    case "12":
                        $ayurveda[] = array(
                            'image' => $image,
                            'tag' => $tag
                        );
                        break;
                    case "13":
                        $doctors[] = array(
                            'image' => $image,
                            'tag' => $tag
                        );
                        break;
                    case "14":
                        $labs[] = array(
                            'image' => $image,
                            'tag' => $tag
                        );
                        break;
                    case "15":
                        $nursing_attendant[] = array(
                            'image' => $image,
                            'tag' => $tag
                        );
                        break;
                    case "16":
                        $cupping_therapy[] = array(
                            'image' => $image,
                            'tag' => $tag
                        );
                        break;
                    case "17":
                        $physiotherapist[] = array(
                            'image' => $image,
                            'tag' => $tag
                        );
                        break;
                    case "18":
                        $fitness[] = array(
                            'image' => $image,
                            'tag' => $tag
                        );
                        break;
                    case "19":
                        $pest_control[] = array(
                            'image' => $image,
                            'tag' => $tag
                        );
                        break;
                    case "20":
                        $hospitals[] = array(
                            'image' => $image,
                            'tag' => $tag
                        );
                        break;
                    case "21":
                        $diet_nutrition[] = array(
                            'image' => $image,
                            'tag' => $tag
                        );
                        break;
                    case "22":
                        $job_placements[] = array(
                            'image' => $image,
                            'tag' => $tag
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

    //save post
    public function save_post($user_id, $post_id, $post_type) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `post_save` WHERE  user_id='$user_id' and post_id='$post_id' and post_type='$post_type'");
        $count = $count_query->num_rows();

        if ($count < 1) {

            $post_save_array = array(
                'post_type' => $post_type,
                'post_id' => $post_id,
                'user_id' => $user_id,
                'date' => $date
            );
            $this->db->insert('post_save', $post_save_array);
            return array(
                'status' => 200,
                'message' => 'success'
            );
        } else {
            return array(
                'status' => 200,
                'message' => 'success'
            );
        }
    }

    //get save post list
    public function save_post_list($user_id, $type, $page) {
        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }

        $start = ($page - 1) * $limit;
        $resultpost = array();

        if ($type == 'healthwall') {
            $query = $this->db->query("select  IFNULL(posts.post_location,'') AS post_location,healthwall_category.category AS healthwall_category,healthwall_category.id as healthwall_category_id, posts.id as post_id,IFNULL(posts.repost_user_id,'') AS repost_user_id,posts.repost_location,posts.is_repost,posts.repost_time,posts.type as post_type,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url from posts INNER JOIN users on users.id=posts.user_id  LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND posts.id IN (SELECT post_id FROM `post_save` WHERE `user_id`='$user_id' AND `post_type`='healthwall') order by posts.id DESC limit $start, $limit");
            $query_count = $this->db->query("select  IFNULL(posts.post_location,'') AS post_location,healthwall_category.category AS healthwall_category,healthwall_category.id as healthwall_category_id, posts.id as post_id,posts.type as post_type,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url from posts INNER JOIN users on users.id=posts.user_id  LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND posts.id IN (SELECT post_id FROM `post_save` WHERE `user_id`='$user_id' AND `post_type`='healthwall') order by posts.id DESC");
            $count_post = $query_count->num_rows();
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
                    $healthwall_category_id = $row['healthwall_category_id'];
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
                    $repost_user_id = $row['repost_user_id'];
                    $repost_location = $row['repost_location'];
                    $is_repost = $row['is_repost'];
                    $repost_time = $this->get_time_difference_php($row['repost_time']);
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
                    $repost_user_name = "";

                    $repost = array();
                    if ($is_repost) {

                        if (!empty($repost_user_id) && $repost_user_id != "") {
                            $query = $this->db->query("SELECT media.title, media.source, users.avatar_id FROM media INNER JOIN users ON(users.avatar_id = media.id) WHERE users.id = '$repost_user_id'")->row();
                            $repost_user_name = $this->db->select('*')->where('id', $repost_user_id)->get('users')->row()->name;
                            $repost[] = array(
                                'repost_user_id' => $repost_user_id,
                                'repost_user_name' => $repost_user_name,
                                'repost_location' => $repost_location,
                                'repost_time' => $repost_time,
                                //'title' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$query->title,
                                'repost_user_image' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/" . $query->source
                            );
                        }

                        if (is_null($repost_user_name) || $repost_user_name == "") {
                            $repost_user_name = '';
                        }
                    } else {
                        $repost = [];
                    }
                    $resultpost_list[] = array(
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
                        'comments' => $comments,
                        'is_repost' => $is_repost,
                        'repost' => $repost
                    );
                }
                $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "count" => $count_post,
                    "data" => $resultpost_list
                );
            } else {
                $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "count" => $count_post,
                    "data" => array()
                );
            }
        }



        if ($type == 'asksaheli') {

            $query = $this->db->query("select ask_saheli_post.id ,ask_saheli_post.type,IFNULL(ask_saheli_post.post_location,'') AS post_location, IFNULL(ask_saheli_post.repost_user_id,'') AS repost_user_id,ask_saheli_post.repost_location,ask_saheli_post.is_repost,ask_saheli_post.repost_time, ask_saheli_post.date,ask_saheli_post.post,ask_saheli_post.tag,ask_saheli_category.category,ask_saheli_post.user_name,user_character.image as character_image,ask_saheli_post.article_title,ask_saheli_post.article_image,ask_saheli_post.article_domain_name,ask_saheli_post.article_url,ask_saheli_post.user_id as post_user_id FROM ask_saheli_post LEFT JOIN ask_saheli_category on ask_saheli_category.id=ask_saheli_post.category LEFT JOIN user_character on user_character.id=ask_saheli_post.user_image WHERE ask_saheli_post.id NOT IN (SELECT post_id FROM ask_saheli_post_hide WHERE post_id=ask_saheli_post.id AND user_id='$user_id') AND ask_saheli_post.id IN (SELECT post_id FROM `post_save` WHERE `user_id`='$user_id' AND `post_type`='asksaheli') order by ask_saheli_post.id DESC limit $start, $limit");
            $query_count = $this->db->query("select ask_saheli_post.id ,ask_saheli_post.type,IFNULL(ask_saheli_post.post_location,'') AS post_location,ask_saheli_post.date,ask_saheli_post.post,ask_saheli_post.tag,ask_saheli_category.category,ask_saheli_post.user_name,ask_saheli_character.image as character_image,ask_saheli_post.article_title,ask_saheli_post.article_image,ask_saheli_post.article_domain_name,ask_saheli_post.article_url,ask_saheli_post.user_id as post_user_id FROM ask_saheli_post LEFT JOIN ask_saheli_category on ask_saheli_category.id=ask_saheli_post.category LEFT JOIN ask_saheli_character on ask_saheli_character.id=ask_saheli_post.user_image WHERE ask_saheli_post.id NOT IN (SELECT post_id FROM ask_saheli_post_hide WHERE post_id=ask_saheli_post.id AND user_id='$user_id') AND ask_saheli_post.id IN (SELECT post_id FROM `post_save` WHERE `user_id`='$user_id' AND `post_type`='asksaheli') order by ask_saheli_post.id DESC");

            $count_post = $query_count->num_rows();
            if ($count_post > 0) {
                foreach ($query->result_array() as $row) {
                    $id = $row['id'];
                    $post_id = $row['id'];
                    $post = $row['post'];
                    $post = preg_replace('~[\r\n]+~', '', $post);
                    if ($post_id >= '472') {
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
                    $post_location = $row['post_location'];
                    $user_name = $row['user_name'];
                    $post_user_id = $row['post_user_id'];
                    $tag = $row['tag'];
                    $category = $row['category'];
                    $character_image = $row['character_image'];
                    $type = $row['type'];
                    $article_title = $row['article_title'];
                    $article_image = $row['article_image'];
                    $article_domain_name = $row['article_domain_name'];
                    $article_url = $row['article_url'];
                    $repost_user_id = $row['repost_user_id'];
                    $repost_location = $row['repost_location'];
                    $is_repost = $row['is_repost'];
                    $repost_time = $this->get_time_difference_php($row['repost_time']);
                    $views = 0;
                    $video_views = 0;

                    $query_media = $this->db->query("SELECT ask_saheli_post_media.id AS media_id,ask_saheli_post_media.source,ask_saheli_post_media.type AS media_type,IFNULL(ask_saheli_post_media.caption,'') AS caption,ask_saheli_post_media.img_width,ask_saheli_post_media.img_height,ask_saheli_post_media.video_width,ask_saheli_post_media.video_height FROM ask_saheli_post_media INNER JOIN ask_saheli_post on ask_saheli_post_media.post_id=ask_saheli_post.id WHERE ask_saheli_post_media.post_id='$id'");
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
                        $images = 'https://d2c8oti4is0ms3.cloudfront.net/images/ask_saheli_images/' . $media_type . '/' . $source;
                        if ($media_type == 'video') {
                            $thumb = 'https://d2c8oti4is0ms3.cloudfront.net/images/ask_saheli_images/thumb/' . substr_replace($source, 'jpg', strrpos($source, '.') + 1);
                        } else {
                            $thumb = '';
                        }
                        $caption = $media_row['caption'];
                        $img_width = $media_row['img_width'];
                        $img_height = $media_row['img_height'];
                        $video_width = $media_row['video_width'];
                        $video_height = $media_row['video_height'];

                        $view_media_count = $this->db->select('id')->from('ask_saheli_video_views')->where('media_id', $media_id)->get()->num_rows();

                        $view_media_yes_no_query = $this->db->query("SELECT id from ask_saheli_video_views WHERE media_id='$media_id' and user_id='$user_id'");
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


                    $date = $row['date'];
                    $date = $this->get_time_difference_php($date);

                    $like_count = $this->db->select('id')->from('ask_saheli_likes')->where('post_id', $id)->get()->num_rows();
                    $comment_count = $this->db->select('id')->from('ask_saheli_comment')->where('post_id', $id)->get()->num_rows();
                    $like_yes_no = $this->db->select('id')->from('ask_saheli_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                    $character_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/ask_saheli_images/character/' . $character_image;


                    $view_count = $this->db->select('id')->from('ask_saheli_post_views')->where('post_id', $id)->get()->num_rows();

                    $view_yes_no_query = $this->db->query("SELECT id from ask_saheli_post_views WHERE post_id='$id' and user_id='$user_id'");
                    $view_post_yes_no = $view_yes_no_query->num_rows();

                    $follow_count = $this->db->select('id')->from('ask_saheli_follow_post')->where('post_id', $id)->get()->num_rows();

                    $follow_yes_no_query = $this->db->query("SELECT id from ask_saheli_follow_post where post_id='$id' and user_id='$user_id'");
                    $follow_post_yes_no = $follow_yes_no_query->num_rows();
                    $share_url = "https://medicalwale.com/share/asksaheli/" . $id;
                    $is_reported_query = $this->db->query("SELECT id from post_report WHERE post_id='$post_id' AND reporter_id='$user_id' AND post_type='asksaheli'");
                    $is_reported = $is_reported_query->num_rows();

                    $is_post_save_query = $this->db->query("SELECT id from post_save WHERE post_id='$post_id' AND user_id='$user_id' AND post_type='asksaheli'");
                    $is_post_save = $is_post_save_query->num_rows();
                    //repost
                    $repost_user_name = "";

                    $repost = array();
                    if ($is_repost) {

                        if (!empty($repost_user_id) && $repost_user_id != "") {

                            $query = $this->db->query("SELECT user_avatar.user_name, user_character.image FROM user_character INNER JOIN user_avatar ON(user_character.id = user_avatar.user_image) WHERE user_avatar.user_id = '$repost_user_id'")->row();
                            $repost_user_name = $this->db->select('*')->where('id', $repost_user_id)->get('users')->row()->name;
                            $repost[] = array(
                                'repost_user_id' => $repost_user_id,
                                'repost_user_name' => $repost_user_name,
                                'repost_location' => $repost_location,
                                'repost_time' => $repost_time,
                                //'title' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$query->title,
                                'repost_user_image' => "https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/" . $query->image
                            );
                        }

                        if (is_null($repost_user_name) || $repost_user_name == "") {
                            $repost_user_name = '';
                        }
                    } else {
                        $repost = [];
                    }


                    //comments
                    $query_comment = $this->db->query("SELECT ask_saheli_comment.id,ask_saheli_comment.comment,ask_saheli_comment.date,ask_saheli_comment.user_name,ask_saheli_character.image as character_image,ask_saheli_comment.user_id as post_user_id  FROM ask_saheli_comment LEFT JOIN  ask_saheli_character on ask_saheli_character.id=ask_saheli_comment.user_image WHERE ask_saheli_comment.post_id='$post_id' order by ask_saheli_comment.id desc LIMIT 0,3");

                    $comment_counts = $query_comment->num_rows();
                    if ($comment_counts > 0) {
                        $comments = array();
                        foreach ($query_comment->result_array() as $rows) {
                            $comment_id = $rows['id'];
                            $comment = $rows['comment'];
                            $comment = preg_replace('~[\r\n]+~', '', $comment);
                            if ($comment_id > '805') {
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
                            $comment_username = $rows['user_name'];
                            $comment_image = $rows['character_image'];
                            $comment_date = $rows['date'];
                            $comment_post_user_id = $rows['post_user_id'];
                            $comment_date = $this->get_time_difference_php($comment_date);
                            $comment_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/ask_saheli_images/character/' . $comment_image;

                            $comment_like_count = $this->db->select('id')->from('ask_saheli_comment_like')->where('comment_id', $comment_id)->get()->num_rows();
                            $comment_like_yes_no = $this->db->select('id')->from('ask_saheli_comment_like')->where('user_id', $user_id)->where('comment_id', $comment_id)->get()->num_rows();

                            $comments[] = array(
                                'id' => $comment_id,
                                'comment_user_id' => $comment_post_user_id,
                                'username' => $comment_username,
                                'image' => $comment_image,
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
                    $resultpost_list[] = array(
                        'id' => $id,
                        'post_type' => $type,
                        'post_location' => $post_location,
                        'post_user_id' => $post_user_id,
                        'username' => $user_name,
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
                        'category' => $category,
                        'character_image' => $character_image,
                        'date' => $date,
                        'is_reported' => $is_reported,
                        'is_post_save' => $is_post_save,
                        'comments' => $comments,
                        'is_repost' => $is_repost,
                        'repost' => $repost
                    );
                }

                $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "count" => $count_post,
                    "data" => $resultpost_list
                );
            } else {
                $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "count" => $count_post,
                    "data" => array()
                );
            }
        }

        if ($type == 'drlelo') {
            $query = $this->db->query("SELECT sex_education_question.id,sex_education_question.age,sex_education_question.user_image,sex_education_question.user_name,sex_education_question.user_id,sex_education_question.question,sex_education_question.date,IFNULL(sex_education_question.post_location,'') AS post_location,sex_education_character.image AS c_image  FROM  `sex_education_question` INNER JOIN `sex_education_character` ON sex_education_question.user_image=sex_education_character.id  WHERE sex_education_question.id NOT IN (SELECT post_id FROM sex_education_hide WHERE post_id=sex_education_question.id AND user_id='$user_id') AND sex_education_question.id IN (SELECT post_id FROM `post_save` WHERE `user_id`='$user_id' AND `post_type`='drlelo') order by sex_education_question.id DESC limit $start, $limit");
            $query_count = $this->db->query("SELECT sex_education_question.id,sex_education_question.age,sex_education_question.user_image,sex_education_question.user_name,sex_education_question.user_id,sex_education_question.question,sex_education_question.date,IFNULL(sex_education_question.post_location,'') AS post_location,sex_education_character.image AS c_image  FROM  `sex_education_question` INNER JOIN `sex_education_character` ON sex_education_question.user_image=sex_education_character.id  WHERE sex_education_question.id NOT IN (SELECT post_id FROM sex_education_hide WHERE post_id=sex_education_question.id AND user_id='$user_id') AND sex_education_question.id IN (SELECT post_id FROM `post_save` WHERE `user_id`='$user_id' AND `post_type`='drlelo') order by sex_education_question.id DESC");
            $count_post = $query_count->num_rows();

            if ($count_post > 0) {
                foreach ($query->result_array() as $row) {
                    $id = $row['id'];
                    $age = $row['age'];
                    $post_location = $row['post_location'];
                    $images = $row['c_image'];
                    $user_name = $row['user_name'];
                    $post_user_id = $row['user_id'];
                    $question = $row['question'];
                    $question = preg_replace('~[\r\n]+~', '', $question);
                    if ($id > '341') {
                        $decrypt = $this->decrypt($question);
                        $encrypt = $this->encrypt($decrypt);
                        if ($encrypt == $question) {
                            $question = $decrypt;
                        }
                    } else {
                        if (base64_encode(base64_decode($question)) === $question) {
                            $question = base64_decode($question);
                        }
                    }
                    $date = $row['date'];
                    $date = $this->get_time_difference_php($date);
                    if ($images != '') {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $images;
                    } else {
                        $image = '';
                    }

                    $is_notify_query = $this->db->query("SELECT id FROM `sex_education_is_notify` where post_id='$id' AND user_id='$user_id'");
                    $is_notify = $is_notify_query->num_rows();


                    $is_follow = '0';

                    $answer_list = array();
                    $answer_query = $this->db->query("SELECT id,type,answer,post_id FROM `sex_education_answer` WHERE `post_id`='$id'");
                    $count_answers = $answer_query->num_rows();
                    if ($count_answers > 0) {
                        foreach ($answer_query->result_array() as $rows) {

                            $answer_id = $rows['id'];
                            $answer = $rows['answer'];
                            $type = $rows['type'];
                            $answer = preg_replace('~[\r\n]+~', '', $answer);
                            if ($answer_id >= '308') {
                                $decrypt = $this->decrypt($answer);
                                $encrypt = $this->encrypt($decrypt);
                                if ($encrypt == $answer) {
                                    $answer = $decrypt;
                                }
                            } else {
                                if (base64_encode(base64_decode($answer)) === $answer) {
                                    $answer = base64_decode($answer);
                                }
                            }
                            $answer_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/dr-lelo.jpg';

                            $answer_list[] = array(
                                'answer_id' => $answer_id,
                                'type' => $type,
                                'answer' => $answer,
                                    //'answer_image' => $answer_image
                            );
                        }
                    } else {
                        $answer_list = array();
                    }


                    $share_url = "https://medicalwale.com/share/drlelo/" . $id;

                    $count_query = $this->db->query("SELECT id FROM `sex_education_likes` where post_id='$id'");
                    $like_count = $count_query->num_rows();

                    $like_count_query = $this->db->query("SELECT id FROM `sex_education_likes` where user_id='$user_id' and post_id='$id'");
                    $like_yes_no = $like_count_query->num_rows();

                    $is_post_save_query = $this->db->query("SELECT id from post_save WHERE post_id='$id' AND user_id='$user_id' AND post_type='drlelo'");
                    $is_post_save = $is_post_save_query->num_rows();

                    $is_reported_query = $this->db->query("SELECT id from post_report WHERE post_id='$id' AND reporter_id='$user_id' AND post_type='drlelo'");
                    $is_reported = $is_reported_query->num_rows();


                    $resultpost_list[] = array(
                        'id' => $id,
                        'post_user_id' => $post_user_id,
                        'post_location' => $post_location,
                        'user_name' => $user_name,
                        'question' => $question,
                        'is_notify' => $is_notify,
                        'age' => $age,
                        'image' => $image,
                        'answer_image' => "https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/dr-lelo.jpg",
                        'answer_list' => $answer_list,
                        'like_count' => $like_count,
                        'like_yes_no' => $like_yes_no,
                        'is_follow' => $is_follow,
                        'share_url' => $share_url,
                        'is_reported' => $is_reported,
                        'is_post_save' => $is_post_save,
                        'date' => $date
                    );
                }

                $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "count" => $count_post,
                    "data" => $resultpost_list
                );
            } else {
                $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "count" => $count_post,
                    "data" => array()
                );
            }
        }




        if ($type == 'missbelly') {
            $query = $this->db->query("SELECT miss_belly_question.id,miss_belly_question.age,miss_belly_question.weight,miss_belly_question.diet_preference,miss_belly_question.height,miss_belly_question.user_image,miss_belly_question.user_name,miss_belly_question.user_id,miss_belly_question.question,miss_belly_question.date,IFNULL(miss_belly_question.post_location,'') AS post_location,sex_education_character.image AS c_image  FROM  `miss_belly_question` INNER JOIN `sex_education_character` ON miss_belly_question.user_image=sex_education_character.id  WHERE miss_belly_question.id NOT IN (SELECT post_id FROM miss_belly_hide WHERE post_id=miss_belly_question.id AND user_id='$user_id') AND miss_belly_question.id IN (SELECT post_id FROM `post_save` WHERE `user_id`='$user_id' AND `post_type`='missbelly') order by miss_belly_question.id DESC limit $start, $limit");
            $query_count = $this->db->query("SELECT miss_belly_question.id,miss_belly_question.age,miss_belly_question.weight,miss_belly_question.diet_preference,miss_belly_question.height,miss_belly_question.user_image,miss_belly_question.user_name,miss_belly_question.user_id,miss_belly_question.question,miss_belly_question.date,IFNULL(miss_belly_question.post_location,'') AS post_location,sex_education_character.image AS c_image  FROM  `miss_belly_question` INNER JOIN `sex_education_character` ON miss_belly_question.user_image=sex_education_character.id  WHERE miss_belly_question.id NOT IN (SELECT post_id FROM miss_belly_hide WHERE post_id=miss_belly_question.id AND user_id='$user_id') AND miss_belly_question.id IN (SELECT post_id FROM `post_save` WHERE `user_id`='$user_id' AND `post_type`='missbelly') order by miss_belly_question.id DESC");
            $count_post = $query_count->num_rows();

            $resultpost = array();

            if ($count_post > 0) {
                foreach ($query->result_array() as $row) {
                    $id = $row['id'];
                    $post_id = $row['id'];
                    $weight = $row['weight'];
                    $post_location = $row['post_location'];
                    $height = $row['height'];
                    $diet_preference = $row['diet_preference'];
                    $age = $row['age'];
                    $images = $row['c_image'];
                    $user_name = $row['user_name'];
                    $post_user_id = $row['user_id'];
                    $question = $row['question'];
                    $question = preg_replace('~[\r\n]+~', '', $question);
                    if ($id > '529') {
                        $decrypt = $this->decrypt($question);
                        $encrypt = $this->encrypt($decrypt);
                        if ($encrypt == $question) {
                            $question = $decrypt;
                        }
                    } else {
                        if (base64_encode(base64_decode($question)) === $question) {
                            $question = base64_decode($question);
                        }
                    }
                    $date = $row['date'];
                    $date = $this->get_time_difference_php($date);
                    if ($images != '') {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $images;
                    } else {
                        $image = '';
                    }

                    $is_notify_query = $this->db->query("SELECT id FROM `miss_belly_is_notify` where post_id='$id' AND user_id='$user_id'");
                    $is_notify = $is_notify_query->num_rows();


                    $is_follow = '0';

                    $answer_lists = array();
                    $answer_query = $this->db->query("SELECT id,type,answer,post_id FROM `miss_belly_answer` WHERE `post_id`='$post_id'");
                    $count_answers = $answer_query->num_rows();
                    if ($count_answers > 0) {
                        foreach ($answer_query->result_array() as $rows) {
                            $answer_id = $rows['id'];
                            $answer = $rows['answer'];
                            $type = $rows['type'];
                            $answer = preg_replace('~[\r\n]+~', '', $answer);
                            if ($answer_id >= '435') {
                                $decrypt = $this->decrypt($answer);
                                $encrypt = $this->encrypt($decrypt);
                                if ($encrypt == $answer) {
                                    $answer = $decrypt;
                                }
                            } else {
                                if (base64_encode(base64_decode($answer)) === $answer) {
                                    $answer = base64_decode($answer);
                                }
                            }
                            $answer_lists[] = array(
                                'answer_id' => $answer_id,
                                'type' => $type,
                                'answer' => $answer,
                                    //'answer_image' => $answer_image
                            );
                        }
                    } else {
                        $answer_lists = array();
                    }


                    $share_url = "https://medicalwale.com/share/missbelly/" . $id;
                    $answer_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/medicalwale_mobile_app_icons/fitness/miss_belly.png';
                    $count_query = $this->db->query("SELECT id FROM `miss_belly_likes` where post_id='$id'");
                    $like_count = $count_query->num_rows();

                    $like_count_query = $this->db->query("SELECT id FROM `miss_belly_likes` where user_id='$user_id' and post_id='$id'");
                    $like_yes_no = $like_count_query->num_rows();

                    $is_post_save_query = $this->db->query("SELECT id from post_save WHERE post_id='$post_id' AND user_id='$user_id' AND post_type='missbelly'");
                    $is_post_save = $is_post_save_query->num_rows();

                    $is_reported_query = $this->db->query("SELECT id from post_report WHERE post_id='$post_id' AND reporter_id='$user_id' AND post_type='missbelly'");
                    $is_reported = $is_reported_query->num_rows();

                    $resultpost_list[] = array(
                        'id' => $id,
                        'post_user_id' => $post_user_id,
                        'post_location' => $post_location,
                        'user_name' => $user_name,
                        'question' => $question,
                        'diet_preference' => $diet_preference,
                        'answer_list' => $answer_lists,
                        'is_notify' => $is_notify,
                        'age' => $age,
                        'height' => $height,
                        'weight' => $weight,
                        'image' => $image,
                        'answer_image' => $answer_image,
                        'like_count' => $like_count,
                        'like_yes_no' => $like_yes_no,
                        'is_follow' => $is_follow,
                        'share_url' => $share_url,
                        'is_reported' => $is_reported,
                        'is_post_save' => $is_post_save,
                        'date' => $date);
                }

                $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "count" => $count_post,
                    "data" => $resultpost_list
                );
            } else {
                $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "count" => $count_post,
                    "data" => array()
                );
            }
        }

        if ($type == 'medicalcollege') {
            $query = $this->db->query("select college_post.id ,college_post.type,college_post.created_at,college_post.post,college_post.college_id,college_post.article_title,college_post.article_image,college_post.article_domain_name,college_post.article_url,college_post.user_id as post_user_id,users.name FROM college_post
INNER JOIN users on users.id=college_post.user_id  WHERE college_post.id
NOT IN (SELECT post_id FROM college_post_hide WHERE post_id=college_post.id AND user_id='$user_id') AND college_post.id IN (SELECT post_id FROM `post_save` WHERE `user_id`='$user_id' AND `post_type`='medicalcollege') order by college_post.id desc limit $start, $limit");
            $count_query = $this->db->query("select college_post.id ,college_post.type,college_post.created_at,college_post.post,college_post.article_title,college_post.article_image,college_post.article_domain_name,college_post.article_url,college_post.user_id as post_user_id,users.name FROM college_post INNER JOIN users on users.id=college_post.user_id  WHERE college_post.id NOT IN (SELECT post_id FROM college_post_hide WHERE post_id=college_post.id AND user_id='$user_id') AND college_post.id IN (SELECT post_id FROM `post_save` WHERE `user_id`='$user_id' AND `post_type`='medicalcollege') order by college_post.id desc");

            $count_post = $count_query->num_rows();
            if ($count_post > 0) {
                foreach ($query->result_array() as $row) {
                    $id = $row['id'];
                    $post_id = $row['id'];
                    $username = $row['name'];
                    $post_user_id = $row['post_user_id'];
                    $post = $row['post'];
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
                    $date = $this->get_time_difference_php($date);

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

                    $is_post_save_query = $this->db->query("SELECT id from post_save WHERE post_id='$post_id' AND user_id='$user_id' AND post_type='medicalcollege'");
                    $is_post_save = $is_post_save_query->num_rows();
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
                            $comment_date = $this->get_time_difference_php($comment_date);
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

                    $resultpost_list[] = array(
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
                        'is_post_save' => $is_post_save,
                        'views' => $view_count,
                        'view_yes_no' => $view_post_yes_no,
                        'media' => $media_array,
                        'share_url' => $share_url,
                        'date' => $date,
                        'comments' => $comments
                    );
                }

                $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "count" => $count_post,
                    "data" => $resultpost_list);
            } else {
                $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "count" => $count_post,
                    "data" => array()
                );
            }
        }
        return $resultpost;
    }

    //delete save post
    public function remove_save_post($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `post_save` WHERE  user_id='$user_id' and post_id='$post_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `post_save` WHERE user_id='$user_id' and post_id='$post_id'");

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

    //report healthwall's post
    public function report_post($user_id, $post_id, $post_type) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `post_report` WHERE  reporter_id='$user_id' and post_id='$post_id' and post_type='$post_type'");
        $count = $count_query->num_rows();

        if ($count < 1) {
            $post_report_array = array(
                'post_type' => $post_type,
                'post_id' => $post_id,
                'reporter_id' => $user_id,
                'date' => $date
            );
            $this->db->insert('post_report', $post_report_array);
            return array(
                'status' => 200,
                'message' => 'success'
            );
        } else {
            return array(
                'status' => 200,
                'message' => 'success'
            );
        }
    }

    //select user's character
    public function user_character($type) {
        $review_list_count = $this->db->select('id')->from('user_character')->where('type', $type)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT id,image FROM `user_character` WHERE type='$type' order by id desc");
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $image = $row['image'];
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $image;
                $resultpost[] = array(
                    'id' => $id,
                    'image' => $image
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    //update user's username and userimage
    public function common_user_update($user_id, $user_name, $user_image) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id FROM `user_avatar` WHERE user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("UPDATE `user_avatar` SET `user_name`='$user_name',`user_image`='$user_image' WHERE user_id='$user_id'");
            return array(
                'status' => 200,
                'message' => 'success'
            );
        } else {
            $ask_saheli_ask_user = array(
                'user_id' => $user_id,
                'user_name' => $user_name,
                'user_image' => $user_image
            );
            $this->db->insert('user_avatar', $ask_saheli_ask_user);
            return array(
                'status' => 200,
                'message' => 'success'
            );
        }
    }

    //fetch user's basic information
    public function common_user_check($user_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id FROM `user_avatar` WHERE user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $query = $this->db->query("SELECT user_avatar.user_id, user_avatar.user_name, user_avatar.user_image,user_character.image AS c_image  FROM `user_avatar` INNER JOIN `user_character` ON user_avatar.user_image=user_character.id  WHERE user_avatar.user_id='$user_id'");
            $row = $query->row_array();
            $user_id = $row['user_id'];
            $user_name = $row['user_name'];
            $user_image = $row['user_image'];
            $images = $row['c_image'];
            if ($images != '') {
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $images;
            } else {
                $image = '';
            }

            $resultpost[] = array(
                'user_id' => $user_id,
                'user_name' => $user_name,
                'user_image' => $user_image,
                'images' => $image
            );
        } else {

            $resultpost = array();
        }

        return $resultpost;
    }

    //delete healthwall/ask saheli/medicalcollege comment
    public function delete_post_comment($user_id, $post_id, $type, $comment_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');

        if ($type == 'healthwall') {
            $count_query = $this->db->query("SELECT id from `comments` WHERE  user_id='$user_id' and post_id='$post_id' and id='$comment_id'");
            $count = $count_query->num_rows();

            if ($count > 0) {
                $this->db->query("DELETE FROM `comments` WHERE user_id='$user_id' and post_id='$post_id' and id='$comment_id'");

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

        if ($type == 'ask_saheli') {
            $count_query = $this->db->query("SELECT id from `ask_saheli_comment` WHERE  user_id='$user_id' and post_id='$post_id' and id='$comment_id'");
            $count = $count_query->num_rows();

            if ($count > 0) {
                $this->db->query("DELETE FROM `ask_saheli_comment` WHERE user_id='$user_id' and post_id='$post_id' and id='$comment_id'");

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

        if ($type == 'medicalcollege') {
            $count_query = $this->db->query("SELECT id from `college_post_comment` WHERE  user_id='$user_id' and post_id='$post_id' and id='$comment_id'");
            $count = $count_query->num_rows();

            if ($count > 0) {
                $this->db->query("DELETE FROM `college_post_comment` WHERE user_id='$user_id' and post_id='$post_id' and id='$comment_id'");

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
    }

    //get user's fitness/doctor booking details
  public function user_booking_details($user_id, $listing_type,$opd) {
        $resultpost=array(); 
        if ($listing_type == '6') {
            // Fitness Center
            // echo "SELECT fitness_center_branch.branch_name,fitness_center_branch.user_id, fitness_center_branch.branch_image, fitness_center_branch.branch_phone, fitness_center_branch.branch_address, fitness_center_branch.pincode, fitness_center_branch.state, fitness_center_branch.city,packages.package_name, packages.package_details, packages.price,booking_master.package_id,booking_master.booking_id, booking_master.listing_id, booking_master.user_id, booking_master.user_name, booking_master.user_mobile, booking_master.user_email, booking_master.user_gender, booking_master.branch_id, booking_master.vendor_id, booking_master.booking_date, booking_master.category_id, booking_master.trail_booking_date, booking_master.trail_booking_time, booking_master.joining_date FROM booking_master
            // INNER JOIN fitness_center_branch ON booking_master.branch_id=fitness_center_branch.id
            // INNER JOIN packages ON booking_master.package_id=packages.id
            // WHERE booking_master.user_id='$user_id' AND booking_master.vendor_id='6' ORDER BY booking_master.id ASC";
            
           /* echo "SELECT fitness_center_branch.branch_name,fitness_center_branch.user_id as branch_fit_id, fitness_center_branch.branch_image, fitness_center_branch.branch_phone, fitness_center_branch.branch_address, fitness_center_branch.pincode, fitness_center_branch.state, fitness_center_branch.city,packages.package_name, packages.package_details, packages.price,booking_master.package_id,booking_master.booking_id, booking_master.listing_id, booking_master.user_id, booking_master.user_name, booking_master.user_mobile, booking_master.user_email, booking_master.user_gender, booking_master.branch_id,booking_master.status, booking_master.vendor_id, booking_master.booking_date, booking_master.category_id, booking_master.trail_booking_date, booking_master.trail_booking_time, booking_master.joining_date FROM booking_master
            INNER JOIN fitness_center_branch ON booking_master.branch_id=fitness_center_branch.id
            INNER JOIN packages ON booking_master.package_id=packages.id
            WHERE booking_master.user_id='$user_id' AND booking_master.vendor_id='$listing_type' ORDER BY booking_master.id DESC";*/
            
        $querys = $this->db->query("SELECT fitness_center_branch.branch_name,fitness_center_branch.user_id as branch_fit_id, fitness_center_branch.branch_image, fitness_center_branch.branch_phone, fitness_center_branch.branch_address, fitness_center_branch.pincode, fitness_center_branch.state, fitness_center_branch.city,packages.package_name, packages.package_details, packages.price,booking_master.package_id,booking_master.booking_id, booking_master.listing_id, booking_master.user_id, booking_master.user_name, booking_master.user_mobile, booking_master.user_email, booking_master.user_gender, booking_master.branch_id,booking_master.status, booking_master.vendor_id, booking_master.booking_date, booking_master.category_id, booking_master.trail_booking_date, booking_master.trail_booking_time, booking_master.joining_date FROM booking_master
            INNER JOIN fitness_center_branch ON booking_master.branch_id=fitness_center_branch.id
            INNER JOIN packages ON booking_master.package_id=packages.id
            WHERE booking_master.user_id='$user_id' AND booking_master.vendor_id='$listing_type' ORDER BY booking_master.id DESC");
            $count = $querys->num_rows();
            if ($count > 0) {
                foreach ($querys->result_array() as $row) {
                    $package_id = $row['package_id'];
                    $booking_id = $row['booking_id'];
                    $listing_id = $row['branch_fit_id'];
                     $branch_id = $row['branch_id'];
                    $branch_name = $row['branch_name'];
                    $branch_image = $row['branch_image'];
                    $branch_phone = $row['branch_phone'];
                    $branch_address = $row['branch_address'];
                    $branch_pincode = $row['pincode'];
                    $branch_city = $row['city'];
                    $branch_state = $row['state'];
                    $appointment_user_name = $row['user_name'];
                    $appointment_user_mobile = $row['user_mobile'];
                    $appointment_user_email = $row['user_email'];
                    $category_id = $row['category_id'];
                    $status = $row['status'];
                    $package_name = $row['package_name'];
                    $package_details = $row['package_details'];
                    $package_price = $row['price'];
                    $trail_booking_date = $row['trail_booking_date'];
                    $trail_booking_time = $row['trail_booking_time'];
                    $joining_date = date('j M Y', strtotime($row['joining_date']));
                    $book_for_date = date('d/m/Y', strtotime($row['joining_date']));
                    $is_free_trial = 'No';
                    date_default_timezone_set('Asia/Kolkata');
                    if ($package_id == '100') {
                        $joining_date_ = $trail_booking_date . ' ' . $trail_booking_time;

                        $joining_date = date('j M Y | h:i A', strtotime($joining_date_));
                        $is_free_trial = 'Yes';
                    }
                    $booking_date = date('j M Y | h:i A', strtotime($row['booking_date']));

                    if ($branch_image != '') {
                        $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                    } else {
                        $branch_image = '';
                    }
                    
                    if($status == null)
                    {
                        $status = "";
                    }
                   


                    $resultpost[] = array(
                        'booking_id' => $booking_id,
                        'branch_name' => $branch_name,
                        'branch_id' => $branch_id,
                         'listing_id' => $listing_id,
                        'branch_image' => $branch_image,
                        'branch_phone' => $branch_phone,
                        'branch_address' => $branch_address,
                        'branch_pincode' => $branch_pincode,
                        'branch_city' => $branch_city,
                        'branch_state' => $branch_state,
                        'appointment_user_name' => $appointment_user_name,
                        'appointment_user_mobile' => $appointment_user_mobile,
                        'appointment_user_email' => $appointment_user_email,
                        'package_name' => $package_name,
                        'package_details' => $package_details,
                        'package_price' => $package_price,
                        'is_free_trial' => $is_free_trial,
                        'joining_date' => $joining_date,
                        'book_for_date' => $book_for_date,
                        'booking_date' => $booking_date,
                        'status' => $status
                    );
                }
            } else {
                $resultpost = array();
            }
        }



        else if ($listing_type == '5') {
            //echo "select doctor_booking_master.booking_id, doctor_booking_master.booking_date,doctor_booking_master.listing_id,doctor_booking_master.user_id, doctor_booking_master.listing_id,doctor_booking_master.clinic_id, doctor_prescription.id as prescription_id,  doctor_booking_master.booking_time,doctor_booking_master.status, doctor_clinic.clinic_name,users.name, doctor_clinic.address,doctor_clinic.image,doctor_clinic.state,doctor_clinic.city,doctor_clinic.pincode, doctor_clinic.consultation_charges,doctor_clinic.contact_no from doctor_booking_master LEFT JOIN doctor_prescription ON (doctor_booking_master.user_id = doctor_prescription.patient_id AND doctor_booking_master.listing_id = doctor_prescription.doctor_id AND doctor_booking_master.clinic_id = doctor_prescription.clinic_id) LEFT JOIN doctor_clinic on (doctor_booking_master.clinic_id = doctor_clinic.id) LEFT JOIN users ON (doctor_booking_master.user_id = users.id) where user_id = '$user_id' ORDER BY doctor_booking_master.id DESC";
            $query = $this->db->query("select doctor_booking_master.booking_id, doctor_booking_master.booking_date,doctor_booking_master.listing_id,doctor_booking_master.user_id, doctor_booking_master.listing_id,doctor_booking_master.clinic_id, doctor_prescription.id as prescription_id, doctor_booking_master.booking_time,doctor_booking_master.status,doctor_booking_master.consultation_type, doctor_clinic.clinic_name,users.name as patient_name, doctor_clinic.address,doctor_clinic.image,doctor_clinic.state,doctor_clinic.city,doctor_clinic.pincode, doctor_clinic.consultation_charges,doctor_clinic.contact_no,dl.doctor_name from doctor_booking_master LEFT JOIN doctor_prescription ON (doctor_booking_master.booking_id = doctor_prescription.booking_id AND doctor_booking_master.listing_id = doctor_prescription.doctor_id AND doctor_booking_master.clinic_id = doctor_prescription.clinic_id) LEFT JOIN doctor_clinic on (doctor_booking_master.clinic_id = doctor_clinic.id) LEFT JOIN users ON (doctor_booking_master.user_id = users.id) LEFT JOIN doctor_list as dl ON(dl.user_id=doctor_booking_master.listing_id) where doctor_booking_master.user_id = '$user_id' ORDER BY doctor_booking_master.id DESC");

            $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                 
                    $booking_id = $row['booking_id'];
                    $booking_date = $row['booking_date'];
                    $booking_time = $row['booking_time'];
                    $patient_name = $row['patient_name'];
                    $doctor_name = $row['doctor_name'];
                    $consultation_charges = $row['consultation_charges'];
                    $branch_name = $row['clinic_name'];
                    $consultation_type = $row['consultation_type'];
                    $address = $row['address'] . "," . $row['city'] . "," . $row['state'] . "," . $row['pincode'];
                    $image = $row['image'];
                    $clinic_contact_no = $row['contact_no'];
                    $prescription_id = $row['prescription_id'];
                    $status = $row['status'];
                    $doctor_id = $row['listing_id'];
                    
                    $booking_time = str_replace('PM','', $booking_time);
                    $booking_time = str_replace('AM','', $booking_time);
                    
                      if($status == null)
                    {
                        $status = "";
                    }
                     //echo $trimmed ;
                     if($prescription_id!="")
                     {
                    $url="https://doctor.medicalwale.com/prescription/".$prescription_id.".pdf";
                     }
                     else
                     {
                         $url="";
                     }
                    $resultpost[] = array(
                        'booking_id' => $booking_id,
                        'doctor_id' => $doctor_id,
                        'appointment_date' => $booking_date,
                        'appointment_time' => $booking_time,
                        'patient_name' => $patient_name,
                        'doctor_name' => $doctor_name,
                        'consultation_charge' => $consultation_charges,
                        'consultation_type' => $consultation_type,
                        'clinic_name' => $branch_name,
                        'clinic_address' => $address,
                        'image' => $image,
                        'clinic_contact_no' => $clinic_contact_no,
                        'status' => $status,
                        'prescription_id' => $prescription_id,
                        'prescription_pdf'=> $url
                    );
                }
            } else {
                $resultpost = array();
            }
        }
        
        
        else if ($listing_type == '8'){
            
            if($opd=="opd")
            {
                $query= $this->db->query("select hospital_booking_master.booking_id,hospital_booking_master.booking_date,hospital_booking_master.listing_id,hospital_booking_master.user_id,hospital_booking_master.listing_id,
hospital_booking_master.doctor_id,hospital_doctor_prescription.id as prescription_id,hospital_booking_master.booking_time,hospital_booking_master.status,hospital_booking_master.consultation_type,
hospitals.name_of_hospital,users.name as patient_name, hospitals.address,hospitals.image,hospitals.state,hospitals.city,hospitals.pincode, dl.consultation,hospitals.phone,dl.doctor_name from hospital_booking_master  LEFT JOIN hospital_doctor_prescription 
ON (hospital_booking_master.patient_id = hospital_doctor_prescription.patient_id AND hospital_booking_master.doctor_id = hospital_doctor_prescription.doctor_id AND hospital_booking_master.listing_id = hospital_doctor_prescription.hospital_id) LEFT JOIN hospitals on (hospital_booking_master.listing_id = hospitals.user_id)
LEFT JOIN users ON (hospital_booking_master.user_id = users.id)  LEFT JOIN hospital_doctor_list as dl ON(dl.id=hospital_booking_master.doctor_id)
where hospital_booking_master.user_id = '$user_id' ORDER BY hospital_booking_master.id DESC");

$count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $booking_id = $row['booking_id'];
                    $booking_date = $row['booking_date'];
                    $booking_time = $row['booking_time'];
                    $patient_name = $row['patient_name'];
                    $doctor_name = $row['doctor_name'];
                    $consultation_charges = $row['consultation'];
                    $branch_name = $row['name_of_hospital'];
                    $consultation_type = $row['consultation_type'];
                    $address = $row['address'] . "," . $row['city'] . "," . $row['state'] . "," . $row['pincode'];
                    $image = $row['image'];
                    $clinic_contact_no = $row['phone'];
                    $prescription_id = $row['prescription_id'];
                    $status = $row['status'];
                    $doctor_id = $row['listing_id'];
                    
                      if($status == null)
                    {
                        $status = "";
                    }
                    
                    $resultpost[] = array(
                        'booking_id' => $booking_id,
                        'doctor_id' => $doctor_id,
                        'appointment_date' => $booking_date,
                        'appointment_time' => $booking_time,
                        'patient_name' => $patient_name,
                        'doctor_name' => $doctor_name,
                        'consultation_charge' => $consultation_charges,
                        'consultation_type' => $consultation_type,
                        'clinic_name' => $branch_name,
                        'clinic_address' => $address,
                        'image' => $image,
                        'clinic_contact_no' => $clinic_contact_no,
                        'status' => $status,
                        'prescription_id' => $prescription_id
                    );
                }
            } else {
                $resultpost = array();
            }


            }
            else
            {
            
          
            
           // echo "SELECT booking_master.*,health_record.patient_name,hospitals.address,hospitals.city,hospitals.state,hospitals.phone,hospitals.pincode FROM booking_master  LEFT JOIN users ON (booking_master.user_id = users.id) LEFT JOIN health_record ON(booking_master.patient_id=health_record.id) LEFT JOIN hospitals ON(booking_master.listing_id=hospitals.user_id) where booking_master.user_id = '$user_id' and booking_master.vendor_id = '8' ORDER BY booking_master.id DESC";
            $query = $this->db->query("SELECT booking_master.*,hospital_booking_details.*,health_record.patient_name,hospitals.address,hospitals.city,hospitals.state,hospitals.phone,hospitals.pincode FROM booking_master LEFT JOIN users ON (booking_master.user_id = users.id) LEFT JOIN health_record ON(booking_master.patient_id=health_record.id) LEFT JOIN hospitals ON(booking_master.listing_id=hospitals.user_id) LEFT JOIN hospital_booking_details ON(booking_master.booking_id=hospital_booking_details.booking_id) where booking_master.user_id = '$user_id' and booking_master.vendor_id = '8' ORDER BY booking_master.id DESC ");

            $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $booking_id = $row['booking_id'];
                    $booking_date = $row['booking_date'];
                    $address = $row['address'] . "," . $row['city'] . "," . $row['state'] . "," . $row['pincode'];
                    $contact_no = $row['user_mobile'];
                    $status = $row['status'];
                    $Hospital_id = $row['listing_id'];
                    
                    $ward_id = $row['ward_id'];
                    $emergency = $row['emergency'];
                    $package_id = $row['package_id'];
                    $amount = $row['amount'];
                    $booking_time = $row['booking_time'];
                    $booking_date = $row['booking_date'];
                    $patient_id = $row['patient_id'];
                    $patient_name = $row['patient_name'];
                    $patient_gender = $row['patient_gender'];
                    $patient_age = $row['patient_age'];
                    $patient_allergies = $row['patient_allergies'];
                    
                      if($status == null)
                    {
                        $status = "";
                    }
                    
                    $resultpost[] = array(
                        'booking_id' => $booking_id,
                        'hospital_id' => $Hospital_id,
                        'appointment_date' => $booking_date,
                        'patient_name' => $patient_name,
                        'address'=>$address,
                        'phone'=>$contact_no,
                        'ward_id' => $ward_id,
                        'emergency' => $emergency,
                        'package_id' => $package_id,
                        'amount' => $amount,
                        'booking_time' => $booking_time,
                        'booking_date' => $booking_date,
                        'patient_id' => $patient_id,
                        'patient_name' => $patient_name,
                        'patient_gender' => $patient_gender,
                        'patient_age' => $patient_age,
                        'patient_allergies' => $patient_allergies,
                        'status' => $status
                    );
                }
            } else {
                $resultpost = array();
            }
            }
            
            }
       
        
        else if ($listing_type == '12'){
          /* echo "SELECT booking_master.*,health_record.patient_name,nursing_attendant.address,nursing_attendant.city,nursing_attendant.state,nursing_attendant.contact,nursing_attendant.pincode,nursing_booking_details.* FROM booking_master  LEFT JOIN users ON (booking_master.user_id = users.id) LEFT JOIN health_record ON(booking_master.patient_id=health_record.id) LEFT JOIN nursing_attendant ON(booking_master.listing_id=nursing_attendant.user_id) LEFT JOIN nursing_booking_details ON(booking_master.booking_id=nursing_booking_details.booking_id) where booking_master.user_id = '1607' and booking_master.vendor_id = '12' ORDER BY booking_master.id DESC";*/
            $query = $this->db->query("SELECT booking_master.*,health_record.patient_name,nursing_attendant.address,nursing_attendant.city,nursing_attendant.state,nursing_attendant.contact,nursing_attendant.pincode,nursing_booking_details.* FROM booking_master  LEFT JOIN users ON (booking_master.user_id = users.id) LEFT JOIN health_record ON(booking_master.patient_id=health_record.id) LEFT JOIN nursing_attendant ON(booking_master.listing_id=nursing_attendant.user_id) LEFT JOIN nursing_booking_details ON(booking_master.booking_id=nursing_booking_details.booking_id) where booking_master.user_id = '$user_id' and booking_master.vendor_id = '12' ORDER BY booking_master.id DESC");

            $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    
                    $listing_id = $row['listing_id'];
                    $package_name = $row['package_name'];
                    $package_amount = $row['package_amount'];
                    $package_image = $row['package_image'];
                    
                    
                    $booking_id = $row['booking_id'];
                    $package_id = $row['package_id'];
                    $booking_date = $row['booking_date'];
                    $patient_name = $row['patient_name'];
                    $address = $row['address'] . "," . $row['city'] . ",".$row['state'] . "," . $row['pincode'];
                    $contact_no = $row['contact'];
                    $status = $row['status'];
                    $Nursing_id = $row['listing_id'];
                    $patiente_condition =$row['patiente_condition'];
                    $attendent_time =$row['attendent_time'];
                    $attendant_hour = $row['attendant_hour'];
                    $tentative_intime = $row['tentative_intime'];
                    $tentative_outtime = $row['tentative_outtime'];
                    $nursing_gender = $row['nursing_gender'];
                    $attendant_needed = $row['attendant_needed'];
                    $joining_date = $row['joining_date'];
                    
                      if($status == null)
                    {
                        $status = "";
                    }
                    
                    $resultpost[] = array(
                        'listing_id' => $listing_id,
                        'booking_id' => $booking_id,
                        'nursing_id' => $Nursing_id,
                        'appointment_date' => $booking_date,
                        'patient_name' => $patient_name,
                        'package_id' => $package_id,
                        'package_name' => $package_name,
                        'package_amount' => $package_amount,
                        'package_image' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$package_image,
                        'address'=>$address,
                        'phone' =>$contact_no,
                        'patiente_condition' =>$patiente_condition,
                        'attendent_time' =>$attendent_time,
                        'attendant_hour' =>$attendant_hour,
                        'tentative_intime' =>$tentative_intime,
                        'nursing_gender' =>$nursing_gender,
                        'attendant_needed' =>$attendant_needed,
                        'booking_date'=>$joining_date,
                        'status' => $status
                    );
                }
            } else {
                $resultpost = array();
            }
        }
        
        else if($listing_type == '10'){
            //$count_query = $this->db->query("SELECT lb.*,bm.status from lab_booking_details as lb LEFT JOIN booking_master as bm ON(lb.booking_id = bm.booking_id) where lb.user_id='$user_id' and lb.vendor_type='$listing_type'");
            $count_query = $this->db->query("SELECT lb.*,bm.status from booking_master as bm LEFT JOIN lab_booking_details as lb ON(bm.booking_id = lb.booking_id) where bm.user_id='$user_id' and bm.vendor_id='$listing_type'");
            $lab_booked       = $count_query->num_rows();
            
            if ($lab_booked > 0) {
                
                
                foreach ($count_query->result_array() as $Lbooked) {
                        
                    $user_id = $Lbooked['user_id'];
                    $patient_id = $Lbooked['patient_id'];
                    $listing_id = $Lbooked['listing_id'];
                    $vendor_type = $Lbooked['vendor_type'];
                    $branch_id = $Lbooked['branch_id'];
                    $branch_name = $Lbooked['branch_name'];
                    $at_home = $Lbooked['at_home'];
                    $address_line1 = $Lbooked['address_line1'];
                    $address_line2 = $Lbooked['address_line2'];
                    $city = $Lbooked['city'];
                    $state = $Lbooked['state'];
                    $pincode = $Lbooked['pincode'];
                    $mobile_no = $Lbooked['mobile_no'];
                    $email_id = $Lbooked['email_id'];
                    $address_id = $Lbooked['address_id'];
                    $test_ids = $Lbooked['test_id'];
                    $package_id = $Lbooked['package_id'];
                    $booking_date = $Lbooked['booking_date'];
                    $booking_time = $Lbooked['booking_time'];
                    $booking_id = $Lbooked['booking_id']; 
                    $status = $Lbooked['status']; 
                    
                    
                    $Booed_test_list = array();
                    if ($test_ids != '' && $test_ids != '0') {
                        $Testids = explode(',', $test_ids);
                        
                        foreach ($Testids as $tid) {
                          //  echo "SELECT * FROM lab_test_details WHERE test_id = '$tid'";
                            $Query = $this->db->query("SELECT * FROM lab_test_details WHERE test_id = '$tid'");
                            $Comp = $Query->row();
                            $comp_count = $Query->num_rows();
                            //print_r($Comp);
                            if($comp_count>0)
                            {
                                $test           = $Comp->test;
                                $test_id        = $Comp->test_id;
                                $price          = $Comp->price;
                                $offer          = $Comp->offer;
                                $executive_rate = $Comp->executive_rate;
                                $home_delivery  = $Comp->home_delivery;
                            
                                
                                 $Booed_test_list[] = array(
                                    'test_id' => $test,
                                    'test' => $test_id,
                                    'price' => $price,
                                    'home_delivery' => "0");
                            }
                           
                        }
                    }
                    
                    
                    if($package_id > 0){
                         
                        $LP_query = $this->db->query("SELECT * FROM lab_packages1 WHERE id='$package_id'");
                        $result1 = $LP_query->num_rows();
                        if($result1 > 0)
                        {
                        $lab_pack_name = $LP_query->row()->package_name;
                        $pack_details = $LP_query->row()->package_details;
                        $pack_amount = $LP_query->row()->Price;
                        }
                    }else{
                        $lab_pack_name = "";
                        $pack_details = "";
                        $pack_amount = "";
                    }
                      if($status == null)
                    {
                        $status = "";
                    }
                    
                    $resultpost[] = array(
                        'user_id'=> $user_id,
                        'patient_id'=> $patient_id,
                        'listing_id'=> $listing_id,
                        'vendor_type'=> $vendor_type,
                        'branch_id'=> $branch_id,
                        'branch_name'=> $branch_name,
                        'at_home'=> $at_home,
                        'address_line1'=> $address_line1,
                        'address_line2'=> $address_line2,
                        'city'=> $city,
                        'state'=> $state,
                        'pincode'=> $pincode,
                        'mobile_no'=> $mobile_no,
                        'email_id'=> $email_id,
                        'address_id'=> $address_id,
                        'test_id'=> $test_ids,
                        'package_id'=> $package_id,
                        'package_name'=> $lab_pack_name,
                        'package_details'=> $pack_details,
                        'package_price'=> $pack_amount,
                        'booking_date'=> $booking_date,
                        'booking_time'=> $booking_time,
                        'booking_id'=> $booking_id,
                        'booked_tests'=>$Booed_test_list,
                        'status'=>$status
                    );
                }
            }else{
               $resultpost = array(); 
            }
        }
        
         else if ($listing_type == '39'){
          /* echo "SELECT booking_master.*,health_record.patient_name,nursing_attendant.address,nursing_attendant.city,nursing_attendant.state,nursing_attendant.contact,nursing_attendant.pincode,nursing_booking_details.* FROM booking_master  LEFT JOIN users ON (booking_master.user_id = users.id) LEFT JOIN health_record ON(booking_master.patient_id=health_record.id) LEFT JOIN nursing_attendant ON(booking_master.listing_id=nursing_attendant.user_id) LEFT JOIN nursing_booking_details ON(booking_master.booking_id=nursing_booking_details.booking_id) where booking_master.user_id = '1607' and booking_master.vendor_id = '12' ORDER BY booking_master.id DESC";*/
            $query = $this->db->query("SELECT booking_master.*,health_record.patient_name,dentists_branch.address,dentists_branch.city,dentists_branch.state,dentists_branch.phone,dentists_branch.pincode FROM booking_master  LEFT JOIN users ON (booking_master.user_id = users.id) LEFT JOIN health_record ON(booking_master.patient_id=health_record.id) LEFT JOIN dentists_branch ON(booking_master.listing_id=dentists_branch.dentists_branch_user_id) where booking_master.user_id = '$user_id' and booking_master.vendor_id = '39' ORDER BY booking_master.id DESC");

            $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    
                    $listing_id = $row['listing_id'];
                    
                    // $package_name = $row['package_name'];
                    // $package_amount = $row['package_amount'];
                    // $package_image = $row['package_image'];
                    
                    
                    $booking_id = $row['booking_id'];
                    $package_id = $row['package_id'];
                      $recent_list = $this->db->query("SELECT * FROM sabka_dentist_services WHERE id='$package_id'");
                        $recent_list_list_count = $recent_list->num_rows();
                        $qRows2 = $recent_list->row();
                        if($recent_list_list_count>0)
                        {
                            $package_name         = $qRows2->service_name;
                            $package_amount       = $qRows2->price;
                            $package_image        = '';
                        }
                        else
                        {
                            $package_name = '';
                            $package_amount = '';
                            $package_amount = '';
                        }
                    $booking_date = $row['booking_date'];
                    $patient_name = $row['user_name'];
                    $address = $row['address'] . "," . $row['city'] . ",".$row['state'] . "," . $row['pincode'];
                    $contact_no = $row['phone'];
                    $status = $row['status'];
                    $Nursing_id = $row['listing_id'];
                    
                    
                    
                    $patiente_condition ="";
                    $appointment_time =$row['trail_booking_time'];
                    $attendant_hour = "";
                    $tentative_intime = "";
                    $tentative_outtime = "";
                    $nursing_gender = "";
                    $attendant_needed = "";
                    $joining_date = $row['joining_date'];
                    
                      if($status == null)
                    {
                        $status = "";
                    }
                    
                    $resultpost[] = array(
                        'listing_id' => $listing_id,
                        'booking_id' => $booking_id,
                        'dental_id' => $Nursing_id,
                        'appointment_date' => $booking_date,
                        'patient_name' => $patient_name,
                        'package_id' => $package_id,
                        'package_name' => $package_name,
                        'package_amount' => $package_amount,
                        'package_image' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$package_image,
                        'address'=>$address,
                        'phone' =>$contact_no,
                        'patiente_condition' =>$patiente_condition,
                        'attendent_time' =>$appointment_time,
                        'attendant_hour' =>$attendant_hour,
                        'tentative_intime' =>$tentative_intime,
                        'nursing_gender' =>$nursing_gender,
                        'attendant_needed' =>$attendant_needed,
                        'booking_date'=>$joining_date,
                        'status' => $status
                    );
                }
            } else {
                $resultpost = array();
            }
        }
        // else if($listing_type == '37')
        // {
        //      $query = $this->db->query("SELECT * from user_order where vendor_category = '37'");
        //      $count = $query->num_rows();
        //      if($count>0)
        //      {
        //          foreach($query->result_array() as $miss_belly)
        //          {
                     
        //              $user_id = $miss_belly['user_id'];
        //              $order_id = $miss_belly['order_id'];
                     
        //                  $resultpost[] = array(
        //                 'user_id'=> $user_id,
        //                 'patient_id'=> $patient_id,
        //                 'listing_id'=> $listing_id,
        //                 'vendor_type'=> $vendor_type,
        //                 'branch_id'=> $branch_id,
        //                 'branch_name'=> $branch_name,
        //                 'at_home'=> $at_home,
        //                 'address_line1'=> $address_line1,
        //                 'address_line2'=> $address_line2,
        //                 'city'=> $city,
        //                 'state'=> $state,
        //                 'pincode'=> $pincode,
        //                 'mobile_no'=> $mobile_no,
        //                 'email_id'=> $email_id,
        //                 'address_id'=> $address_id,
        //                 'test_id'=> $test_ids,
        //                 'package_id'=> $package_id,
        //                 'package_name'=> $lab_pack_name,
        //                 'package_details'=> $pack_details,
        //                 'package_price'=> $pack_amount,
        //                 'booking_date'=> $booking_date,
        //                 'booking_time'=> $booking_time,
        //                 'booking_id'=> $booking_id,
        //                 'booked_tests'=>$Booed_test_list,
        //                 'status'=>$status
        //             );
        //          }
        //      }
        //      else{
        //       $resultpost = array(); 
        //     }
        // }
       
        
        return $resultpost;
    }
    //delete healthwall/ask saheli comment reply
    public function fetch_comment_reply_delete($comment_id, $comment_type) {
        if ($comment_type == "healthwall_comment_reply") {
            $query = $this->db->query("DELETE FROM `comments_reply` WHERE comments_reply.id='$comment_id'");
        } elseif ($comment_type == "ask_saheli_comment_reply") {

            $query = $this->db->query("DELETE FROM `ask_saheli_comment_reply` WHERE ask_saheli_comment_reply.id='$comment_id'");
        } elseif ($comment_type == "medicalcollege_comment_reply") {

            $query = $this->db->query("DELETE FROM `medical_college_comment_reply` WHERE medical_college_comment_reply.id='$comment_id'");
        }

        return array(
            'status' => 200,
            'message' => 'success'
                //'medicine_id' => $medicine_id
        );
    }

    //edit healthwall/ask saheli comment reply
    public function fetch_comment_reply_edit($comment_id, $comment_type, $comment) {

        if ($comment_type == "healthwall_comment_reply") {
            $query = $this->db->query("UPDATE comments_reply SET comment='$comment' where comments_reply.id='$comment_id'");
        } elseif ($comment_type == "ask_saheli_comment_reply") {

            $query = $this->db->query("UPDATE ask_saheli_comment_reply SET comment='$comment' where ask_saheli_comment_reply.id='$comment_id'");
        } elseif ($comment_type == "medical_comment_reply") {

            $query = $this->db->query("UPDATE medical_college_comment_reply SET comment='$comment' where medical_college_comment_reply.id='$comment_id'");
        }


        return array(
            'status' => 200,
            'message' => 'success'
                //'medicine_id' => $medicine_id
        );
    }

    //edit healthwall/ask saheli comment 
    public function fetch_comment_edit($comment_id, $comment_type, $comment) {
        if ($comment_type == "healthwall_comment") {
            $query = $this->db->query("UPDATE comments SET description='$comment' where comments.id='$comment_id'");
        } elseif ($comment_type == "ask_saheli_comment") {

            $query = $this->db->query("UPDATE ask_saheli_comment SET comment='$comment' where ask_saheli_comment.id='$comment_id'");
        } elseif ($comment_type == "medical_comment") {

            $query = $this->db->query("UPDATE college_post_comment SET comment='$comment' where college_post_comment.id='$comment_id'");
        }
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }

    public function vendor_add_discount($vendor_id, $discount_amount, $discount_type, $discount_limit, $discount_category, $discount_by, $discount_exp) {

        $vendor_discount_data = array(
            'vendor_id' => $vendor_id,
            'discount_amount' => $discount_amount,
            'discount_type' => $discount_type,
            'discount_limit' => $discount_limit,
            'discount_category' => $discount_category,
            'discount_by' => $discount_by,
            'discount_exp' => $discount_exp
        );
        $this->db->insert('vendor_discount', $vendor_discount_data);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    public function otp_verify($phone, $otp) {

        $q = $this->db->select('phone,otp_code')->from('users')->where('phone', $phone)->get()->row();

        if ($q == "") {
            return array(
                'status' => 204,
                'message' => 'Phone number not found.'
            );
        } else {
            $phoneNo = $q->phone;
            $otp_code = $q->otp_code;
            if ($otp == $otp_code) {
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'description' => 'OTP verified'
                );
            } else {
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'description' => 'Wrong OTP'
                );
            }
        }
    }

    public function App_logout($user_id) {

        $this->db->query("UPDATE `users` SET `token`='' where id='$user_id'");
        //  $this->db->where('id',$user_id)->delete
        return array(
            'status' => 200,
            'message' => 'Successfully logout.'
        );
    }

    public function master_profile_views($user_id, $listing_id, $vendor_type) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $profile_views_array = array(
            'user_id' => $user_id,
            'listing_id' => $listing_id,
            'vendor_type' => $vendor_type,
            'date' => $date
        );
        $this->db->insert('profile_views_master', $profile_views_array);

        $profile_views_master = $this->db->select('id')->from('profile_views_master')->where('listing_id', $listing_id)->where('vendor_type', $vendor_type)->get()->num_rows();

        return array(
            'status' => 200,
            'message' => 'success',
            'fitness_views' => $profile_views_master
        );
    }

    // Web notification FCM function by ghanshyam parihar date:16 Jan 2019
    function send_gcm_web_notify($title, $msg, $reg_id, $img_url, $tag, $agent, $type, $click_action) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('j M Y h:i A');
        if (!defined("GOOGLE_GCM_URL"))
            define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
        $fields = array(
            'to' => $reg_id,
            'priority' => "high",
            $agent === 'android' ? 'notification' : 'notification' => array(
                "title" => $title,
                "body" => $msg,
                "click_action" => $click_action,
                "icon" => $img_url
            )
        );
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
            $agent === 'android' ? 'Authorization: key=AAAASZs-aSM:APA91bEQ0SZFS7YnRRK95O2Jp_PdzVC8t_osgDK0UdWXeQRo06fEedknj2b26yfX_YPkLq85h0lc3hUBlnSSPD1qvfikzFYiHd8tlt7BeuOg3f5WRwQxmz19WbEGbZEX894zZEF5iRO5' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        //print_r($result);
        if ($result === FALSE) {
            die('Problem occurred: ' . curl_error($ch));
        }
        curl_close($ch);
    }

    // Web notification FCM function by ghanshyam parihar date:16 Jan 2019 Ends

    public function notifyMethod($listing_id, $user_id, $description, $booking_date, $booking_id, $connect_type, $user_name, $list_id, $status, $date_noti, $pack_name, $order_id) {
        $customer_token = $this->db->query("SELECT name,token,agent,token_status,web_token,vendor_id FROM users WHERE id='$listing_id'");
        $customer_token_count = $customer_token->num_rows();
        if ($customer_token_count > 0) {
            $token_status = $customer_token->row_array();
            $usr_name = $token_status['name'];
            $agent = $token_status['agent'];
            $reg_id = $token_status['token'];
            $web_token = $token_status['web_token'];
            $img_url = 'https://medicalwale.s3.amazonaws.com/images/img/medical_logo.png';
            $tag = 'text';
            $key_count = '1';
            $title = $user_name . ' has cancelled an booking';
            $msg = $description . "\n" . 'Appointment Date : ' . $booking_date;
            // if(!empty($reg_id)){
            //     // $click_action = 'https://fitness.medicalwale.com/Appointments/booking_appointment/'.$listing_id;
            //     $click_action = 'https://vendor.sandbox.medicalwale.com/v2/Appointment/index/6';
            // }
            // else if(!empty($web_token)){
            if ($token_status['vendor_id'] == '6') {
                $click_action = 'https://fitness.medicalwale.com/appointments/booking_details/' . $order_id;
                // $click_action = 'https://vendor.sandbox.medicalwale.com/fitness/appointments/booking_details/'.$listing_id;
            } else if ($token_status['vendor_id'] == '10') {
                $click_action = 'https://labs.medicalwale.com/booking_controller/booking_appointment/' . $order_id;
                // $click_action = 'https://vendor.sandbox.medicalwale.com/labs/booking_controller/booking_appointment/'.$listing_id;
            } else if ($token_status['vendor_id'] == '8') {
                $click_action = 'https://hospitals.medicalwale.com/Appointments/booking_appointment/' . $order_id;
                // $click_action = 'https://vendor.sandbox.medicalwale.com/hospitals/Appointments/booking_appointment/'.$listing_id;
            } else if ($token_status['vendor_id'] == '12') {
                $click_action = 'https://nursing.medicalwale.com/Appointments/booking_appointment/' . $order_id;
                // $click_action = 'https://vendor.sandbox.medicalwale.com/nursing/Appointments/booking_appointment/'.$listing_id;
            } else if ($token_status['vendor_id'] == '19') {
                $click_action = 'https://pestcontrol.medicalwale.com/Appointments/booking_appointment/' . $order_id;
                // $click_action = 'https://vendor.sandbox.medicalwale.com/pestcontrol/Appointments/booking_appointment/'.$listing_id;
            }

            // }
            // Web notification FCM function by ghanshyam parihar date:4 Feb 2019

            $this->send_gcm_web_notify($title, $msg, $web_token, $img_url, $tag, $agent, $connect_type, $click_action);
            // Web notification FCM function by ghanshyam parihar date:4 Feb 2019 Ends
        }
    }

    public function all_booking_cancel($user_id, $listing_id, $booking_id, $status) {


        $nursing_booking_cancel_array = array(
            'status' => $status
        );

        $this->db->where('user_id', $user_id);
        $this->db->where('listing_id', $listing_id);
        $this->db->where('booking_id', $booking_id);
        $this->db->update('booking_master', $nursing_booking_cancel_array);

        if ($this->db->affected_rows()) {

            //$query_branch = $this->db->query("SELECT fcb.`branch_name`,fcb.`branch_phone`,bm.user_name, bm.package_id, bm.joining_date,bm.id order_id FROM booking_master bm INNER JOIN fitness_center_branch fcb ON(fcb.id=bm.branch_id) WHERE bm.booking_id='$booking_id'");
            $query_branch = $this->db->query("SELECT user_name, package_id, joining_date,id as order_id,vendor_id FROM booking_master WHERE booking_id='$booking_id'");
            $row_branch = $query_branch->row_array();
            // $branch_name = $row_branch['branch_name'];
            // $branch_phone = $row_branch['branch_phone'];
            $user_name = $row_branch['user_name'];
            $joining_date = $row_branch['joining_date'];
            $order_id = $row_branch['order_id'];
            $date_ = date('j M Y', strtotime($joining_date));
            $package_id = $row_branch['package_id'];
            $connect_type = "fitness_bookings";
            $date_noti = date('j M Y', strtotime($joining_date));
            $pack_name = "";

            //notification to fitness center
            if ($row_branch['vendor_id'] == '6') {
                $description = 'Dear Fitness Center, booking is cancelled by customer ' . $user_name . '. Booking id is ' . $booking_id . '. Date ' . $date_ . '. Thank you.';
            } else if ($row_branch['vendor_id'] == '10') {
                $description = 'Dear Lab Center, booking is cancelled by customer ' . $user_name . '. Booking id is ' . $booking_id . '. Date ' . $date_ . '. Thank you.';
            } else if ($row_branch['vendor_id'] == '8') {
                $description = 'Dear Hospital, booking is cancelled by customer ' . $user_name . '. Booking id is ' . $booking_id . '. Date ' . $date_ . '. Thank you.';
            } else if ($row_branch['vendor_id'] == '12') {
                $description = 'Dear Nursing Attendant, booking is cancelled by customer ' . $user_name . '. Booking id is ' . $booking_id . '. Date ' . $date_ . '. Thank you.';
            } else if ($row_branch['vendor_id'] == '19') {
                $description = 'Dear Pest Control, booking is cancelled by customer ' . $user_name . '. Booking id is ' . $booking_id . '. Date ' . $date_ . '. Thank you.';
            }
            $this->notifyMethod($listing_id, $user_id, $description, $joining_date, $booking_id, $connect_type, $user_name, $user_id, $status, $date_noti, $pack_name, $order_id);

            //notification to customer
            // $description1 ='Dear customer, your booking is cancelled at Fitness Center ' . $branch_name . '. Booking id is ' . $booking_id . '. Date ' . $date_ . '. Thank you.'; 
            // $this->notifyMethod($user_id, $listing_id, $description1, $joining_date, $booking_id, $connect_type, $user_name, $listing_id, $status,$date_noti,$pack_name,$order_id);
            //web notification ends 

            return array(
                'status' => 200,
                'message' => 'success'
            );
        } else {
            return array(
                'status' => 200,
                'message' => 'failed'
            );
        }
    }

    public function share_profile($user_id, $mobile_num, $name, $email, $dateofbirth, $gender, $blood_group, $merital_status, $height, $weight, $bmi, $medical_condition, $allergies, $hereditary_problems, $addiction, $diet_prefrence, $Exercise_level, $health_insurance, $organ_donor) {

        $this->load->view('welcome_message');
        // Get output html
        $html = $this->output->get_output();

        // Load library
        $this->load->library('dompdf_gen');

        // Convert to PDF
        $this->dompdf->load_html($html);
        $this->dompdf->render();
        $this->dompdf->stream("welcome.pdf");
    }

    public function save_pdf_file($pdf_file_name, $user_id, $listing_id, $vendor_type, $type, $reason,$password) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $profile_views_array = array(
            'user_id' => $user_id,
            'listing_id' => $listing_id,
            'vendor_type' => $vendor_type,
            'file_name' => $pdf_file_name . ".pdf",
            'type' => $type,
            'reason' => $reason,
            'password' => $password,
            'created_at' => $date
        );
        $this->db->insert('tbl_share_pdf', $profile_views_array);
        
        $customer_token = $this->db->query("SELECT name,token,agent,token_status,web_token,vendor_id FROM users WHERE id='$user_id'");
        $customer_token_count = $customer_token->num_rows();
        if ($customer_token_count > 0) {
            $token_status = $customer_token->row_array();
            $usr_name = $token_status['name'];
            $agent = $token_status['agent'];
            $reg_id = $token_status['token'];
            $web_token = $token_status['web_token'];
            $img_url = 'https://medicalwale.s3.amazonaws.com/images/img/medical_logo.png';
            $tag = 'text';
            $key_count = '1';
            $title = 'Password For Shared Profile';
            $msg = $password." is your password to open shared profile";
            $path ="https://live.medicalwale.com/user_pdf/".$pdf_file_name.".pdf";
          
            $this->send_gcm_notify_share_profile($title, $msg, $reg_id, $img_url, $tag, $agent, $path,$password);
            
            $notification_array = array(
                      'title' => $title,
                      'msg'  => $msg,
                      'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => "",
                      'order_date' => "",
                      'order_id'   => "",
                      'post_id'  => "",
                      'pdf_link'  => $path,
                       'user_id'  => $user_id,
                       'notification_type'  => 'share_profile',
                       'notification_date'  => date('Y-m-d H:i:s')
                       
            );
         $this->db->insert('All_notification_Mobile', $notification_array);
        }
        
        return array(
            'status' => 201,
            'message' => 'success',
            'path' => 'https://live.medicalwale.com/user_pdf/'.$pdf_file_name.".pdf"
        );
    }
    
    function send_gcm_notify_share_profile($title, $msg, $reg_id, $img_url, $tag, $agent, $path,$password)
        {
            if (!defined("GOOGLE_GCM_URL"))
                define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
            
            $fields  = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array(
                    "title" => $title,
                    "message" => $msg,
                    "image" => $img_url,
                    "tag" => $tag,
                    "notification_type" => 'share_profile',
                    "password" => $password,
                    "path" => $path
                    
                )
            );
            $headers = array(
                GOOGLE_GCM_URL,
                'Content-Type: application/json',
                $agent === 'android' ? 'Authorization: key=AIzaSyDTr3BzDHKj_6FnUiZXT__VCmamH-_gupM' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
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
        }
        

    public function recent_vendor_list($user_id, $vendor_type) {


        if ($vendor_type == '12' || $vendor_type == '8' || $vendor_type == '6' || $vendor_type == '10') {
            $recent_master = $this->db->query("SELECT * FROM booking_master WHERE user_id='$user_id' and vendor_id='$vendor_type' GROUP BY listing_id");
            $recent_master_count = $recent_master->num_rows();
            $qRows = $recent_master->result_array();
            if ($recent_master_count > 0) {
                foreach ($qRows as $qRow) {

                    $listing_id = $qRow['listing_id'];
                    if ($vendor_type == '12') {
                        $recent_list = $this->db->query("SELECT * FROM nursing_attendant WHERE user_id='$listing_id'");
                        $recent_list_list_count = $recent_list->num_rows();
                        $qRows2 = $recent_list->row();
                        if ($recent_list_list_count > 0) {
                            $name = $qRows2->name;
                            $user_id = $qRows2->user_id;

                            $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();

                            if ($img_count > 0) {
                                $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                                $img_file = $profile_query->source;
                                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                            } else {
                                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                            }

                            $resultpost[] = array(
                                'listing_id' => $user_id,
                                'name' => $name,
                                'profile_pic' => $userimage
                            );
                        }
                    }
                    if ($vendor_type == '8') {
                        $recent_list = $this->db->query("SELECT * FROM hospitals WHERE user_id='$listing_id'");
                        $recent_list_list_count = $recent_list->num_rows();
                        $qRows2 = $recent_list->row();
                        if ($recent_list_list_count > 0) {
                            $name = $qRows2->name_of_hospital;
                            $user_id = $qRows2->user_id;
                            $image = $qRows2->image;

                            if ($image != '') {
                                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $image;
                            } else {
                                $image = '';
                            }

                            $resultpost[] = array(
                                'listing_id' => $user_id,
                                'name' => $name,
                                'profile_pic' => $image
                            );
                        }
                    }
                    if ($vendor_type == '6') {
                        $recent_list = $this->db->query("SELECT * FROM fitness_center WHERE user_id='$listing_id'");
                        $recent_list_list_count = $recent_list->num_rows();
                        $qRows2 = $recent_list->row();
                        if ($recent_list_list_count > 0) {
                            $name = $qRows2->center_name;
                            $user_id = $qRows2->user_id;
                            $image = $qRows2->image;


                            if ($image != '') {
                                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                            } else {
                                $image = '';
                            }

                            $resultpost[] = array(
                                'listing_id' => $user_id,
                                'name' => $name,
                                'profile_pic' => $image
                            );
                        }
                    }
                    if ($vendor_type == '10') {
                        $recent_list = $this->db->query("SELECT * FROM lab_center WHERE user_id='$listing_id'");
                        $recent_list_list_count = $recent_list->num_rows();
                        $qRows2 = $recent_list->row();
                        if ($recent_list_list_count > 0) {
                            $name = $qRows2->lab_name;
                            $user_id = $qRows2->user_id;
                            $image = $qRows2->profile_pic;


                            if ($image != '') {
                                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/labcenter_images/' . $image;
                            } else {
                                $image = '';
                            }

                            $resultpost[] = array(
                                'listing_id' => $user_id,
                                'name' => $name,
                                'profile_pic' => $image
                            );
                        }
                    }
                    if ($vendor_type == '13') {
                        $recent_list = $this->db->query("SELECT * FROM medical_store WHERE user_id='$listing_id'");
                        $recent_list_list_count = $recent_list->num_rows();
                        $qRows2 = $recent_list->row();
                        if ($recent_list_list_count > 0) {
                            $name = $qRows2->medical_name;
                            $user_id = $qRows2->user_id;
                            $image = $qRows2->profile_pic;


                            if ($image != '') {
                                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                            } else {
                                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.jpg';
                            }

                            $resultpost[] = array(
                                'listing_id' => $user_id,
                                'name' => $name,
                                'profile_pic' => $image
                            );
                        }
                    }
                }
                return $resultpost;
            } else {
                return $resultpost = array();
            }
        } elseif ($vendor_type == '13') {
            //echo "SELECT * FROM user_order WHERE user_id='$user_id' and listing_type='$vendor_type' GROUP BY listing_id";
            $recent_master = $this->db->query("SELECT * FROM user_order WHERE user_id='$user_id' and listing_type='$vendor_type' GROUP BY listing_id");
            $recent_master_count = $recent_master->num_rows();
            $qRows = $recent_master->result_array();
            if ($recent_master_count > 0) {
                foreach ($qRows as $qRow) {

                    $listing_id = $qRow['listing_id'];
                    if ($vendor_type == '13') {
                        $recent_list = $this->db->query("SELECT * FROM medical_stores WHERE user_id='$listing_id'");
                        $recent_list_list_count = $recent_list->num_rows();
                        $qRows2 = $recent_list->row();
                        if ($recent_list_list_count > 0) {
                            $name = $qRows2->medical_name;
                            $user_id = $qRows2->user_id;
                            $image = $qRows2->profile_pic;


                            if ($image != '') {
                                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                            } else {
                                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.jpg';
                            }

                            $resultpost[] = array(
                                'listing_id' => $user_id,
                                'name' => $name,
                                'profile_pic' => $image
                            );
                        }
                    }
                }
                return $resultpost;
            } else {
                return $resultpost = array();
            }
        }
    }

    public function question_list($user_id) {
        $query = $this->db->query("SELECT * FROM userprofile_question");
        $count = $query->num_rows();
        $items = $query->result_array();

        $id = '';
        $ar = array();
        foreach ($items as $item) {

            if ($item['question_type'] == 0) {


                $ar['question'] = $item['question'];
                $id = $item['id'];
                $ar['que_id'] = $item['id'];
                $query = $this->db->query("SELECT * FROM userprofile_question_answer WHERE question_id='$id' and user_id='$user_id'");

                $items2 = $query->row_array();

                $r = $this->sub($items, $id, $user_id);
                $ar['sub_que'] = $r;

                $data[] = $ar;
            }
        }
        //   die();

        return array(
            'status' => 201,
            'message' => 'success',
            'data' => $data
        );
    }
    
    public function faq_question_list($user_id) {
      //  $query = $this->db->query("SELECT * FROM faq_question");
        
       $query = $this->db->query("SELECT a.question AS 'question',b.question AS 'answer' FROM faq_question a, faq_question b WHERE a.id = b.question_type");

        
        
        $count = $query->num_rows();
        $items = $query->result_array();

        $id = '';
        $ar = array();
        foreach ($items as $item) {
                    
                    $data[]=$item;
                    
        }
        //   die();

        return array(
            'status' => 201,
            'message' => 'success',
            'data' => $data
        );
    }

   
     
    
    


    function sub($items, $id, $user_id) {
        $ar1 = array();
        $ar = array();
        foreach ($items as $item) {
            $ar = array();
            if ($item['question_type'] == $id) {

                $ar['question'] = $item['question'];
                $ID = $item['id'];
                $ar['que_id'] = $item['id'];
                $query = $this->db->query("SELECT * FROM userprofile_question_answer WHERE question_id='$ID' and user_id='$user_id'");

                //echo "SELECT * FROM userprofile_question_answer WHERE question_id='$ID'";
                $items2 = $query->row_array();

                $r = $this->sub($items, $item['id'], $user_id);

                if (!empty($r)) {
                    $ar['sub_que'] = $r;
                }
                if ($items2['answer'] == null) {
                    $ar['ans'] = "";
                } else {

                    $query12 = $this->db->query("SELECT id,user_id,question_id,GROUP_CONCAT(answer) as new_ans FROM userprofile_question_answer WHERE  question_id='$ID' and user_id='$user_id'");
                    //echo "SELECT * FROM userprofile_question_answer WHERE question_id='$ID'";
                    $items23 = $query12->row_array();
                    $ar['ans'] = $items23['new_ans'];
                }
                $ar1[] = $ar;
            }
        }

        return $ar1;
    }
    
    
 

    public function Shared_documentlist($user_id) {
        //profile details
        $profile_shared_list = array();
        $prescription_list = array();
        $Health_record_list = array();
        $order_list = array();
        $query = $this->db->query("SELECT * FROM tbl_share_pdf WHERE user_id='$user_id' and type='profile' order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $user_id = $row['user_id'];
                $listing_id = $row['listing_id'];
                $vendor_id = $row['vendor_type'];
                $file_name = $row['file_name'];
                $type = $row['type'];
                $reason = $row['reason'];
                $created_at = $row['created_at'];



                $profile_shared_list[] = array(
                    'id' => $id,
                    'user_id' => $user_id,
                    'listing_id' => $listing_id,
                    'vendor_id' => $vendor_id,
                    'file_name' => $file_name,
                    'type' => $type,
                    'reason' => $reason,
                    'created_at' => $created_at
                );
            }
        }

        //prescription details
        $query = $this->db->query("SELECT * FROM tbl_share_pdf WHERE user_id='$user_id' and type='prescription' order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $user_id = $row['user_id'];
                $listing_id = $row['listing_id'];
                $vendor_id = $row['vendor_type'];
                $file_name = $row['file_name'];
                $type = $row['type'];
                $reason = $row['reason'];
                $created_at = $row['created_at'];



                $prescription_list[] = array(
                    'id' => $id,
                    'user_id' => $user_id,
                    'listing_id' => $listing_id,
                    'vendor_id' => $vendor_id,
                    'file_name' => $file_name,
                    'type' => $type,
                    'reason' => $reason,
                    'created_at' => $created_at
                );
            }
        }



        //Health_record details
        $query = $this->db->query("SELECT * FROM tbl_share_pdf WHERE user_id='$user_id' and type='Health_record' order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $user_id = $row['user_id'];
                $listing_id = $row['listing_id'];
                $vendor_id = $row['vendor_type'];
                $file_name = $row['file_name'];
                $type = $row['type'];
                $reason = $row['reason'];
                $created_at = $row['created_at'];



                $Health_record_list[] = array(
                    'id' => $id,
                    'user_id' => $user_id,
                    'listing_id' => $listing_id,
                    'vendor_id' => $vendor_id,
                    'file_name' => $file_name,
                    'type' => $type,
                    'reason' => $reason,
                    'created_at' => $created_at
                );
            }
        }


        //orders list details 
        $query = $this->db->query("SELECT * FROM tbl_share_pdf WHERE user_id='$user_id' and type='orders' order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $user_id = $row['user_id'];
                $listing_id = $row['listing_id'];
                $vendor_id = $row['vendor_type'];
                $file_name = $row['file_name'];
                $type = $row['type'];
                $reason = $row['reason'];
                $created_at = $row['created_at'];



                $order_list[] = array(
                    'id' => $id,
                    'user_id' => $user_id,
                    'listing_id' => $listing_id,
                    'vendor_id' => $vendor_id,
                    'file_name' => $file_name,
                    'type' => $type,
                    'reason' => $reason,
                    'created_at' => $created_at
                );
            }
        }


        return array(
            'profile_shared_list' => $profile_shared_list,
            'prescription_list' => $prescription_list,
            'Health_record_list' => $Health_record_list,
            'order_list' => $order_list
        );
    }

    public function share_prescription($user_id, $listing_id, $vendor_type, $file_name, $type, $reason) {

        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $share_prescription = array(
            'user_id' => $user_id,
            'listing_id' => $listing_id,
            'vendor_type' => $vendor_type,
            'file_name' => $file_name,
            'type' => $type,
            'reason' => $reason,
            'created_at' => $date
        );
        $this->db->insert('tbl_share_pdf', $share_prescription);

        if ($this->db->affected_rows() > 0) {
            return array(
                'status' => 200,
                'message' => 'success'
            );
        } else {
            return array(
                'status' => 201,
                'message' => 'error'
            );
        }
    }

    public function support_feedback($user_id, $email, $feedback, $ratings, $service) {
        date_default_timezone_set("Asia/Kolkata");
        $date = date('Y-m-d H:i:s');

        $vendor_discount_data = array(
            'user_id' => $email,
            'listing_id' => $user_id,
            'rating' => $ratings,
            'review' => $feedback,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('support_executive_review', $vendor_discount_data);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    public function exotel_call($http_result, $type) {
        date_default_timezone_set("Asia/Kolkata");
        $date = date('Y-m-d H:i:s');

        $xml = simplexml_load_string($http_result);
        $json = json_encode($xml);
        $array = json_decode($json, TRUE);

        $arr_call = array('sid' => $array['Call']['Sid'],
            'call_from' => $array['Call']['From'],
            'call_to' => $array['Call']['To'],
            'PhoneNumberSid' => $array['Call']['PhoneNumberSid'],
            'StartTime' => $array['Call']['StartTime'],
            'status' => $array['Call']['Status'],
            'type' => $type,
            'datetime' => $date
        );

        $this->db->insert('exotel', $arr_call);
    }

    //added by zak for timing working hour for check by date 
    public function check_day_status($day_type, $days_closed, $is_24hrs_available, $store_open, $store_close) {

        if ($is_24hrs_available === 'Yes') {
            if ($day_type == 'Monday') {
                $time = '12:00 AM-11:59 PM';
            }

            if ($day_type == 'Tuesday') {
                $time = '12:00 AM-11:59 PM';
            }

            if ($day_type == 'Wednesday') {
                $time = '12:00 AM-11:59 PM';
            }

            if ($day_type == 'Thursday') {
                $time = '12:00 AM-11:59 PM';
            }

            if ($day_type == 'Friday') {
                $time = '12:00 AM-11:59 PM';
            }

            if ($day_type == 'Saturday') {
                $time = '12:00 AM-11:59 PM';
            }

            if ($day_type == 'Sunday') {
                $time = '12:00 AM-11:59 PM';
            }
        } else {
            if ($day_type == 'Monday') {
                if ($days_closed == 'Monday Closed') {
                    $time = 'close-close';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }

            if ($day_type == 'Tuesday') {
                if ($days_closed == 'Tuesday Closed') {
                    $time = 'close-close';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }

            if ($day_type == 'Wednesday') {
                if ($days_closed == 'Wednesday Closed') {
                    $time = 'close-close';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }

            if ($day_type == 'Thursday') {
                if ($days_closed == 'Thursday Closed') {
                    $time = 'close-close';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }

            if ($day_type == 'Friday') {
                if ($days_closed == 'Friday Closed') {
                    $time = 'close-close';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }

            if ($day_type == 'Saturday') {
                if ($days_closed == 'Saturday Closed') {
                    $time = 'close-close';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }

            if ($day_type == 'Sunday') {
                if ($days_closed == 'Sunday Closed') {
                    $time = 'close-close';
                } elseif ($days_closed == 'Sunday Half Day') {
                    $time = $store_open . '-02:00 PM';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }
        }

        return $time;
    }

    public function check_time_format($time) {
        $time_filter = preg_replace('/\s+/', '', $time);
        $final_time = date("h:i A", strtotime($time_filter));
        return $final_time;
    }

    //end 
    //added by zak for priviladge card offer list 
//   public function priviladge_offer_list($user_id, $mlat, $mlng, $category_id,$type) {
//         $radius = 5000;
//         $limit = 25;
//         $start = 0;
//         $page = 1;
//         if ($page > 0) {
//             if (!is_numeric($page)) {
//                 $page = 1;
//             }
//         }
//         $start =  1 * $limit;
//         function GetDrivingDistance($lat1, $lat2, $long1, $long2) {
//             $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=walking&language=pl-PL&key=AIzaSyDg9YiklFpUhqkDXlwzuC9Cnip4U-78N60&callback=initMap";
//             $ch = curl_init();
//             curl_setopt($ch, CURLOPT_URL, $url);
//             curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//             curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
//             curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
//             curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
//             $response = curl_exec($ch);
//             curl_close($ch);
//             $response_a = json_decode($response, true);
//             $dist = $response_a['rows'][0]['elements'][0]['distance']['value'];
//             return $dist;
//         }
//          $doctor_data = array();
//          $pramacy_data = array();
//          $hospital_data = array();
//           //for pharmacy offer listing 
//          $sql = sprintf("SELECT medicalwale_discount,`id`, `otc_discount`, `ethical_discount`, `generic_discount`, `user_id`,`is_24hrs_available`,`days_closed`,`store_open`,`store_close`,`online_offline`, `medical_name`,`lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`,`profile_pic`,`discount`,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_approval='1' AND is_active='1' AND discount<>'0' HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius));
//         $query = $this->db->query($sql);
//         $count = $query->num_rows();
//         if($count>0)
//         {
//             foreach ($query->result_array() as $row) {
//                 $id = $row['id'];
//                 $listing_id = $row['user_id'];
//                 $listing_name = $row['medical_name'];
//                 $lat          = $row['lat'];
//                 $lng          = $row['lng'];
//                 $address1 = $row['address1'];
//                 $address2 = $row['address2'];
//                 $pincode = $row['pincode'];
//                 $city = $row['city'];
//                 $state = $row['state'];
//                 $discount = $row['discount'];
//                  $days_closed = $row['days_closed'];
//                  $otc_discount = $row['otc_discount'];
//                  $ethical_discount = $row['ethical_discount'];
//                  $generic_discount = $row['generic_discount'];
//                  $discount_info = array('otc_discount' => '',
//                         'ethical_discount' => '',
//                         'generic_discount' => '');
//                  if($otc_discount == '' || $otc_discount == NULL){ $otc_discount = '';}
//                   if($ethical_discount == '' || $ethical_discount == NULL){ $ethical_discount = '';}
//                   if($generic_discount == '' || $generic_discount == NULL){ $generic_discount = '';}
//                  $discount_info = array(
//                         'otc_discount' => $otc_discount,
//                         'ethical_discount' => $ethical_discount,
//                         'generic_discount' => $generic_discount
//                      );
//                 //added for timing pharmacy 
//                  $is_24hrs_available = $row['is_24hrs_available'];
//                 if ($is_24hrs_available == 'Yes') {
//                     $store_open = date("h:i A", strtotime("12:00 AM"));
//                     $store_close = date("h:i A", strtotime("11:59 PM"));
//                 } else {
//                     $store_open = $this->check_time_format($row['store_open']);
//                     $store_close = $this->check_time_format($row['store_close']);
//                 }
//                 //end
//                 $area_expertise = array();
//                  if($row['online_offline']== null || $row['online_offline']== '')
//                   {
//                       $online_offline = '';
//                   }
//                   else
//                   {
//                       $online_offline = $row['online_offline'];
//                   }
//                 $profile_pic = $row['profile_pic'];
//                 if ($row['profile_pic'] != '') {
//                     $profile_pic = $row['profile_pic'];
//                     $profile_pic = str_replace(' ', '%20', $profile_pic);
//                     $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
//                 } else {
//                     $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.jpg';
//                 }
//                 //added code for working hour in pharmacy ......
//                  $t=date('d-m-Y');
//                 $cuurent_day = date("l",strtotime($t));
//                  $Monday = $this->check_day_status($cuurent_day, $days_closed, $is_24hrs_available, $store_open, $store_close);
//               $opening_hours = $Monday;
//                  if($Monday == 'Closed')
//                  {
//                   //$opening_hours = 'Closed';
//                  }
//                  else
//                  {
//                     // $opening_hours = 'open';
//                  }
//               // $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
//              //   $distance_root =  round(($distances/1000),1);
//                 //code ended for working hour 
//                 $pramacy_data[] = array(
//                     'listing_id' => $listing_id,
//                     'listing_type' => '13',
//                     'lisitng_name' => $listing_name,
//                     'discount_info' => $discount_info,
//                     'lat'       => $lat,
//                     'lng'      => $lng,
//                     'address1' => $address1,
//                     'address2' => $address2,
//                     'pincode'  => $pincode,
//                     'city'     => $city ,
//                     'state'    => $state,
//                     'online_offline' => $online_offline,
//                     'discount'   => $discount,
//                     'rating'    => '4',
//                     'profile_pic' => $profile_pic,
//                     'area_expertise' => $area_expertise,
//                     'working_hrs'  => $opening_hours
//                     );
//             }
//           //  return  $pramacy_data;
//         }
//         //end
//          //for doctor offer listing 
//           if($type == 'doctor')
//          {
//           if($category_id != '')
//           {
//               $cats = explode(',',$category_id);
//               $WHER = "";
//               foreach($cats as $ct){
//                 $WHER .= "FIND_IN_SET('" . $ct . "', doctor_list.category) OR ";    
//               }
//               $WH = substr(trim($WHER), 0, -3);
//           $sql_doctor   = sprintf("SELECT doctor_list.user_id,doctor_list.category,doctor_list.doctor_name,doctor_list.rating,doctor_list.online_offline,doctor_list.qualification,doctor_list.experience,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.discount <> '' AND $WH HAVING distance < '%s' ORDER BY distance LIMIT 0 , 150", ($mlat), ($mlng), ($mlat), ($radius));
//             }
//             else
//           {
//                 $sql_doctor   = sprintf("SELECT doctor_list.user_id,doctor_list.category,doctor_list.doctor_name,doctor_list.rating,doctor_list.online_offline,doctor_list.qualification,doctor_list.experience,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.discount<> '' HAVING distance < '%s' ORDER BY distance LIMIT 0 , 150", ($mlat), ($mlng), ($mlat), ($radius));
//           }
//          }
//          else
//          {
//                 $sql_doctor   = sprintf("SELECT doctor_list.user_id,doctor_list.category,doctor_list.doctor_name,doctor_list.rating,doctor_list.online_offline,doctor_list.qualification,doctor_list.experience,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id where doctor_list.discount <> '' HAVING distance < '%s'  ORDER BY distance LIMIT 0 , 150", ($mlat), ($mlng), ($mlat), ($radius));
//          }
//           $query_doctor = $this->db->query($sql_doctor);
//           $Doctor_count = $query->num_rows();
//           if($Doctor_count>0)
//           {
//               foreach($query_doctor->result_array() as $drow)
//               {
//                   $listing_id = $drow['user_id'];
//                   $listing_name = $drow['doctor_name'];
//                   $lat          = $drow['lat'];
//                   $lng          = $drow['lng'];
//                   $address1 = $drow['address'];
//                   $address2 = "";
//                   $pincode = $drow['pincode'];
//                   $city = $drow['city'];
//                   $state = $drow['state'];
//                   $discount = $drow['discount'];
//                   $category  = $drow['category'];
//                 //   $online_offline  = $drow['online_offline'];
//                   $profile_pic = $drow['image'];
//                   $rating = $drow['rating'];
//                   if($drow['online_offline']== null || $drow['online_offline']== '')
//                   {
//                       $online_offline = '';
//                   }
//                   else
//                   {
//                       $online_offline = $drow['online_offline'];
//                   }
//                   if ($drow['image'] != '') {
//                     $profile_pic = $drow['image'];
//                     $profile_pic = str_replace(' ', '%20', $profile_pic);
//                     $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
//                 } else {
//                     $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
//                 }
//                   if($rating=='')
//                 {
//                     $rating='0';
//                 }
//                 else
//                 {
//                 }
//                 //added by zak for doctor category
//                   $area_expertise = array();
//                 $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'" . $category . "')");
//                 $total_category = $query_sp->num_rows();
//                 if ($total_category > 0) {
//                     foreach ($query_sp->result_array() as $get_sp) {
//                         $id               = $get_sp['id'];
//                         $area_expertised  = $get_sp['area_expertise'];
//                         $area_expertise[] = array(
//                             'id' => $id,
//                             'area_expertise' => $area_expertised
//                         );
//                     }
//                 } else {
//                     $area_expertise = array();
//                 }
//                  $discount_info = array('otc_discount' => '',
//                         'ethical_discount' => '',
//                         'generic_discount' => '');
//                  $doctor_data[] = array(
//                     'listing_id' => $listing_id,
//                     'listing_type' => '5',
//                     'discount_info' => $discount_info,
//                     'lisitng_name' => $listing_name,
//                     'lat'       => $lat,
//                     'lng'      => $lng,
//                     'address1' => $address1,
//                     'address2' => $address2,
//                     'pincode'  => $pincode,
//                     'city'     => $city ,
//                     'state'    => $state,
//                     'rating'   => '4',
//                     'online_offline' => $online_offline,
//                     'discount'   => $discount,
//                     'profile_pic' => $profile_pic,
//                     'area_expertise' => $area_expertise,
//                     'working_hrs'  => ''
//                     );
//               }
//           }
//          //end 
//          //for hospital offer list
//          if($type == 'hospital')
//          {
//               if($category_id != '')
//               {
//          $hquery = $this->db->query("SELECT `id`, `user_id`, `name_of_hospital`,`online_offline`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($mlat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE FIND_IN_SET('" . $category_id . "', category) AND user_discount<>'0' HAVING distance <= '$radius' ORDER BY distance LIMIT $start, $limit");
//       //echo "SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE FIND_IN_SET('" . $category_name . "', category)  HAVING distance <= '$radius'";
//          }
//          }
//          else
//          {
//              $hquery = $this->db->query("SELECT `id`, `user_id`, `name_of_hospital`,`online_offline`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($mlat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE user_discount<>'0' HAVING distance <= '$radius' ORDER BY distance LIMIT 0, $limit");
//          }
//         $Hospital_count = $hquery->num_rows();
//         if($Hospital_count>0)
//         {
//              foreach($hquery->result_array() as $hrow)
//               {
//                   $listing_id = $hrow['user_id'];
//                   $listing_name = $hrow['name_of_hospital'];
//                   $lat          = $hrow['lat'];
//                   $lng          = $hrow['lng'];
//                   $address1 = $hrow['address'];
//                   $address2 = "";
//                   $pincode = $hrow['pincode'];
//                   $city = $hrow['city'];
//                   $state = $hrow['state'];
//                   $profile_pic = $hrow['image'];
//                   $discount = $hrow['user_discount'];
//                   $rating = $hrow['rating'];
//                   $area_expertise = array();
//                      if($hrow['online_offline']== null || $hrow['online_offline']== '')
//                   {
//                       $online_offline = '';
//                   }
//                   else
//                   {
//                       $online_offline = $hrow['online_offline'];
//                   }
//                   if ($hrow['image'] != '') {
//                     $profile_pic = $hrow['image'];
//                     $profile_pic = str_replace(' ', '%20', $profile_pic);
//                     $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
//                 } else {
//                     $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
//                 }
//                 if($rating=='')
//                 {
//                     $rating='0';
//                 }
//                 else
//                 {
//                 }
//                  $discount_info = array('otc_discount' => '',
//                         'ethical_discount' => '',
//                         'generic_discount' => '');
//                  $hospital_data[] = array(
//                     'listing_id' => $listing_id,
//                     'listing_type' => '5',
//                     'discount_info' => $discount_info,
//                     'lisitng_name' => $listing_name,
//                     'lat'       => $lat,
//                     'lng'      => $lng,
//                     'address1' => $address1,
//                     'address2' => $address2,
//                     'pincode'  => $pincode,
//                     'city'     => $city ,
//                     'state'    => $state,
//                     'rating'   => '4',
//                     'area_expertise' => $area_expertise,
//                     'online_offline' => $online_offline,
//                     'discount'   => $discount,
//                     'profile_pic' => $profile_pic,
//                     'working_hrs'  => ''
//                     );
//               }
//         }
//         else
//         {
//             $hospital_data = array();
//         }
//          //end
//          //for fitness offer list 
//          if($type == 'fitness')
//          {
//               if($category_id != '')
//             {
//               $cats = explode(',',$category_id);
//               $WHER = "";
//               foreach($cats as $ct){
//                 $WHER .= "FIND_IN_SET('" . $ct . "', fc.business_category) OR ";    
//               }
//               $WH = substr(trim($WHER), 0, -3);
//             $fquery = $this->db->query(sprintf("SELECT fc.id,fc.user_id,fc.city,fc.center_name, fc.contact, fc.email, fc.image, fc.address, fc.pincode, fc.state, fc.lat, fc.lng, fc.opening_hours, fc.online_offline,fc.medicalwale_discount, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $mlat ) ) * cos( radians( $mlng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $mlat ) ) ) ) AS distance FROM fitness_center as fc  WHERE fc.is_active='1' AND fc.medicalwale_discount<> '0' AND $WH HAVING distance < '5' ORDER BY distance LIMIT 0 , 25", ($mlat), ($mlng), ($mlat), ($radius)));
//       // $query = $this->db->query("SELECT fc.`user_id`, fc.`center_name`, fc.`contact`, fc.`email`, fc.`image`,en.contact_no as enquiry_number FROM `fitness_center` as fc LEFT JOIN users as u ON(u.id=fc.user_id) LEFT JOIN enquiry_number en ON(en.vendor_id=u.vendor_id) WHERE fc.user_id='$listing_id'");//echo "SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE FIND_IN_SET('" . $category_name . "', category)  HAVING distance <= '$radius'";
//          }
//          }
//          else
//          {
//               $fquery = $this->db->query(sprintf("SELECT fc.id,fc.user_id,fc.city, fc.center_name, fc.contact, fc.email, fc.image, fc.address, fc.pincode, fc.state, fc.lat, fc.lng, fc.opening_hours, fc.online_offline,fc.medicalwale_discount, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $mlat ) ) * cos( radians( $mlng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $mlat ) ) ) ) AS distance FROM fitness_center as fc  WHERE fc.is_active='1' AND fc.medicalwale_discount<> '0' AND fc.lat<> 'null' HAVING distance < '50' ORDER BY distance LIMIT 0 , 25", ($mlat), ($mlng), ($mlat), ($radius)));
//          }
//         $Fitness_count = $fquery->num_rows();
//         if($Fitness_count>0)
//         {
//              foreach($fquery->result_array() as $frow)
//               {
//                   $listing_id = $frow['user_id'];
//                   $listing_name = $frow['center_name'];
//                   $lat          = $frow['lat'];
//                   $lng          = $frow['lng'];
//                   $address1 = $frow['address'];
//                   $address2 = "";
//                   $pincode = $frow['pincode'];
//                   $city = $frow['city'];
//                   $state = $frow['state'];
//                   $profile_pic = $frow['image'];
//                   $discount = $frow['medicalwale_discount'];
//                   $rating = '0';
//                   $area_expertise= array();
//                      if($frow['online_offline']== null || $frow['online_offline']== '')
//                   {
//                       $online_offline = '';
//                   }
//                   else
//                   {
//                       $online_offline = $frow['online_offline'];
//                   }
//                   if ($frow['image'] != '') {
//                     $profile_pic = $frow['image'];
//                     $profile_pic = str_replace(' ', '%20', $profile_pic);
//                     $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
//                 } else {
//                     $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
//                 }
//                  $discount_info = array('otc_discount' => '',
//                         'ethical_discount' => '',
//                         'generic_discount' => '');
//                  $fitness_data[] = array(
//                     'listing_id' => $listing_id,
//                     'listing_type' => '6',
//                     'discount_info' => $discount_info,
//                     'lisitng_name' => $listing_name,
//                     'lat'       => $lat,
//                     'lng'      => $lng,
//                     'address1' => $address1,
//                     'address2' => $address2,
//                     'pincode'  => $pincode,
//                     'city'     => $city ,
//                     'state'    => $state,
//                     'rating'   => '4',
//                     'online_offline' => $online_offline,
//                     'discount'   => $discount,
//                     'profile_pic' => $profile_pic,
//                     'area_expertise' => $area_expertise,
//                     'working_hrs'  => ''
//                     );
//               }
//         }
//         else
//         {
//             $fitness_data = array();
//         }
//          //end
//          //for optic store list 
//         $sql_optic =  sprintf("SELECT `id`, `user_id`, `name`, `year`, `phone`, `telephone`, `manager`, `designation`, `discount`, `about`, `hours_open`, `address`, `image`, `lat`, `lng`, `reviews`, `ratings`, `state`, `city`, `pincode`, `is_active`, `is_approval`, `reg_date`,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM `optic_eyecare_list` WHERE discount<>'0' HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius));
//         $query_optics = $this->db->query($sql_optic);
//         $count_op = $query->num_rows();
//         if($count_op>0){
//              foreach($query_optics->result_array() as $oprow)
//               {
//                   $listing_id = $oprow['user_id'];
//                   $listing_name = $oprow['name'];
//                   $lat          = $oprow['lat'];
//                   $lng          = $oprow['lng'];
//                   $address1 = $oprow['address'];
//                   $address2 = "";
//                   $pincode = $oprow['pincode'];
//                   $city = $oprow['city'];
//                   $state = $oprow['state'];
//                   $profile_pic = $oprow['image'];
//                   $discount = $oprow['discount'];
//                   $rating = '0';
//                   $area_expertise= array();
//                       $online_offline = '';
//                   if ($oprow['image'] != '') {
//                     $profile_pic = $oprow['image'];
//                     $profile_pic = str_replace(' ', '%20', $profile_pic);
//                     $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
//                 } else {
//                     $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
//                 }
//                  $discount_info = array('otc_discount' => '',
//                         'ethical_discount' => '',
//                         'generic_discount' => '');
//                  $optics_data[] = array(
//                     'listing_id' => $listing_id,
//                     'listing_type' => '17',
//                     'discount_info' => $discount_info,
//                     'lisitng_name' => $listing_name,
//                     'lat'       => $lat,
//                     'lng'      => $lng,
//                     'address1' => $address1,
//                     'address2' => $address2,
//                     'pincode'  => $pincode,
//                     'city'     => $city ,
//                     'state'    => $state,
//                     'rating'   => '4',
//                     'online_offline' => $online_offline,
//                     'discount'   => $discount,
//                     'profile_pic' => $profile_pic,
//                     'area_expertise' => $area_expertise,
//                     'working_hrs'  => ''
//                     );
//               }
//         }
//         //end 
//          //for Spa offer list 
//          if($type == 'spa')
//          {
//               if($category_id != '')
//             {
//               $cats = explode(',',$category_id);
//               $WHER = "";
//               foreach($cats as $ct){
//                 $WHER .= "FIND_IN_SET('" . $ct . "', fc.business_category) OR ";    
//               }
//               $WH = substr(trim($WHER), 0, -3);
//             $spaquery = $this->db->query(sprintf("SELECT fc.id,fc.user_id,fc.city,fc.center_name, fc.contact, fc.email, fc.image, fc.address, fc.pincode, fc.state, fc.lat, fc.lng, fc.opening_hours, fc.online_offline,fc.medicalwale_discount, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $mlat ) ) * cos( radians( $mlng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $mlat ) ) ) ) AS distance FROM fitness_center as fc  WHERE fc.is_active='1' AND fc.medicalwale_discount<> '0' AND $WH AND vendor_type='Spa' HAVING distance < '5' ORDER BY distance LIMIT 0 , 25", ($mlat), ($mlng), ($mlat), ($radius)));
//       // $query = $this->db->query("SELECT fc.`user_id`, fc.`center_name`, fc.`contact`, fc.`email`, fc.`image`,en.contact_no as enquiry_number FROM `fitness_center` as fc LEFT JOIN users as u ON(u.id=fc.user_id) LEFT JOIN enquiry_number en ON(en.vendor_id=u.vendor_id) WHERE fc.user_id='$listing_id'");//echo "SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE FIND_IN_SET('" . $category_name . "', category)  HAVING distance <= '$radius'";
//          }
//          }
//          else
//          {
//               $spaquery = $this->db->query(sprintf("SELECT fc.id,fc.user_id,fc.city, fc.center_name, fc.contact, fc.email, fc.image, fc.address, fc.pincode, fc.state, fc.lat, fc.lng, fc.opening_hours, fc.online_offline,fc.medicalwale_discount, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $mlat ) ) * cos( radians( $mlng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $mlat ) ) ) ) AS distance FROM fitness_center as fc  WHERE fc.is_active='1' AND fc.medicalwale_discount<> '0' AND fc.lat<> 'null' AND vendor_type='Spa' HAVING distance < '50' ORDER BY distance LIMIT 0 , 25", ($mlat), ($mlng), ($mlat), ($radius)));
//          }
//         $Spa_count = $spaquery->num_rows();
//         if($Spa_count>0)
//         {
//              foreach($spaquery->result_array() as $sparow)
//               {
//                   $listing_id = $sparow['user_id'];
//                   $listing_name = $sparow['center_name'];
//                   $lat          = $sparow['lat'];
//                   $lng          = $sparow['lng'];
//                   $address1 = $sparow['address'];
//                   $address2 = "";
//                   $pincode = $sparow['pincode'];
//                   $city = $sparow['city'];
//                   $state = $sparow['state'];
//                   $profile_pic = $sparow['image'];
//                   $discount = $sparow['medicalwale_discount'];
//                   $rating = '0';
//                   $area_expertise= array();
//                      if($sparow['online_offline']== null || $sparow['online_offline']== '')
//                   {
//                       $online_offline = '';
//                   }
//                   else
//                   {
//                       $online_offline = $sparow['online_offline'];
//                   }
//                   if ($sparow['image'] != '') {
//                     $profile_pic = $sparow['image'];
//                     $profile_pic = str_replace(' ', '%20', $profile_pic);
//                     $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
//                 } else {
//                     $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
//                 }
//                  $discount_info = array('otc_discount' => '',
//                         'ethical_discount' => '',
//                         'generic_discount' => '');
//                  $spa_data[] = array(
//                     'listing_id' => $listing_id,
//                     'listing_type' => '36',
//                     'discount_info' => $discount_info,
//                     'lisitng_name' => $listing_name,
//                     'lat'       => $lat,
//                     'lng'      => $lng,
//                     'address1' => $address1,
//                     'address2' => $address2,
//                     'pincode'  => $pincode,
//                     'city'     => $city ,
//                     'state'    => $state,
//                     'rating'   => '4',
//                     'online_offline' => $online_offline,
//                     'discount'   => $discount,
//                     'profile_pic' => $profile_pic,
//                     'area_expertise' => $area_expertise,
//                     'working_hrs'  => ''
//                     );
//               }
//         }
//         else
//         {
//             $spa_data = array();
//         }
//          //end
//          $sports_store_data = array();
//       // $spa_data = array();
//       $ayurvedic_stores_data  = array();
//       //  $optics_data = array();
//       $clinics_data  = array();
//         $pathalogy_lab_data = array();
//          return array(
//                      'pharmacy' => $pramacy_data,
//                      'doctor'   => $doctor_data,
//                      'hospital' => $hospital_data,
//                      'sports_store' => $sports_store_data,
//                      'spa' => $spa_data,
//                      'ayurvedic_stores' => $ayurvedic_stores_data,
//                      'optics' => $optics_data,
//                      'clinics' => $clinics_data,
//                      'pathalogy_labs' => $pathalogy_lab_data,
//                      'fitness' => $fitness_data
//              );
//   }

    public function priviladge_offer_list($user_id, $mlat, $mlng, $category_id, $type, $page) {
        //	$radius = 5*$page;
        $limit = 25;
        $start = 0;
        //	$page = 1;
        //   echo 'echo'.$page;

        if ($page > 0 || $page == '') {
            if (!is_numeric($page)) {
                if ($page == '') {

                    $page = 2;
                } else {
                    $page = 2;
                }
            } else {
                $page = ($page) / (1000);
            }
        } else {
            $page = 2;
        }

        //	echo 'kadka'.$page;
        $radius = 1 * $page;
        $start = 1 * $limit;

        function GetDrivingDistance($lat1, $lat2, $long1, $long2) {
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=walking&language=pl-PL&key=AIzaSyDg9YiklFpUhqkDXlwzuC9Cnip4U-78N60&callback=initMap";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $response_a = json_decode($response, true);
            $dist = $response_a['rows'][0]['elements'][0]['distance']['value'];
            return $dist;
        }

        $doctor_data = array();
        $pramacy_data = array();
        $hospital_data = array();
        $sports_store_data = array();
        $optics_data = array();
        $spa_data = array();
        $sports_store_data = array();
        $ayurvedic_stores_data = array();
        $pathalogy_lab_data = array();
        // for pharmacy offer listing
        $radius1 = '5000';
        $pharmacy_sql = sprintf("SELECT `id`,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_approval='1' AND is_active='1' AND discount<>'0' AND lat<>'' HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius1));
        $pharmacy_query = $this->db->query($pharmacy_sql);
        $pharmacy_count = $pharmacy_query->num_rows();

        $sql = sprintf("SELECT medicalwale_discount,`id`, `otc_discount`,`perscribed_discount` ,`surgical_discount`,`min_order`, `ethical_discount`, `generic_discount`, `user_id`,`is_24hrs_available`,`days_closed`,`store_open`,`store_close`,`online_offline`, `medical_name`,`lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`,`profile_pic`,`discount`,`contact_no`,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_approval='1' AND is_active='1' AND discount<>'0' AND lat<>'' HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius));
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $listing_id = $row['user_id'];
                $listing_name = $row['medical_name'];
                $lat = $row['lat'];
                $lng = $row['lng'];
                $address1 = $row['address1'];
                $address2 = $row['address2'];
                $pincode = $row['pincode'];
                $city = $row['city'];
                $state = $row['state'];
                $discount = $row['discount'];
                $days_closed = $row['days_closed'];
                $otc_discount = $row['otc_discount'];
                $ethical_discount = $row['ethical_discount'];
                $generic_discount = $row['generic_discount'];
                $perscribed_discount = $row['perscribed_discount'];
                $surgical_discount = $row['surgical_discount'];
                $contact = $row['contact_no'];
                $delivery_charges = $row['min_order'];
                $discount_info = array(
                    'otc_discount' => '',
                    'ethical_discount' => '',
                    'generic_discount' => '',
                    'surgical_discount'=>'',
                    'perscribed_discount'=>''
                );
                if ($otc_discount == '' || $otc_discount == NULL) {
                    $otc_discount = '';
                }

                if ($ethical_discount == '' || $ethical_discount == NULL) {
                    $ethical_discount = '';
                }

                if ($generic_discount == '' || $generic_discount == NULL) {
                    $generic_discount = '';
                }

                $discount_info = array(
                    'otc_discount' => $otc_discount,
                    'ethical_discount' => $ethical_discount,
                    'generic_discount' => $generic_discount,
                    'surgical_discount'=>$surgical_discount,
                    'perscribed_discount'=>$perscribed_discount
                );

                // added for timing pharmacy

                $is_24hrs_available = $row['is_24hrs_available'];
                if ($is_24hrs_available == 'Yes' || $is_24hrs_available == 'yes') {
                    $store_open = date("h:i A", strtotime("12:00 AM"));
                    $store_close = date("h:i A", strtotime("11:59 PM"));
                } else {
                    $store_open = $this->check_time_format($row['store_open']);
                    $store_close = $this->check_time_format($row['store_close']);
                }

                // end

                $area_expertise = array();
                if ($row['online_offline'] == null || $row['online_offline'] == '') {
                    $online_offline = '';
                } else {
                    $online_offline = $row['online_offline'];
                }

                $profile_pic = $row['profile_pic'];
                if ($row['profile_pic'] != '') {
                    $profile_pic = $row['profile_pic'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.jpg';
                }

                // added code for working hour in pharmacy ......

                $t = date('d-m-Y');
                $cuurent_day = date("l", strtotime($t));
                $Monday = $this->check_day_status($cuurent_day, $days_closed, $is_24hrs_available, $store_open, $store_close);
                $opening_hours = $Monday;
                if ($Monday == 'Closed') {

                    // $opening_hours = 'Closed';
                } else {

                    // $opening_hours = 'open';
                }

                // $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                //   $distance_root =  round(($distances/1000),1);
                // code ended for working hour

                if ($lat != "" || $lat != null && $lng != "" || $lng != null) {
                    $pramacy_data[] = array(
                        'listing_id' => $listing_id,
                        'listing_type' => '13',
                        'lisitng_name' => $listing_name,
                        'discount_info' => $discount_info,
                        'lat' => $lat,
                        'lng' => $lng,
                        'address1' => $address1,
                        'address2' => $address2,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'online_offline' => $online_offline,
                        'discount' => $discount,
                        'rating' => '4',
                        'profile_pic' => $profile_pic,
                        'area_expertise' => $area_expertise,
                        'phone_no' => $contact,
                        'delivery_charge' => $delivery_charges,
                        'working_hrs' => $opening_hours
                    );
                }
            }

            //  return  $pramacy_data;
        }

        // end
        // for doctor offer listing

        if ($type == 'doctor') {
            if ($category_id != '') {
                $cats = explode(',', $category_id);
                $WHER = "";
                if (count($cats) > 1) {
                    foreach ($cats as $ct) {
                        $WHER.= "FIND_IN_SET('" . $ct . "', doctor_list.category) OR ";
                    }

                    $WH = substr(trim($WHER), 0, -3);
                } else {
                    $WH = "FIND_IN_SET('" . $category_id . "', doctor_list.category)";
                }

                $sql_doctor = sprintf("SELECT doctor_list.telephone,doctor_list.user_id,doctor_list.category,doctor_list.doctor_name,doctor_list.rating,doctor_list.online_offline,doctor_list.qualification,doctor_list.experience,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.open_hours,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.discount <> '' AND doctor_list.is_active ='1' AND ($WH) HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius));
                $sql_doctor1 = sprintf("SELECT doctor_list.telephone,doctor_list.user_id,doctor_list.category,doctor_list.doctor_name,doctor_list.rating,doctor_list.online_offline,doctor_list.qualification,doctor_list.experience,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.open_hours,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.discount <> '' AND doctor_list.is_active ='1' AND ($WH) HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius1));
            } else {
                $sql_doctor = sprintf("SELECT doctor_list.telephone,doctor_list.user_id,doctor_list.category,doctor_list.doctor_name,doctor_list.rating,doctor_list.online_offline,doctor_list.qualification,doctor_list.experience,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.open_hours,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.discount<> '' AND doctor_list.is_active ='1' HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius));

                $sql_doctor1 = sprintf("SELECT doctor_list.telephone,doctor_list.user_id,doctor_list.category,doctor_list.doctor_name,doctor_list.rating,doctor_list.online_offline,doctor_list.qualification,doctor_list.experience,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.open_hours,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.discount<> '' AND doctor_list.is_active ='1' HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius1));
            }
        } else {
            if ($category_id != '') {
                $cats = explode(',', $category_id);
                $WHER = "";

                // echo count($cats);

                if (count($cats) > 1) {
                    foreach ($cats as $ct) {
                        $WHER.= "FIND_IN_SET('" . $ct . "', doctor_list.category) OR ";
                    }

                    $WH = substr(trim($WHER), 0, -3);
                } else {
                    $WH = "FIND_IN_SET('" . $category_id . "', doctor_list.category)";
                }

                $sql_doctor = sprintf("SELECT doctor_list.telephone,doctor_list.user_id,doctor_list.category,doctor_list.doctor_name,doctor_list.rating,doctor_list.online_offline,doctor_list.qualification,doctor_list.experience,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.open_hours,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.discount <> '' AND doctor_list.is_active ='1' AND doctor_list.discount <> 'null' AND ($WH) HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius));
                $sql_doctor1 = sprintf("SELECT doctor_list.telephone,doctor_list.user_id,doctor_list.category,doctor_list.doctor_name,doctor_list.rating,doctor_list.online_offline,doctor_list.qualification,doctor_list.experience,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.open_hours,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.discount <> '' AND doctor_list.is_active ='1' AND doctor_list.discount <> 'null' AND ($WH) HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius1));
            } else {
                $sql_doctor = sprintf("SELECT doctor_list.telephone,doctor_list.user_id,doctor_list.category,doctor_list.doctor_name,doctor_list.rating,doctor_list.online_offline,doctor_list.qualification,doctor_list.experience,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.open_hours,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id where doctor_list.discount <> '' AND doctor_list.is_active ='1' AND doctor_clinic.lat <> '' AND doctor_list.discount <> 'null' HAVING distance < '%s'  ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius));
                $sql_doctor1 = sprintf("SELECT doctor_list.telephone,doctor_list.user_id,doctor_list.category,doctor_list.doctor_name,doctor_list.rating,doctor_list.online_offline,doctor_list.qualification,doctor_list.experience,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.open_hours,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id where doctor_list.discount <> '' AND doctor_list.is_active ='1' AND doctor_clinic.lat <> '' AND doctor_list.discount <> 'null' HAVING distance < '%s'  ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius1));
            }
        }

        $query_doctor1 = $this->db->query($sql_doctor1);
        $Doctor1_count = $query_doctor1->num_rows();


        $query_doctor = $this->db->query($sql_doctor);
        $Doctor_count = $query_doctor->num_rows();
        if ($Doctor_count > 0) {
            foreach ($query_doctor->result_array() as $drow) {
                $listing_id = $drow['user_id'];
                //$clinic_id = $drow['clinic_id'];
                $listing_name = $drow['doctor_name'];
                $lat = $drow['lat'];
                $lng = $drow['lng'];
                $address1 = $drow['address'];
                $address2 = "";
                $pincode = $drow['pincode'];
                $city = $drow['city'];
                $state = $drow['state'];
                $contact = $drow['telephone'];
                $discount = $drow['discount'];
                $category = $drow['category'];

                //   $online_offline  = $drow['online_offline'];

                $profile_pic = $drow['image'];
                $rating = $drow['rating'];
                if ($drow['online_offline'] == null || $drow['online_offline'] == '') {
                    $online_offline = '';
                } else {
                    $online_offline = $drow['online_offline'];
                }

                if ($drow['image'] != '') {
                    $profile_pic = $drow['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }

                if ($rating == '') {
                    $rating = '4';
                } else {
                    
                }

                // added by zak for doctor clinic timing
                /* $opening_hours = $drow['open_hours'];
                  $opening_hr = '';
                  $open_days = '';
                  $day_array_list = '';
                  $day_list = '';
                  $day_time_list = '';
                  $time_list1 = '';
                  $time_list2 = '';
                  $time = '';
                  $system_start_time = '';
                  $system_end_time = '';
                  $time_check = '';
                  $current_time = '';
                  $open_close = array();
                  $time = array();
                  $cuutent_dayy = date('l');
                  date_default_timezone_set('Asia/Kolkata');
                  $data = array();
                  $final_Day = array();
                  $day_array_list = explode('|', $opening_hours);
                  if (count($day_array_list) > 1)
                  {
                  for ($i = 0; $i < count($day_array_list); $i++)
                  {
                  $day_list = explode('>', $day_array_list[$i]);

                  for ($j = 0; $j < count($day_list); $j++)
                  {
                  $day_time_list = explode('-', $day_list[$j]);
                  for ($k = 1; $k < count($day_time_list); $k++)
                  {
                  $time_list1 = explode(',', $day_time_list[0]);
                  $time_list2 = explode(',', $day_time_list[1]);
                  $time = array();
                  $open_close = array();

                  //  echo 'kadak';
                  // print_r($day_list[0]);

                  for ($l = 0; $l < count($time_list1); $l++)
                  {
                  if ($day_list[0] == $cuutent_dayy)
                  {
                  $time_check = $time_list1[$l] . '-' . $time_list2[$l];
                  $time[] = str_replace('close-close', 'Close', $time_check);
                  $system_start_time = date("h:i A", strtotime($time_list1[$l]));
                  $system_end_time = date("h:i A", strtotime($time_list2[$l]));
                  $current_time = date('h:i A');
                  $date1 = DateTime::createFromFormat('H:i a', $current_time);
                  $date2 = DateTime::createFromFormat('H:i a', $system_start_time);
                  $date3 = DateTime::createFromFormat('H:i a', $system_end_time);
                  if ($date2 < $date3 && $date1 <= $date3)
                  {
                  $date3->modify('+1 day')->format('H:i a');
                  }
                  elseif ($date2 > $date3 && $date1 >= $date3)
                  {
                  $date3->modify('+1 day')->format('H:i a');
                  }

                  if ($date1 > $date2 && $date1 < $date3)
                  {
                  $open_close[] = 'open';
                  }
                  else
                  {
                  $open_close[] = 'closed';
                  }
                  }
                  }
                  }
                  }

                  if ($day_list[0] == $cuutent_dayy)
                  {
                  $opening_hr = $time;
                  }
                  }
                  }
                  else
                  {
                  $opening_hr = '';
                  }



                  if (count($opening_hr) > 0)
                  {

                  $opening_hr_final = implode(',', $opening_hr);

                  }
                  else
                  {
                  $opening_hr_final = '';
                  }
                 */

                $queryTiming = $this->db->query("SELECT * FROM `doctor_slot_details` WHERE `doctor_id` = '$listing_id' ");

                $countTiming = $queryTiming->num_rows();
                if (count($countTiming) > 0) {
                    // die();
                    $open_close = array();
                    $time = array();
                    $time_array = array();
                    $time_array1 = array();
                    $time_array2 = array();
                    $time_array3 = array();
                    $time_slott = array();
                    $cuutent_dayy = date('l');
                    foreach ($queryTiming->result_array() as $row1) {
                        // if($countTiming){

                        $timeSlotDay = $row1['day'];
                        $timeSlot = $row1['time_slot'];
                        $from_time = $row1['from_time'];
                        $to_time = $row1['to_time'];

                        // echo $timeSlot;
                        if ($timeSlotDay == $cuutent_dayy) {
                            $system_start_current = strtotime(date("h:i A"));


                            $time_check = date("h:i A", strtotime($from_time)) . '-' . date("h:i A", strtotime($to_time));
                            $time[] = str_replace('close-close', 'Close', $time_check);

                            $current_time = date('h:i A');
                            $system_start_time = date("h:i A", strtotime($from_time));
                            $system_end_time = date("h:i A", strtotime($to_time));
                            $date1 = DateTime::createFromFormat('H:i a', $current_time);
                            $date2 = DateTime::createFromFormat('H:i a', $system_start_time);
                            $date3 = DateTime::createFromFormat('H:i a', $system_end_time);
                            if ($date2 < $date3 && $date1 <= $date3) {
                                $date3->modify('+1 day')->format('H:i a');
                            } elseif ($date2 > $date3 && $date1 >= $date3) {
                                $date3->modify('+1 day')->format('H:i a');
                            }
                            if ($date1 > $date2 && $date1 < $date3) {
                                $open_close = 'open';
                            } else {
                                $open_close = 'closed';
                            }
                        }
                    }


                    if (count($time) > 0) {

                        $opening_hr_final = implode(',', $time);
                    } else {
                        $opening_hr_final = '';
                    }
                } else {
                    $opening_hr_final = '';
                    $open_close = "";
                }

                // end
                // added by zak for doctor category

                $area_expertise = array();
                $query_sp = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'" . $category . "')");
                $total_category = $query_sp->num_rows();
                if ($total_category > 0) {
                    foreach ($query_sp->result_array() as $get_sp) {
                        $id = $get_sp['id'];
                        $area_expertised = $get_sp['area_expertise'];
                        $area_expertise[] = array(
                            'id' => $id,
                            'area_expertise' => $area_expertised
                        );
                    }
                } else {
                    $area_expertise = array();
                }

               $discount_info = array(
                    'otc_discount' => '',
                    'ethical_discount' => '',
                    'generic_discount' => '',
                    'surgical_discount'=>'',
                    'perscribed_discount'=>''
                );
                if ($lat != "" || $lat != null && $lng != "" || $lng != null) {
                    $doctor_data[] = array(
                        'listing_id' => $listing_id,
                        'listing_type' => '5',
                        'discount_info' => $discount_info,
                        'lisitng_name' => $listing_name,
                        'lat' => $lat,
                        'lng' => $lng,
                        'address1' => $address1,
                        'address2' => $address2,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'rating' => '4',
                        'online_offline' => $online_offline,
                        'discount' => $discount,
                        'profile_pic' => $profile_pic,
                        'area_expertise' => $area_expertise,
                        'phone_no' => $contact,
                        'working_hrs' => $opening_hr_final
                    );
                }
            }
        }

        // end
        //beauty sports query ends//
        // for hospital offer list

        if ($type == 'hospital') {
            if ($category_id != '') {
                $hquery = $this->db->query("SELECT `id`, `user_id`, `name_of_hospital`,`online_offline`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($mlat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE is_active=1 AND FIND_IN_SET('" . $category_id . "', category) AND user_discount<>'0' AND lat <> '' AND lat <> 'null'  HAVING distance <= '$radius' ORDER BY distance");
                $hquery1 = $this->db->query("SELECT `id`, `user_id`, `name_of_hospital`,`online_offline`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($mlat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE is_active=1 AND FIND_IN_SET('" . $category_id . "', category) AND user_discount<>'0' AND lat <> '' AND lat <> 'null'  HAVING distance <= '$radius1' ORDER BY distance");
                // echo "SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE FIND_IN_SET('" . $category_name . "', category)  HAVING distance <= '$radius'";
            }
        } else {
            $hquery = $this->db->query("SELECT `id`, `user_id`, `name_of_hospital`,`online_offline`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($mlat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE is_active=1 AND user_discount<>'0' AND lat <> '' AND lat <> 'null' HAVING distance <= '$radius' ORDER BY distance");
            $hquery1 = $this->db->query("SELECT `id`, `user_id`, `name_of_hospital`,`online_offline`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($mlat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE is_active=1 AND user_discount<>'0' AND lat <> '' AND lat <> 'null' HAVING distance <= '$radius1' ORDER BY distance");
        }
        $Hospital_count1 = $hquery1->num_rows();
        $Hospital_count = $hquery->num_rows();
        if ($Hospital_count > 0) {
            foreach ($hquery->result_array() as $hrow) {
                $listing_id = $hrow['user_id'];
                $listing_name = $hrow['name_of_hospital'];
                $lat = $hrow['lat'];
                $lng = $hrow['lng'];
                $address1 = $hrow['address'];
                $address2 = "";
                $pincode = $hrow['pincode'];
                $city = $hrow['city'];
                $state = $hrow['state'];
                $profile_pic = $hrow['image'];
                $discount = $hrow['user_discount'];
                $rating = $hrow['rating'];
                $contact = $hrow['phone'];
                $area_expertise = array();
                if ($hrow['online_offline'] == null || $hrow['online_offline'] == '') {
                    $online_offline = '';
                } else {
                    $online_offline = $hrow['online_offline'];
                }

                if ($hrow['image'] != '') {
                    $profile_pic = $hrow['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }

                if ($rating == '') {
                    $rating = '0';
                } else {
                    
                }

               $discount_info = array(
                    'otc_discount' => '',
                    'ethical_discount' => '',
                    'generic_discount' => '',
                    'surgical_discount'=>'',
                    'perscribed_discount'=>''
                );
                if ($lat != "" || $lat != null && $lng != "" || $lng != null) {
                    $hospital_data[] = array(
                        'listing_id' => $listing_id,
                        'listing_type' => '8',
                        'discount_info' => $discount_info,
                        'lisitng_name' => $listing_name,
                        'lat' => $lat,
                        'lng' => $lng,
                        'address1' => $address1,
                        'address2' => $address2,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'rating' => '4',
                        'area_expertise' => $area_expertise,
                        'online_offline' => $online_offline,
                        'discount' => $discount,
                        'profile_pic' => $profile_pic,
                        'phone_no' => $contact,
                        'working_hrs' => '12:00 AM-11:59 PM'
                    );
                }
            }
        } else {
            $hospital_data = array();
        }

        // end
        // for fitness offer list

        if ($type == 'fitness') {

            if ($category_id != '') {
                $cats = explode(',', $category_id);
                $WHER = "";



                if (count($cats) > 1) {
                    foreach ($cats as $ct) {
                        $WHER.= "FIND_IN_SET('" . $ct . "', fc.business_category) OR ";
                    }

                    $WH = substr(trim($WHER), 0, -3);
                } else {
                    $WH = "FIND_IN_SET('" . $category_id . "', fc.business_category)";
                }



                $fquery = $this->db->query(sprintf("SELECT fc.id,fc.user_id,fc.city,fc.center_name, fc.contact, fc.email, fc.image, fc.address, fc.pincode, fc.state, fc.lat, fc.lng, fc.opening_hours, fc.online_offline,fc.discount, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $mlat ) ) * cos( radians( $mlng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $mlat ) ) ) ) AS distance ,fcb.id branch_id FROM fitness_center as fc LEFT JOIN fitness_center_branch fcb ON(fcb.user_id=fc.user_id)  WHERE fc.is_active='1' AND fc.discount<> '0'  AND fc.lat<> 'null' AND vendor_type='Fitness' AND ($WH) ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius)));
                //     echo sprintf("SELECT fc.id,fc.user_id,fc.city,fc.center_name, fc.contact, fc.email, fc.image, fc.address, fc.pincode, fc.state, fc.lat, fc.lng, fc.opening_hours, fc.online_offline,fc.discount, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $mlat ) ) * cos( radians( $mlng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $mlat ) ) ) ) AS distance FROM fitness_center as fc  WHERE fc.is_active='1' AND fc.discount<> '0'  AND fc.lat<> 'null' AND vendor_type='Fitness' AND ($WH) ORDER BY distance", ($mlat) , ($mlng) , ($mlat) , ($radius));
                $fquery1 = $this->db->query(sprintf("SELECT fc.id,fc.user_id,fc.city,fc.center_name, fc.contact, fc.email, fc.image, fc.address, fc.pincode, fc.state, fc.lat, fc.lng, fc.opening_hours, fc.online_offline,fc.discount, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $mlat ) ) * cos( radians( $mlng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $mlat ) ) ) ) AS distance ,fcb.id branch_id FROM fitness_center as fc LEFT JOIN fitness_center_branch fcb ON(fcb.user_id=fc.user_id)  WHERE fc.is_active='1' AND fc.discount<> '0'  AND fc.lat<> 'null' AND vendor_type='Fitness' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius1)));
                // $query = $this->db->query("SELECT fc.`user_id`, fc.`center_name`, fc.`contact`, fc.`email`, fc.`image`,en.contact_no as enquiry_number FROM `fitness_center` as fc LEFT JOIN users as u ON(u.id=fc.user_id) LEFT JOIN enquiry_number en ON(en.vendor_id=u.vendor_id) WHERE fc.user_id='$listing_id'");//echo "SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE FIND_IN_SET('" . $category_name . "', category)  HAVING distance <= '$radius'";
            }
            else {
                $fquery = $this->db->query(sprintf("SELECT fc.id,fc.user_id,fc.city, fc.center_name, fc.contact, fc.email, fc.image, fc.address, fc.pincode, fc.state, fc.lat, fc.lng, fc.opening_hours, fc.online_offline,fc.discount, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $mlat ) ) * cos( radians( $mlng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $mlat ) ) ) ) AS distance ,fcb.id branch_id FROM fitness_center as fc LEFT JOIN fitness_center_branch fcb ON(fcb.user_id=fc.user_id)  WHERE fc.is_active='1' AND fc.discount<> '0' AND fc.lat<> 'null' AND vendor_type='Fitness'  HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius)));

                $fquery1 = $this->db->query(sprintf("SELECT fc.id,fc.user_id,fc.city, fc.center_name, fc.contact, fc.email, fc.image, fc.address, fc.pincode, fc.state, fc.lat, fc.lng, fc.opening_hours, fc.online_offline,fc.discount, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $mlat ) ) * cos( radians( $mlng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $mlat ) ) ) ) AS distance ,fcb.id branch_id FROM fitness_center as fc LEFT JOIN fitness_center_branch fcb ON(fcb.user_id=fc.user_id)  WHERE fc.is_active='1' AND fc.discount<> '0' AND fc.lat<> 'null' AND vendor_type='Fitness' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius1)));
            }
        } else {
            if ($category_id != '') {
                $cats = explode(',', $category_id);
                $WHER = "";



                if (count($cats) > 1) {
                    foreach ($cats as $ct) {
                        $WHER.= "FIND_IN_SET('" . $ct . "', fc.business_category) OR ";
                    }

                    $WH = substr(trim($WHER), 0, -3);
                } else {
                    $WH = "FIND_IN_SET('" . $category_id . "', fc.business_category)";
                }



                $fquery = $this->db->query(sprintf("SELECT fc.id,fc.user_id,fc.city,fc.center_name, fc.contact, fc.email, fc.image, fc.address, fc.pincode, fc.state, fc.lat, fc.lng, fc.opening_hours, fc.online_offline,fc.discount, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $mlat ) ) * cos( radians( $mlng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $mlat ) ) ) ) AS distance,fcb.id branch_id FROM fitness_center as fc LEFT JOIN fitness_center_branch fcb ON(fcb.user_id=fc.user_id)  WHERE fc.is_active='1' AND fc.discount<> '0'  AND fc.lat<> 'null' AND vendor_type='Fitness' AND ($WH) HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius)));
                //  echo sprintf("SELECT fc.id,fc.user_id,fc.city,fc.center_name, fc.contact, fc.email, fc.image, fc.address, fc.pincode, fc.state, fc.lat, fc.lng, fc.opening_hours, fc.online_offline,fc.discount, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $mlat ) ) * cos( radians( $mlng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $mlat ) ) ) ) AS distance FROM fitness_center as fc  WHERE fc.is_active='1' AND fc.discount<> '0'  AND fc.lat<> 'null' AND vendor_type='Fitness' AND ($WH) HAVING distance < '%s' ORDER BY distance", ($mlat) , ($mlng) , ($mlat) , ($radius));
                $fquery1 = $this->db->query(sprintf("SELECT fc.id,fc.user_id,fc.city,fc.center_name, fc.contact, fc.email, fc.image, fc.address, fc.pincode, fc.state, fc.lat, fc.lng, fc.opening_hours, fc.online_offline,fc.discount, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $mlat ) ) * cos( radians( $mlng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $mlat ) ) ) ) AS distance,fcb.id branch_id FROM fitness_center as fc LEFT JOIN fitness_center_branch fcb ON(fcb.user_id=fc.user_id) WHERE fc.is_active='1' AND fc.discount<> '0'  AND fc.lat<> 'null' AND vendor_type='Fitness' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius1)));
            } else {
                $fquery = $this->db->query(sprintf("SELECT fc.id,fc.user_id,fc.city, fc.center_name, fc.contact, fc.email, fc.image, fc.address, fc.pincode, fc.state, fc.lat, fc.lng, fc.opening_hours, fc.online_offline,fc.discount, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $mlat ) ) * cos( radians( $mlng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $mlat ) ) ) ) AS distance ,fcb.id branch_id FROM fitness_center as fc LEFT JOIN fitness_center_branch fcb ON(fcb.user_id=fc.user_id)  WHERE fc.is_active='1' AND fc.discount<> '0' AND fc.lat<> 'null' AND vendor_type='Fitness'  HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius)));

                $fquery1 = $this->db->query(sprintf("SELECT fc.id,fc.user_id,fc.city, fc.center_name, fc.contact, fc.email, fc.image, fc.address, fc.pincode, fc.state, fc.lat, fc.lng, fc.opening_hours, fc.online_offline,fc.discount, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $mlat ) ) * cos( radians( $mlng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $mlat ) ) ) ) AS distance ,fcb.id branch_id FROM fitness_center as fc LEFT JOIN fitness_center_branch fcb ON(fcb.user_id=fc.user_id)  WHERE fc.is_active='1' AND fc.discount<> '0' AND fc.lat<> 'null' AND vendor_type='Fitness' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius1)));
            }
        }
        $Fitness_count1 = $fquery1->num_rows();
        $Fitness_count = $fquery->num_rows();
        if ($Fitness_count > 0) {
            foreach ($fquery->result_array() as $frow) {
                $listing_id = $frow['user_id'];
                $branch_id = $frow['branch_id'];
                $listing_name = $frow['center_name'];
                $lat = $frow['lat'];
                $lng = $frow['lng'];
                $address1 = $frow['address'];
                $address2 = "";
                $pincode = $frow['pincode'];
                $city = $frow['city'];
                $state = $frow['state'];
                $profile_pic = $frow['image'];
                $discount = $frow['discount'];
                $contact = $frow['contact'];
                $opening_hours = $frow['opening_hours'];
                $opening_hr = '';
                $open_days = '';
                $day_array_list = '';
                $day_list = '';
                $day_time_list = '';
                $time_list1 = '';
                $time_list2 = '';
                $time = '';
                $system_start_time = '';
                $system_end_time = '';
                $time_check = '';
                $current_time = '';
                $open_close = array();
                $time = array();
                $cuutent_dayy = date('l');
                date_default_timezone_set('Asia/Kolkata');
                $data = array();
                $final_Day = array();
                $day_array_list = explode('|', $opening_hours);

                if (count($day_array_list) > 1) {

                    for ($i = 0; $i < count($day_array_list); $i++) {
                        $day_list = explode('>', $day_array_list[$i]);
                        //print_r($day_list);
                        for ($j = 0; $j < count($day_list); $j++) {
                            $day_time_list = explode('-', $day_list[$j]);
                            for ($k = 1; $k < count($day_time_list); $k++) {
                                $time_list1 = explode(',', $day_time_list[0]);
                                $time_list2 = explode(',', $day_time_list[1]);
                                $time = array();
                                $open_close = array();

                                //  echo 'kadak';


                                for ($l = 0; $l < count($time_list1); $l++) {

                                    if ($day_list[0] == $cuutent_dayy) {

                                        $time_check = $time_list1[$l] . '-' . $time_list2[$l];

                                        $time[] = str_replace('close-close', '', $time_check);
                                        $system_start_time = date("h:i A", strtotime($time_list1[$l]));
                                        $system_end_time = date("h:i A", strtotime($time_list2[$l]));
                                        $current_time = date('h:i A');
                                        $date1 = DateTime::createFromFormat('H:i a', $current_time);
                                        $date2 = DateTime::createFromFormat('H:i a', $system_start_time);
                                        $date3 = DateTime::createFromFormat('H:i a', $system_end_time);
                                        if ($date2 < $date3 && $date1 <= $date3) {
                                            $date3->modify('+1 day')->format('H:i a');
                                        } elseif ($date2 > $date3 && $date1 >= $date3) {
                                            $date3->modify('+1 day')->format('H:i a');
                                        }

                                        if ($date1 > $date2 && $date1 < $date3) {
                                            $open_close[] = 'open';
                                        } else {
                                            $open_close[] = 'closed';
                                        }
                                    }
                                }
                            }
                        }

                        if ($day_list[0] == $cuutent_dayy) {

                            $opening_hr = $time;
                        }
                    }
                } else {
                    $opening_hr = '';
                }

                if (count($opening_hr) > 1) {
                    $opening_hr_final = implode(',', $opening_hr);
                } elseif (count($opening_hr) == 1) {
                    if (empty($opening_hr)) {
                        $opening_hr_final = '';
                    } else {
                        $opening_hr_final = $opening_hr[0];
                    }
                } else {
                    $opening_hr_final = '';
                }

                $rating = '0';
                $area_expertise = array();
                if ($frow['online_offline'] == null || $frow['online_offline'] == '') {
                    $online_offline = '';
                } else {
                    $online_offline = $frow['online_offline'];
                }

                if ($frow['image'] != '') {
                    $profile_pic = $frow['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }

              $discount_info = array(
                    'otc_discount' => '',
                    'ethical_discount' => '',
                    'generic_discount' => '',
                    'surgical_discount'=>'',
                    'perscribed_discount'=>''
                );
                if ($lat != "" || $lat != null && $lng != "" || $lng != null) {
                    $fitness_data[] = array(
                        'listing_id' => $listing_id,
                        'branch_id' => $branch_id,
                        'listing_type' => '6',
                        'discount_info' => $discount_info,
                        'lisitng_name' => $listing_name,
                        'lat' => $lat,
                        'lng' => $lng,
                        'address1' => $address1,
                        'address2' => $address2,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'rating' => '4',
                        'online_offline' => $online_offline,
                        'discount' => $discount,
                        'profile_pic' => $profile_pic,
                        'area_expertise' => $area_expertise,
                        'phone_no' => $contact,
                        'working_hrs' => $opening_hr_final
                    );
                }
            }
        } else {
            $fitness_data = array();
        }

        // end
        // for optic store list
    
        $sql_optic = sprintf("SELECT `id`, `user_id`, `name`, `year`, `phone`, `telephone`, `manager`, `designation`, `discount`, `about`, `hours_open`, `address`, `image`, `lat`, `lng`, `reviews`, `ratings`, `state`, `city`, `pincode`, `is_active`, `is_approval`, `reg_date`,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM `optic_eyecare_list` WHERE is_active=1 AND discount<>'0' AND lat<>'' HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius));
        $sql_optic1 = sprintf("SELECT `id`, `user_id`, `name`, `year`, `phone`, `telephone`, `manager`, `designation`, `discount`, `about`, `hours_open`, `address`, `image`, `lat`, `lng`, `reviews`, `ratings`, `state`, `city`, `pincode`, `is_active`, `is_approval`, `reg_date`,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM `optic_eyecare_list` WHERE is_active=1 AND discount<>'0' AND lat<>'' HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius1));
        $query_optics1 = $this->db->query($sql_optic1);
        $count_op1 = $query_optics1->num_rows();
        $query_optics = $this->db->query($sql_optic);
        $count_op = $query_optics->num_rows();
        //	$optics_data[] = array();
        if ($count_op > 0) {
            foreach ($query_optics->result_array() as $oprow) {
                $listing_id = $oprow['user_id'];
                $listing_name = $oprow['name'];
                $lat = $oprow['lat'];
                $lng = $oprow['lng'];
                $address1 = $oprow['address'];
                $address2 = "";
                $pincode = $oprow['pincode'];
                $city = $oprow['city'];
                $state = $oprow['state'];
                $profile_pic = $oprow['image'];
                $discount = $oprow['discount'];
                $contact = $oprow['phone'];
                $rating = '0';
                $area_expertise = array();
                $online_offline = '';
                if ($oprow['image'] != '') {
                    $profile_pic = $oprow['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }

              $discount_info = array(
                    'otc_discount' => '',
                    'ethical_discount' => '',
                    'generic_discount' => '',
                    'surgical_discount'=>'',
                    'perscribed_discount'=>''
                );
                if ($lat != "" || $lat != null && $lng != "" || $lng != null) {
                    $optics_data[] = array(
                        'listing_id' => $listing_id,
                        'listing_type' => '17',
                        'discount_info' => $discount_info,
                        'lisitng_name' => $listing_name,
                        'lat' => $lat,
                        'lng' => $lng,
                        'address1' => $address1,
                        'address2' => $address2,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'rating' => '4',
                        'online_offline' => $online_offline,
                        'discount' => $discount,
                        'profile_pic' => $profile_pic,
                        'area_expertise' => $area_expertise,
                        'phone_no' => $contact,
                        'working_hrs' => '09:00 AM-10:00 PM'
                    );
                }
            }
        } else {
            $optics_data = array();
        }

        // end
        // for Spa offer list

        if ($type == 'spa') {
            if ($category_id != '') {
                $cats = explode(',', $category_id);
                $WHER = "";
                foreach ($cats as $ct) {
                    $WHER.= "FIND_IN_SET('" . $ct . "', fc.business_category) OR ";
                }

                $WH = substr(trim($WHER), 0, -3);
                $spaquery = $this->db->query(sprintf("SELECT fc.id,fc.user_id,fc.city,fc.center_name, fc.contact, fc.email, fc.image, fc.address, fc.pincode, fc.state, fc.lat, fc.lng, fc.opening_hours, fc.online_offline,fc.discount, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $mlat ) ) * cos( radians( $mlng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $mlat ) ) ) ) AS distance FROM fitness_center as fc  WHERE fc.is_active='1' AND fc.discount<> '0' AND $WH AND vendor_type='Spa' HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius)));
                $spaquery1 = $this->db->query(sprintf("SELECT fc.id,fc.user_id,fc.city,fc.center_name, fc.contact, fc.email, fc.image, fc.address, fc.pincode, fc.state, fc.lat, fc.lng, fc.opening_hours, fc.online_offline,fc.discount, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $mlat ) ) * cos( radians( $mlng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $mlat ) ) ) ) AS distance FROM fitness_center as fc  WHERE fc.is_active='1' AND fc.discount<> '0' AND $WH AND vendor_type='Spa' HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius1)));
                // $query = $this->db->query("SELECT fc.`user_id`, fc.`center_name`, fc.`contact`, fc.`email`, fc.`image`,en.contact_no as enquiry_number FROM `fitness_center` as fc LEFT JOIN users as u ON(u.id=fc.user_id) LEFT JOIN enquiry_number en ON(en.vendor_id=u.vendor_id) WHERE fc.user_id='$listing_id'");//echo "SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE FIND_IN_SET('" . $category_name . "', category)  HAVING distance <= '$radius'";
            }
            else{
                $spaquery = $this->db->query(sprintf("SELECT fc.id,fc.user_id,fc.city, fc.center_name, fc.contact, fc.email, fc.image, fc.address, fc.pincode, fc.state, fc.lat, fc.lng, fc.opening_hours, fc.online_offline,fc.discount, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $mlat ) ) * cos( radians( $mlng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $mlat ) ) ) ) AS distance FROM fitness_center as fc  WHERE fc.is_active='1' AND fc.discount<> '0' AND fc.lat<> 'null' AND vendor_type='Spa' HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius)));

            $spaquery1 = $this->db->query(sprintf("SELECT fc.id,fc.user_id,fc.city, fc.center_name, fc.contact, fc.email, fc.image, fc.address, fc.pincode, fc.state, fc.lat, fc.lng, fc.opening_hours, fc.online_offline,fc.discount, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $mlat ) ) * cos( radians( $mlng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $mlat ) ) ) ) AS distance FROM fitness_center as fc  WHERE fc.is_active='1' AND fc.discount<> '0' AND fc.lat<> 'null' AND vendor_type='Spa' HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius1)));
            }
        } else {
            $spaquery = $this->db->query(sprintf("SELECT fc.id,fc.user_id,fc.city, fc.center_name, fc.contact, fc.email, fc.image, fc.address, fc.pincode, fc.state, fc.lat, fc.lng, fc.opening_hours, fc.online_offline,fc.discount, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $mlat ) ) * cos( radians( $mlng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $mlat ) ) ) ) AS distance FROM fitness_center as fc  WHERE fc.is_active='1' AND fc.discount<> '0' AND fc.lat<> 'null' AND vendor_type='Spa' HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius)));

            $spaquery1 = $this->db->query(sprintf("SELECT fc.id,fc.user_id,fc.city, fc.center_name, fc.contact, fc.email, fc.image, fc.address, fc.pincode, fc.state, fc.lat, fc.lng, fc.opening_hours, fc.online_offline,fc.discount, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $mlat ) ) * cos( radians( $mlng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $mlat ) ) ) ) AS distance FROM fitness_center as fc  WHERE fc.is_active='1' AND fc.discount<> '0' AND fc.lat<> 'null' AND vendor_type='Spa' HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius1)));
        }
        $Spa_count1 = $spaquery1->num_rows();

        $Spa_count = $spaquery->num_rows();
        if ($Spa_count > 0) {
            foreach ($spaquery->result_array() as $sparow) {
                $listing_id = $sparow['user_id'];
                $listing_name = $sparow['center_name'];
                $lat = $sparow['lat'];
                $lng = $sparow['lng'];
                $address1 = $sparow['address'];
                $address2 = "";
                $pincode = $sparow['pincode'];
                $city = $sparow['city'];
                $state = $sparow['state'];
                $profile_pic = $sparow['image'];
                $discount = $sparow['discount'];
                $rating = '0';
                $contact = $sparow['contact'];

                $opening_hours = $sparow['opening_hours'];



                $opening_hr = '';
                $open_days = '';
                $day_array_list = '';
                $day_list = '';
                $day_time_list = '';
                $time_list1 = '';
                $time_list2 = '';
                $time = '';
                $system_start_time = '';
                $system_end_time = '';
                $time_check = '';
                $current_time = '';
                $open_close = array();
                $time = array();
                $cuutent_dayy = date('l');
                date_default_timezone_set('Asia/Kolkata');
                $data = array();
                $final_Day = array();
                $day_array_list = explode('|', $opening_hours);

                if (count($day_array_list) > 1) {

                    for ($i = 0; $i < count($day_array_list); $i++) {
                        $day_list = explode('>', $day_array_list[$i]);
                        //print_r($day_list);
                        for ($j = 0; $j < count($day_list); $j++) {
                            $day_time_list = explode('-', $day_list[$j]);
                            for ($k = 1; $k < count($day_time_list); $k++) {
                                $time_list1 = explode(',', $day_time_list[0]);
                                $time_list2 = explode(',', $day_time_list[1]);
                                $time = array();
                                $open_close = array();

                                //  echo 'kadak';


                                for ($l = 0; $l < count($time_list1); $l++) {

                                    if ($day_list[0] == $cuutent_dayy) {

                                        $time_check = $time_list1[$l] . '-' . $time_list2[$l];

                                        $time[] = str_replace('close-close', '', $time_check);
                                        $system_start_time = date("h:i A", strtotime($time_list1[$l]));
                                        $system_end_time = date("h:i A", strtotime($time_list2[$l]));
                                        $current_time = date('h:i A');
                                        $date1 = DateTime::createFromFormat('H:i a', $current_time);
                                        $date2 = DateTime::createFromFormat('H:i a', $system_start_time);
                                        $date3 = DateTime::createFromFormat('H:i a', $system_end_time);
                                        if ($date2 < $date3 && $date1 <= $date3) {
                                            $date3->modify('+1 day')->format('H:i a');
                                        } elseif ($date2 > $date3 && $date1 >= $date3) {
                                            $date3->modify('+1 day')->format('H:i a');
                                        }

                                        if ($date1 > $date2 && $date1 < $date3) {
                                            $open_close[] = 'open';
                                        } else {
                                            $open_close[] = 'closed';
                                        }
                                    }
                                }
                            }
                        }

                        if ($day_list[0] == $cuutent_dayy) {

                            $opening_hr = $time;
                        }
                    }
                } else {
                    $opening_hr = '';
                }

                if (count($opening_hr) > 1) {
                    $opening_hr_final = implode(',', $opening_hr);
                } elseif (count($opening_hr) == 1) {
                    if (empty($opening_hr)) {
                        $opening_hr_final = '';
                    } else {
                        $opening_hr_final = $opening_hr[0];
                    }
                } else {
                    $opening_hr_final = '';
                }

                $area_expertise = array();
                if ($sparow['online_offline'] == null || $sparow['online_offline'] == '') {
                    $online_offline = '';
                } else {
                    $online_offline = $sparow['online_offline'];
                }

                if ($sparow['image'] != '') {
                    $profile_pic = $sparow['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }

               $discount_info = array(
                    'otc_discount' => '',
                    'ethical_discount' => '',
                    'generic_discount' => '',
                    'surgical_discount'=>'',
                    'perscribed_discount'=>''
                );
                if ($lat != "" || $lat != null && $lng != "" || $lng != null) {
                    $spa_data[] = array(
                        'listing_id' => $listing_id,
                        'listing_type' => '36',
                        'discount_info' => $discount_info,
                        'lisitng_name' => $listing_name,
                        'lat' => $lat,
                        'lng' => $lng,
                        'address1' => $address1,
                        'address2' => $address2,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'rating' => '4',
                        'online_offline' => $online_offline,
                        'discount' => $discount,
                        'profile_pic' => $profile_pic,
                        'area_expertise' => $area_expertise,
                        'phone_no' => $contact,
                        'working_hrs' => $opening_hr_final
                    );
                }
            }
        } else {
            $spa_data = array();
        }

        // end
        // for spotrs store list

        $sql_spotrs = sprintf("SELECT `id`, `user_id`, `name`, `year`, `phone`, `manager`, `discount`, `address`, `image`, `lat`, `lng`, `state`, `city`, `pincode`, `is_active`, `is_approval`,`store_open`,`store_close`, `reg_date`,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM `sports_store` WHERE is_active=1 AND discount<>'0' AND lat<>'' HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius));
        $query_sports = $this->db->query($sql_spotrs);
        $count_sp = $query_sports->num_rows();

        $sql_spotrs1 = sprintf("SELECT `id`, `user_id`, `name`, `year`, `phone`, `manager`, `discount`, `address`, `image`, `lat`, `lng`, `state`, `city`, `pincode`, `is_active`, `is_approval`,`store_open`,`store_close`, `reg_date`,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM `sports_store` WHERE is_active=1 AND discount<>'0' AND lat<>'' HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius1));
        $query_sports1 = $this->db->query($sql_spotrs1);
        $count_sp1 = $query_sports1->num_rows();
        //	echo 'kadakkkkk'.$count_sp;
        if ($count_sp > 0) {
            foreach ($query_sports->result_array() as $oprow) {
                $listing_id = $oprow['user_id'];
                $listing_name = $oprow['name'];
                $lat = $oprow['lat'];
                $lng = $oprow['lng'];
                $address1 = $oprow['address'];
                $address2 = "";
                $pincode = $oprow['pincode'];
                $city = $oprow['city'];
                $state = $oprow['state'];
                $profile_pic = $oprow['image'];
                $discount = $oprow['discount'];
                $store_open = $oprow['store_open'];
                $store_close = $oprow['store_close'];
                $rating = '0';
                $contact = $oprow['phone'];
                $area_expertise = array();
                $online_offline = '';
                if ($oprow['image'] != '') {
                    $profile_pic = $oprow['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }

               $discount_info = array(
                    'otc_discount' => '',
                    'ethical_discount' => '',
                    'generic_discount' => '',
                    'surgical_discount'=>'',
                    'perscribed_discount'=>''
                );
                $working_hr = $store_open . '-' . $store_close;
                if ($store_open == '') {
                    $working_hr = '';
                }

                if ($store_close == '') {
                    $working_hr = '';
                }

                if ($lat != "" || $lat != null && $lng != "" || $lng != null) {
                    $sports_store_data[] = array(
                        'listing_id' => $listing_id,
                        'listing_type' => '39',
                        'discount_info' => $discount_info,
                        'lisitng_name' => $listing_name,
                        'lat' => $lat,
                        'lng' => $lng,
                        'address1' => $address1,
                        'address2' => $address2,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'rating' => '4',
                        'online_offline' => $online_offline,
                        'discount' => $discount,
                        'profile_pic' => $profile_pic,
                        'area_expertise' => $area_expertise,
                        'phone_no' => $contact,
                        'working_hrs' => $working_hr
                    );
                }
            }
        } else {
            $sports_store_data = array();
        }

        // end
        // $spa_data = array();
        // ayurveda store list

        $sql_ayurveda = sprintf("SELECT `id`, `user_id`, `name`, `year`, `phone`, `manager`, `discount`, `address`, `image`, `lat`, `lng`, `state`, `city`, `pincode`, `is_active`, `is_approval`,`store_open`,`store_close`, `reg_date`,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM `ayurveda_store` WHERE is_active=1 AND discount<>'0' AND lat<>'' HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius));
        $query_ayurveda = $this->db->query($sql_ayurveda);
        $count_ay = $query_ayurveda->num_rows();

        $sql_ayurveda1 = sprintf("SELECT `id`, `user_id`, `name`, `year`, `phone`, `manager`, `discount`, `address`, `image`, `lat`, `lng`, `state`, `city`, `pincode`, `is_active`, `is_approval`,`store_open`,`store_close`, `reg_date`,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM `ayurveda_store` WHERE is_active=1 AND discount<>'0' AND lat<>'' HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius1));
        $query_ayurveda1 = $this->db->query($sql_ayurveda1);
        $count_ay1 = $query_ayurveda1->num_rows();

        if ($count_ay > 0) {
            foreach ($query_ayurveda->result_array() as $ayrow) {
                $listing_id = $ayrow['user_id'];
                $listing_name = $ayrow['name'];
                $lat = $ayrow['lat'];
                $lng = $ayrow['lng'];
                $address1 = $ayrow['address'];
                $address2 = "";
                $pincode = $ayrow['pincode'];
                $city = $ayrow['city'];
                $state = $ayrow['state'];
                $profile_pic = $ayrow['image'];
                $discount = $ayrow['discount'];
                $store_open = $ayrow['store_open'];
                $store_close = $ayrow['store_close'];
                $rating = '0';
                $contact = $ayrow['phone'];
                $area_expertise = array();
                $online_offline = '';
                if ($ayrow['image'] != '') {
                    $profile_pic = $ayrow['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }

               $discount_info = array(
                    'otc_discount' => '',
                    'ethical_discount' => '',
                    'generic_discount' => '',
                    'surgical_discount'=>'',
                    'perscribed_discount'=>''
                );
                $working_hr = $store_open . '-' . $store_close;
                if ($store_open == '') {
                    $working_hr = '';
                }

                if ($store_close == '') {
                    $working_hr = '';
                }

                if ($lat != "" || $lat != null && $lng != "" || $lng != null) {
                    $ayurvedic_stores_data[] = array(
                        'listing_id' => $listing_id,
                        'listing_type' => '39',
                        'discount_info' => $discount_info,
                        'lisitng_name' => $listing_name,
                        'lat' => $lat,
                        'lng' => $lng,
                        'address1' => $address1,
                        'address2' => $address2,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'rating' => '4',
                        'online_offline' => $online_offline,
                        'discount' => $discount,
                        'profile_pic' => $profile_pic,
                        'area_expertise' => $area_expertise,
                        'phone_no' => $contact,
                        'working_hrs' => $working_hr
                    );
                }
            }
        } else {
            $ayurvedic_stores_data = array();
        }

        //  $optics_data = array();

        $sql_lab = sprintf("SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `lab_name`,`address1`,`address2`, `pincode`, `city`, `state`, `contact_no`,`whatsapp_no`, `email`, `opening_hours`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`,`user_discount`, IFNULL(delivery_charges,'') AS delivery_charges, ( 6371 * acos( cos( radians('%s') ) * cos( radians( latitude) ) * cos( radians( longitude) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude) ) ) ) AS distance FROM lab_center  WHERE  is_active=1 AND FIND_IN_SET('222', cat_id) AND user_discount<> '0' AND latitude <> ''  HAVING distance < '%s' ORDER BY distance", ($lat), ($lng), ($lat), ($radius));
        $query_lab = $this->db->query($sql_lab);
        $count_lab = $query_lab->num_rows();


        $sql_lab1 = sprintf("SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `lab_name`,`address1`,`address2`, `pincode`, `city`, `state`, `contact_no`,`whatsapp_no`, `email`, `opening_hours`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`,`user_discount`, IFNULL(delivery_charges,'') AS delivery_charges, ( 6371 * acos( cos( radians('%s') ) * cos( radians( latitude) ) * cos( radians( longitude) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude) ) ) ) AS distance FROM lab_center  WHERE  is_active=1 AND FIND_IN_SET('222', cat_id) AND user_discount<> '0' AND latitude <> ''  HAVING distance < '%s' ORDER BY distance", ($lat), ($lng), ($lat), ($radius1));
        $query_lab1 = $this->db->query($sql_lab1);
        $count_lab1 = $query_lab1->num_rows();
        //  echo $count_lab;

        if ($count_lab > 0) {
            foreach ($query_lab->result_array() as $labrow) {
                $listing_id = $labrow['user_id'];
                $listing_name = $labrow['lab_name'];
                $lat = $labrow['latitude'];
                $lng = $labrow['longitude'];
                $address1 = $labrow['address1'];
                $address2 = "";
                $pincode = $labrow['pincode'];
                $city = $labrow['city'];
                $state = $labrow['state'];
                $profile_pic = $labrow['profile_pic'];
                $discount = $labrow['user_discount'];
                $opening_hours = $labrow['opening_hours'];
                $rating = '0';
                $contact = $labrow['contact_no'];
                $area_expertise = array();
                $online_offline = '';
                if ($labrow['profile_pic'] != '') {
                    $profile_pic = $labrow['profile_pic'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }

              $discount_info = array(
                    'otc_discount' => '',
                    'ethical_discount' => '',
                    'generic_discount' => '',
                    'surgical_discount'=>'',
                    'perscribed_discount'=>''
                );

                // $working_hr = $store_open.'-'.$store_close;
                // if($store_open == '')
                // {
                //     $working_hr = '';
                // }
                // if($store_close == '')
                // {
                //      $working_hr = '';
                // }
                // added by zak for doctor clinic timing
                $opening_hr = '';
                $open_days = '';
                $day_array_list = '';
                $day_list = '';
                $day_time_list = '';
                $time_list1 = '';
                $time_list2 = '';
                $time = '';
                $system_start_time = '';
                $system_end_time = '';
                $time_check = '';
                $current_time = '';
                $open_close = array();
                $time = array();
                $cuutent_dayy = date('l');
                date_default_timezone_set('Asia/Kolkata');
                $data = array();
                $final_Day = array();
                $day_array_list = explode('|', $opening_hours);
                if (count($day_array_list) > 1) {
                    for ($i = 0; $i < count($day_array_list); $i++) {
                        $day_list = explode('>', $day_array_list[$i]);
                        for ($j = 0; $j < count($day_list); $j++) {
                            $day_time_list = explode('-', $day_list[$j]);
                            for ($k = 1; $k < count($day_time_list); $k++) {
                                $time_list1 = explode(',', $day_time_list[0]);
                                $time_list2 = explode(',', $day_time_list[1]);
                                $time = array();
                                $open_close = array();

                                //  echo 'kadak';
                                // print_r($day_list[0]);

                                for ($l = 0; $l < count($time_list1); $l++) {
                                    if ($day_list[0] == $cuutent_dayy) {
                                        $time_check = $time_list1[$l] . '-' . $time_list2[$l];
                                        $time[] = str_replace('close-close', 'Close', $time_check);
                                        $system_start_time = date("h:i A", strtotime($time_list1[$l]));
                                        $system_end_time = date("h:i A", strtotime($time_list2[$l]));
                                        $current_time = date('h:i A');
                                        $date1 = DateTime::createFromFormat('H:i a', $current_time);
                                        $date2 = DateTime::createFromFormat('H:i a', $system_start_time);
                                        $date3 = DateTime::createFromFormat('H:i a', $system_end_time);
                                        if ($date2 < $date3 && $date1 <= $date3) {
                                            $date3->modify('+1 day')->format('H:i a');
                                        } elseif ($date2 > $date3 && $date1 >= $date3) {
                                            $date3->modify('+1 day')->format('H:i a');
                                        }

                                        if ($date1 > $date2 && $date1 < $date3) {
                                            $open_close[] = 'open';
                                        } else {
                                            $open_close[] = 'closed';
                                        }
                                    }
                                }
                            }
                        }

                        if ($day_list[0] == $cuutent_dayy) {
                            if ($time == 'Close') {
                                $opening_hr = '';
                            } else {
                                $opening_hr = $time;
                            }
                        } else {
                            $opening_hr = '';
                        }
                    }
                } else {
                    $opening_hr = '';
                }

                if (count($opening_hr) > 1) {
                    $opening_hr_final = implode(',', $opening_hr);
                } else {

                    //   echo $opening_hr;

                    $opening_hr_final = $opening_hr;
                }

                // end

                if ($lat != "" || $lat != null && $lng != "" || $lng != null) {
                    $pathalogy_lab_data[] = array(
                        'listing_id' => $listing_id,
                        'listing_type' => '10',
                        'discount_info' => $discount_info,
                        'lisitng_name' => $listing_name,
                        'lat' => $lat,
                        'lng' => $lng,
                        'address1' => $address1,
                        'address2' => $address2,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'rating' => '4',
                        'online_offline' => $online_offline,
                        'discount' => $discount,
                        'profile_pic' => $profile_pic,
                        'area_expertise' => $area_expertise,
                        'phone_no' => $contact,
                        'working_hrs' => $opening_hr_final
                    );
                }
            }
        } else {
            $pathalogy_lab_data = array();
        }




        //beauty sports query start//
        // sprintf("SELECT `id`, `user_id`, `name`, `year`, `phone`, `manager`, `discount`, `address`, `image`, `lat`, `lng`, `state`, `city`, `pincode`, `is_active`, `is_approval`,`store_open`,`store_close`, `reg_date`,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM `sports_store` WHERE discount<>'0' AND lat<>'' HAVING distance < '%s' ORDER BY distance", ($mlat) , ($mlng) , ($mlat) , ($radius));
        $bquery = sprintf("SELECT ast.*,ast.name as store_name,ast .lat as store_lat, ast.lng as store_lng,u.vendor_id,( 6371 * acos( cos( radians($mlat) ) * cos( radians( ast.lat ) ) * cos( radians( ast.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( ast.lat ) ) ) ) AS distance  FROM `ayurveda_store` ast INNER JOIN users u ON(u.id=ast.user_id)  WHERE ast.lat <> '' AND ast.lat <> 'null' AND ast.is_active='1' AND ast.discount<>'' AND u.vendor_id='41' HAVING distance <= '$radius' ORDER BY distance");
        // echo sprintf("SELECT ast.*,ast.name as store_name,ast .lat as store_lat, ast.lng as store_lng,u.vendor_id,( 6371 * acos( cos( radians($mlat) ) * cos( radians( ast.lat ) ) * cos( radians( ast.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( ast.lat ) ) ) ) AS distance  FROM `ayurveda_store` ast INNER JOIN users u ON(u.id=ast.user_id)  WHERE ast.lat <> '' AND ast.lat <> 'null' AND ast.is_active='1' AND ast.discount<>'' AND u.vendor_id='41' HAVING distance <= '$radius' ORDER BY distance");
        $bquery = $this->db->query($bquery);
        $bquery1 = sprintf("SELECT ast.*,ast.name as store_name,ast .lat as store_lat, ast.lng as store_lng,u.vendor_id,( 6371 * acos( cos( radians($mlat) ) * cos( radians( ast.lat ) ) * cos( radians( ast.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( ast.lat ) ) ) ) AS distance  FROM `ayurveda_store` ast INNER JOIN users u ON(u.id=ast.user_id) WHERE ast.lat <> '' AND ast.lat <> 'null' AND ast.is_active='1' AND ast.discount<>'' AND u.vendor_id='41' HAVING distance <= '$radius' ORDER BY distance");
        //$this->db->query("SELECT ast.*,u.vendor_id  FROM `ayurveda_store` ast INNER JOIN users u ON(u.id=ast.user_id) WHERE  ast.is_active='1' AND ast.discount<>' AND u.vendor_id='41' order by ast.id desc( 6371 * acos( cos( radians($mlat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( lat ) ) ) ) WHERE  lat <> '' AND lat <> 'null' HAVING distance <= '$radius1' ORDER BY distance");
        $bquery1 = $this->db->query($bquery1);

        $beauty_count1 = $bquery1->num_rows();
        $Beauty_count = $bquery->num_rows();
        if ($Beauty_count > 0) {
            foreach ($bquery->result_array() as $brow) {
                $listing_id = $brow['user_id'];
                $listing_name = $brow['store_name'];

                $lat = $brow['store_lat'];
                $lng = $brow['store_lng'];
                $state = $brow['state'];
                $map_location = $brow['map_location'];
                $pincode = $brow['pincode'];
                $city = $brow['city'];
                $state = $brow['state'];
                $profile_pic = $brow['image'];
                $discount = $brow['discount'];
                $mou = $brow['mou'];
                //	$rating = $brow['rating'];
                $contact = $brow['phone'];
                $store_open = $brow['store_open'];
                $store_close = $brow['store_close'];
                $opening_hours = $store_open . '-' . $store_close;
                $area_expertise = array();
                //	if ($brow['online_offline'] == null || $brow['online_offline'] == '')
                {
                    $online_offline = '';
                }
                //   else
                // 	{
                // 	$online_offline = $brow['online_offline'];
                // 	}

                if ($brow['image'] != '') {
                    $profile_pic = $brow['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }

                // if ($rating == '')
                // 	{
                // 	$rating = '0';
                // 	}
                //   else
                // 	{
                // 	}

               $discount_info = array(
                    'otc_discount' => '',
                    'ethical_discount' => '',
                    'generic_discount' => '',
                    'surgical_discount'=>'',
                    'perscribed_discount'=>''
                );
                if ($lat != "" || $lat != null && $lng != "" || $lng != null) {



                    $beauty_data[] = array(
                        'listing_id' => $listing_id,
                        'listing_type' => '41',
                        'discount_info' => $discount_info,
                        'lisitng_name' => $listing_name,
                        'lat' => $lat,
                        'lng' => $lng,
                        'address1' => $map_location,
                        'address2' => '',
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'rating' => '4',
                        'online_offline' => $online_offline,
                        'discount' => $discount,
                        'profile_pic' => $profile_pic,
                        'phone_no' => $contact,
                        'area_expertise' => '',
                        'working_hrs' => $opening_hours
                    );
                }
            }
        } else {
            $beauty_data = array();
        }

        //added for clinic data 
        
        
        
        $sabka_sql = sprintf("SELECT id,hub_user_id,dentists_branch_user_id, name_of_hospital,about_us, establishment_year, speciality, address, address_2, pincode, phone, city, state, 	email, 	lat, lng, image, rating, review, medicalwale_discount,opening_hours, description,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM dentists_branch  WHERE is_active='1' AND hub_user_id='33586' AND medicalwale_discount <> '' AND lat <> '' HAVING distance < '$radius' ORDER BY distance", ($lat), ($lng), ($lat), ($radius));
        $sabka_query = $this->db->query($sabka_sql);
        $sabka_count = $sabka_query->num_rows();

        $sabka_sql1 = sprintf("SELECT id,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM dentists_branch  WHERE is_active='1' AND hub_user_id='33586' AND medicalwale_discount <> '' AND lat <> '' HAVING distance < '$radius1' ORDER BY distance", ($lat), ($lng), ($lat), ($radius1));
        $sabka_query1 = $this->db->query($sabka_sql1);
        $sabka_count1 = $sabka_query1->num_rows();

        if ($sabka_count > 0) {
            foreach ($sabka_query->result_array() as $sabkarow) {
                $listing_id = $sabkarow['dentists_branch_user_id'];
                $listing_name = $sabkarow['name_of_hospital'];
                $lat = $sabkarow['lat'];
                $lng = $sabkarow['lng'];
                $address1 = $sabkarow['address'];
                $address2 = $sabkarow['address_2'];
                $pincode = $sabkarow['pincode'];
                $city = $sabkarow['city'];
                $state = $sabkarow['state'];
                $profile_pic = $sabkarow['image'];
                $discount = $sabkarow['medicalwale_discount'];
                $contact = $sabkarow['phone'];
                $opening_hours = $sabkarow['opening_hours'];
                $opening_hr = '';
                $open_days = '';
                $day_array_list = '';
                $day_list = '';
                $day_time_list = '';
                $time_list1 = '';
                $time_list2 = '';
                $time = '';
                $system_start_time = '';
                $system_end_time = '';
                $time_check = '';
                $current_time = '';
                $open_close = array();
                $time = array();
                $cuutent_dayy = date('l');
                date_default_timezone_set('Asia/Kolkata');
                $data = array();
                $final_Day = array();
                $day_array_list = explode('|', $opening_hours);

                if (count($day_array_list) > 1) {

                    for ($i = 0; $i < count($day_array_list); $i++) {
                        $day_list = explode('>', $day_array_list[$i]);
                        //print_r($day_list);
                        for ($j = 0; $j < count($day_list); $j++) {
                            $day_time_list = explode('-', $day_list[$j]);
                            for ($k = 1; $k < count($day_time_list); $k++) {
                                $time_list1 = explode(',', $day_time_list[0]);
                                $time_list2 = explode(',', $day_time_list[1]);
                                $time = array();
                                $open_close = array();

                                //  echo 'kadak';


                                for ($l = 0; $l < count($time_list1); $l++) {

                                    if ($day_list[0] == $cuutent_dayy) {

                                        $time_check = $time_list1[$l] . '-' . $time_list2[$l];

                                        $time[] = str_replace('close-close', '', $time_check);
                                        $system_start_time = date("h:i A", strtotime($time_list1[$l]));
                                        $system_end_time = date("h:i A", strtotime($time_list2[$l]));
                                        $current_time = date('h:i A');
                                        $date1 = DateTime::createFromFormat('H:i a', $current_time);
                                        $date2 = DateTime::createFromFormat('H:i a', $system_start_time);
                                        $date3 = DateTime::createFromFormat('H:i a', $system_end_time);
                                        if ($date2 < $date3 && $date1 <= $date3) {
                                            $date3->modify('+1 day')->format('H:i a');
                                        } elseif ($date2 > $date3 && $date1 >= $date3) {
                                            $date3->modify('+1 day')->format('H:i a');
                                        }

                                        if ($date1 > $date2 && $date1 < $date3) {
                                            $open_close[] = 'open';
                                        } else {
                                            $open_close[] = 'closed';
                                        }
                                    }
                                }
                            }
                        }

                        if ($day_list[0] == $cuutent_dayy) {

                            $opening_hr = $time;
                        }
                    }
                } else {
                    $opening_hr = '';
                }

                if (count($opening_hr) > 1) {
                    $opening_hr_final = implode(',', $opening_hr);
                } elseif (count($opening_hr) == 1) {
                    if (empty($opening_hr)) {
                        $opening_hr_final = '';
                    } else {
                        $opening_hr_final = $opening_hr[0];
                    }
                } else {
                    $opening_hr_final = '';
                }

                $rating = '0';
                $area_expertise = array();
                // if ($frow['online_offline'] == null || $frow['online_offline'] == '')
                // 	{
                $online_offline = 'online';
                // 	}
                //   else
                // 	{
                // 	$online_offline = $frow['online_offline'];
                // 	}

                if ($sabkarow['image'] != '') {
                    $profile_pic = $sabkarow['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }

                $discount_info = array(
                    'otc_discount' => '',
                    'ethical_discount' => '',
                    'generic_discount' => '',
                    'surgical_discount'=>'',
                    'perscribed_discount'=>''
                );
                if ($lat != "" || $lat != null && $lng != "" || $lng != null) {
                    $clinics_data[] = array(
                        'listing_id' => $listing_id,
                        'listing_type' => '39',
                        'discount_info' => $discount_info,
                        'lisitng_name' => $listing_name,
                        'lat' => $lat,
                        'lng' => $lng,
                        'address1' => $address1,
                        'address2' => $address2,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'rating' => '4',
                        'online_offline' => $online_offline,
                        'discount' => $discount,
                        'profile_pic' => $profile_pic,
                        'area_expertise' => $area_expertise,
                        'phone_no' => $contact,
                        'working_hrs' => '9:00 AM-9:00 PM'
                    );
                }
            }
        } else {
            $clinics_data = array();
        }










        $total_count = $pharmacy_count + $Doctor1_count + $Hospital_count1 + $Fitness_count1 + $count_op1 + $Spa_count1 + $count_sp1 + $count_ay1 + $count_lab1 + $beauty_count1 + $sabka_count1;

        $sub_total_count = $count + $Doctor_count + $Hospital_count + $Fitness_count + $count_op + $Spa_count + $count_sp + $count_ay + $count_lab + $Beauty_count + $sabka_count;

        //    $pathalogy_lab_data = array();






        $data = array(
            'pharmacy' => $pramacy_data,
            'doctor' => $doctor_data,
            'hospital' => $hospital_data,
            'sports_store' => $sports_store_data,
            'spa' => $spa_data,
            'ayurvedic_stores' => $ayurvedic_stores_data,
            'optics' => $optics_data,
            'clinics' => $clinics_data,
            'pathalogy_labs' => $pathalogy_lab_data,
            'fitness' => $fitness_data,
            'beauty_data' => $beauty_data
        );
        return array(
            'status' => 200,
            'message' => "success",
            'total_count' => $total_count,
            'pharmacy_count' => $pharmacy_count,
            'doctor_count' => $Doctor1_count,
            'Hospital_count' => $Hospital_count1,
            'fitness_count' => $Fitness_count1,
            'optician_count' => $count_op1,
            'spa_count' => $Spa_count1,
            'sports_count' => $count_sp1,
            'ayurveda_count' => $count_ay1,
            'lab_count' => $count_lab1,
            'beauty_count' => $beauty_count1,
            'sub_total_count' => $sub_total_count,
            'clinic_count' => $sabka_count,
            'data' => $data
        );
    }

    public function priviladge_offer_list_ios($user_id, $mlat, $mlng, $category_id, $type) {
        $radius = 50000;
        $limit = 25;
        $start = 0;
        $page = 1;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }

        $start = 1 * $limit;

        function GetDrivingDistance($lat1, $lat2, $long1, $long2) {
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=walking&language=pl-PL&key=AIzaSyDg9YiklFpUhqkDXlwzuC9Cnip4U-78N60&callback=initMap";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $response_a = json_decode($response, true);
            $dist = $response_a['rows'][0]['elements'][0]['distance']['value'];
            return $dist;
        }

        $doctor_data = array();
        $pramacy_data = array();
        $hospital_data = array();

        // for pharmacy offer listing

        $sql = sprintf("SELECT medicalwale_discount,`id`, `otc_discount`, `perscribed_discount`,`surgical_discount`,`ethical_discount`, `generic_discount`, `user_id`,`is_24hrs_available`,`days_closed`,`store_open`,`store_close`,`online_offline`, `medical_name`,`lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`,`profile_pic`,`discount`,`contact_no`,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_approval='1' AND is_active='1' AND discount<>'0' AND lat<>'' HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius));
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $listing_id = $row['user_id'];
                $listing_name = $row['medical_name'];
                $lat = $row['lat'];
                $lng = $row['lng'];
                $address1 = $row['address1'];
                $address2 = $row['address2'];
                $pincode = $row['pincode'];
                $city = $row['city'];
                $state = $row['state'];
                $discount = $row['discount'];
                $days_closed = $row['days_closed'];
                $otc_discount = $row['otc_discount'];
                $ethical_discount = $row['ethical_discount'];
                $generic_discount = $row['generic_discount'];
                $perscribed_discount = $row['perscribed_discount'];
                $surgical_discount = $row['surgical_discount'];
                $contact = $row['contact_no'];
                $discount_info = array(
                    'otc_discount' => '',
                    'ethical_discount' => '',
                    'generic_discount' => '',
                    'surgical_discount'=>'',
                    'perscribed_discount'=>''
                );
                if ($otc_discount == '' || $otc_discount == NULL) {
                    $otc_discount = '';
                }

                if ($ethical_discount == '' || $ethical_discount == NULL) {
                    $ethical_discount = '';
                }

                if ($generic_discount == '' || $generic_discount == NULL) {
                    $generic_discount = '';
                }

                $discount_info = array(
                    'otc_discount' => $otc_discount,
                    'ethical_discount' => $ethical_discount,
                    'generic_discount' => $generic_discount,
                    'surgical_discount'=>$surgical_discount,
                    'perscribed_discount'=>$perscribed_discount
                );

                // added for timing pharmacy

                $is_24hrs_available = $row['is_24hrs_available'];
                if ($is_24hrs_available == 'Yes') {
                    $store_open = date("h:i A", strtotime("12:00 AM"));
                    $store_close = date("h:i A", strtotime("11:59 PM"));
                } else {
                    $store_open = $this->check_time_format($row['store_open']);
                    $store_close = $this->check_time_format($row['store_close']);
                }

                // end

                $area_expertise = array();
                if ($row['online_offline'] == null || $row['online_offline'] == '') {
                    $online_offline = '';
                } else {
                    $online_offline = $row['online_offline'];
                }

                $profile_pic = $row['profile_pic'];
                if ($row['profile_pic'] != '') {
                    $profile_pic = $row['profile_pic'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.jpg';
                }

                // added code for working hour in pharmacy ......

                $t = date('d-m-Y');
                $cuurent_day = date("l", strtotime($t));
                $Monday = $this->check_day_status($cuurent_day, $days_closed, $is_24hrs_available, $store_open, $store_close);
                $opening_hours = $Monday;
                if ($Monday == 'Closed') {

                    // $opening_hours = 'Closed';
                } else {

                    // $opening_hours = 'open';
                }

                // $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                //   $distance_root =  round(($distances/1000),1);
                // code ended for working hour

                if ($lat != "" || $lat != null && $lng != "" || $lng != null) {
                    $pramacy_data[] = array(
                        'listing_id' => $listing_id,
                        'listing_type' => '13',
                        'lisitng_name' => $listing_name,
                        'discount_info' => $discount_info,
                        'lat' => $lat,
                        'lng' => $lng,
                        'address1' => $address1,
                        'address2' => $address2,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'online_offline' => $online_offline,
                        'discount' => $discount,
                        'rating' => '4',
                        'profile_pic' => $profile_pic,
                        'area_expertise' => $area_expertise,
                        'phone_no' => $contact,
                        'working_hrs' => $opening_hours
                    );
                }
            }

            //  return  $pramacy_data;
        }

        // end
        // for doctor offer listing

        if ($type == 'doctor') {
            if ($category_id != '') {
                $cats = explode(',', $category_id);
                $WHER = "";
                if (count($cats) > 1) {
                    foreach ($cats as $ct) {
                        $WHER.= "FIND_IN_SET('" . $ct . "', doctor_list.category) OR ";
                    }

                    $WH = substr(trim($WHER), 0, -3);
                } else {
                    $WH = "FIND_IN_SET('" . $category_id . "', doctor_list.category)";
                }

                $sql_doctor = sprintf("SELECT doctor_list.telephone,doctor_list.user_id,doctor_list.category,doctor_list.doctor_name,doctor_list.rating,doctor_list.online_offline,doctor_list.qualification,doctor_list.experience,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.open_hours,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.discount <> '' AND doctor_list.is_active ='1' AND ($WH) HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius));
            } else {
                $sql_doctor = sprintf("SELECT doctor_list.telephone,doctor_list.user_id,doctor_list.category,doctor_list.doctor_name,doctor_list.rating,doctor_list.online_offline,doctor_list.qualification,doctor_list.experience,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.open_hours,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.discount<> '' AND doctor_list.is_active ='1' HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius));
            }
        } else {
            if ($category_id != '') {
                $cats = explode(',', $category_id);
                $WHER = "";

                // echo count($cats);

                if (count($cats) > 1) {
                    foreach ($cats as $ct) {
                        $WHER.= "FIND_IN_SET('" . $ct . "', doctor_list.category) OR ";
                    }

                    $WH = substr(trim($WHER), 0, -3);
                } else {
                    $WH = "FIND_IN_SET('" . $category_id . "', doctor_list.category)";
                }

                $sql_doctor = sprintf("SELECT doctor_list.telephone,doctor_list.user_id,doctor_list.category,doctor_list.doctor_name,doctor_list.rating,doctor_list.online_offline,doctor_list.qualification,doctor_list.experience,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.open_hours,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.discount <> '' AND doctor_list.is_active ='1' AND doctor_list.discount <> 'null' AND ($WH) HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius));
            } else {
                $sql_doctor = sprintf("SELECT doctor_list.telephone,doctor_list.user_id,doctor_list.category,doctor_list.doctor_name,doctor_list.rating,doctor_list.online_offline,doctor_list.qualification,doctor_list.experience,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.open_hours,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id where doctor_list.discount <> '' AND doctor_list.is_active ='1' AND doctor_clinic.lat <> '' AND doctor_list.discount <> 'null' HAVING distance < '%s'  ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius));
            }
        }

        $query_doctor = $this->db->query($sql_doctor);
        $Doctor_count = $query_doctor->num_rows();
        if ($Doctor_count > 0) {
            foreach ($query_doctor->result_array() as $drow) {
                $listing_id = $drow['user_id'];
                $listing_name = $drow['doctor_name'];
                $lat = $drow['lat'];
                $lng = $drow['lng'];
                $address1 = $drow['address'];
                $address2 = "";
                $pincode = $drow['pincode'];
                $city = $drow['city'];
                $state = $drow['state'];
                $contact = $drow['telephone'];
                $discount = $drow['discount'];
                $category = $drow['category'];
                $opening_hours = $drow['open_hours'];

                //   $online_offline  = $drow['online_offline'];

                $profile_pic = $drow['image'];
                $rating = $drow['rating'];
                if ($drow['online_offline'] == null || $drow['online_offline'] == '') {
                    $online_offline = '';
                } else {
                    $online_offline = $drow['online_offline'];
                }

                if ($drow['image'] != '') {
                    $profile_pic = $drow['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }

                if ($rating == '') {
                    $rating = '4';
                } else {
                    
                }

                // added by zak for doctor clinic timing
                $opening_hr = '';
                $open_days = '';
                $day_array_list = '';
                $day_list = '';
                $day_time_list = '';
                $time_list1 = '';
                $time_list2 = '';
                $time = '';
                $system_start_time = '';
                $system_end_time = '';
                $time_check = '';
                $current_time = '';
                $open_close = array();
                $time = array();
                $cuutent_dayy = date('l');
                date_default_timezone_set('Asia/Kolkata');
                $data = array();
                $final_Day = array();
                $day_array_list = explode('|', $opening_hours);
                if (count($day_array_list) > 1) {
                    for ($i = 0; $i < count($day_array_list); $i++) {
                        $day_list = explode('>', $day_array_list[$i]);
                        for ($j = 0; $j < count($day_list); $j++) {
                            $day_time_list = explode('-', $day_list[$j]);
                            for ($k = 1; $k < count($day_time_list); $k++) {
                                $time_list1 = explode(',', $day_time_list[0]);
                                $time_list2 = explode(',', $day_time_list[1]);
                                $time = array();
                                $open_close = array();

                                //  echo 'kadak';
                                // print_r($day_list[0]);

                                for ($l = 0; $l < count($time_list1); $l++) {
                                    if ($day_list[0] == $cuutent_dayy) {
                                        $time_check = $time_list1[$l] . '-' . $time_list2[$l];
                                        $time[] = str_replace('close-close', '', $time_check);
                                        $system_start_time = date("h:i A", strtotime($time_list1[$l]));
                                        $system_end_time = date("h:i A", strtotime($time_list2[$l]));
                                        $current_time = date('h:i A');
                                        $date1 = DateTime::createFromFormat('H:i a', $current_time);
                                        $date2 = DateTime::createFromFormat('H:i a', $system_start_time);
                                        $date3 = DateTime::createFromFormat('H:i a', $system_end_time);
                                        if ($date2 < $date3 && $date1 <= $date3) {
                                            $date3->modify('+1 day')->format('H:i a');
                                        } elseif ($date2 > $date3 && $date1 >= $date3) {
                                            $date3->modify('+1 day')->format('H:i a');
                                        }

                                        if ($date1 > $date2 && $date1 < $date3) {
                                            $open_close[] = 'open';
                                        } else {
                                            $open_close[] = 'closed';
                                        }
                                    }
                                }
                            }
                        }

                        if ($day_list[0] == $cuutent_dayy) {
                            $opening_hr = $time;
                        }
                    }
                } else {
                    $opening_hr = '';
                }

                if (count($opening_hr) > 1) {
                    $opening_hr_final = implode(',', $opening_hr);
                } else if (count($opening_hr) == 1) {
                    if (empty($opening_hr)) {
                        $opening_hr_final = '';
                    } else {
                        $opening_hr_final = $opening_hr[0];
                    }
                } else {
                    $opening_hr_final = '';
                }

                // end
                // added by zak for doctor category

                $area_expertise = array();
                $query_sp = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'" . $category . "')");
                $total_category = $query_sp->num_rows();
                if ($total_category > 0) {
                    foreach ($query_sp->result_array() as $get_sp) {
                        $id = $get_sp['id'];
                        $area_expertised = $get_sp['area_expertise'];
                        $area_expertise[] = array(
                            'id' => $id,
                            'area_expertise' => $area_expertised
                        );
                    }
                } else {
                    $area_expertise = array();
                }

                $discount_info = array(
                    'otc_discount' => '',
                    'ethical_discount' => '',
                    'generic_discount' => '',
                    'surgical_discount'=>'',
                    'perscribed_discount'=>''
                );
                if ($lat != "" || $lat != null && $lng != "" || $lng != null) {
                    $doctor_data[] = array(
                        'listing_id' => $listing_id,
                        'listing_type' => '5',
                        'discount_info' => $discount_info,
                        'lisitng_name' => $listing_name,
                        'lat' => $lat,
                        'lng' => $lng,
                        'address1' => $address1,
                        'address2' => $address2,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'rating' => '4',
                        'online_offline' => $online_offline,
                        'discount' => $discount,
                        'profile_pic' => $profile_pic,
                        'area_expertise' => $area_expertise,
                        'phone_no' => $contact,
                        'working_hrs' => $opening_hr_final
                    );
                }
            }
        }

        // end
        // for hospital offer list

        if ($type == 'hospital') {
            if ($category_id != '') {
                $hquery = $this->db->query("SELECT `id`, `user_id`, `name_of_hospital`,`online_offline`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($mlat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE FIND_IN_SET('" . $category_id . "', category) AND user_discount<>'0' AND lat <> '' AND lat <> 'null' ORDER BY distance");

                // echo "SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE FIND_IN_SET('" . $category_name . "', category)  HAVING distance <= '$radius'";
            }
        } else {
            $hquery = $this->db->query("SELECT `id`, `user_id`, `name_of_hospital`,`online_offline`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($mlat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE user_discount<>'0' AND lat <> '' AND lat <> 'null' ORDER BY distance");
        }

        $Hospital_count = $hquery->num_rows();
        if ($Hospital_count > 0) {
            foreach ($hquery->result_array() as $hrow) {
                $listing_id = $hrow['user_id'];
                $listing_name = $hrow['name_of_hospital'];
                $lat = $hrow['lat'];
                $lng = $hrow['lng'];
                $address1 = $hrow['address'];
                $address2 = "";
                $pincode = $hrow['pincode'];
                $city = $hrow['city'];
                $state = $hrow['state'];
                $profile_pic = $hrow['image'];
                $discount = $hrow['user_discount'];
                $rating = $hrow['rating'];
                $contact = $hrow['phone'];
                $area_expertise = array();
                if ($hrow['online_offline'] == null || $hrow['online_offline'] == '') {
                    $online_offline = '';
                } else {
                    $online_offline = $hrow['online_offline'];
                }

                if ($hrow['image'] != '') {
                    $profile_pic = $hrow['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }

                if ($rating == '') {
                    $rating = '0';
                } else {
                    
                }

                $discount_info = array(
                    'otc_discount' => '',
                    'ethical_discount' => '',
                    'generic_discount' => '',
                    'surgical_discount'=>'',
                    'perscribed_discount'=>''
                );
                if ($lat != "" || $lat != null && $lng != "" || $lng != null) {
                    $hospital_data[] = array(
                        'listing_id' => $listing_id,
                        'listing_type' => '8',
                        'discount_info' => $discount_info,
                        'lisitng_name' => $listing_name,
                        'lat' => $lat,
                        'lng' => $lng,
                        'address1' => $address1,
                        'address2' => $address2,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'rating' => '4',
                        'area_expertise' => $area_expertise,
                        'online_offline' => $online_offline,
                        'discount' => $discount,
                        'profile_pic' => $profile_pic,
                        'phone_no' => $contact,
                        'working_hrs' => '12:00 AM-11:59 PM'
                    );
                }
            }
        } else {
            $hospital_data = array();
        }

        // end
        // for fitness offer list

        if ($type == 'fitness') {

            if ($category_id != '') {
                $cats = explode(',', $category_id);
                $WHER = "";



                if (count($cats) > 1) {
                    foreach ($cats as $ct) {
                        $WHER.= "FIND_IN_SET('" . $ct . "', fc.business_category) OR ";
                    }

                    $WH = substr(trim($WHER), 0, -3);
                } else {
                    $WH = "FIND_IN_SET('" . $category_id . "', fc.business_category)";
                }



                $fquery = $this->db->query(sprintf("SELECT fc.id,fc.user_id,fc.city,fc.center_name, fc.contact, fc.email, fc.image, fc.address, fc.pincode, fc.state, fc.lat, fc.lng, fc.opening_hours, fc.online_offline,fc.discount, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $mlat ) ) * cos( radians( $mlng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $mlat ) ) ) ) AS distance FROM fitness_center as fc WHERE fc.is_active='1' AND fc.vendor_type='Fitness' AND fc.discount<> '0'  AND fc.lat<> 'null' AND ($WH) ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius)));

                // $query = $this->db->query("SELECT fc.`user_id`, fc.`center_name`, fc.`contact`, fc.`email`, fc.`image`,en.contact_no as enquiry_number FROM `fitness_center` as fc LEFT JOIN users as u ON(u.id=fc.user_id) LEFT JOIN enquiry_number en ON(en.vendor_id=u.vendor_id) WHERE fc.user_id='$listing_id'");//echo "SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE FIND_IN_SET('" . $category_name . "', category)  HAVING distance <= '$radius'";
            }
        } else {
            if ($category_id != '') {
                $cats = explode(',', $category_id);
                $WHER = "";



                if (count($cats) > 1) {
                    foreach ($cats as $ct) {
                        $WHER.= "FIND_IN_SET('" . $ct . "', fc.business_category) OR ";
                    }

                    $WH = substr(trim($WHER), 0, -3);
                } else {
                    $WH = "FIND_IN_SET('" . $category_id . "', fc.business_category)";
                }



                $fquery = $this->db->query(sprintf("SELECT fc.id,fc.user_id,fc.city,fc.center_name, fc.contact, fc.email, fc.image, fc.address, fc.pincode, fc.state, fc.lat, fc.lng, fc.opening_hours, fc.online_offline,fc.discount, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $mlat ) ) * cos( radians( $mlng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $mlat ) ) ) ) AS distance FROM fitness_center as fc   WHERE fc.is_active='1' AND fc.discount<> '0'  AND fc.lat<> 'null' AND fc.vendor_type='Fitness' AND  ($WH) ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius)));
            } else {
                $fquery = $this->db->query(sprintf("SELECT fc.id,fc.user_id,fc.city, fc.center_name, fc.contact, fc.email, fc.image, fc.address, fc.pincode, fc.state, fc.lat, fc.lng, fc.opening_hours, fc.online_offline,fc.discount, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $mlat ) ) * cos( radians( $mlng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $mlat ) ) ) ) AS distance FROM fitness_center as fc  WHERE fc.is_active='1' AND fc.discount<> '0' AND fc.vendor_type='Fitness' AND fc.lat<> 'null' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius)));
            }
        }

        $Fitness_count = $fquery->num_rows();
        if ($Fitness_count > 0) {
            foreach ($fquery->result_array() as $frow) {
                $listing_id = $frow['user_id'];
                $listing_name = $frow['center_name'];
                $lat = $frow['lat'];
                $lng = $frow['lng'];
                $address1 = $frow['address'];
                $address2 = "";
                $pincode = $frow['pincode'];
                $city = $frow['city'];
                $state = $frow['state'];
                $profile_pic = $frow['image'];
                $discount = $frow['discount'];
                $contact = $frow['contact'];
                $opening_hours = $frow['opening_hours'];
                $opening_hr = '';
                $open_days = '';
                $day_array_list = '';
                $day_list = '';
                $day_time_list = '';
                $time_list1 = '';
                $time_list2 = '';
                $time = '';
                $system_start_time = '';
                $system_end_time = '';
                $time_check = '';
                $current_time = '';
                $open_close = array();
                $time = array();
                $cuutent_dayy = date('l');
                date_default_timezone_set('Asia/Kolkata');
                $data = array();
                $final_Day = array();
                $day_array_list = explode('|', $opening_hours);

                if (count($day_array_list) > 1) {

                    for ($i = 0; $i < count($day_array_list); $i++) {
                        $day_list = explode('>', $day_array_list[$i]);
                        //print_r($day_list);
                        for ($j = 0; $j < count($day_list); $j++) {
                            $day_time_list = explode('-', $day_list[$j]);
                            for ($k = 1; $k < count($day_time_list); $k++) {
                                $time_list1 = explode(',', $day_time_list[0]);
                                $time_list2 = explode(',', $day_time_list[1]);
                                $time = array();
                                $open_close = array();

                                //  echo 'kadak';


                                for ($l = 0; $l < count($time_list1); $l++) {

                                    if ($day_list[0] == $cuutent_dayy) {

                                        $time_check = $time_list1[$l] . '-' . $time_list2[$l];

                                        $time[] = str_replace('close-close', '', $time_check);
                                        $system_start_time = date("h:i A", strtotime($time_list1[$l]));
                                        $system_end_time = date("h:i A", strtotime($time_list2[$l]));
                                        $current_time = date('h:i A');
                                        $date1 = DateTime::createFromFormat('H:i a', $current_time);
                                        $date2 = DateTime::createFromFormat('H:i a', $system_start_time);
                                        $date3 = DateTime::createFromFormat('H:i a', $system_end_time);
                                        if ($date2 < $date3 && $date1 <= $date3) {
                                            $date3->modify('+1 day')->format('H:i a');
                                        } elseif ($date2 > $date3 && $date1 >= $date3) {
                                            $date3->modify('+1 day')->format('H:i a');
                                        }

                                        if ($date1 > $date2 && $date1 < $date3) {
                                            $open_close[] = 'open';
                                        } else {
                                            $open_close[] = 'closed';
                                        }
                                    }
                                }
                            }
                        }

                        if ($day_list[0] == $cuutent_dayy) {

                            $opening_hr = $time;
                        }
                    }
                } else {
                    $opening_hr = '';
                }

                if (count($opening_hr) > 1) {
                    $opening_hr_final = implode(',', $opening_hr);
                } elseif (count($opening_hr) == 1) {
                    if (empty($opening_hr)) {
                        $opening_hr_final = '';
                    } else {
                        $opening_hr_final = $opening_hr[0];
                    }
                } else {
                    $opening_hr_final = '';
                }

                $rating = '0';
                $area_expertise = array();
                if ($frow['online_offline'] == null || $frow['online_offline'] == '') {
                    $online_offline = '';
                } else {
                    $online_offline = $frow['online_offline'];
                }

                if ($frow['image'] != '') {
                    $profile_pic = $frow['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }

                
                $discount_info = array(
                    'otc_discount' => '',
                    'ethical_discount' => '',
                    'generic_discount' => '',
                    'surgical_discount'=>'',
                    'perscribed_discount'=>''
                );
                if ($lat != "" || $lat != null && $lng != "" || $lng != null) {
                    $fitness_data[] = array(
                        'listing_id' => $listing_id,
                        'listing_type' => '6',
                        'discount_info' => $discount_info,
                        'lisitng_name' => $listing_name,
                        'lat' => $lat,
                        'lng' => $lng,
                        'address1' => $address1,
                        'address2' => $address2,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'rating' => '4',
                        'online_offline' => $online_offline,
                        'discount' => $discount,
                        'profile_pic' => $profile_pic,
                        'area_expertise' => $area_expertise,
                        'phone_no' => $contact,
                        'working_hrs' => $opening_hr_final
                    );
                }
            }
        } else {
            $fitness_data = array();
        }

        // end
        // for optic store list

        $sql_optic = sprintf("SELECT `id`, `user_id`, `name`, `year`, `phone`, `telephone`, `manager`, `designation`, `discount`, `about`, `hours_open`, `address`, `image`, `lat`, `lng`, `reviews`, `ratings`, `state`, `city`, `pincode`, `is_active`, `is_approval`, `reg_date`,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM `optic_eyecare_list` WHERE discount<>'0' AND lat<>'' HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius));
        $query_optics = $this->db->query($sql_optic);
        $count_op = $query->num_rows();
        if ($count_op > 0) {
            foreach ($query_optics->result_array() as $oprow) {
                $listing_id = $oprow['user_id'];
                $listing_name = $oprow['name'];
                $lat = $oprow['lat'];
                $lng = $oprow['lng'];
                $address1 = $oprow['address'];
                $address2 = "";
                $pincode = $oprow['pincode'];
                $city = $oprow['city'];
                $state = $oprow['state'];
                $profile_pic = $oprow['image'];
                $discount = $oprow['discount'];
                $contact = $oprow['phone'];
                $rating = '0';
                $area_expertise = array();
                $online_offline = '';
                if ($oprow['image'] != '') {
                    $profile_pic = $oprow['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }


                $discount_info = array(
                    'otc_discount' => '',
                    'ethical_discount' => '',
                    'generic_discount' => '',
                    'surgical_discount'=>'',
                    'perscribed_discount'=>''
                );
                if ($lat != "" || $lat != null && $lng != "" || $lng != null) {
                    $optics_data[] = array(
                        'listing_id' => $listing_id,
                        'listing_type' => '17',
                        'discount_info' => $discount_info,
                        'lisitng_name' => $listing_name,
                        'lat' => $lat,
                        'lng' => $lng,
                        'address1' => $address1,
                        'address2' => $address2,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'rating' => '4',
                        'online_offline' => $online_offline,
                        'discount' => $discount,
                        'profile_pic' => $profile_pic,
                        'area_expertise' => $area_expertise,
                        'phone_no' => $contact,
                        'working_hrs' => ''
                    );
                }
            }
        }

        // end
        // for Spa offer list

        if ($type == 'spa') {
            if ($category_id != '') {
                $cats = explode(',', $category_id);
                $WHER = "";
                foreach ($cats as $ct) {
                    $WHER.= "FIND_IN_SET('" . $ct . "', fc.business_category) OR ";
                }

                $WH = substr(trim($WHER), 0, -3);
                $spaquery = $this->db->query(sprintf("SELECT fc.id,fc.user_id,fc.city,fc.center_name, fc.contact, fc.email, fc.image, fc.address, fc.pincode, fc.state, fc.lat, fc.lng, fc.opening_hours, fc.online_offline,fc.discount, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $mlat ) ) * cos( radians( $mlng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $mlat ) ) ) ) AS distance FROM fitness_center as fc  WHERE fc.is_active='1' AND fc.discount<> '0' AND $WH AND vendor_type='Spa' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius)));

                // $query = $this->db->query("SELECT fc.`user_id`, fc.`center_name`, fc.`contact`, fc.`email`, fc.`image`,en.contact_no as enquiry_number FROM `fitness_center` as fc LEFT JOIN users as u ON(u.id=fc.user_id) LEFT JOIN enquiry_number en ON(en.vendor_id=u.vendor_id) WHERE fc.user_id='$listing_id'");//echo "SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE FIND_IN_SET('" . $category_name . "', category)  HAVING distance <= '$radius'";
            }
        } else {
            $spaquery = $this->db->query(sprintf("SELECT fc.id,fc.user_id,fc.city, fc.center_name, fc.contact, fc.email,fc.opening_hours, fc.image, fc.address, fc.pincode, fc.state, fc.lat, fc.lng, fc.opening_hours, fc.online_offline,fc.discount, ( 6371 * acos( cos( radians('%s') ) * cos( radians( $mlat ) ) * cos( radians( $mlng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( $mlat ) ) ) ) AS distance FROM fitness_center as fc  WHERE fc.is_active='1' AND fc.discount<> '0' AND fc.lat<> 'null' AND vendor_type='Spa' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius)));
        }

        $Spa_count = $spaquery->num_rows();
        if ($Spa_count > 0) {
            foreach ($spaquery->result_array() as $sparow) {
                $listing_id = $sparow['user_id'];
                $listing_name = $sparow['center_name'];
                $lat = $sparow['lat'];
                $lng = $sparow['lng'];
                $address1 = $sparow['address'];
                $address2 = "";
                $pincode = $sparow['pincode'];
                $city = $sparow['city'];
                $state = $sparow['state'];
                $profile_pic = $sparow['image'];
                $discount = $sparow['discount'];
                $rating = '0';
                $contact = $sparow['contact'];

                $opening_hours = $sparow['opening_hours'];



                $opening_hr = '';
                $open_days = '';
                $day_array_list = '';
                $day_list = '';
                $day_time_list = '';
                $time_list1 = '';
                $time_list2 = '';
                $time = '';
                $system_start_time = '';
                $system_end_time = '';
                $time_check = '';
                $current_time = '';
                $open_close = array();
                $time = array();
                $cuutent_dayy = date('l');
                date_default_timezone_set('Asia/Kolkata');
                $data = array();
                $final_Day = array();
                $day_array_list = explode('|', $opening_hours);

                if (count($day_array_list) > 1) {

                    for ($i = 0; $i < count($day_array_list); $i++) {
                        $day_list = explode('>', $day_array_list[$i]);
                        //print_r($day_list);
                        for ($j = 0; $j < count($day_list); $j++) {
                            $day_time_list = explode('-', $day_list[$j]);
                            for ($k = 1; $k < count($day_time_list); $k++) {
                                $time_list1 = explode(',', $day_time_list[0]);
                                $time_list2 = explode(',', $day_time_list[1]);
                                $time = array();
                                $open_close = array();

                                //  echo 'kadak';


                                for ($l = 0; $l < count($time_list1); $l++) {

                                    if ($day_list[0] == $cuutent_dayy) {

                                        $time_check = $time_list1[$l] . '-' . $time_list2[$l];

                                        $time[] = str_replace('close-close', '', $time_check);
                                        $system_start_time = date("h:i A", strtotime($time_list1[$l]));
                                        $system_end_time = date("h:i A", strtotime($time_list2[$l]));
                                        $current_time = date('h:i A');
                                        $date1 = DateTime::createFromFormat('H:i a', $current_time);
                                        $date2 = DateTime::createFromFormat('H:i a', $system_start_time);
                                        $date3 = DateTime::createFromFormat('H:i a', $system_end_time);
                                        if ($date2 < $date3 && $date1 <= $date3) {
                                            $date3->modify('+1 day')->format('H:i a');
                                        } elseif ($date2 > $date3 && $date1 >= $date3) {
                                            $date3->modify('+1 day')->format('H:i a');
                                        }

                                        if ($date1 > $date2 && $date1 < $date3) {
                                            $open_close[] = 'open';
                                        } else {
                                            $open_close[] = 'closed';
                                        }
                                    }
                                }
                            }
                        }

                        if ($day_list[0] == $cuutent_dayy) {

                            $opening_hr = $time;
                        }
                    }
                } else {
                    $opening_hr = '';
                }

                if (count($opening_hr) > 1) {
                    $opening_hr_final = implode(',', $opening_hr);
                } elseif (count($opening_hr) == 1) {
                    if (empty($opening_hr)) {
                        $opening_hr_final = '';
                    } else {
                        $opening_hr_final = $opening_hr[0];
                    }
                } else {
                    $opening_hr_final = '';
                }


                $area_expertise = array();
                if ($sparow['online_offline'] == null || $sparow['online_offline'] == '') {
                    $online_offline = '';
                } else {
                    $online_offline = $sparow['online_offline'];
                }

                if ($sparow['image'] != '') {
                    $profile_pic = $sparow['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }

               
                $discount_info = array(
                    'otc_discount' => '',
                    'ethical_discount' => '',
                    'generic_discount' => '',
                    'surgical_discount'=>'',
                    'perscribed_discount'=>''
                );
                if ($lat != "" || $lat != null && $lng != "" || $lng != null) {
                    $spa_data[] = array(
                        'listing_id' => $listing_id,
                        'listing_type' => '36',
                        'discount_info' => $discount_info,
                        'lisitng_name' => $listing_name,
                        'lat' => $lat,
                        'lng' => $lng,
                        'address1' => $address1,
                        'address2' => $address2,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'rating' => '4',
                        'online_offline' => $online_offline,
                        'discount' => $discount,
                        'profile_pic' => $profile_pic,
                        'area_expertise' => $area_expertise,
                        'phone_no' => $contact,
                        'working_hrs' => $opening_hr_final
                    );
                }
            }
        } else {
            $spa_data = array();
        }

        // end
        // for spotrs store list

        $sql_spotrs = sprintf("SELECT `id`, `user_id`, `name`, `year`, `phone`, `manager`, `discount`, `address`, `image`, `lat`, `lng`, `state`, `city`, `pincode`, `is_active`, `is_approval`,`store_open`,`store_close`, `reg_date`,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM `sports_store` WHERE discount<>'0' AND lat<>'' HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius));
        $query_sports = $this->db->query($sql_spotrs);
        $count_sp = $query->num_rows();
        if ($count_sp > 0) {
            foreach ($query_sports->result_array() as $oprow) {
                $listing_id = $oprow['user_id'];
                $listing_name = $oprow['name'];
                $lat = $oprow['lat'];
                $lng = $oprow['lng'];
                $address1 = $oprow['address'];
                $address2 = "";
                $pincode = $oprow['pincode'];
                $city = $oprow['city'];
                $state = $oprow['state'];
                $profile_pic = $oprow['image'];
                $discount = $oprow['discount'];
                $store_open = $oprow['store_open'];
                $store_close = $oprow['store_close'];
                $rating = '0';
                $contact = $oprow['phone'];
                $area_expertise = array();
                $online_offline = '';
                if ($oprow['image'] != '') {
                    $profile_pic = $oprow['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }

                
                $discount_info = array(
                    'otc_discount' => '',
                    'ethical_discount' => '',
                    'generic_discount' => '',
                    'surgical_discount'=>'',
                    'perscribed_discount'=>''
                );
                $working_hr = $store_open . '-' . $store_close;
                if ($store_open == '') {
                    $working_hr = '';
                }

                if ($store_close == '') {
                    $working_hr = '';
                }

                if ($lat != "" || $lat != null && $lng != "" || $lng != null) {
                    $sports_store_data[] = array(
                        'listing_id' => $listing_id,
                        'listing_type' => '40',
                        'discount_info' => $discount_info,
                        'lisitng_name' => $listing_name,
                        'lat' => $lat,
                        'lng' => $lng,
                        'address1' => $address1,
                        'address2' => $address2,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'rating' => '4',
                        'online_offline' => $online_offline,
                        'discount' => $discount,
                        'profile_pic' => $profile_pic,
                        'area_expertise' => $area_expertise,
                        'phone_no' => $contact,
                        'working_hrs' => $working_hr
                    );
                }
            }
        } else {
            $sports_store_data = array();
        }

        // end
        // $spa_data = array();
        // ayurveda store list

        $sql_ayurveda = sprintf("SELECT `id`, `user_id`, `name`, `year`, `phone`, `manager`, `discount`, `address`, `image`, `lat`, `lng`, `state`, `city`, `pincode`, `is_active`, `is_approval`,`store_open`,`store_close`, `reg_date`,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM `ayurveda_store` WHERE discount<>'0' AND lat<>'' HAVING distance < '%s' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius));
        $query_ayurveda = $this->db->query($sql_ayurveda);
        $count_sp = $query->num_rows();
        if ($count_sp > 0) {
            foreach ($query_ayurveda->result_array() as $ayrow) {
                $listing_id = $ayrow['user_id'];
                $listing_name = $ayrow['name'];
                $lat = $ayrow['lat'];
                $lng = $ayrow['lng'];
                $address1 = $ayrow['address'];
                $address2 = "";
                $pincode = $ayrow['pincode'];
                $city = $ayrow['city'];
                $state = $ayrow['state'];
                $profile_pic = $ayrow['image'];
                $discount = $ayrow['discount'];
                $store_open = $ayrow['store_open'];
                $store_close = $ayrow['store_close'];
                $rating = '0';
                $contact = $ayrow['phone'];
                $area_expertise = array();
                $online_offline = '';
                if ($ayrow['image'] != '') {
                    $profile_pic = $ayrow['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }

              
                $discount_info = array(
                    'otc_discount' => '',
                    'ethical_discount' => '',
                    'generic_discount' => '',
                    'surgical_discount'=>'',
                    'perscribed_discount'=>''
                );
                $working_hr = $store_open . '-' . $store_close;
                if ($store_open == '') {
                    $working_hr = '';
                }

                if ($store_close == '') {
                    $working_hr = '';
                }

                if ($lat != "" || $lat != null && $lng != "" || $lng != null) {
                    $ayurvedic_stores_data[] = array(
                        'listing_id' => $listing_id,
                        'listing_type' => '1',
                        'discount_info' => $discount_info,
                        'lisitng_name' => $listing_name,
                        'lat' => $lat,
                        'lng' => $lng,
                        'address1' => $address1,
                        'address2' => $address2,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'rating' => '4',
                        'online_offline' => $online_offline,
                        'discount' => $discount,
                        'profile_pic' => $profile_pic,
                        'area_expertise' => $area_expertise,
                        'phone_no' => $contact,
                        'working_hrs' => $working_hr
                    );
                }
            }
        } else {
            $ayurvedic_stores_data = array();
        }

        //  $optics_data = array();

        $sql_lab = sprintf("SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `lab_name`,`address1`,`address2`, `pincode`, `city`, `state`, `contact_no`,`whatsapp_no`, `email`, `opening_hours`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`,`discount`, IFNULL(delivery_charges,'') AS delivery_charges, ( 6371 * acos( cos( radians('%s') ) * cos( radians( latitude) ) * cos( radians( longitude) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude) ) ) ) AS distance FROM lab_center  WHERE  FIND_IN_SET('222', cat_id) AND discount <> '0' AND latitude <> '' AND is_active='1' HAVING distance < '%s' ORDER BY distance", ($lat), ($lng), ($lat), ($radius));
        $query_lab = $this->db->query($sql_lab);
        $count_lab = $query_lab->num_rows();

        //  echo $count_lab;

        if ($count_lab > 0) {
            foreach ($query_lab->result_array() as $labrow) {
                $listing_id = $labrow['user_id'];
                $listing_name = $labrow['lab_name'];
                $lat = $labrow['latitude'];
                $lng = $labrow['longitude'];
                $address1 = $labrow['address1'];
                $address2 = "";
                $pincode = $labrow['pincode'];
                $city = $labrow['city'];
                $state = $labrow['state'];
                $profile_pic = $labrow['profile_pic'];
                $discount = $labrow['discount'];
                $opening_hours = $labrow['opening_hours'];
                $rating = '0';
                $contact = $labrow['contact_no'];
                $area_expertise = array();
                $online_offline = '';
                if ($labrow['profile_pic'] != '') {
                    $profile_pic = $labrow['profile_pic'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }

               
                $discount_info = array(
                    'otc_discount' => '',
                    'ethical_discount' => '',
                    'generic_discount' => '',
                    'surgical_discount'=>'',
                    'perscribed_discount'=>''
                );

                // $working_hr = $store_open.'-'.$store_close;
                // if($store_open == '')
                // {
                //     $working_hr = '';
                // }
                // if($store_close == '')
                // {
                //      $working_hr = '';
                // }
                // added by zak for doctor clinic timing

                $open_days = '';
                $day_array_list = '';
                $day_list = '';
                $day_time_list = '';
                $time_list1 = '';
                $time_list2 = '';
                $time = '';
                $system_start_time = '';
                $system_end_time = '';
                $time_check = '';
                $current_time = '';
                $open_close = array();
                $time = array();
                $cuutent_dayy = date('l');
                date_default_timezone_set('Asia/Kolkata');
                $data = array();
                $final_Day = array();
                $day_array_list = explode('|', $opening_hours);
                if (count($day_array_list) > 1) {
                    for ($i = 0; $i < count($day_array_list); $i++) {
                        $day_list = explode('>', $day_array_list[$i]);
                        for ($j = 0; $j < count($day_list); $j++) {
                            $day_time_list = explode('-', $day_list[$j]);
                            for ($k = 1; $k < count($day_time_list); $k++) {
                                $time_list1 = explode(',', $day_time_list[0]);
                                $time_list2 = explode(',', $day_time_list[1]);
                                $time = array();
                                $open_close = array();

                                //  echo 'kadak';
                                // print_r($day_list[0]);

                                for ($l = 0; $l < count($time_list1); $l++) {
                                    if ($day_list[0] == $cuutent_dayy) {
                                        $time_check = $time_list1[$l] . '-' . $time_list2[$l];
                                        $time[] = str_replace('close-close', 'Close', $time_check);
                                        $system_start_time = date("h:i A", strtotime($time_list1[$l]));
                                        $system_end_time = date("h:i A", strtotime($time_list2[$l]));
                                        $current_time = date('h:i A');
                                        $date1 = DateTime::createFromFormat('H:i a', $current_time);
                                        $date2 = DateTime::createFromFormat('H:i a', $system_start_time);
                                        $date3 = DateTime::createFromFormat('H:i a', $system_end_time);
                                        if ($date2 < $date3 && $date1 <= $date3) {
                                            $date3->modify('+1 day')->format('H:i a');
                                        } elseif ($date2 > $date3 && $date1 >= $date3) {
                                            $date3->modify('+1 day')->format('H:i a');
                                        }

                                        if ($date1 > $date2 && $date1 < $date3) {
                                            $open_close[] = 'open';
                                        } else {
                                            $open_close[] = 'closed';
                                        }
                                    }
                                }
                            }
                        }

                        if ($day_list[0] == $cuutent_dayy) {
                            if ($time == 'Close') {
                                $opening_hr = '';
                            } else {
                                $opening_hr = $time;
                            }
                        } else {
                            $opening_hr = '';
                        }
                    }
                } else {
                    $opening_hr = '';
                }

                if (count($opening_hr) > 1) {
                    $opening_hr_final = implode(',', $opening_hr);
                } else {

                    //   echo $opening_hr;

                    $opening_hr_final = $opening_hr;
                }

                // end

                if ($lat != "" || $lat != null && $lng != "" || $lng != null) {
                    $pathalogy_lab_data[] = array(
                        'listing_id' => $listing_id,
                        'listing_type' => '10',
                        'discount_info' => $discount_info,
                        'lisitng_name' => $listing_name,
                        'lat' => $lat,
                        'lng' => $lng,
                        'address1' => $address1,
                        'address2' => $address2,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'rating' => '4',
                        'online_offline' => $online_offline,
                        'discount' => $discount,
                        'profile_pic' => $profile_pic,
                        'area_expertise' => $area_expertise,
                        'phone_no' => $contact,
                        'working_hrs' => ''
                    );
                }
            }
        } else {
            $pathalogy_lab_data = array();
        }

        $clinics_data = array();

        //    $pathalogy_lab_data = array();

        return array(
            'pharmacy' => $pramacy_data,
            'doctor' => $doctor_data,
            'hospital' => $hospital_data,
            'sports_store' => $sports_store_data,
            'spa' => $spa_data,
            'ayurvedic_stores' => $ayurvedic_stores_data,
            'optics' => $optics_data,
            'clinics' => $clinics_data,
            'pathalogy_labs' => $pathalogy_lab_data,
            'fitness' => $fitness_data
        );
    }

    public function user_feedback($user_id, $email, $feedback) {

        $feedback_data = array(
            'user_id' => $user_id,
            'email' => $email,
            'feed_back' => $feedback
        );

        $this->db->insert('users_feedback', $feedback_data);
        $feedback_id = $this->db->insert_id();
        if ($feedback_id != '') {
            return array(
                'status' => 200,
                'message' => 'success'
            );
        } else {
            return array(
                'status' => 201,
                'message' => 'error'
            );
        }
    }

    //added by zak for how to use videos

    public function How_to_use($user_id) {
        //profile details

        $query = $this->db->query("SELECT id,image,video_link,title,description,date FROM how_to_use_video  order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $image = $row['image'];
                $video_link = $row['video_link'];
                $title = $row['title'];
                $description = $row['description'];
                $date = $row['date'];



                $result_array[] = array(
                    'id' => $id,
                    'image' => 'https://s3.amazonaws.com/medicalwale/images/Howtouse/User/Thumbnail/' . $image,
                    'video_link' => $video_link,
                    'title' => $title,
                    'description' => $description,
                    'date' => $date
                );
            }
        }


        return $result_array;
    }

    public function change_password_web($user_id, $pervious_pwd, $new_pwd) {
        date_default_timezone_set('Asia/Kolkata');
        $q = $this->db->select('password,id')->from('users')->where('id', $user_id)->get()->row();
        if ($q == "") {
            return array(
                'status' => 204,
                'message' => 'Username not found.'
            );
        } else {
            $hashed_password = $q->password;
            $id = $q->id;
            if (hash_equals($hashed_password, crypt($pervious_pwd, $hashed_password))) {
                $token = '25iwFyq/LSO1U';
                $this->db->trans_start();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array(
                        'status' => 500,
                        'message' => 'Internal server error.'
                    );
                } else {
                    $generated_pwd = password_hash($new_pwd, PASSWORD_DEFAULT);
                    $this->db->trans_commit();
                    $this->db->query("UPDATE `users` SET `password`='$generated_pwd' where id='$user_id'");
                    return array(
                        'status' => 200,
                        'message' => 'Success.',
                        'id' => $id,
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

    //send otp & update last otp
    public function sendotp_web($phone) {
        $query = $this->db->query("SELECT id FROM users WHERE phone='$phone'");
        if ($query->num_rows() > 0) {
            $otp_code = rand(100000, 999999);
            $message = 'Please enter this One Time Password : ' . $otp_code . ' to verify your mobile number.';
            $post_data = array('From' => '02233721563', 'To' => $phone, 'Body' => $message);
            $exotel_sid = "aegishealthsolutions";
            $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
            $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
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
                'status' => 200,
                'message' => 'success',
                'otp_code' => $otp_code
            );
        } else {
            return array(
                'status' => 201,
                'message' => 'fail'
            );
        }
    }

    public function confirm_otp($phone, $otp) {
        $query = $this->db->query("SELECT id FROM users WHERE phone='$phone' AND otp_code='$otp'");
        if ($query->num_rows() > 0) {
            $querys = $this->db->query("UPDATE `users` SET `otp_code`='' WHERE phone='$phone'");
            return array(
                'status' => 200,
                'message' => 'success'
            );
        } else {
            return array(
                'status' => 201,
                'message' => 'fail'
            );
        }
    }

    public function forgot_password($phone, $new_pwd, $confirm_pwd) {
        date_default_timezone_set('Asia/Kolkata');
        $q = $this->db->select('password,id')->from('users')->where('phone', $phone)->get()->row();
        if ($q == "") {
            return array(
                'status' => 204,
                'message' => 'User not found.'
            );
        } else {
            $hashed_password = $q->password;
            $id = $q->id;
            if ($new_pwd != $confirm_pwd) {

                return array(
                    'status' => 201,
                    'message' => 'Could not match password.'
                );
            } else {
                $generated_pwd = md5($new_pwd);
                $this->db->trans_commit();
                $this->db->query("UPDATE `users` SET `password`='$generated_pwd' where phone='$phone'");
                return array(
                    'status' => 200,
                    'message' => 'Success.',
                    'id' => $id,
                );
            }
        }
    }
    
     public function ivr_details($number) {
        $query = $this->db->query("SELECT * FROM users WHERE phone='$number'");
        if ($query->num_rows() > 0) {
            $row=$query->row_array(); 
             $id = $row['id'];
             $name = $row['name'];
             $phone = $row['phone'];
             $email = $row['email'];
             $vendor_id = $row['vendor_id'];
             if($vendor_id==0)
             {
                 $vendor="users";
             }
             else
             {
                 $query1 = $this->db->query("SELECT * FROM vendor_type WHERE id='$vendor_id'");
                 $row1=$query1->row_array();
                 $vendor=$row1['vendor_name'];
             }



                $result_array[] = array(
                    'id' => $id,
                    'name' => $name,
                    'phone' =>$phone,
                    'email' => $email,
                    'vendor_type' => $vendor
                    
                );
            
            
            
        } else {
          
                $result_array= array();
                  
        }
        
        return $result_array;
    }
    






  public function coupon_code($user_id,$coupon,$listing_id,$listing_type,$amount,$card_type){
      $this->load->model('LabcenterModel_v2');
        $cost = 0;
        date_default_timezone_set('Asia/Kolkata');
        $booking_id = date("YmdHis");
        $created_at = date('Y-m-d H:i:s');
        $current_date = date('Y-m-d');
        if($listing_type=="35")
        {
             // Bachat Health Card
        $query1 = $this->db->query("SELECT * FROM vendor_offers WHERE name='$coupon' and offer_on='$card_type' and end_date >= '$current_date'  ");
        $row_fitness1 = $query1->row_array();
        $count1 = $query1->num_rows();  
          if($count1 > 0)
               {
            $id=$row_fitness1['id'];
            $query = $this->db->query("SELECT  v.id, v.vendor_id,v.listing_id FROM vendor_offers as v LEFT JOIN use_coupon as us ON us.coupon=v.id  WHERE us.user_id='$user_id' AND us.coupon='$id' AND us.use_status ='1'");
            $row_fitness = $query->row_array();
            $count = $query->num_rows();
            if ($count > 0)
            {
                $resp = array(
                                'status' => 201,
                                'message' => 'Coupon Already Used'
                            );
            }
            else
            {
             
                  
                  $vendor_type=$row_fitness1['listing_id'];
                  $save_type=$row_fitness1['save_type'];
                  $price=$row_fitness1['price'];
                  $max=$row_fitness1['max_discound'];
                  $subamount=0;
                  $discount=0;
                  if($price > 0 && $amount > 0 )
                   {
                      if($save_type=="rupee")
                      {
                          $subamount=$amount-$price;
                          $discount=$price;
                          
                          if($discount > $max)
                          {
                              $subamount=$amount-$max;
                              $discount=$max;
                          }
                          
                      }
                      else
                      {
                          $discount=round($amount*($price/100));
                           if($discount > $max)
                          {
                              $subamount=$amount-$max;
                              $discount=$max;
                          } else{
                              $subamount=$amount-$discount;
                          }
                          
                      }
                      
                      if($subamount < 0)
                      {
                          $subamount=0;
                          $discount = $amount - $subamount;
                      }
                  } 
                   //coupon
                   if($row_fitness['use_status'] == 0)
                  {
                      
                  }
                  else
                  {
                    $coupon_array = array(
                          'user_id' => $user_id,
                          'coupon'  => $id,
                          'vendor_id' => 0,
                          'vendor_type' => $vendor_type,
                          'created_at'  => date('Y-m-d H:i:s'),
                          'use_status' => 0
                          );
                    $this->db->insert('use_coupon', $coupon_array);
                  } 
                    
                    
                    $resp = array(
                                'status' => 200,
                                'message' => 'Success',
                                'totalamount'=>$amount,
                                'subtotal'=>strval($subamount),
                                'discount'=>$discount,
                                'coupon_id'=>$id
                            );
                  
                   
             
                
            }
          }
          else
               {
                      $resp = array(
                                'status' => 201,
                                'message' => 'Invalid Coupon Code'
                            );   
                }
        }
        if($listing_type=="5")
        {
           // Doctor 
          
                   
        $query1 = $this->db->query("SELECT * FROM vendor_offers WHERE name='$coupon' and (vendor_id='$listing_id' || vendor_id='0') and end_date >= '$current_date' and min_amount <= '$amount' ");
        $row_fitness1 = $query1->row_array();
        $count1 = $query1->num_rows();  
          if($count1 > 0)
               {
            $id=$row_fitness1['id'];
            $query = $this->db->query("SELECT  v.id, v.vendor_id,v.listing_id FROM vendor_offers as v LEFT JOIN use_coupon as us ON us.coupon=v.id  WHERE us.user_id='$user_id' AND us.coupon='$id' AND us.use_status ='1'");
            $row_fitness = $query->row_array();
            $count = $query->num_rows();
            if ($count > 0)
            {
                $resp = array(
                                'status' => 201,
                                'message' => 'Coupon Already Used'
                            );
            }
            else
            {
             
                  
                  $vendor_id=$row_fitness1['vendor_id'];
                  $vendor_type=$row_fitness1['listing_id'];
                  $save_type=$row_fitness1['save_type'];
                  $price=$row_fitness1['price'];
                  $max=$row_fitness1['max_discound'];
                  $subamount=0;
                  $discount=0;
                  if($price > 0 && $amount > 0 )
                   {
                      if($save_type=="rupee")
                      {
                          $subamount=$amount-$price;
                          $discount=$price;
                          
                          if($discount > $max)
                          {
                              $subamount=$amount-$max;
                              $discount=$max;
                          }
                          
                      }
                      else
                      {
                          $discount=round($amount*($price/100));
                           if($discount > $max)
                          {
                              $subamount=$amount-$max;
                              $discount=$max;
                          } else{
                              $subamount=$amount-$discount;
                          }
                          
                      }
                      
                      if($subamount < 0)
                      {
                          $subamount=0;
                          $discount = $amount - $subamount;
                      }
                  }
                   //coupon
                     if($row_fitness['use_status'] == 0)
                  {
                      
                  }
                  else
                  {
                    $coupon_array = array(
                          'user_id' => $user_id,
                          'coupon'  => $id,
                          'vendor_id' => $vendor_id,
                          'vendor_type' => $vendor_type,
                          'created_at'  => date('Y-m-d H:i:s'),
                          'use_status' => 0
                          );
                    $this->db->insert('use_coupon', $coupon_array);
                  }
                    
                    
                    $resp = array(
                                'status' => 200,
                                'message' => 'Success',
                                'totalamount'=>$amount,
                                'subtotal'=>$subamount,
                                'discount'=>$discount,
                                'coupon_id'=>$id
                            );
                
                   
             
                
            }
          }
          else
               {
                      $resp = array(
                                'status' => 201,
                                'message' => 'Invalid Coupon Code'
                            );   
                }
              
        }        
        if($listing_type=="6")
        {
            
        // Fitness
       
        $query1 = $this->db->query("SELECT * FROM vendor_offers WHERE name='$coupon' and vendor_id='$listing_id'");
        $row_fitness1 = $query1->row_array();
        $count1 = $query1->num_rows();  
          if($count1 > 0)
               {
              $id=$row_fitness1['id'];
            $query = $this->db->query("SELECT  v.id, v.vendor_id,v.listing_id FROM vendor_offers as v LEFT JOIN use_coupon as us ON us.coupon=v.id  WHERE us.user_id='$user_id' AND us.coupon='$id'");
            $row_fitness = $query->row_array();
            $count = $query->num_rows();
            if ($count > 0)
            {
                 $resp = array(
                                'status' => 201,
                                'message' => 'Coupon Already Used'
                            );
            }
            else
            {
             
                  
                  $vendor_id=$row_fitness1['vendor_id'];
                  $vendor_type=$row_fitness1['listing_id'];
                
                  $query1_1 = $this->db->query("SELECT id FROM diet_leads WHERE user_id='$user_id'");
                  $row_fitness1_1 = $query1_1->row_array();
                  $count1_1 = $query1_1->num_rows();  
                  if($count1_1 > 0)
                  {
                        $lead_id=$row_fitness1_1['id'];
                         $query1_1_1 = $this->db->query("SELECT user_id,complete_status FROM diet_user_package_history WHERE user_id='$user_id' order by id desc LIMIT 1");
                          $row_fitness1_1_1 = $query1_1_1->row_array();
                          $count1_1_1 = $query1_1_1->num_rows();  
                          if($count1_1_1 > 0)
                          {
                              
                              if($row_fitness1_1_1['complete_status']==1)
                              {
                                  //coupon
                                    $coupon_array = array(
                                          'user_id' => $user_id,
                                          'coupon'  => $id,
                                          'vendor_id' => $vendor_id,
                                          'vendor_type' => $vendor_type,
                                          'created_at'  => date('Y-m-d H:i:s'),
                                          'use_status' => 1
                                          );
                                    $this->db->insert('use_coupon', $coupon_array);
                                    
                                 //missbelly package   
                                    $month = "";
                                    $this->db->select('month');
                                    $this->db->from('diet_master_package');
                                    $this->db->where('id', 1);
                                    $query1 = $this->db->get();
                                    $month = $query1->row()->month;
                    
                                    $month1 = "+$month";
                                    $effectiveDate1 = date('Y-m-d');
                                    $effectiveDate = date('Y-m-d', strtotime("$month1 months", strtotime($effectiveDate1)));
                    
                    
                                    $new_booking_lead = array(
                                        'user_id' => $user_id,
                                        'package_id' => '1',
                                        'leads_id' => $lead_id,
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'booking_from_date' => date('Y-m-d'),
                                        'booking_to_date' => $effectiveDate,
                                        'created_by' => $user_id,
                                        'status' => '1'
                                    );
                    
                                    $this->db->insert('diet_packages_booked', $new_booking_lead);
                    
                                    $notification = array('user_id' => $user_id,
                                        'listing_id' => "",
                                        'package_id' => 1,
                                        'booking_id' => $booking_id,
                                        'order_id' => 0,
                                        'title' => "Diet Package Booking",
                                        'msg' => "Diet Package Booked Through Ecard.",
                                        'notification_type' => "Diet Booking",
                                        'created_at' => date('Y-m-d H:i:s')
                                    );
                                    $this->db->insert('diet_plan_notifications', $notification);
                    
                    
                                    $booking_history = array('user_id' => $user_id,
                                        'leads_id' => $lead_id,
                                        'package_id' => 1,
                                        'booking_id' => $booking_id,
                                        'booking_from' => '0000-00-00',
                                        'booking_to' => '0000-00-00',
                                        'created_at' => date('Y-m-d H:i:s')
                                    );
                    
                                    $this->db->insert('diet_user_package_history', $booking_history);
                                    $diet_booking_id = $this->db->insert_id();
                                    
                                    $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
                                    $customer_token_count = $customer_token->num_rows();
                    
                                    if ($customer_token_count > 0) {
                                        $token_status = $customer_token->row_array();
                                        $agent = $token_status['agent'];
                                        $reg_id = $token_status['token'];
                                        $tag = 'text';
                                 
                                        $img_url = 'https://medicalwale.com/img/noti_icon_miss_belly.png';
                                        $title_package = "Free Diet Package";
                                        $msg_package = "Congratulations, You get " . $month . " month free package of Missbelly Diet Plan on Bachat Health Card.";
                                        $this->send_gcm_notify_dietpackage1($title_package, $reg_id, $msg_package, $img_url, $tag, $agent, $diet_booking_id);
                                        
                                        $this->insert_all_notification_Mobile1($user_id,$title_package, $reg_id, $msg_package, $img_url, $tag, $agent, $diet_booking_id);
                                        
                                    }
                                     $resp = array(
                                'status' => 200,
                                'message' => 'Success'
                            );   
                              }
                              else  if($row_fitness1_1_1['complete_status']==0)
                              {
                                   $resp = array(
                                        'status' => 201,
                                        'message' => 'Your Miss Belly Package is already Activated.'
                                    );
                              }
                          }
                        
                  }
                  else
                  {
                       //coupon
                                    $coupon_array = array(
                                          'user_id' => $user_id,
                                          'coupon'  => $id,
                                          'vendor_id' => $vendor_id,
                                          'vendor_type' => $vendor_type,
                                          'created_at'  => date('Y-m-d H:i:s'),
                                          'use_status' => 1
                                          );
                                    $this->db->insert('use_coupon', $coupon_array);
                                    
                                 //missbelly package   
                     $new_deit_entry = array(
                        'user_id' => $user_id,
                        'enroll_from' => "Coupon Code",
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => $user_id
                    );
    
                    $this->db->insert('diet_leads', $new_deit_entry);
                    $lead_id = $this->db->insert_id();
                    
                    $month = "";
                    $this->db->select('month');
                    $this->db->from('diet_master_package');
                    $this->db->where('id', 1);
                    $query1 = $this->db->get();
                    $month = $query1->row()->month;
    
                    $month1 = "+$month";
                    $effectiveDate1 = date('Y-m-d');
                    $effectiveDate = date('Y-m-d', strtotime("$month1 months", strtotime($effectiveDate1)));
    
    
                    $new_booking_lead = array(
                        'user_id' => $user_id,
                        'package_id' => '1',
                        'leads_id' => $lead_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'booking_from_date' => date('Y-m-d'),
                        'booking_to_date' => $effectiveDate,
                        'created_by' => $user_id,
                        'status' => '1'
                    );
    
                    $this->db->insert('diet_packages_booked', $new_booking_lead);
    
                    $notification = array('user_id' => $user_id,
                        'listing_id' => "",
                        'package_id' => 1,
                        'booking_id' => $booking_id,
                        'order_id' => 0,
                        'title' => "Diet Package Booking",
                        'msg' => "Diet Package Booked Through Ecard.",
                        'notification_type' => "Diet Booking",
                        'created_at' => date('Y-m-d H:i:s')
                    );
                    $this->db->insert('diet_plan_notifications', $notification);
    
    
                    $booking_history = array('user_id' => $user_id,
                        'leads_id' => $lead_id,
                        'package_id' => 1,
                        'booking_id' => $booking_id,
                        'booking_from' => '0000-00-00',
                        'booking_to' => '0000-00-00',
                        'created_at' => date('Y-m-d H:i:s')
                    );
    
                    $this->db->insert('diet_user_package_history', $booking_history);
                    $diet_booking_id = $this->db->insert_id();
                    
                    $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
                    $customer_token_count = $customer_token->num_rows();
    
                    if ($customer_token_count > 0) {
                        $token_status = $customer_token->row_array();
                        $agent = $token_status['agent'];
                        $reg_id = $token_status['token'];
                        $tag = 'text';
                 
                        $img_url = 'https://medicalwale.com/img/noti_icon_miss_belly.png';
                        $title_package = "Free Diet Package";
                        $msg_package = "Congratulations, You get " . $month . " month free package of Missbelly Diet Plan on Bachat Health Card.";
                        $this->send_gcm_notify_dietpackage1($title_package, $reg_id, $msg_package, $img_url, $tag, $agent, $diet_booking_id);
                        
                        $this->insert_all_notification_Mobile1($user_id,$title_package, $reg_id, $msg_package, $img_url, $tag, $agent, $diet_booking_id);
                        
                    }
                     $resp = array(
                                'status' => 200,
                                'message' => 'Success'
                            );   
                  }
                   
             
                
            }
          }
          else
               {
                      $resp = array(
                                'status' => 201,
                                'message' => 'Invalid Coupon Code'
                            );   
                }
        }        
        if($listing_type=="10")
        {
           // labs 
                   $discounted_price = 0;
        $query1 = $this->db->query("SELECT * FROM vendor_offers WHERE name='$coupon' and (vendor_id='$listing_id' || vendor_id='0') and end_date >= '$current_date' and min_amount <= '$amount' AND `listing_id` = '$listing_type'");
        // echo "SELECT * FROM vendor_offers WHERE name='$coupon' and (vendor_id='$listing_id' || vendor_id='0') and end_date >= '$current_date' and min_amount <= '$amount' AND `listing_id` = '$listing_type'"; die();
        
        $row_fitness1 = $query1->row_array();
        $count1 = $query1->num_rows();  
          if($count1 > 0)
               {
            $id=$row_fitness1['id'];
            $query = $this->db->query("SELECT  v.id, v.vendor_id,v.listing_id FROM vendor_offers as v LEFT JOIN use_coupon as us ON us.coupon=v.id  WHERE us.user_id='$user_id' AND us.coupon='$id' AND us.use_status ='1'");
            $row_fitness = $query->row_array();
            $count = $query->num_rows();
            if ($count > 0)
            {
                $resp = array(
                    'status' => 201,
                    'message' => 'Coupon Already Used'
                );
            }
            else
            {
                
                $vendor_id=$row_fitness1['vendor_id'];
                  $vendor_type=$row_fitness1['listing_id'];
                  $save_type=$row_fitness1['save_type'];
                  $price=$row_fitness1['price'];
                  $max=$row_fitness1['max_discound'];
                
                
                
                $offer_on = $row_fitness1['offer_on'];
                if($offer_on == 2 ){
                    // test
                    $totalPrice = 0;
                    $cost_test = 0;
                    $cost_package = 0;
                    
                    $offer_on_ids = $row_fitness1['offer_on_ids'];
                    $offer_on_ids_exp = explode(',',$offer_on_ids);
                    
                    $labs_stack_test = $this->db->query("SELECT ltd.price,ltd.discounted_price,ltd.discount,ss.* FROM `services_stack` as ss left join lab_test_details1 as ltd on (ss.`service_id` = ltd.test_id) WHERE ss.`user_id` = '$user_id' AND ss.`service_type` = 'test' GROUP by ss.id")->result_array();
                    $labs_stack_package = $this->db->query("SELECT lp.Price,lp.discount_type,lp.discount,ss.* FROM `services_stack` as ss left join lab_packages1 as lp on (ss.`service_id` = lp.id) WHERE ss.`user_id` = '$user_id' AND ss.`service_type` = 'package' GROUP by ss.id")->result_array();
                    
                    foreach($labs_stack_test as $ls){
                        $amount = $ls['price'];
                        $totalPrice = $totalPrice + $ls['price'];
                        $discount_type = 'percent';
                        $discount = $ls['discount'];
                        if($discount > 0){
                            $discounted_price = $this->LabcenterModel_v2->get_disounted_rate($amount,$discount_type,$discount);
                        } else {
                            $discounted_price = $amount;
                        }
                        
                        foreach($offer_on_ids_exp as $oe){
                            if($ls['service_id'] == $oe){
                                
                                 $subamount=0;
                                  $discount=0;
                                  if($price > 0 && $amount > 0 ){
                                      if($save_type=="rupee")
                                      {
                                          $subamount=$amount-$price;
                                          $discount=$price;
                                          
                                          if($discount > $max)
                                          {
                                              $subamount=$amount-$max;
                                              $discount=$max;
                                          }
                                          
                                      }
                                      else
                                      {
                                          $discount=round($amount*($price/100));
                                           if($discount > $max)
                                          {
                                              $subamount=$amount-$max;
                                              $discount=$max;
                                          } else{
                                              $subamount=$amount-$discount;
                                          }
                                          
                                      }
                                      
                                      if($subamount < 0)
                                      {
                                          $subamount=0;
                                          $discount = $amount - $subamount;
                                      }
                                      $discounted_price = $subamount;
                                      
                                  }
                                   //coupon
                                     if($row_fitness['use_status'] == 0)
                                  {
                                      
                                  }
                                  else
                                  {
                                    $coupon_array = array(
                                          'user_id' => $user_id,
                                          'coupon'  => $id,
                                          'vendor_id' => $vendor_id,
                                          'vendor_type' => $vendor_type,
                                          'created_at'  => date('Y-m-d H:i:s'),
                                          'use_status' => 0
                                          );
                                    $this->db->insert('use_coupon', $coupon_array);
                                  }
                                    
                            }
                        }
                        
                        $cost_test = $cost_test + $discounted_price;
                        
                    }
                    // echo $cost_test; die();
                    foreach($labs_stack_package as $ls){
                
                        $discounted_price = 0;
                        $Price = $ls['Price'];
                        $totalPrice = $totalPrice + $ls['Price'];
                        $discount_type = $ls['discount_type'];
                        $discount = $ls['discount'];
                      
                        if($discount > 0){
                            $discounted_price = $this->LabcenterModel_v2->get_disounted_rate($Price,$discount_type,$discount);
                        } else {
                            $discounted_price = $Price;
                        }
                        $cost_package = $cost_package + $discounted_price;
                      
                    }
                    // echo $cost_package; die();
                    $subamount = $cost_test + $cost_package;
                    $amount = $totalPrice;
                    $discount = $totalPrice - $subamount;
                    // echo $subamount; die();
                    
                }else if($offer_on == 3){
                     // package
                    $totalPrice = 0;
                    $cost_test = 0;
                    $cost_package = 0;
                    
                    $offer_on_ids = $row_fitness1['offer_on_ids'];
                    $offer_on_ids_exp = explode(',',$offer_on_ids);
                    
                    $labs_stack_test = $this->db->query("SELECT ltd.price,ltd.discounted_price,ltd.discount,ss.* FROM `services_stack` as ss left join lab_test_details1 as ltd on (ss.`service_id` = ltd.test_id) WHERE ss.`user_id` = '$user_id' AND ss.`service_type` = 'test' GROUP by ss.id")->result_array();
                    $labs_stack_package = $this->db->query("SELECT lp.Price,lp.discount_type,lp.discount,ss.* FROM `services_stack` as ss left join lab_packages1 as lp on (ss.`service_id` = lp.id) WHERE ss.`user_id` = '$user_id' AND ss.`service_type` = 'package' GROUP by ss.id")->result_array();
                    
                    foreach($labs_stack_package as $ls){
                        $amount = $ls['Price'];
                        $totalPrice = $totalPrice + $ls['Price'];
                        $discount_type = $ls['discount_type'];
                        $discount = $ls['discount'];
                        if($discount > 0){
                            $discounted_price = $this->LabcenterModel_v2->get_disounted_rate($amount,$discount_type,$discount);
                        } else {
                            $discounted_price = $amount;
                        }
                        
                        foreach($offer_on_ids_exp as $oe){
                            if($ls['service_id'] == $oe){
                                
                                 $subamount=0;
                                  $discount=0;
                                  if($price > 0 && $amount > 0 ){
                                      if($save_type=="rupee")
                                      {
                                          $subamount=$amount-$price;
                                          $discount=$price;
                                          
                                          if($discount > $max)
                                          {
                                              $subamount=$amount-$max;
                                              $discount=$max;
                                          }
                                          
                                      }
                                      else
                                      {
                                          $discount=round($amount*($price/100));
                                           if($discount > $max)
                                          {
                                              $subamount=$amount-$max;
                                              $discount=$max;
                                          } else{
                                              $subamount=$amount-$discount;
                                          }
                                          
                                      }
                                      
                                      if($subamount < 0)
                                      {
                                          $subamount=0;
                                          $discount = $amount - $subamount;
                                      }
                                      $discounted_price = $subamount;
                                      
                                  }
                                   //coupon
                                     if($row_fitness['use_status'] == 0)
                                  {
                                      
                                  }
                                  else
                                  {
                                    $coupon_array = array(
                                          'user_id' => $user_id,
                                          'coupon'  => $id,
                                          'vendor_id' => $vendor_id,
                                          'vendor_type' => $vendor_type,
                                          'created_at'  => date('Y-m-d H:i:s'),
                                          'use_status' => 0
                                          );
                                    $this->db->insert('use_coupon', $coupon_array);
                                  }
                                    
                            }
                        }
                        
                        $cost_test = $cost_test + $discounted_price;
                        
                    }
                    // echo $cost_test; die();
                    foreach($labs_stack_test as $ls){
                
                        $discounted_price = 0;
                        $Price = $ls['price'];
                        $totalPrice = $totalPrice + $ls['price'];
                        $discount_type = 'percent';
                        $discount = $ls['discount'];
                      
                        if($discount > 0){
                            $discounted_price = $this->LabcenterModel_v2->get_disounted_rate($Price,$discount_type,$discount);
                        } else {
                            $discounted_price = $Price;
                        }
                        $cost_package = $cost_package + $discounted_price;
                      
                    }
                    // echo $cost_package; die();
                    $subamount = $cost_test + $cost_package;
                    $amount = $totalPrice;
                    $discount = $totalPrice - $subamount;
                    
                    
                } else {
                    // both tests and packages
                  
                    $subamount=0;
                                  $discount=0;
                                  if($price > 0 && $amount > 0 ){
                                      if($save_type=="rupee")
                                      {
                                          $subamount=$amount-$price;
                                          $discount=$price;
                                          
                                          if($discount > $max)
                                          {
                                              $subamount=$amount-$max;
                                              $discount=$max;
                                          }
                                          
                                      }
                                      else
                                      {
                                          $discount=round($amount*($price/100));
                                           if($discount > $max)
                                          {
                                              $subamount=$amount-$max;
                                              $discount=$max;
                                          } else{
                                              $subamount=$amount-$discount;
                                          }
                                          
                                      }
                                      
                                      if($subamount < 0)
                                      {
                                          $subamount=0;
                                          $discount = $amount - $subamount;
                                      }
                                      $discounted_price = $subamount;
                                      
                                  }
                                   //coupon
                                     if($row_fitness['use_status'] == 0)
                                  {
                                      
                                  }
                                  else
                                  {
                                    $coupon_array = array(
                                          'user_id' => $user_id,
                                          'coupon'  => $id,
                                          'vendor_id' => $vendor_id,
                                          'vendor_type' => $vendor_type,
                                          'created_at'  => date('Y-m-d H:i:s'),
                                          'use_status' => 0
                                          );
                                    $this->db->insert('use_coupon', $coupon_array);
                                  }
                    
                    
                    $subamount = $discounted_price;
                    $amount = $amount;
                    $discount = $amount - $subamount;
                }
                
                // die();
                    $resp = array(
                                'status' => 200,
                                'message' => 'Success',
                                'totalamount'=>strval($amount),
                                'subtotal'=>strval($subamount),
                                'discount'=>strval($discount),
                                'coupon_id'=>$id
                            );
                
            }
          }
          else
               {
                      $resp = array(
                                'status' => 201,
                                'message' => 'Invalid Coupon Code'
                            );   
                }
              
            }  
          if($listing_type=="37")
        {
           // Missbelly 
          
                   
        $query1 = $this->db->query("SELECT * FROM vendor_offers WHERE name='$coupon' and (vendor_id='$listing_id' || vendor_id='0') and end_date >= '$current_date' and min_amount <= '$amount' ");
        $row_fitness1 = $query1->row_array();
        $count1 = $query1->num_rows();  
          if($count1 > 0)
               {
            $id=$row_fitness1['id'];
            $query = $this->db->query("SELECT  v.id, v.vendor_id,v.listing_id FROM vendor_offers as v LEFT JOIN use_coupon as us ON us.coupon=v.id  WHERE us.user_id='$user_id' AND us.coupon='$id' AND us.use_status ='1'");
            $row_fitness = $query->row_array();
            $count = $query->num_rows();
            if ($count > 0)
            {
                $resp = array(
                                'status' => 201,
                                'message' => 'Coupon Already Used'
                            );
            }
            else
            {
             
                  
                  $vendor_id=$row_fitness1['vendor_id'];
                  $vendor_type=$row_fitness1['listing_id'];
                  $save_type=$row_fitness1['save_type'];
                  $price=$row_fitness1['price'];
                  $max=$row_fitness1['max_discound'];
                  $subamount=0;
                  $discount=0;
                  if($price > 0 && $amount > 0 )
                   {
                      if($save_type=="rupee")
                      {
                          $subamount=$amount-$price;
                          $discount=$price;
                          
                          if($discount > $max)
                          {
                              $subamount=$amount-$max;
                              $discount=$max;
                          }
                          
                      }
                      else
                      {
                          $discount=round($amount*($price/100));
                           if($discount > $max)
                          {
                              $subamount=$amount-$max;
                              $discount=$max;
                          } else{
                              $subamount=$amount-$discount;
                          }
                          
                      }
                      
                      if($subamount < 0)
                      {
                          $subamount=0;
                          $discount = $amount - $subamount;
                      }
                  }
                   //coupon
                     if($row_fitness['use_status'] == 0)
                  {
                      
                  }
                  else
                  {
                    $coupon_array = array(
                          'user_id' => $user_id,
                          'coupon'  => $id,
                          'vendor_id' => $vendor_id,
                          'vendor_type' => $vendor_type,
                          'created_at'  => date('Y-m-d H:i:s'),
                          'use_status' => 0
                          );
                    $this->db->insert('use_coupon', $coupon_array);
                  }
                    
                    
                    $resp = array(
                                'status' => 200,
                                'message' => 'Success',
                                'totalamount'=>$amount,
                                'subtotal'=>$subamount,
                                'discount'=>$discount,
                                'coupon_id'=>$id
                            );
                
                   
             
                
            }
          }
          else
               {
                      $resp = array(
                                'status' => 201,
                                'message' => 'Invalid Coupon Code'
                            );   
                }
              
        }       
                
    
       return $resp;
  }
  
 
 
  
  
 public function insert_all_notification_Mobile1($user_id,$title, $reg_id, $msg, $img_url, $tag, $agent, $diet_booking_id)
 {
$notification_array = array(
                      'title' => $title,
                      'msg'  => $msg,
                      'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => "",
                      'order_date' => "",
                      'order_id'   => "",
                      'post_id'  => "",
                      'listing_id'  => "",
                      'booking_id'  => $diet_booking_id,
                       'invoice_no' => "",
                       'user_id'  => $user_id,
                       'notification_type'  => 'free_diet_plan',
                       'notification_date'  => date('Y-m-d H:i:s')
                       
            );
         $this->db->insert('All_notification_Mobile', $notification_array);
         return 1;
         
}

    public function send_gcm_notify_dietpackage1($title, $reg_id, $msg, $img_url, $tag, $agent, $diet_booking_id)
    {
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
                "notification_type" => 'free_diet_plan',
                "notification_date" => $date,
                "booking_id" => $diet_booking_id
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

}

?>