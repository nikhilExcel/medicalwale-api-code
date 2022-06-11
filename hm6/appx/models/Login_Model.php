<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login_Model extends CI_Model {

    var $client_service = "frontend-client";
    var $auth_key = "medicalwalerestapi";

    //validate auth key and client
    public function check_auth_client() {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key = $this->input->get_request_header('Auth-Key', TRUE);
        if ($client_service == $this->client_service && $auth_key == $this->auth_key) {
            return true;
        } else {
            return  array(
                'status' => 401,
                'message' => 'Unauthorized.'
            );
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

    //token logout to stop notification
    public function logout() {
        $users_id = $this->input->get_request_header('User-ID', TRUE);
        $token = $this->input->get_request_header('Authorization', TRUE);
        $this->db->where('users_id', $users_id)->where('token', $token)->delete('api_users_authentication');
        $this->session->sess_destroy();
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
            return  array(
                'status' => 401,
                'message' => 'Unauthorizedq.'
            );
        } 
        /*else {
            if ($q->expired_at < date('Y-m-d H:i:s')) {
                return  array(
                    'status' => 401,
                    'message' => 'Your session has been expired.'
                );
            } else {
                $updated_at = date('Y-m-d H:i:s');
                $expired_at = '2018-11-15 08:57:58';
                $this->db->where('users_id', $users_id)->where('token', $token)->update('api_users_authentication', array(
                    'expired_at' => $expired_at,
                    'updated_at' => $updated_at
                ));
                return array(
                    'status' => 200,
                    'message' => 'Authorized.'
                );
            }
        }*/
        
        else {
            
             if ($q->expired_at < date("Y-m-d H:i:s", strtotime("-1 days"))) {
                // return json_output(401, array(
                //     'status' => 401,
                //     'message' => 'Your session has been expired.'
                // ));
                return  array(
                    'status' => 401,
                    'message' => 'Your session has been expired.'
                );
            } else {
                $updated_at = date('Y-m-d H:i:s');
                $expired_at = '2030-11-12 08:57:58';//'2020-12-12 08:57:58';
                $this->db->where('users_id', $users_id)->where('token', $token)->update('api_users_authentication', array(
                    'expired_at' => $expired_at,
                    'updated_at' => $updated_at
                ));
                //echo $this->db->last_query(); die();
                return 200;
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

        $querys = $this->db->query("UPDATE `users` SET `otp_code`='$otp_code' WHERE phone='$phone'");

        return array(
            'status' => 204,
            'message' => 'success',
            'otp_code' => $otp_code
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
                $this->db->query("UPDATE `user_privilage_card` SET `is_active`='1',user_id='$user_id' where card_no='$card_no'");


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

    public function generate_privilage($user_id) {
        date_default_timezone_set('Asia/Kolkata');
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
                $blood_is_active = $q->blood_is_active;
                $heradiatry_problem = $q->heradiatry_problem;
                $lat = $q->lat;
                $lng = $q->lng;
                $height_cm_ft = $q->height_cm_ft;
                $map_location = $q->map_location;
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
                    'height_cm_ft' => $height_cm_ft,
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
                    'addiction' => str_replace('null', '', $addiction),
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

    //validate user login
    public function userlogin($phone, $token, $agent) {
        if ($phone == "") {
            return array(
                'status' => 208,
                'message' => 'Please enter phone no.'
            );
        } else {
            $check_user = $this->db->select('id')->from('users')->where('phone', $phone)->get()->row();
            if ($check_user != "") {
                $id = $check_user->id;
                $q = $this->db->select('password,id,name,phone,email,gender,dob,height,weight,marital_status,blood_group,diet_fitness,organ_donor,sex_history')->from('users')->where('users.id', $id)->get()->row();
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
                if ($card_count > 0) {
                    $card_list = $card_query->row_array();
                    $card_no = $card_list['card_no'];
                    $card_type = $card_list['card_type'];
                }

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

                $querys = $this->db->query("UPDATE `users` SET `otp_code`='$otp_code',`token`='$token',`agent`='$agent',`token_status`='1' WHERE id='$id'");

                return array( 'status' => 200, 'message' => 'success',
                    'data' => array('otp_code' => (int) $otp_code,
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
                    'sex_history' => $sex_history )
                );
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
                return array( 'status' => 200, 'message' => 'success',
                    'data' => array('otp_code' => (int) $otp_code,
                    'user' => 'new',
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
                    'sex_history' => '' )
                );
            }
        }
    }
	
	//check login
	    public function checklogin() {
        if(empty($this->session->userdata('id'))) {
            return array(
                'status' => 208,
                'message' => 'User not loggedin'
            );
        } else {
		    $id=$this->session->userdata('id');
            $check_user = $this->db->select('id')->from('users')->where('id', $id)->get()->row();
            if ($check_user != "") {
                $id = $check_user->id;
                $q = $this->db->select('password,id,name,phone,email,gender,dob,height,weight,marital_status,blood_group,diet_fitness,organ_donor,sex_history')->from('users')->where('users.id', $id)->get()->row();
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
                if ($card_count > 0) {
                    $card_list = $card_query->row_array();
                    $card_no = $card_list['card_no'];
                    $card_type = $card_list['card_type'];
                }

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

                // $querys = $this->db->query("UPDATE `users` SET `otp_code`='$otp_code',`token`='$token',`agent`='$agent',`token_status`='1' WHERE id='$id'");

                return array( 'status' => 200, 'message' => 'success',
                    'data' => array('otp_code' => (int) $otp_code,
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
                    'sex_history' => $sex_history )
                );
            } else {
				return array(
					'status' => 208,
					'message' => 'User not exist'
				);
            }
        }
    }

    //create new user
    public function usersignup($name, $phone, $gender, $dob, $image, $token, $agent) {
        $count_user2 = $this->db->select('id')->from('users')->where('phone', $phone)->where('vendor_id', '0')->get()->num_rows();
        if ($count_user2 > 0) {
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
                $user_data = array(
                    'name' => $name,
                    'phone' => $phone,
                    'email' => '',
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
                        'email' => '',
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
                        'email' => '',
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
    public function userprofile($user_id, $marital_status, $blood_group, $height, $height_cm_ft, $weight, $diet_fitness, $exercise_level, $health_condition, $allergies, $ask_saheli, $organ_donor, $bmi, $activity_level, $addiction, $health_insurance, $blood_is_active, $heradiatry_problem, $lat, $lng, $map_location) {
        $count_user = $this->db->select('id')->from('users')->where('id', $user_id)->get()->num_rows();
        if ($count_user > 0) {
            if ($user_id != '') {
                date_default_timezone_set('Asia/Kolkata');
                $updated_at = date('Y-m-d H:i:s');
                $user_data = array(
                    'height_cm_ft' => $height_cm_ft,
                    'marital_status' => $marital_status,
                    'blood_group' => $blood_group,
                    'blood_is_active' => $blood_is_active,
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
                    'addiction' => $addiction,
                    'health_insurance' => $health_insurance,
                    'heradiatry_problem' => $heradiatry_problem,
                    'map_location' => $map_location,
                    'lng' => $lng,
                    'lat' => $lat
                );
                $this->db->where('id', $user_id)->update('users', $user_data);


                $q = $this->db->select('avatar_id,password,height_cm_ft,id,name,IFNULL(activity_level,"") AS activity_level,phone,email,gender,dob,IFNULL(height,"") AS height,IFNULL(weight,"") AS weight,IFNULL(marital_status,"") AS marital_status,IFNULL(blood_group,"") AS blood_group,IFNULL(blood_is_active,"") AS blood_is_active,IFNULL(diet_fitness,"") AS diet_fitness,IFNULL(organ_donor,"") AS organ_donor,sex_history,exercise_level,health_condition,health_insurance,addiction,allergies,IFNULL(bmi,"") AS bmi ,IFNULL(heradiatry_problem,"") AS heradiatry_problem,IFNULL(lat,"") AS lat,IFNULL(lng,"") AS lng,IFNULL(map_location,"") AS map_location')->from('users')->where('users.id', $user_id)->get()->row();

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
    public function userupdate($user_id, $gender, $dob, $name, $phone, $email, $profile_pic) {
        $Q = "SET source = '$profile_pic', title='$profile_pic'";
        $sql = "SELECT avatar_id FROM users  WHERE users.id ='" . $user_id . "'";
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
            'email' => $email
        );
    }

    //get all review list

    //update user's latitude and longitude
    public function user_lat_lng($user_id, $latitude, $longitude, $map_location) {
        $query = $this->db->query("UPDATE `users` SET `lat`='$latitude',`lng`='$longitude',`map_location`='$map_location' WHERE id='$user_id'");
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
       //activate privilage card
    public function activate_privilage_with_Coupon($user_id, $card_no, $coupon_code) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $query = $this->db->query("SELECT id from users where id='$user_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            $card_query = $this->db->query("SELECT id,card_type,card_no,pin_number from user_priviladge_card_new where status='inactive' and card_no='$card_no' and pin_number='$coupon_code'");
           
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
                $msg = $usr_name . ', Your Physical Bachat card No :' . $card_no . ' is activated . with Coupon Code is : ' .$coupon_code. ' ';
                $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
                $title = $usr_name . ', Your Physical Bachat card No :' . $card_no . ' is activated . with Coupon Code is : ' .$coupon_code. ' ';
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
                    'coupon_number' => $coupon_code,
                    'status' => 200,
                    'card_status' => 1
                );
            }
            else if($old_card_count>0)
            {
                 $card_type = $old_card_querys->card_type;

                $card_no = $old_card_querys->card_no;
                $this->db->query("UPDATE `user_privilage_card` SET `is_active`='1',user_id='$user_id' where card_no='$card_no' and pin_number='$coupon_code'");


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
                $msg = $usr_name . ', Your Physical Bachat card No :' . $card_no . ' is activated . with Coupon Code is : ' .$coupon_code. ' ';
                $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
                $title = $usr_name . ', Your Physical Bachat card No :' . $card_no . ' is activated . with Coupon Code is : ' .$coupon_code. ' ';
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
                    'coupon_number' => $coupon_code,
                    'status' => 200,
                    'card_status' => 1
                );
            }
            else {

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
    

  public function check_privilage_card_with_Coupon($user_id,$card_no)
    {
         date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $query = $this->db->query("SELECT id from users where id='$user_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            $card_query = $this->db->query("SELECT id,card_type,card_no,pin_number from user_priviladge_card_new where status='inactive' and card_no='$card_no'");
           
            $card_querys = $card_query->row();

            $card_count = $card_query->num_rows();
//die();
            if ($card_count > 0)
            {
                 return array(
                    'card_no' => $card_no,
                    'message' => "coupon code is present for this card.",
                    'status' => 200,
                );
            }
            else
            {
                $query = $this->db->query("SELECT id from users where id='$user_id'");
        $count = $query->num_rows();
        if ($count > 0) {
              $max1 = strlen($card_no);
           $generated_coupon = "";
        for ($i=0; $i < 8; $i++)
        {
            $generated_coupon .=$card_no[random_int(0, $max1-1)];
        }
        
         $order_info = $this->db->select('phone,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $order_info->token_status;
            $customer_phone = $order_info->phone;
     
        $this->db->query("UPDATE `user_privilage_card` SET `pin_number`='$generated_coupon',user_id='$user_id' where card_no='$card_no'");
           $message = 'Your generated coupon code is .'.$generated_coupon.' with cord no is . '.$card_no.'';
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
            
        
        
        }
                
                return array(
                    'card_no' => $card_no,
                    'message' => "coupon code is successfully send on registered mobile number for this card.",
                    'coupon_code' => $generated_coupon,
                    'status' => 200,
                ); 
            }
        }
        else
        {
             return array(
                'status' => 201,
                'card_status' => '0'
            );
        }
    }
    
     public function generate_coupon_code_for_card($user_id,$card_no)
   {
      date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $query = $this->db->query("SELECT id from users where id='$user_id'");
        $count = $query->num_rows();
        if ($count > 0) {
              $max1 = strlen($card_no);
        
        for ($i=0; $i < 8; $i++)
        {
            $generated_coupon .=$card_no[random_int(0, $max1-1)];
        }
        
         $order_info = $this->db->select('phone,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $order_info->token_status;
            $customer_phone = $order_info->phone;
           $message = 'Your generated coupon code is .'.$generated_coupon.' with card no is . '.$card_no.'';
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
        
        
        }
        else
        {
              return array(
                'status' => 201,
                'card_status' => '0'
            );
        }
        
   }
    
    
    
     
   //added for validate generated coupon and entry table in card number with coupon code 
   
   public function verify_generated_coupon($user_id, $card_no, $coupon_code)
   {
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
                $msg = $usr_name . ', Your Physical Bachat card No :' . $card_no . ' with coupon card:'. $coupon_code .' is activated . ';
                $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
                $title = $usr_name . ', Your Physical Bachat card No :' . $card_no . ' with coupon card:'. $coupon_code .' is activated . ';
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
        }
        else
        {
          return array(
                'status' => 201,
                'card_status' => '0'
            );   
        }
   }
   
     public function otp_verify($phone, $otp) {
        
        $q = $this->db->select('id,name,email,phone,otp_code')->from('users')->where('phone', $phone)->get()->row();
        
        if ($q == "") {
            return array(
                'status' => 204,
                'message' => 'Phone number not found.'
            );
        } else {
            $phoneNo = $q->phone;
            $otp_code = $q->otp_code;
            if($otp == $otp_code){
                $data = array(
                        'email' => $q->email,
                        'name' => $q->name,
                        'id' => $q->id,
                        'loggedin' => TRUE,
                    );
                    $this->session->set_userdata($data);
                    $uid = $this->session->userdata('id');
                    $sql = "SELECT users.name,media.source,users.id FROM users LEFT JOIN media ON(users.avatar_id=media.id) WHERE users.id= '$uid'";
                    $result = $this->db->query($sql)->row();
                    if (!empty($result->source)) {
                        $uimage = $result->source;
                    } else {
                        $uimage = 'user_avatar.jpg';
                    }
                    $data = array('uimage' => $result->source, 'uname' => $result->name);
                    $this->session->set_userdata($data);
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
   
   
}
