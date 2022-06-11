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

    //check app version
    public function appversion() {
        // $text = "1. Offline Bachat Health Card <br>2. Reply on comments is added on Healthwall and Ask Saheli <br>3. UI enhancements and bugs resolved";
        // $version='26';
        /*$text = '1. Now find doctors, hospitals, fitness centres, articles, success stories, users and more easily by just typing the name.
2. Learn a new word each day and search for any health related word.
3. View related doctors in doctor listing for quick & easy navigation.
4. Bug fixes.';
        $version = '42';
        $version_name='2.1.14';
        return array(
                'status' => 200,
                'message' => 'success',
                'version' => (int)$version,
                'version_name' => $version_name,
                'text' => $text
            );*/
            /* $text = '1. Get FREE E-Bachat Health Card on 30th Dec on completion of 1 year of App release.<br/>
2. Now order medicines from Medlife at discounted prices. Prescribed medicines only.<br/>
3. View doctors, hospitals, pharmacy, labs, fitness centers and others in new map view.<br/>
4. View all newly arrived products in Health Mall.<br/>
5. Finding difficult to use the app? Check how to use videos now.<br/>
6. Bug fixes';
        $version = '58';
        $version_name='2.1.27';*/

       /* $text = '1. Now track your cycle tracker with history by using Menstrual Cycle Tracker.<br>
        2. Order Medicine - by prescription or generic products by placing order to local pharmacy stores, your favourite pharmacy store or with Medlife.<br>
        3. Get a free diet consultation from our diet expert Miss Belly. <br>
        4. Ledger UI Changes.<br>
        5. Performance Improvements and Bug Fixes.<br>';
        $version = '72';
        $version_name='2.1.41';*/

       /* $text = '1. Now always keep your teeth shine with Sabka Dentist. <br/>
        2. Avail great offers and free dental consultation. <br/>
        3. Get upto 70% off on lab and diagnostic treatments with Thyrocare.<br/>
        4. View ALL the partners associated with us on a dynamic maps when you purchase Bachat Health Card.<br/>
        5. Bug fixes.';
        $version = '62';
        $version_name='2.1.31';*/

         $text = '1. Now track your cycle tracker with history by using Menstrual Cycle Tracker.<br>
        2. Order Medicine - by prescription or generic products by placing order to local pharmacy stores, your favourite pharmacy store or with Medlife.<br>
        3. Get a free diet consultation from our diet expert Miss Belly. <br>
        4. Ledger UI Changes.<br>
        5. Performance Improvements and Bug Fixes.<br>';
        $version = '76';
        $version_name='2.1.45';

        return array(
                'status' => 200,
                'message' => 'success',
                'version' => (int)$version,
                'version_name' => $version_name,
                'text' => $text
            );
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

   /* public function auth()
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
    }*/
     public function auth() {
        date_default_timezone_set('Asia/Kolkata');
        $users_id = $this->input->get_request_header('User-ID', TRUE);
        $token = $this->input->get_request_header('Authorizations', TRUE);
        $q = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id', $users_id)->where('token', $token)->get()->row();
        if ($q == "") {
            return json_output(401, array(
                'status' => 401,
                'message' => 'Unauthorizedq.'
            ));
        } else {
             if ($q->expired_at < date("Y-m-d H:i:s", strtotime("-50 days"))) {
                return json_output(401, array(
                    'status' => 401,
                    'message' => 'Your session has been expired.'
                ));
            } else {
                $updated_at = date('Y-m-d H:i:s');
                $expired_at = date("Y-m-d H:i:s", strtotime("+50 days"));//'2020-12-12 08:57:58';
                $this->db->where('users_id', $users_id)->where('token', $token)->update('api_users_authentication', array(
                    'expired_at' => $expired_at,
                    'updated_at' => $updated_at
                ));
               // echo $this->db->last_query();
                return array(
                    'status' => 200,
                    'message' => 'Authorized.'
                );
            }
        }
    }

    public function sendotp($phone)
    {
        if ($phone == '7655369076') {
            $otp_code = '123456';
        } else {
            $otp_code = rand(100000, 999999);
        }

        $message      = 'Please enter this One Time Password : ' . $otp_code . ' to verify your mobile number.';
        $post_data    = array(
            'From' => '02233721563',
            'To' => $phone,
            'Body' => $message
        );
        $exotel_sid   = "aegishealthsolutions";
        $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
        $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
        $ch           = curl_init();
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
                	$q  = $this->db->select('password,IFNULL(height_cm_ft,"") AS height_cm_ft,id,name,IFNULL(activity_level,"") AS activity_level,phone,email,gender,dob,IFNULL(height,"") AS height,IFNULL(weight,"") AS weight,IFNULL(marital_status,"") AS marital_status,IFNULL(blood_group,"") AS blood_group,IFNULL(blood_is_active,"") AS blood_is_active,IFNULL(diet_fitness,"") AS diet_fitness,IFNULL(organ_donor,"") AS organ_donor,sex_history,exercise_level,health_condition,health_insurance,addiction,allergies,IFNULL(bmi,"") AS bmi ,IFNULL(heradiatry_problem,"") AS heradiatry_problem,IFNULL(lat,"") AS lat,IFNULL(lng,"") AS lng,IFNULL(map_location,"") AS map_location')->from('users')->where('users.id', $listing_id)->get()->row();

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
                $height_cm_ft = $q-> height_cm_ft;
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

                if ($phone == '8655369076' || $phone=='9824242411' || $phone=='9733331211' || $phone=='8676008901' || $phone=='9107766551' || $phone=='7545032401' || $phone=='8500005213' || $phone=='9822212411' || $phone=='8080262411' || $phone=='9323465785' || $phone == '8898929758') {
                    $otp_code = '123456';
                } else {
                    $otp_code     = rand(100000, 999999);
                    $message      = 'Please enter this One Time Password : ' . $otp_code . ' to verify your mobile number.';
                    $post_data    = array(
                        'From' => '02233721563',
                        'To' => $phone,
                        'Body' => $message
                    );
                    $exotel_sid   = "aegishealthsolutions";
                    $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                    $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
                    $ch           = curl_init();
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

                return array(
                    'otp_code' => (int)$otp_code,
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
                if ($phone == '8655369076' || $phone == '9824242411' || $phone == '9733331211' || $phone == '8676008901' || $phone == '9107766551' || $phone == '7545032401' || $phone == '8500005213' || $phone == '9822212411' || $phone == '8080262411' || $phone == '9323465785') {
                    $otp_code = '123456';
                } else {
                    $otp_code     = rand(100000, 999999);
                    $message      = 'Please enter this One Time Password : ' . $otp_code . ' to verify your mobile number.';
                    $post_data    = array(
                        'From' => '02233721563',
                        'To' => $phone,
                        'Body' => $message
                    );
                    $exotel_sid   = "aegishealthsolutions";
                    $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                    $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
                    $ch           = curl_init();
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
                    'otp_code' => (int)$otp_code,
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

    public function user_login($country_code,$phone,$token,$agent)
    {
        $numlength = strlen((string)$phone);
        if ($numlength>='10' && $numlength<='15' && $country_code!='' && $token!='' && $agent!='') {
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

                if ($phone == '9824242411' || $phone == '9733331211' || $phone == '8676008901' || $phone == '9107766551' || $phone == '7545032401' || $phone == '8500005213' || $phone == '9822212411' || $phone == '8080262411' || $phone == '9323465785') {
                    $otp_code = '123456';
                } else {
                    $otp_code     = rand(100000, 999999);
                    $message      = 'Please enter this One Time Password : ' . $otp_code . ' to verify your mobile number.';
                    $post_data    = array(
                        'From' => '02233721563',
                        'To' => $phone,
                        'Body' => $message
                    );
                    $exotel_sid   = "aegishealthsolutions";
                    $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                    $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
                    $ch           = curl_init();
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

                $resultpost[]=array(
                    'otp_code' => $otp_code,
                    'user' => 'old',
                    'id' => $id,
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'country_code' => $country_code,
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
                return array(
                    'status' => 200,
                    'message' => 'success',
                    'data'=> $resultpost
                );
            } else {
                if ($phone == '9824242411' || $phone == '9733331211' || $phone == '8676008901' || $phone == '9107766551' || $phone == '7545032401' || $phone == '8500005213' || $phone == '9822212411' || $phone == '8080262411' || $phone == '9323465785') {
                    $otp_code = '123456';
                } else {
                    $otp_code     = rand(100000, 999999);
                    $message      = 'Please enter this One Time Password : ' . $otp_code . ' to verify your mobile number.';
                    $post_data    = array(
                        'From' => '02233721563',
                        'To' => $phone,
                        'Body' => $message
                    );
                    $exotel_sid   = "aegishealthsolutions";
                    $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                    $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
                    $ch           = curl_init();
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
                $resultpost[]=array(
                    'otp_code' => $otp_code,
                    'user' => 'new',
                    'id' => '',
                    'name' => '',
                    'email' => '',
                    'phone' => $phone,
                    'country_code' => $country_code,
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
                return array(
                    'status' => 200,
                    'message' => 'success',
                    'data'=> $resultpost
                );
            }
        } else {
            return array(
                'status' => 208,
                'message' => 'Please enter phone no.',
                'data'=> array()
            );
        }
    }

   public function usersignup($name, $phone,  $gender, $dob,  $image, $token, $agent)
    {
        $count_user2 = $this->db->select('id')->from('users')->where('phone', $phone)->get()->num_rows();
        if ($count_user2 > 0) {
            return array(
                'status' => 208,
                'message' => 'Phone number already exist'
            );
        } else {
            if ($name != '' && $phone != '' && $gender != '' ) {
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
                $userpassword ='';
                $user_data    = array(
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


  public function userprofile($user_id, $marital_status, $blood_group, $height, $height_cm_ft, $weight, $diet_fitness, $exercise_level, $health_condition, $allergies, $ask_saheli, $organ_donor, $bmi, $activity_level,$addiction, $health_insurance,$blood_is_active,$heradiatry_problem,$lat,$lng,$map_location)
    {
        $count_user = $this->db->select('id')->from('users')->where('id', $user_id)->get()->num_rows();
        if ($count_user > 0) {
            if ($user_id != '') {
                date_default_timezone_set('Asia/Kolkata');
                $updated_at = date('Y-m-d H:i:s');
                $user_data = array(
                    'height_cm_ft' => $height_cm_ft,
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


                $q  = $this->db->select('avatar_id,password,height_cm_ft,id,name,IFNULL(activity_level,"") AS activity_level,phone,email,gender,dob,IFNULL(height,"") AS height,IFNULL(weight,"") AS weight,IFNULL(marital_status,"") AS marital_status,IFNULL(blood_group,"") AS blood_group,IFNULL(blood_is_active,"") AS blood_is_active,IFNULL(diet_fitness,"") AS diet_fitness,IFNULL(organ_donor,"") AS organ_donor,sex_history,exercise_level,health_condition,health_insurance,addiction,allergies,IFNULL(bmi,"") AS bmi ,IFNULL(heradiatry_problem,"") AS heradiatry_problem,IFNULL(lat,"") AS lat,IFNULL(lng,"") AS lng,IFNULL(map_location,"") AS map_location')->from('users')->where('users.id', $user_id)->get()->row();

                $height_cm_ft     =  $q -> height_cm_ft;
                $id               = $q->id;
                $name             = $q->name;
                $avatar_id             = $q->avatar_id;
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
                    'profile_completed'=>$profile_completed
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


    public function blood_group_update($user_id, $blood_group, $blood_is_active)
    {
        $count_user = $this->db->select('id')->from('users')->where('id', $user_id)->get()->num_rows();
        if ($count_user > 0) {
            if ($user_id != '') {
                date_default_timezone_set('Asia/Kolkata');
                $updated_at = date('Y-m-d H:i:s');
                $user_data  = array(

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
                $address_id   = $row['address_id'];
                $name         = $row['name'];
                $mobile       = $row['mobile'];
                $address1     = $row['address1'];
                $address2     = $row['address2'];
                $landmark     = $row['landmark'];
                $city         = $row['city'];
                $state        = $row['state'];
                $pincode      = $row['pincode'];
                $address_type = $row['address_type'];
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

    public function address_add($user_id, $name, $mobile, $pincode, $address1, $address2, $landmark, $city, $state, $address_type)
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

    public function address_update($user_id, $address_id, $name, $address1, $address2, $landmark, $mobile, $city, $state, $pincode, $address_type)
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
            $image  = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $source;
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

            case "Pharmacy":
                $this->db->query("DELETE FROM `medical_stores_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `medical_stores_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");

                //$this->db->query("DELETE FROM `medical_stores_review_likes` WHERE post_id='$post_id'");

                /*$result = $this->db->query("SELECT id, count(id) as count FROM medical_stores_review_likes where user_id = '$user_id'; ");
                for($i=0 ; $i<$result->count ; $i++){
                    $this->db->query("DELETE FROM `medical_stores_review_comment_like` WHERE `user_id`= '$result->$id'  AND `comment_id` = '$result->id' ");
                }*/


                //$this->db->query("DELETE FROM `medical_stores_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
                //$this->db->query("DELETE FROM `medical_stores_review_comment_like` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Nursing Attendant":
                $this->db->query("DELETE FROM `nursing_attendant_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `nursing_attendant_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");

                //$this->db->query("DELETE FROM `nursing_attendant_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
                //$this->db->query("DELETE FROM `nursing_attendant_review_comment_like` WHERE user_id='$user_id' and post_id='$post_id'");
                break;
            case "Fitness Center":
                $this->db->query("DELETE FROM `fitness_center_review` WHERE user_id='$user_id' and id='$post_id'");
                $this->db->query("DELETE FROM `fitness_center_review_comment` WHERE user_id='$user_id' and post_id='$post_id'");

                //$this->db->query("DELETE FROM `fitness_center_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
                //$this->db->query("DELETE FROM `fitness_center_review_comment_like` WHERE user_id='$user_id' and post_id='$post_id'");
                break;

        }
        return array(
            'status' => 200,
            'message' => 'deleted'
        );
    }

   public function app_banner()
    {

        $query = $this->db->query("SELECT `id`, `cat_id`, `image`,`tag` FROM `app_banners` WHERE is_active='1' order by id desc");
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
                $tag  = $row['tag'];
                $image  = $row['image'];
                $image  = 'https://d2c8oti4is0ms3.cloudfront.net/images/medicalwale_mobile_app_icons/banners/'.$image;

                switch ($cat_id) {
                    case "1":
                        $home[] = array(
                            'image' => $image,
                            'tag' => $tag
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


    public function save_post($user_id, $post_id,$post_type)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
       $count_query = $this->db->query("SELECT id from `post_save` WHERE  user_id='$user_id' and post_id='$post_id' and post_type='$post_type'");
       $count = $count_query->num_rows();

          if ($count < 1){

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
        }
        else {
            return array(
             'status' => 200,
            'message' => 'success'
            );
        }
    }

      public function save_post_list($user_id,$type,$page)
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


         $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }

       $start = ($page - 1) * $limit;


        $resultpost = array();

		if($type == 'healthwall'){
        $query = $this->db->query("select  IFNULL(posts.post_location,'') AS post_location,healthwall_category.category AS healthwall_category, posts.id as post_id,posts.type as post_type,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url from posts INNER JOIN users on users.id=posts.user_id  LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND posts.id IN (SELECT post_id FROM `post_save` WHERE `user_id`='$user_id' AND `post_type`='healthwall') order by posts.id DESC limit $start, $limit");
        $query_count = $this->db->query("select  IFNULL(posts.post_location,'') AS post_location,healthwall_category.category AS healthwall_category, posts.id as post_id,posts.type as post_type,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url from posts INNER JOIN users on users.id=posts.user_id  LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND posts.id IN (SELECT post_id FROM `post_save` WHERE `user_id`='$user_id' AND `post_type`='healthwall') order by posts.id DESC");
        $count_post = $query_count->num_rows();
        if ($count_post > 0) {
            foreach ($query->result_array() as $row) {
                $post_id             = $row['post_id'];
                $listing_type        = $row['vendor_id'];
                $post                = $row['post'];
                $post = preg_replace('~[\r\n]+~', '', $post);
                if(base64_encode(base64_decode($post)) === $post){
                    $post=base64_decode($post);
                }
                $category            = '';
                $is_anonymous        = $row['is_anonymous'];
                $tag                 = $row['tag'];
                $post_type           = $row['post_type'];
                $post_location          = $row['post_location'];
                $date                = $row['created_at'];
                $caption             = $row['caption'];
                $username            = $row['name'];
                $post_user_id        = $row['post_user_id'];
                $article_title       = $row['article_title'];
                $article_image       = $row['article_image'];
                $article_domain_name = $row['article_domain_name'];
                $article_url         = $row['article_url'];
                $healthwall_category_name = $row['healthwall_category'];

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();

                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file      = $profile_query->source;
                    $userimage     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $media_query  = $this->db->query("SELECT media.id AS media_id,media.type AS media_type,media.caption,media.source,post_media.img_width,post_media.img_height,post_media.video_width,post_media.video_height FROM media INNER JOIN post_media on media.id=post_media.media_id where post_media.post_id='$post_id'");
                $img_val      = '';
                $images       = '';
                $img_comma    = '';
                $img_width    = '';
                $img_height   = '';
                $video_width  = '';
                $video_height = '';

                $media_array = array();
                foreach ($media_query->result_array() as $media_row) {
                    $media_id   = $media_row['media_id'];
                    $media_type = $media_row['media_type'];
                    $source     = $media_row['source'];
                   $images     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_media/' . $media_type . '/' . $source;
                    if ($media_type == 'video') {
                        $thumb = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_media/thumb/' . substr_replace($source, 'jpg', strrpos($source, '.') + 1);
                    } else {
                        $thumb = '';
                    }
                    $caption      = $media_row['caption'];
                    $img_width    = $media_row['img_width'];
                    $img_height   = $media_row['img_height'];
                    $video_width  = $media_row['video_width'];
                    $video_height = $media_row['video_height'];


                    $view_media_count = $this->db->select('id')->from('post_video_views')->where('media_id', $media_id)->get()->num_rows();

                    $view_media_yes_no_query = $this->db->query("SELECT id from post_video_views WHERE media_id='$media_id' and user_id='$user_id'");
                    $view_media_yes_no       = $view_media_yes_no_query->num_rows();

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

                $like_count          = $this->db->select('id')->from('post_likes')->where('post_id', $post_id)->get()->num_rows();
                $comment_count       = $this->db->select('id')->from('comments')->where('post_id', $post_id)->get()->num_rows();
                $follow_count        = $this->db->select('id')->from('post_follows')->where('post_id', $post_id)->get()->num_rows();
                $follow_yes_no_query = $this->db->query("SELECT id from post_follows where post_id='$post_id' and user_id='$user_id'");
                $follow_post_yes_no  = $follow_yes_no_query->num_rows();
                $view_count          = $this->db->select('id')->from('post_views')->where('post_id', $post_id)->get()->num_rows();
                $like_yes_no_query   = $this->db->query("SELECT id from post_likes where post_id='$post_id' and user_id='$user_id'");
                $like_yes_no         = $like_yes_no_query->num_rows();
                $view_yes_no_query   = $this->db->query("SELECT id from post_views where post_id='$post_id' and user_id='$user_id'");
                $view_post_yes_no    = $view_yes_no_query->num_rows();

			   $is_reported_query   = $this->db->query("SELECT id from post_report WHERE post_id='$post_id' AND reporter_id='$user_id' AND post_type='healthwall'");
               $is_reported    = $is_reported_query->num_rows();

               $is_post_save_query   = $this->db->query("SELECT id from post_save WHERE post_id='$post_id' AND user_id='$user_id' AND post_type='healthwall'");
               $is_post_save    = $is_post_save_query->num_rows();


                $share_url = "https://medicalwale.com/share/healthwall/".$post_id;
                $tag       = str_replace('&nbsp;', '', $tag);
                $tag       = str_replace('&nbs', '', $tag);
                $tag       = rtrim(str_replace(' ', '', $tag), ",");


                $date         = get_time_difference_php($date);




			//comments
			$query_comment = $this->db->query("SELECT comments.id,comments.post_id,comments.description as comment,comments.updated_at as date,users.name,comments.user_id as post_user_id FROM comments INNER JOIN users on users.id=comments.user_id WHERE comments.post_id='$post_id' order by comments.id desc LIMIT 0,3");

			$comments = array();

			$comment_counts = $query_comment->num_rows();
            if ($comment_counts > 0) {

            foreach ($query_comment->result_array() as $rows) {
                $comment_id           = $rows['id'];
                $comment_post_id      = $rows['post_id'];
                $comment      = $rows['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if(base64_encode(base64_decode($comment)) === $comment){
                    $comment=base64_decode($comment);
                }
                $comment_username     = $rows['name'];
                $comment_date         = $rows['date'];
                $comment_post_user_id = $rows['post_user_id'];

                $comment_like_count   = $this->db->select('id')->from('comment_likes')->where('comment_id', $comment_id)->get()->num_rows();
                $comment_like_yes_no  = $this->db->select('id')->from('comment_likes')->where('comment_id', $comment_id)->where('user_id', $user_id)->get()->num_rows();
                $comment_date = get_time_difference_php($comment_date);

                $comment_img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_post_user_id)->get()->num_rows();

                if ($comment_img_count > 0) {
                    $comment_profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_post_user_id)->get()->row();
                    $comment_img_file      = $comment_profile_query->source;
                    $comment_userimage     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $comment_img_file;
                } else {
                    $comment_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }


                $query_listing_type = $this->db->query("SELECT vendor_id FROM users WHERE id='$comment_post_user_id'");
                $get_type = $query_listing_type->row_array();
                $listing_type  = $get_type['vendor_id'];


                $comments[] = array(
                    'id' => $comment_id,
                    'listing_type' =>  $listing_type,
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


            }
		else {
            $comments = array();
        }

			//comments

                $resultpost_list[] = array(
                    'id' => $post_id,
                    'post_user_id' => $post_user_id,
                    'listing_type' => $listing_type,
                    'post_location'=> $post_location,
                    'healthwall_category' => $healthwall_category_name,
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
		            'comments' => $comments
                );
            }
			$resultpost = array(
            "status" => 200,
            "message" => "success",
            "count" => $count_post,
            "data" => $resultpost_list
			);

        }
		else
        {
          	$resultpost = array(
            "status" => 200,
            "message" => "success",
            "count" => $count_post,
            "data" => array()
			);

         }
		}



        if($type == 'asksaheli') {
        $query = $this->db->query("select ask_saheli_post.id ,ask_saheli_post.type,IFNULL(ask_saheli_post.post_location,'') AS post_location,ask_saheli_post.date,ask_saheli_post.post,ask_saheli_post.tag,ask_saheli_category.category,ask_saheli_post.user_name,user_character.image as character_image,ask_saheli_post.article_title,ask_saheli_post.article_image,ask_saheli_post.article_domain_name,ask_saheli_post.article_url,ask_saheli_post.user_id as post_user_id FROM ask_saheli_post INNER JOIN ask_saheli_category on ask_saheli_category.id=ask_saheli_post.category INNER JOIN user_character on user_character.id=ask_saheli_post.user_image WHERE ask_saheli_post.id NOT IN (SELECT post_id FROM ask_saheli_post_hide WHERE post_id=ask_saheli_post.id AND user_id='$user_id') AND ask_saheli_post.id IN (SELECT post_id FROM `post_save` WHERE `user_id`='$user_id' AND `post_type`='asksaheli') order by ask_saheli_post.id DESC limit $start, $limit");
        $query_count = $this->db->query("select ask_saheli_post.id ,ask_saheli_post.type,IFNULL(ask_saheli_post.post_location,'') AS post_location,ask_saheli_post.date,ask_saheli_post.post,ask_saheli_post.tag,ask_saheli_category.category,ask_saheli_post.user_name,user_character.image as character_image,ask_saheli_post.article_title,ask_saheli_post.article_image,ask_saheli_post.article_domain_name,ask_saheli_post.article_url,ask_saheli_post.user_id as post_user_id FROM ask_saheli_post INNER JOIN ask_saheli_category on ask_saheli_category.id=ask_saheli_post.category INNER JOIN user_character on user_character.id=ask_saheli_post.user_image WHERE ask_saheli_post.id NOT IN (SELECT post_id FROM ask_saheli_post_hide WHERE post_id=ask_saheli_post.id AND user_id='$user_id') AND ask_saheli_post.id IN (SELECT post_id FROM `post_save` WHERE `user_id`='$user_id' AND `post_type`='asksaheli') order by ask_saheli_post.id DESC");

        $count_post  = $query_count ->num_rows();
        if ($count_post > 0) {
            foreach ($query->result_array() as $row) {
                $id              = $row['id'];
                $post_id              = $row['id'];
                $post            = $row['post'];
                $post = preg_replace('~[\r\n]+~', '', $post);
                if(base64_encode(base64_decode($post)) === $post){
                    $post=base64_decode($post);
                }
                 $post_location                = $row['post_location'];
                $user_name       = $row['user_name'];
                $post_user_id      = $row['post_user_id'];
                $tag             = $row['tag'];
                $category        = $row['category'];
                $character_image = $row['character_image'];
                $type            = $row['type'];
                $article_title   = $row['article_title'];
                $article_image   = $row['article_image'];
                $article_domain_name= $row['article_domain_name'];
                $article_url        = $row['article_url'];
                $views           = 0;
                $video_views     = 0;

                $query_media = $this->db->query("SELECT ask_saheli_post_media.id AS media_id,ask_saheli_post_media.source,ask_saheli_post_media.type AS media_type,IFNULL(ask_saheli_post_media.caption,'') AS caption,ask_saheli_post_media.img_width,ask_saheli_post_media.img_height,ask_saheli_post_media.video_width,ask_saheli_post_media.video_height FROM ask_saheli_post_media INNER JOIN ask_saheli_post on ask_saheli_post_media.post_id=ask_saheli_post.id WHERE ask_saheli_post_media.post_id='$id'");
                $img_val      = '';
                $images       = '';
                $img_comma    = '';
                $img_width    = '';
                $img_height   = '';
                $video_width  = '';
                $video_height = '';

                $media_array = array();

                foreach ($query_media->result_array() as $media_row) {
                    $media_id     = $media_row['media_id'];
                    $media_type   = $media_row['media_type'];
                    $source       = $media_row['source'];
                    $images       = 'https://d2c8oti4is0ms3.cloudfront.net/images/ask_saheli_images/'.$media_type.'/'.$source;
					if($media_type=='video'){
                        $thumb       = 'https://d2c8oti4is0ms3.cloudfront.net/images/ask_saheli_images/thumb/'.substr_replace($source,'jpg',strrpos($source,'.')+1);
                    }else{ $thumb=''; }
                    $caption      = $media_row['caption'];
                    $img_width    = $media_row['img_width'];
                    $img_height   = $media_row['img_height'];
                    $video_width  = $media_row['video_width'];
                    $video_height = $media_row['video_height'];

                    $view_media_count = $this->db->select('id')->from('ask_saheli_video_views')->where('media_id', $media_id)->get()->num_rows();

                    $view_media_yes_no_query = $this->db->query("SELECT id from ask_saheli_video_views WHERE media_id='$media_id' and user_id='$user_id'");
                    $view_media_yes_no       = $view_media_yes_no_query->num_rows();


                    $media_array[] = array(
                        'media_id' => $media_id,
                        'type' => $media_type,
                        'images' => str_replace(' ','%20',$images),
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
                $date = get_time_difference_php($date);

                $like_count      = $this->db->select('id')->from('ask_saheli_likes')->where('post_id', $id)->get()->num_rows();
                $comment_count   = $this->db->select('id')->from('ask_saheli_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no     = $this->db->select('id')->from('ask_saheli_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                $character_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/ask_saheli_images/character/' . $character_image;


                $view_count = $this->db->select('id')->from('ask_saheli_post_views')->where('post_id', $id)->get()->num_rows();

                $view_yes_no_query = $this->db->query("SELECT id from ask_saheli_post_views WHERE post_id='$id' and user_id='$user_id'");
                $view_post_yes_no  = $view_yes_no_query->num_rows();

                $follow_count = $this->db->select('id')->from('ask_saheli_follow_post')->where('post_id', $id)->get()->num_rows();

                $follow_yes_no_query = $this->db->query("SELECT id from ask_saheli_follow_post where post_id='$id' and user_id='$user_id'");
                $follow_post_yes_no  = $follow_yes_no_query->num_rows();
                $share_url           = "https://medicalwale.com/share/asksaheli/".$id;
                $is_reported_query   = $this->db->query("SELECT id from post_report WHERE post_id='$post_id' AND reporter_id='$user_id' AND post_type='asksaheli'");
                $is_reported    = $is_reported_query->num_rows();

                $is_post_save_query   = $this->db->query("SELECT id from post_save WHERE post_id='$post_id' AND user_id='$user_id' AND post_type='asksaheli'");
                $is_post_save    = $is_post_save_query->num_rows();


			//comments

			$query_comment = $this->db->query("SELECT ask_saheli_comment.id,ask_saheli_comment.comment,ask_saheli_comment.date,ask_saheli_comment.user_name,ask_saheli_character.image as character_image,ask_saheli_comment.user_id as post_user_id  FROM ask_saheli_comment LEFT JOIN  ask_saheli_character on ask_saheli_character.id=ask_saheli_comment.user_image WHERE ask_saheli_comment.post_id='$post_id' order by ask_saheli_comment.id desc LIMIT 0,3");

			$comment_counts = $query_comment->num_rows();
			if ($comment_counts > 0) {
			$comments = array();
            foreach ($query_comment->result_array() as $rows) {
                $comment_id           = $rows['id'];
                $comment      = $rows['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if(base64_encode(base64_decode($comment)) === $comment){
                    $comment=base64_decode($comment);
                }
                $comment_username     = $rows['user_name'];
                $comment_image        = $rows['character_image'];
                $comment_date         = $rows['date'];
                $comment_post_user_id = $rows['post_user_id'];
                $comment_date = get_time_difference_php($comment_date);
                $comment_image        = 'https://d2c8oti4is0ms3.cloudfront.net/images/ask_saheli_images/character/' . $comment_image;

                $comment_like_count  = $this->db->select('id')->from('ask_saheli_comment_like')->where('comment_id', $comment_id)->get()->num_rows();
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
			}
			else{
				$comments = array();
			}

			//comments


                $resultpost_list[] = array(
                    'id' => $id,
                    'post_type' => $type,
                    'post_location'=> $post_location,
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
                    'comments' => $comments
                );
            }

			$resultpost = array(
            "status" => 200,
            "message" => "success",
            "count" => $count_post,
            "data" => $resultpost_list
			);

        }
		else {
          	$resultpost = array(
            "status" => 200,
            "message" => "success",
            "count" => $count_post,
            "data" => array()
			);
        }
        }





        if($type == 'drlelo'){
         $query = $this->db->query("SELECT sex_education_question.id,sex_education_question.age,sex_education_question.user_image,sex_education_question.user_name,sex_education_question.user_id,sex_education_question.question,sex_education_question.date,IFNULL(sex_education_question.post_location,'') AS post_location,sex_education_character.image AS c_image  FROM  `sex_education_question` INNER JOIN `sex_education_character` ON sex_education_question.user_image=sex_education_character.id  WHERE sex_education_question.id NOT IN (SELECT post_id FROM sex_education_hide WHERE post_id=sex_education_question.id AND user_id='$user_id') AND sex_education_question.id IN (SELECT post_id FROM `post_save` WHERE `user_id`='$user_id' AND `post_type`='drlelo') order by sex_education_question.id DESC limit $start, $limit");
        $query_count = $this->db->query("SELECT sex_education_question.id,sex_education_question.age,sex_education_question.user_image,sex_education_question.user_name,sex_education_question.user_id,sex_education_question.question,sex_education_question.date,IFNULL(sex_education_question.post_location,'') AS post_location,sex_education_character.image AS c_image  FROM  `sex_education_question` INNER JOIN `sex_education_character` ON sex_education_question.user_image=sex_education_character.id  WHERE sex_education_question.id NOT IN (SELECT post_id FROM sex_education_hide WHERE post_id=sex_education_question.id AND user_id='$user_id') AND sex_education_question.id IN (SELECT post_id FROM `post_save` WHERE `user_id`='$user_id' AND `post_type`='drlelo') order by sex_education_question.id DESC");
 $count_post  = $query_count ->num_rows();

        if ($count_post > 0) {
            foreach ($query->result_array() as $row) {
                $id           = $row['id'];
				$age          = $row['age'];
				$post_location       = $row['post_location'];
				$images   = $row['c_image'];
				$user_name    = $row['user_name'];
				$post_user_id      = $row['user_id'];
				$question     = $row['question'];
				$question = preg_replace('~[\r\n]+~', '', $question);
                if(base64_encode(base64_decode($question)) === $question){
                    $question=base64_decode($question);
                }
				$date         = $row['date'];
				$date=get_time_difference_php($date);
				if($images!='')
				{
					$image='https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/'.$images;
				}
				else
				{
					$image='';
				}

			    $is_notify_query = $this->db->query("SELECT id FROM `sex_education_is_notify` where post_id='$id' AND user_id='$user_id'");
				$is_notify  = $is_notify_query->num_rows();


				$is_follow='0';

                $answer_list=array();
				 $answer_query =$this->db->query("SELECT id,type,answer,post_id FROM `sex_education_answer` WHERE `post_id`='$id'");
				 $count_answers = $answer_query->num_rows();
				if($count_answers>0){
				 foreach ($answer_query->result_array() as $rows) {

				  $answer_id = $rows['id'];
				  $answer = $rows['answer'];
				   $type = $rows['type'];
				  $answer = preg_replace('~[\r\n]+~', '', $answer);
                  if(base64_encode(base64_decode($answer)) === $answer){
                    $answer=base64_decode($answer);
                  }
			  	  $answer_image='https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/dr-lelo.jpg';

				  $answer_list[]=array(
				            'answer_id' => $answer_id,
                            'type' =>$type,
                            'answer' => $answer,
                            //'answer_image' => $answer_image
							);

				 }
			   }
				else
				{
				 $answer_list=array();
				}


                $share_url           = "https://medicalwale.com/share/drlelo/".$id;

				$count_query = $this->db->query("SELECT id FROM `sex_education_likes` where post_id='$id'");
				$like_count  = $count_query->num_rows();

				$like_count_query = $this->db->query("SELECT id FROM `sex_education_likes` where user_id='$user_id' and post_id='$id'");
				$like_yes_no  = $like_count_query->num_rows();

				$is_post_save_query   = $this->db->query("SELECT id from post_save WHERE post_id='$id' AND user_id='$user_id' AND post_type='drlelo'");
                $is_post_save    = $is_post_save_query->num_rows();

                $is_reported_query   = $this->db->query("SELECT id from post_report WHERE post_id='$id' AND reporter_id='$user_id' AND post_type='drlelo'");
                $is_reported    = $is_reported_query->num_rows();


                $resultpost_list[] = array(
                    'id'=>$id,
                    'post_user_id'=>$post_user_id,
                    'post_location' => $post_location,
                    'user_name'=>$user_name,
                    'question'=>$question,
                    'is_notify'=>$is_notify,
                    'age'=>$age,
                    'image'=>$image,
                    'answer_image' => "https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/dr-lelo.jpg",
                    'answer_list'=>$answer_list,
                    'like_count'=>$like_count,
                    'like_yes_no'=>$like_yes_no,
                    'is_follow'=>$is_follow,
                    'share_url'=>$share_url,
                    'is_reported'=>$is_reported,
                    'is_post_save'=>$is_post_save,
                    'date'=>$date
                    );
            }

			 $resultpost = array(
            "status" => 200,
            "message" => "success",
            "count" => $count_post,
            "data" => $resultpost_list
			);

        }
		else {
             	$resultpost = array(
            "status" => 200,
            "message" => "success",
            "count" => $count_post,
            "data" => array()
			);
        }
		}




        if($type =='missbelly'){
         $query = $this->db->query("SELECT miss_belly_question.id,miss_belly_question.age,miss_belly_question.weight,miss_belly_question.diet_preference,miss_belly_question.height,miss_belly_question.user_image,miss_belly_question.user_name,miss_belly_question.user_id,miss_belly_question.question,miss_belly_question.date,IFNULL(miss_belly_question.post_location,'') AS post_location,sex_education_character.image AS c_image  FROM  `miss_belly_question` INNER JOIN `sex_education_character` ON miss_belly_question.user_image=sex_education_character.id  WHERE miss_belly_question.id NOT IN (SELECT post_id FROM miss_belly_hide WHERE post_id=miss_belly_question.id AND user_id='$user_id') AND miss_belly_question.id IN (SELECT post_id FROM `post_save` WHERE `user_id`='$user_id' AND `post_type`='missbelly') order by miss_belly_question.id DESC limit $start, $limit");
         $query_count = $this->db->query("SELECT miss_belly_question.id,miss_belly_question.age,miss_belly_question.weight,miss_belly_question.diet_preference,miss_belly_question.height,miss_belly_question.user_image,miss_belly_question.user_name,miss_belly_question.user_id,miss_belly_question.question,miss_belly_question.date,IFNULL(miss_belly_question.post_location,'') AS post_location,sex_education_character.image AS c_image  FROM  `miss_belly_question` INNER JOIN `sex_education_character` ON miss_belly_question.user_image=sex_education_character.id  WHERE miss_belly_question.id NOT IN (SELECT post_id FROM miss_belly_hide WHERE post_id=miss_belly_question.id AND user_id='$user_id') AND miss_belly_question.id IN (SELECT post_id FROM `post_save` WHERE `user_id`='$user_id' AND `post_type`='missbelly') order by miss_belly_question.id DESC");
            $count_post  = $query_count->num_rows();

        $resultpost = array();

        if ($count_post > 0) {
            foreach ($query->result_array() as $row) {
                $id           = $row['id'];
                $post_id           = $row['id'];
				$weight       = $row['weight'];
				$post_location       = $row['post_location'];
				$height       = $row['height'];
				$diet_preference       = $row['diet_preference'];
				$age          = $row['age'];
				$images   = $row['c_image'];
				$user_name    = $row['user_name'];
				$post_user_id      = $row['user_id'];
				$question     = $row['question'];
				$question = preg_replace('~[\r\n]+~', '', $question);
                if(base64_encode(base64_decode($question)) === $question){
                    $question=base64_decode($question);
                }
				$date         = $row['date'];
				$date=get_time_difference_php($date);
				if($images!='')
				{
					$image='https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/'.$images;
				}
				else
				{
					$image='';
				}

			    $is_notify_query = $this->db->query("SELECT id FROM `miss_belly_is_notify` where post_id='$id' AND user_id='$user_id'");
				$is_notify  = $is_notify_query->num_rows();


				$is_follow='0';

             $answer_lists=array();
			 $answer_query =$this->db->query("SELECT id,type,answer,post_id FROM `miss_belly_answer` WHERE `post_id`='$post_id'");
				 $count_answers = $answer_query->num_rows();
				if($count_answers>0){
				 foreach ($answer_query->result_array() as $rows) {
				  $answer_id = $rows['id'];
				  $answer = $rows['answer'];
				  $type = $rows['type'];
				  $answer = preg_replace('~[\r\n]+~', '', $answer);
                  if(base64_encode(base64_decode($answer)) === $answer){
                    $answer=base64_decode($answer);
                  }
				  $answer_lists[]=array(
				            'answer_id' =>$answer_id,
                            'type' =>$type,
                            'answer' => $answer,
                            //'answer_image' => $answer_image
							);
				 }
			   }
				else
				{
				 $answer_lists=array();
				}


          $share_url  = "https://medicalwale.com/share/missbelly/".$id;
         $answer_image='https://d2c8oti4is0ms3.cloudfront.net/images/medicalwale_mobile_app_icons/fitness/miss_belly.png';
				$count_query = $this->db->query("SELECT id FROM `miss_belly_likes` where post_id='$id'");
				$like_count  = $count_query->num_rows();

				$like_count_query = $this->db->query("SELECT id FROM `miss_belly_likes` where user_id='$user_id' and post_id='$id' limit 1");
				$like_yes_no  = $like_count_query->num_rows();

				$is_post_save_query   = $this->db->query("SELECT id from post_save WHERE post_id='$post_id' AND user_id='$user_id' AND post_type='missbelly'");
                $is_post_save    = $is_post_save_query->num_rows();

                $is_reported_query   = $this->db->query("SELECT id from post_report WHERE post_id='$post_id' AND reporter_id='$user_id' AND post_type='missbelly'");
                $is_reported    = $is_reported_query->num_rows();

                $resultpost_list[] = array(
                    'id'=>$id,
                    'post_user_id'=>$post_user_id,
                    'post_location'=> $post_location,
                    'user_name'=>$user_name,
                    'question'=>$question,
                    'diet_preference'=>$diet_preference,
                    'answer_list'=>$answer_lists,
                    'is_notify'=>$is_notify,
                    'age'=>$age,
                    'height'=> $height,
                    'weight'=> $weight,
                    'image'=>$image,
                    'answer_image'=>$answer_image,
                    'like_count'=>$like_count,
                    'like_yes_no'=>$like_yes_no,
                    'is_follow'=>$is_follow,
                    'share_url'=>$share_url,
                    'is_reported'=>$is_reported,
                    'is_post_save'=>$is_post_save,
                    'date'=>$date);
            }

         $resultpost = array(
            "status" => 200,
            "message" => "success",
            "count" => $count_post,
            "data" => $resultpost_list
        );

        }
		else {
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


     public function remove_save_post($user_id, $post_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `post_save` WHERE  user_id='$user_id' and post_id='$post_id'");
        $count       = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `post_save` WHERE user_id='$user_id' and post_id='$post_id'");

            return array(
                'status' => 200,
                'message' => 'deleted'
            );
        }

        else {
            return array(
                'status' => 401,
                'message' => 'unauthorized'
            );
        }

    }


     public function report_post($user_id, $post_id,$post_type)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `post_report` WHERE  reporter_id='$user_id' and post_id='$post_id' and post_type='$post_type'");
       $count = $count_query->num_rows();

          if ($count < 1){
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
        }
        else {
            return array(
             'status' => 200,
            'message' => 'success'
            );
        }
    }


    public function user_character($type)
    {
        $review_list_count = $this->db->select('id')->from('user_character')->where('type', $type)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT id,image FROM `user_character` WHERE type='$type' order by id desc");
            foreach ($query->result_array() as $row) {
                $id           = $row['id'];
				$image        = $row['image'];
				$image='https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/'.$image;
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

     public function common_user_update($user_id,$user_name,$user_image)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id FROM `user_avatar` WHERE user_id='$user_id'");
        $count  = $count_query->num_rows();

        if ($count > 0) {
		    $this->db->query("UPDATE `user_avatar` SET `user_name`='$user_name',`user_image`='$user_image' WHERE user_id='$user_id'");
            return array(
                'status' => 200,
                'message' => 'success'
            );
        }
		else {

            $ask_saheli_ask_user = array(
                'user_id' 	 => $user_id,
                'user_name'  => $user_name,
                'user_image' => $user_image
            );
            $this->db->insert('user_avatar', $ask_saheli_ask_user);
            return array(
                'status' => 200,
                'message' => 'success'
            );
        }
    }


     public function common_user_check($user_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id FROM `user_avatar` WHERE user_id='$user_id'");
        $count  = $count_query->num_rows();

        if ($count > 0) {
            $query = $this->db->query("SELECT user_avatar.user_id, user_avatar.user_name, user_avatar.user_image,user_character.image AS c_image  FROM `user_avatar` INNER JOIN `user_character` ON user_avatar.user_image=user_character.id  WHERE user_avatar.user_id='$user_id'");


    	    $row=$query->row_array();
			$user_id = $row['user_id'];
			$user_name  = $row['user_name'];
			$user_image  = $row['user_image'];
			$images    = $row['c_image'];
			if($images!='')
			{
				$image='https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/'.$images;
			}
			else
			{
			  $image='';
			}

			$resultpost[] = array(
			'user_id'=>$user_id,
			'user_name'=>$user_name	,
			'user_image'=>$user_image,
			'images'=>$image
			);
           }
		else {

		     $resultpost=array();
        }

          return $resultpost;
    }



  public function user_booking_details($user_id, $listing_type)
     {


            if ($listing_type == '6') {
                // Fitness Center

  $querys  = $this->db->query("SELECT fitness_center_branch.branch_name, fitness_center_branch.branch_image, fitness_center_branch.branch_phone, fitness_center_branch.branch_address, fitness_center_branch.pincode, fitness_center_branch.state, fitness_center_branch.city,packages.package_name, packages.package_details, packages.price,booking_master.package_id,booking_master.booking_id, booking_master.listing_id, booking_master.user_id, booking_master.user_name, booking_master.user_mobile, booking_master.user_email, booking_master.user_gender, booking_master.branch_id, booking_master.vendor_id, booking_master.booking_date, booking_master.category_id, booking_master.trail_booking_date, booking_master.trail_booking_time, booking_master.joining_date FROM booking_master
INNER JOIN fitness_center_branch ON booking_master.branch_id=fitness_center_branch.id
INNER JOIN packages ON booking_master.package_id=packages.id
WHERE booking_master.user_id='$user_id' AND booking_master.vendor_id='6'");

        $count = $querys->num_rows();
        if ($count > 0) {
              foreach ($querys->result_array() as $row) {
                $package_id         	= $row['package_id'];
                $booking_id         	= $row['booking_id'];
                $branch_name        	= $row['branch_name'];
                $branch_image       	= $row['branch_image'];
                $branch_phone       	= $row['branch_phone'];
                $branch_address       	= $row['branch_address'];
                $branch_pincode		    = $row['pincode'];
                $branch_city       		= $row['city'];
                $branch_state       	= $row['state'];
                $appointment_user_name  = $row['user_name'];
                $appointment_user_mobile= $row['user_mobile'];
                $appointment_user_email = $row['user_email'];
                $category_id       		= $row['category_id'];
                $package_name       	= $row['package_name'];
                $package_details       	= $row['package_details'];
                $package_price       	= $row['price'];
                $trail_booking_date 	= $row['trail_booking_date'];
                $trail_booking_time 	= $row['trail_booking_time'];
				$joining_date       	= date('j M Y', strtotime($row['joining_date']));
				$is_free_trial  ='No';
			    date_default_timezone_set('Asia/Kolkata');
				if($package_id=='100'){
					$joining_date_= $trail_booking_date.' '.$trail_booking_time;

					$joining_date = date('j M Y | h:i A', strtotime($joining_date_));
					$is_free_trial  ='Yes';
				}
				$booking_date = date('j M Y | h:i A', strtotime($row['booking_date']));

			   if ($branch_image != '') {
                    $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                } else {
                    $branch_image = '';
                }


                $resultpost[] = array(
                    'booking_id' => $booking_id,
					'branch_name' => $branch_name,
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
                    'booking_date' => $booking_date
                );

            }
            }else {
            $resultpost = array();
        }

     }



          return $resultpost;
    }




}
