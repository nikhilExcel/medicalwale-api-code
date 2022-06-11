<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PharmacyPartnerModel extends CI_Model
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
    
    
    public function is_free_delivery_staus($free_start_time, $free_end_time)
    {
        $free_start_time_st = date("h:i A", strtotime($free_start_time));
        $free_end_time_st   = date("h:i A", strtotime($free_end_time));
        $current_time_st    = date('h:i A');
        
        
        $date1 = DateTime::createFromFormat('H:i a', $current_time_st);
        $date2 = DateTime::createFromFormat('H:i a', $free_start_time_st);
        $date3 = DateTime::createFromFormat('H:i a', $free_end_time_st);
        
        if ($date2 < $date3 && $date1 <= $date3) {
            $date3->modify('+1 day')->format('H:i a');
        } elseif ($date2 > $date3 && $date1 >= $date3) {
            $date3->modify('+1 day')->format('H:i a');
        }
        
        
        if ($date1 > $date2 && $date1 < $date3) {
            $is_free_delivery = 'Yes';
        } else {
            $is_free_delivery = 'No';
        }
        
        return $is_free_delivery;
        
    }
    
    public function check_time_format($time)
    {
        $time_filter = preg_replace('/\s+/', '', $time);
        $final_time  = date("h:i A", strtotime($time_filter));
        return $final_time;
    }
    
    function sendmail($email, $password, $login_url)
    {
        $subject = "REGISTRATION INFORMATION";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: Medicalwale.com <no-reply@medicalwale.com>' . "\r\n";
        $headers .= 'Cc: ' . "\r\n";
        $message = '<div style="max-width: 700px;float: none;margin: 0px auto;"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1"><div id="styles_holder"><style>.ReadMsgBody{width:100%;background-color:#fff}.ExternalClass{width:100%;background-color:#fff}.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div{line-height:100%}html{width:100%}body{-webkit-text-size-adjust:none;-ms-text-size-adjust:none;margin:0;padding:0}table{border-spacing:0;border-collapse:collapse;table-layout:fixed;margin:0 auto}table table table{table-layout:auto}img{display:block !important}table td{border-collapse:collapse}.yshortcuts a{border-bottom:none !important}a{color:#1abc9c;text-decoration:none}@media only screen and (max-width: 640px){body{width:auto !important}table[class="table-inner"]{width:90% !important}table[class="table-full"]{width:100% !important;text-align:center !important}img[class="img1"]{width:100% !important;height:auto !important}}@media only screen and (max-width: 479px){body{width:auto !important}table[class="table-inner"]{width:90% !important}table[class="table-full"]{width:100% !important;text-align:center !important}img[class="img1"]{width:100% !important}}</style></div><div id="frame" class="ui-sortable"><table data-thumb="" data-module="header-bar" data-bgcolor="Main BG" align="center" width="100%" bgcolor="#e7e7e7" border="0" cellspacing="0" cellpadding="0" class=""><tbody><tr><td data-border-top-color="Top Border" style="border-top:4px solid #049341;"></td></tr><tr><td height="25"></td></tr><tr><td align="center"><table data-bgcolor="Container" width="600" class="table-inner" bgcolor="#ffffff" style="border-top-left-radius:5px;border-top-right-radius:5px;" border="0" align="center" cellpadding="0" cellspacing="0"><tbody><tr><td align="center" style="border-bottom: 5px solid #049341;"><table class="table-inner" width="520" border="0" align="center" cellpadding="0" cellspacing="0"><tbody><tr><td style="padding: 10px 0px;"><table class="table-full" border="0" align="left" cellpadding="0" cellspacing="0"><tbody><tr><td align="center" style="line-height:0px;"> <img data-crop="false" mc:edit="quinn-box-1" style="display:block; line-height:0px; font-size:0px; border:0px;height: 70px;" src="https://d2c8oti4is0ms3.cloudfront.net/images/img/email-logo.png" alt="logo" ></td></tr></tbody></table><table border="0" align="right" cellpadding="0" cellspacing="0" class="table-full"><tbody><tr><td height="15"></td></tr><tr><td align="center"><table border="0" align="center" cellpadding="0" cellspacing="0"><tbody><tr><td align="center" style=""> <font style="font-size:11px;line-height:16px" face="font-family: arial,sans-serif;" color="#666666"> <b style="font-size: 12px;font-family: arial, sans-serif;">Congratulations On Registration</b><br> </font> <font style="font-size:14px" face="HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue, Helvetica, Arial, sans-serif" color="#666666"> Call Us: </font><font style="font-size:16px" face="HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica, Arial, sans-serif" color="#7db701"><strong>022-60123457</strong></font></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table data-thumb="" data-module="1-2-right" data-bgcolor="Main BG" width="100%" align="center" bgcolor="#e7e7e7" border="0" cellspacing="0" cellpadding="0" class=""><tbody><tr><td align="center"><table data-bgcolor="Container" bgcolor="#FFFFFF" class="table-inner" width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="height: 250px;"><tbody style="background: url(https://d2c8oti4is0ms3.cloudfront.net/images/img/mail_bg.jpg);background-size: cover;"><tr><td height="20"></td></tr><tr><td align="center"><table class="table-inner" width="570" border="0" align="center" cellpadding="0" cellspacing="0"><tbody ><tr><td><table class="table-full" border="0" align="left" cellpadding="0" cellspacing="0" style="width: 320px;"><tbody><tr><td align="left" style="padding-bottom: 10px;"><p><font style="font-size:14px;color:#fff;line-height: 20px;" face="arial,sans-serif" >Welcome to </font><br> <font style="font-size:22px;color:#fff;" face="arial,sans-serif" ><span class="il">Medicalwale.com</span></font></p><p><font style="font-size:14px;color:#fff;line-height: 20px;" face="arial,sans-serif" >You are now part of 50,000+ Healthcare Service providers. We are delighted to have you on board with us. </font></p><p><font style="font-size:14px;color:#fff;" face="arial,sans-serif" >For any clarification <br> Mail us on: <a href="mailto:partner@medicalwale.com" style="color:#fff">partner@medicalwale.com</a> </font></p></td></tr></tbody></table><table border="0" align="right" cellpadding="0" cellspacing="0" class="table-full" style="width: 250px;"><tbody><tr><td><table style="background-color:rgb(249,246,246);border:0px solid rgb(213,210,210);width:100%;" cellpadding="0" cellspacing="0"><tbody><tr><td style="padding-left:20px;padding-top:10px;padding-bottom:10px;padding-right:10px; background: #a8abaf; text-align: center;" valign="top" align="left"><font style="font-size:16px" face="arial,sans-serif" color="#ffffff">Your Login Details</font></td></tr><tr><td style="padding:18px 15px 4px;background: #fff;" align="left"> <font style="font-size:14px;color: #656060;font-weight: 600;" face="arial,sans-serif">Email:</font> <font style="font-size:14px;line-height:21px" face="arial,sans-serif" color="#333333"> <a href="mailto:' . $email . '" target="_blank" style="color: #656060;text-decoration: none;">' . $email . '</a></font></td></tr><tr><td style="padding-bottom:18px;padding-top:4px;padding-left:15px;background: #fff;" align="left"> <font style="font-size:14px;color: #656060;font-weight: 600;" face="arial,sans-serif">Password :</font> <font style="font-size:14px;line-height:21px;color: #656060" face="arial,sans-serif" >' . $password . '</font></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td height="20"></td></tr></tbody></table></td></tr></tbody></table><table data-thumb="" data-module="quote" data-bgcolor="Main BG" width="100%" align="center" bgcolor="#e7e7e7" border="0" cellspacing="0" cellpadding="0" class="" ><tbody><tr><td data-bgcolor="Feature BG" align="center" bgcolor="#e7e7e7"><table data-bgcolor="Feature inner" bgcolor="#fff" class="table-inner" width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;"><tbody><tr><td align="center"><table class="table-inner" width="520" border="0" align="center" cellpadding="0" cellspacing="0"><tbody><tr><td height="35"></td></tr><tr><td height="5"></td></tr><tr><td data-link-style="text-decoration:none; color:#4a4a4a;" data-link-color="Quote" data-size="Quote" data-color="Quote" mc:edit="quinn-box-56" align="center" style="font-family: century Gothic, arial, sans-serif; font-size:16px; color:#4a4a4a; font-weight:bold;">"Your partnership means a lot to us and your constant support is what keeps us going. We look forward to continuing our relation in years to come!"</td></tr><tr><td height="35"></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table data-thumb="" data-module="footer" data-bgcolor="Main BG" width="100%" bgcolor="#e7e7e7" border="0" align="center" cellpadding="0" cellspacing="0" class=""><tbody><tr><td data-bgcolor="Footer Bar" bgcolor="#191919" align="center" style="font-family: Open Sans, Arial, sans-serif; font-size:11px; color:#ffffff;background: #e7e7e7;"><table class="table-inner" width="600" border="0" align="center" cellpadding="0" cellspacing="0"><tbody><tr><td><table class="table-full" bgcolor="#e7e7e7" border="0" align="left" cellpadding="0" cellspacing="0" style="width: 100%;"><tbody><tr><td data-link-style="text-decoration:none; color:#ffffff;" data-link-color="Footer Link" data-size="Content" data-color="Footer Text" mc:edit="quinn-box-81" height="30" style="font-family: Open Sans, Arial, sans-serif; font-size:12px; color: #6d6d6d;line-height: 19px;text-align:center; padding: 10px 0px;"> By using these service, you agree that you are bound by the Terms of Service.<br/>Copyright 2017 AEGIS HEALTH SOLUTIONS PVT. LTD. All rights reserved.</td></tr><tr><td height="15"></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></div></div>';
        
        $sentmail = mail($email, $subject, $message, $headers);
    }
    
    public function category_list()
    {
        $query = $this->db->query("SELECT * FROM `vendor_type` order by id DESC");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $vendor_name  = $row['vendor_name'];
                $vendor_id    = $row['id'];
                $resultpost[] = array(
                    "vendor_name" => $vendor_name,
                    "vendor_id" => $vendor_id
                );
            }
        } else {
            $resultpost = '';
        }
        return $resultpost;
    }
    
    function randomPassword()
    {
        $pass = rand(100000, 999999);
        return $pass;
    }
    
    public function signup($category, $name, $email, $city, $phone, $token, $agent)
    {
        if ($name != '' && $email != '' && $city != '' && $phone != '') {
            
            $vendor_id = $category;
            
            $query = $this->db->query("SELECT id from users WHERE phone='$phone' and vendor_id=13 ");
            $count = $query->num_rows();
            
            $query2 = $this->db->query("SELECT id from users WHERE email='$email'and vendor_id=13  ");
            $count2 = $query2->num_rows();
            
            
            if ($count > 0) {
                return array(
                    'status' => 208,
                    'message' => 'Phone number already exist'
                );
            } else if ($count2 > 0) {
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
                    'phone' => $phone,
                    'email' => $email,
                    'city' => $city,
                    'vendor_id' => $vendor_id,
                    'password' => $password,
                    'token' => $token,
                    'agent' => $agent,
                    'token_status' => '1',
                    'created_at' => $updated_at
                );
                
                
                
                $success = $this->db->insert('users', $user_data);
                $id      = $this->db->insert_id();
                if ($success) {
                    $date_array = array(
                        'listing_id' => $id,
                        'name' => $name,
                        'type' => $vendor_id,
                        'phone' => $phone,
                        'email' => $email,
                        'city' => $city
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
        } else {
            return array(
                'status' => 400,
                'message' => 'Please enter all fields'
            );
        }
    }
    
    public function sendotp($phone)
    {
        if ($phone == '8655369076') {
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
            'status' => 200,
            'message' => 'success',
            'otp_code' => (int) $otp_code
        );
    }
    
    
    
    /*public function login($phone, $token, $agent)
    {
        $query = $this->db->query("SELECT id,otp_code,name,email,phone,city,vendor_id FROM users WHERE phone='$phone' and vendor_id = '13'");
        $count = $query->num_rows();
        if ($count > 0) {
            $get_list        = $query->row_array();
            $id              = $get_list['id'];
            $name            = $get_list['name'];
            $phone           = $get_list['phone'];
            $email           = $get_list['email'];
            $city            = $get_list['city'];
            $otp            = $get_list['otp_code'];
            $vendor_id_multi = $get_list['vendor_id'];            
            $category_query = $this->db->select('vendor_name')->from('vendor_type')->where('vendor_type.id', $vendor_id_multi)->get()->row();
            $category       = $category_query->vendor_name;
            $img_count      = $this->db->select('profile_pic')->from('medical_stores')->where('user_id', $id)->get()->num_rows();
            if ($img_count > 0) {
                $media    = $this->db->select('profile_pic')->from('medical_stores')->where('user_id', $id)->get()->row();
                $img_file = $media->profile_pic;
                $image    = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
            } else {
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.png';
            }                        
            $vendor_id       = $get_list['vendor_id'];
            $accounts_list[] = array(
                "listing_id" => $id,
                "type" => $vendor_id_multi,
                "type_name" => $category,
                "username" => $name,
                "profile_pic" => $image
                
            );            
            $last_login = date('Y-m-d H:i:s', strtotime('+12 hours'));
            $this->db->where('id', $id)->update('users', array('last_login' => $last_login));
            
            $querys = $this->db->query("UPDATE `users` SET `token`='$token',`agent`='$agent',`token_status`='1' WHERE id='$id'");
			$userdata = array(                        
                'email' => $email,
                'id' => $id,
                'v_id' => $vendor_id_multi,
				'uimage' => $image,
                'uname' => $name,
                'loggedin' => TRUE
            );      

            
            $is_approval = '';
            $is_active   = '';
            
            if ($vendor_id == '5') {
                $query_doctor = $this->db->query("SELECT `is_approval`, `is_active` FROM `doctor_list` WHERE user_id='$id'");
                $count_doctor = $query_doctor->num_rows();
                if ($count_doctor > 0) {
                    $row         = $query_doctor->row_array();
                    $is_approval = $row['is_approval'];
                    $is_active   = $row['is_active'];
                }
            }
            
            elseif ($vendor_id == '13') {
                $query_pharmacy = $this->db->query("SELECT `is_approval`, `is_active` FROM `medical_stores` WHERE user_id='$id'");
                $count_pharmacy = $query_pharmacy->num_rows();
                if ($count_pharmacy > 0) {
                    $row         = $query_pharmacy->row_array();
                    $is_approval = $row['is_approval'];
                    $is_active   = $row['is_active'];
                }
            } elseif ($vendor_id == '25') {
                $query_personal_trainers = $this->db->query("SELECT `is_approval`, `is_active` FROM `personal_trainers` WHERE user_id='$id'");
                $count_personal_trainers = $query_personal_trainers->num_rows();
                if ($count_personal_trainers > 0) {
                    $row         = $query_personal_trainers->row_array();
                    $is_approval = $row['is_approval'];
                    $is_active   = $row['is_active'];
                }
            }
                $date_array = array(
                'listing_id' => $id,
                'name' => $name,
                'type' => $vendor_id,
                'phone' => $phone,
                'email' => $email,
                'city' => str_replace('null','',$city),
                'accounts_list' => $accounts_list,
                'is_approval' => $is_approval,
                'otp' => (int) $otp,
                'is_active' => $is_active
            );
            
            return array(
                'status' => 200,
                'message' => 'success',
                'data' => $date_array
            );
            
        } else {
            return array(
                'status' => '208',
                'message' => 'Username not found'
            );
        }
        
    }
    */
    
     public function login($phone, $token, $agent) 
    {
        $image = "";
        $query = $this->db->query("SELECT id,name,email,phone,city,vendor_id FROM users WHERE phone='$phone' and vendor_id ='13' and is_active=1");
        $count = $query->num_rows();
        if ($count > 0) {

            $get_list = $query->row();
            $vendor_id = $get_list->vendor_id;

            $q_user = $this->db->select('id,name,email,phone,city,vendor_id')->from('users')->where('phone', $phone)->where('vendor_id', $vendor_id)->get()->row();

            $id = $q_user->id;
            $name = $q_user->name;
            $phone = $q_user->phone;
            $email = $q_user->email;
            $city = $q_user->city;
            $vendor_id = $q_user->vendor_id;

            $multi_user = $this->db->query("SELECT id,name,email,phone,city,vendor_id,staff_hub_user_id FROM `users` WHERE  phone='$phone' AND vendor_id ='13'");


            foreach ($multi_user->result_array() as $row) {
                $id = $row['id'];
                $name = $row['name'];
                $phone = $row['phone'];
                $email = $row['email'];
                $city = $row['city'];
                $vendor_id_multi = $row['vendor_id'];
                $staff_hub_user_id = $row['staff_hub_user_id'];
                
                $category_query = $this->db->select('vendor_name')->from('vendor_type')->where('vendor_type.id', $vendor_id_multi)->get()->row();
                $category = $category_query->vendor_name;

                if ($vendor_id == '13' && $staff_hub_user_id != NULL) {
                    $img_count = $this->db->select('profile_pic')->from('medical_stores')->where('user_id', $staff_hub_user_id)->get()->num_rows();
                    if ($img_count > 0) {
                        $media = $this->db->select('profile_pic')->from('medical_stores')->where('user_id', $staff_hub_user_id)->get()->row();
                        $img_file = $media->profile_pic;
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                    } else {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.png';
                    }
                }
                else if($vendor_id == '13')
                {
                      $img_count = $this->db->select('profile_pic')->from('medical_stores')->where('user_id', $id)->get()->num_rows();
                    if ($img_count > 0) {
                        $media = $this->db->select('profile_pic')->from('medical_stores')->where('user_id', $id)->get()->row();
                        $img_file = $media->profile_pic;
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                    } else {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.png';
                    }
                }

                $vendor_id = $row['vendor_id'];
                $accounts_list[] = array(
                    "listing_id" => $id,
                    "type" => $vendor_id_multi,
                    "type_name" => $category,
                    "username" => $name,
                    "profile_pic" => $image
                );
            }




            $last_login = date('Y-m-d H:i:s', strtotime('+12 hours'));
            $this->db->where('id', $id)->update('users', array(
                'last_login' => $last_login
            ));

            $querys = $this->db->query("UPDATE `users` SET `token`='$token',`agent`='$agent',`token_status`='1' WHERE id='$id'");



            $is_approval = '';
            $is_active = '';

            if ($vendor_id == '5') {
                $query_doctor = $this->db->query("SELECT `is_approval`, `is_active` FROM `doctor_list` WHERE user_id='$id'");
                $count_doctor = $query_doctor->num_rows();
                if ($count_doctor > 0) {
                    $row = $query_doctor->row_array();
                    $is_approval = $row['is_approval'];
                    $is_active = $row['is_active'];
                }
            } elseif ($vendor_id == '13' && $staff_hub_user_id != NULL) {
                $query_pharmacy = $this->db->query("SELECT `is_approval`, `is_active` FROM `medical_stores` WHERE user_id='$staff_hub_user_id'");
                $count_pharmacy = $query_pharmacy->num_rows();
                if ($count_pharmacy > 0) {
                    $row = $query_pharmacy->row_array();
                    $is_approval = $row['is_approval'];
                    $is_active = $row['is_active'];
                }
            } elseif ($vendor_id == '13') {
                $query_pharmacy = $this->db->query("SELECT `is_approval`, `is_active` FROM `medical_stores` WHERE user_id='$id'");
                $count_pharmacy = $query_pharmacy->num_rows();
                if ($count_pharmacy > 0) {
                    $row = $query_pharmacy->row_array();
                    $is_approval = $row['is_approval'];
                    $is_active = $row['is_active'];
                }
            } elseif ($vendor_id == '25') {
                $query_personal_trainers = $this->db->query("SELECT `is_approval`, `is_active` FROM `personal_trainers` WHERE user_id='$id'");
                $count_personal_trainers = $query_personal_trainers->num_rows();
                if ($count_personal_trainers > 0) {
                    $row = $query_personal_trainers->row_array();
                    $is_approval = $row['is_approval'];
                    $is_active = $row['is_active'];
                }
            }

            $staff_user_id = $id;
            if($staff_hub_user_id != NULL)
            {
                $id = $staff_hub_user_id;
            }

            $date_array = array(
                'listing_id' => $id,
                'staff_user_id' => $staff_user_id,
                'name' => $name,
                'type' => $vendor_id,
                'phone' => $phone,
                'email' => $email,
                'city' => $city,
                'profile_pic' => $image,
                'accounts_list' => $accounts_list,
                'is_approval' => $is_approval,
                'is_active' => $is_active
            );

            return array(
                'status' => 200,
                'message' => 'success',
                'data' => $date_array
            );
        } else {
            return array(
                'status' => '208',
                'message' => 'Username not found'
            );
        }
    }
    public function update_registration_token($listing_id, $token)
    {
        
        $query = $this->db->query("SELECT id,name,email,phone,city,vendor_id FROM users WHERE id='$listing_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            
            $q_user = $this->db->select('id,name,email,phone,city,vendor_id')->from('users')->where('id', $listing_id)->get()->row();
            
            $id        = $q_user->id;
            $name      = $q_user->name;
            $phone     = $q_user->phone;
            $email     = $q_user->email;
            $city      = $q_user->city;
            $vendor_id = $q_user->vendor_id;
            
            $last_login = date('Y-m-d H:i:s', strtotime('+12 hours'));
            $this->db->where('id', $listing_id)->update('users', array(
                'last_login' => $last_login
            ));
            
            $querys = $this->db->query("UPDATE `users` SET `token`='$token',`token_status`='1' WHERE id='$listing_id'");
            
            
            $date_array = array(
                'listing_id' => $id,
                'name' => $name,
                'type' => $vendor_id,
                'phone' => $phone,
                'email' => $email,
                'city' => $city
                
            );
            
            return array(
                'status' => 200,
                'message' => 'success',
                'data' => $date_array
            );
            
        } else {
            return array(
                'status' => '208',
                'message' => 'Username not found'
            );
        }
    }
    
    
   /* public function order_list($listing_id, $listing_type, $order_type)
    {
        $query = $this->db->query("select user_id,order_id,order_type,action_by,delivery_time,cancel_reason,action_by,listing_id,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where listing_id='$listing_id' and listing_type='$listing_type' group by invoice_no order by order_id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $order_id            = $row['order_id'];
                $delivery_time       = $row['delivery_time'];
                $order_type          = $row['order_type'];
                $listing_id          = $row['listing_id'];
                $listing_name        = $row['listing_name'];
                $listing_type        = $row['listing_type'];
                $invoice_no          = $row['invoice_no'];
                $chat_id             = $row['chat_id'];
                $address_id          = $row['address_id'];
                $order_date          = $row['order_date'];
                $order_date          = date('j M Y h:i A', strtotime($order_date));
                $name                = $row['name'];
                $mobile              = $row['mobile'];
                $pincode             = $row['pincode'];
                $address1            = $row['address1'];
                $address2            = $row['address2'];
                $landmark            = $row['landmark'];
                $city                = $row['city'];
                $state               = $row['state'];
                $payment_method      = $row['payment_method'];
                $delivery_charge     = $row['delivery_charge'];
                $product_resultpost  = array();
                $prescription_result = array();
                $order_total         = '0';
                $order_status        = $row['order_status'];
                $order_type          = $row['order_type'];
                $action_by           = $row['action_by'];
                if ($action_by == 'customer') {
                    $cancel_reason = $row['cancel_reason'];
                } else {
                    $cancel_reason = '';
                }
                
                $user_id_     = $row['user_id'];
                $user_info    = $this->db->query("SELECT name,phone FROM users WHERE id='$user_id_'");
                $getuser_info = $user_info->row_array();
                $user_name    = $getuser_info['name'];
                $user_mobile  = $getuser_info['phone'];
                
                if ($order_type == 'order') {
                    $product_query = $this->db->query("select id as product_order_id,product_id,product_discount,product_name,product_unit,product_unit_value,product_img,product_quantity,product_price,sub_total,product_status,product_status_type,product_status_value,order_status from user_order_product where order_id='$order_id' order by product_order_id asc");
                    $product_count = $product_query->num_rows();
                    if ($product_count > 0) {
                        foreach ($product_query->result_array() as $product_row) {
                            $product_order_id     = $product_row['product_order_id'];
                            $product_id           = $product_row['product_id'];
                            $product_name         = $product_row['product_name'];
                            $product_img          = $product_row['product_img'];
                            $product_price        = $product_row['product_price'];
                            $product_unit         = $product_row['product_unit'];
                            $product_unit_value   = $product_row['product_unit_value'];
                            $product_quantity     = $product_row['product_quantity'];
                            $product_discount     = $product_row['product_discount'];
                            $sub_total            = $product_row['sub_total'];
                            $product_status       = $product_row['product_status'];
                            $product_status_type  = $product_row['product_status_type'];
                            $product_status_value = $product_row['product_status_value'];
                            $product_order_status = $product_row['order_status'];
                            
                            $order_total = $order_total + ($product_price * $product_quantity);
                            
                            $product_resultpost[] = array(
                                "product_order_id" => $product_order_id,
                                "product_id" => $product_id,
                                "product_name" => $product_name,
                                "product_img" => $product_img,
                                "product_quantity" => $product_quantity,
                                "product_price" => $product_price,
                                "product_unit" => $product_unit,
                                "product_unit_value" => $product_unit_value,
                                "product_discount" => $product_discount,
                                "sub_total" => $sub_total,
                                "product_status" => $product_status,
                                "product_status_type" => $product_status_type,
                                "product_status_value" => $product_status_value,
                                "product_order_status" => $product_order_status
                            );
                        }
                    }
                } else {
                    $product_query = $this->db->query("SELECT id as product_order_id, order_status,prescription_image,prescription_image2,prescription_image3,prescription_image4 FROM prescription_order_details WHERE order_id='$order_id' order by product_order_id asc");
                    $product_count = $product_query->num_rows();
                    if ($product_count > 0) {
                        foreach ($product_query->result_array() as $product_row) {
                            $product_order_id     = $product_row['product_order_id'];
                            $product_id           = $product_row['product_order_id'];
                            $product_name         = '';
                            $product_img          = '';
                            $product_img          = $product_row['prescription_image'];
                            $product_quantity     = '';
                            $product_price        = '';
                            $sub_total            = '';
                            $product_status       = '';
                            $product_status_type  = '';
                            $product_status_value = '';
                            $product_order_status = $product_row['order_status'];
                            
                            $product_resultpost[] = array(
                                "product_order_id" => $product_order_id,
                                "product_id" => $product_id,
                                "product_name" => $product_name,
                                "product_img" => "https://d2c8oti4is0ms3.cloudfront.net/images/prescription_images/" . $product_img,
                                "product_quantity" => $product_quantity,
                                "product_price" => $product_price,
                                "product_unit" => '',
                                "product_unit_value" => '',
                                "product_discount" => '0',
                                "sub_total" => $sub_total,
                                "product_status" => $product_status,
                                "product_status_type" => $product_status_type,
                                "product_status_value" => $product_status_value,
                                "product_order_status" => $product_order_status
                            );
                        }
                        
                        
                        $prescription_query = $this->db->query("SELECT * FROM `prescription_order_list` where order_id='$order_id' order by id desc");
                        $prescription_count = $prescription_query->num_rows();
                        if ($prescription_count > 0) {
                            foreach ($prescription_query->result_array() as $prescription_row) {
                                
                                $prescription_name     = $prescription_row['prescription_name'];
                                $prescription_quantity = $prescription_row['prescription_quantity'];
                                $prescription_price    = $prescription_row['prescription_price'];
                                $prescription_discount = $prescription_row['prescription_discount'];
                                $prescription_status   = $prescription_row['prescription_status'];
                                
                                $prescription_result[] = array(
                                    "prescription_name" => $prescription_name,
                                    "prescription_quantity" => $prescription_quantity,
                                    "prescription_price" => $prescription_price,
                                    "prescription_discount" => $prescription_discount,
                                    "prescription_status" => $prescription_status
                                );
                                
                            }
                        }
                        
                    }
                }
                
                $resultpost[] = array(
                    "order_id" => $order_id,
                    "delivery_time" => $delivery_time,
                    "order_type" => $order_type,
                    "listing_id" => $listing_id,
                    "listing_name" => $listing_name,
                    "listing_type" => $listing_type,
                    "invoice_no" => $invoice_no,
                    "chat_id" => $chat_id,
                    "address_id" => $address_id,
                    "name" => $name,
                    "mobile" => $mobile,
                    "pincode" => $pincode,
                    "address1" => $address1,
                    "address2" => $address2,
                    "landmark" => $landmark,
                    "city" => $city,
                    "state" => $state,
                    "user_name" => $user_name,
                    "user_mobile" => $user_mobile,
                    "order_total" => $order_total,
                    "payment_method" => $payment_method,
                    "order_date" => $order_date,
                    "order_status" => $order_status,
                    "cancel_reason" => $cancel_reason,
                    "delivery_charge" => $delivery_charge,
                    "product_order" => $product_resultpost,
                    "prescription_order" => $prescription_result
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
        
    }*/
    public function order_list($listing_id, $listing_type, $order_type)
    {
       /* echo "select user_id,order_id,order_type,action_by,delivery_time,cancel_reason,action_by,listing_id,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where FIND_IN_SET('" . $listing_id . "',listing_id) and listing_type='$listing_type' group by invoice_no order by order_id desc";*/
        $query = $this->db->query("select user_id,order_id,order_type,action_by,delivery_time,cancel_reason,action_by,listing_id,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where FIND_IN_SET('" . $listing_id . "',listing_id) and listing_type='$listing_type' group by invoice_no order by order_id desc");
       
        $count = $query->num_rows();
      
      if ($count > 0) {
            foreach ($query->result_array() as $row) {
                
                $prescription_resultpost=array();
                $product_resultpost1  = array();
                $product_resultpost2  = array();
                
                $order_id            = $row['order_id'];
                $delivery_time       = $row['delivery_time'];
                $order_type          = $row['order_type'];
                $listing_id          = $row['listing_id'];
                $listing_name        = $row['listing_name'];
                $listing_type        = $row['listing_type'];
                $invoice_no          = $row['invoice_no'];
                $chat_id             = $row['chat_id'];
                $address_id          = $row['address_id'];
                $order_date          = $row['order_date'];
                $order_date          = date('j M Y h:i A', strtotime($order_date));
                $name                = $row['name'];
                $mobile              = $row['mobile'];
                $pincode             = $row['pincode'];
                $address1            = $row['address1'];
                $address2            = $row['address2'];
                $landmark            = $row['landmark'];
                $city                = $row['city'];
                $state               = $row['state'];
                $payment_method      = $row['payment_method'];
                $delivery_charge     = $row['delivery_charge'];
                $product_resultpost  = array();
                $prescription_result = array();
                $order_total         = 0;
                $order_status        = $row['order_status'];
                $order_type          = $row['order_type'];
                $action_by           = $row['action_by'];
                if ($action_by == 'customer') {
                    $cancel_reason = $row['cancel_reason'];
                } else {
                    $cancel_reason = '';
                }
                
                $user_id_     = $row['user_id'];
                $user_info    = $this->db->query("SELECT name,phone FROM users WHERE id='$user_id_'");
                $getuser_info = $user_info->row_array();
                $user_name    = $getuser_info['name'];
                $user_mobile  = $getuser_info['phone'];
                
                $query1 = $this->db->query("select user_id,order_id,order_type,action_by,delivery_time,cancel_reason,action_by,listing_id,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where invoice_no='$invoice_no'");
        $count1 = $query1->num_rows();
      
      if ($count1 > 0) {
            foreach ($query1->result_array() as $row1) {
                    $order_id1            = $row1['order_id'];
                    $product_query = $this->db->query("select id as product_order_id,product_id,product_discount,product_name,product_unit,product_unit_value,product_img,product_quantity,product_price,sub_total,product_status,product_status_type,product_status_value,order_status from user_order_product where order_id='$order_id1' order by product_order_id asc");
                    $product_count = $product_query->num_rows();
                    if ($product_count > 0) {
                        foreach ($product_query->result_array() as $product_row) {
                            
                            $product_order_id     = $product_row['product_order_id'];
                            $product_id           = $product_row['product_id'];
                            $product_name         = $product_row['product_name'];
                            $product_img          = $product_row['product_img'];
                            $product_price        = $product_row['product_price'];
                            $product_unit         = $product_row['product_unit'];
                            $product_unit_value   = $product_row['product_unit_value'];
                            $product_quantity     = $product_row['product_quantity'];
                            $product_discount     = $product_row['product_discount'];
                            $sub_total            = $product_row['sub_total'];
                            $product_status       = $product_row['product_status'];
                            $product_status_type  = $product_row['product_status_type'];
                            $product_status_value = $product_row['product_status_value'];
                            $product_order_status = $product_row['order_status'];
                            
                            $final_order_total = $order_total + ($product_price * $product_quantity);
                            
                                $order_total =$final_order_total-$product_discount;
                            $product_resultpost[] = array(
                                "order_id" => $order_id1,
                                "product_order_id" => $product_order_id,
                                "product_id" => $product_id,
                                "product_name" => $product_name,
                                "product_img" => $product_img,
                                "product_quantity" => $product_quantity,
                                "product_price" => $product_price,
                                "product_unit" => $product_unit,
                                "product_unit_value" => $product_unit_value,
                                "product_discount" => $product_discount,
                                "sub_total" => $sub_total,
                                "product_status" => $product_status,
                                "product_status_type" => $product_status_type,
                                "product_status_value" => $product_status_value,
                                "product_order_status" => $product_order_status
                            );
                        }
                    }
              
                    $product_query1 = $this->db->query("SELECT id as product_order_id, order_status,prescription_image,description FROM prescription_order_details WHERE order_id='$order_id1' order by product_order_id asc");
                    $product_count1 = $product_query1->num_rows();
                    if ($product_count1 > 0) {
                        foreach ($product_query1->result_array() as $product_row1) {
                            $product_order_id1     = $product_row1['product_order_id'];
                            $product_id1           = $product_row1['product_order_id'];
                            $product_name1         = '';
                            $product_img1          = '';
                            $product_img1          = $product_row1['prescription_image'];
                            $product_quantity1     = '';
                            $product_price1        = '';
                            $sub_total1            = '';
                            $product_status1       = '';
                            $product_status_type1  = '';
                            $product_status_value1 = '';
                            $product_order_status1 = $product_row1['order_status'];
                            $product_description = $product_row1['description'];
                              if (strpos($product_img1, '/') == true) {
                                $images_1 = "https://d2c8oti4is0ms3.cloudfront.net/" . $product_img1;
                           }
                           else
                           {
                               $images_1= "https://d2c8oti4is0ms3.cloudfront.net/images/prescription_images/" . $product_img1;
                           }
                           if(empty($product_description) || $product_description=="")
                           {
                               $product_description="";
                           }
                           else
                           {
                               $product_description;
                           }
                            $prescription_resultpost[] = array(
                                "order_id" => $order_id1,
                                "product_order_id" => $product_order_id1,
                                "product_id" => $product_id1,
                                "product_name" => $product_name1,
                                "product_img" => $images_1,
                                "product_quantity" => $product_quantity1,
                                "product_price" => $product_price1,
                                "product_unit" => '',
                                "product_unit_value" => '',
                                "product_discount" => '0',
                                "sub_total" => $sub_total1,
                                "product_status" => $product_status1,
                                "product_status_type" => $product_status_type1,
                                "product_status_value" => $product_status_value1,
                                "product_order_status" => $product_order_status1,
                                "product_description" => $product_description
                            );
                        }
                        
                        
                        $prescription_query = $this->db->query("SELECT * FROM `prescription_order_list` where order_id='$order_id' order by id desc");
                        $prescription_count = $prescription_query->num_rows();
                        if ($prescription_count > 0) {
                            foreach ($prescription_query->result_array() as $prescription_row) {
                                
                                $prescription_name     = $prescription_row['prescription_name'];
                                $prescription_quantity = $prescription_row['prescription_quantity'];
                                $prescription_price    = $prescription_row['prescription_price'];
                                $prescription_discount = $prescription_row['prescription_discount'];
                                $prescription_status   = $prescription_row['prescription_status'];
                                $final_order_total = $order_total + ($prescription_quantity * $prescription_price); 
                                $order_total =$final_order_total-$prescription_discount;
                                $prescription_result[] = array(
                                    "prescription_name" => $prescription_name,
                                    "prescription_quantity" => $prescription_quantity,
                                    "prescription_price" => $prescription_price,
                                    "prescription_discount" => $prescription_discount,
                                    "prescription_status" => $prescription_status
                                );
                                
                            }
                        }
                        
                    }
                
                   
            }
      }
    /*  print_r($prescription_resultpost2);
           if(!empty($product_resultpost1) && !empty($prescription_resultpost2))
                    {
                        $product_resultpost = array_merge($product_resultpost1,$product_resultpost2);
                       
                    }   
                    die;*/
                    if(empty($delivery_time))
                    {
                        $delivery_time="";
                    }
                    else
                    {
                        $delivery_time;
                    }
                  
                $resultpost[] = array(
                    
                    "delivery_time" => $delivery_time,
                    "order_type" => $order_type,
                    "listing_id" => $listing_id,
                    "listing_name" => $listing_name,
                    "listing_type" => $listing_type,
                    "invoice_no" => $invoice_no,
                    "chat_id" => $chat_id,
                    "address_id" => $address_id,
                    "name" => $name,
                    "mobile" => $mobile,
                    "pincode" => $pincode,
                    "address1" => $address1,
                    "address2" => $address2,
                    "landmark" => $landmark,
                    "city" => $city,
                    "state" => $state,
                    "user_name" => $user_name,
                    "user_mobile" => $user_mobile,
                    "order_total" => $order_total,
                    "payment_method" => $payment_method,
                    "order_date" => $order_date,
                    "order_status" => $order_status,
                    "cancel_reason" => $cancel_reason,
                    "delivery_charge" => $delivery_charge,
                    "product_order" => $product_resultpost,
                    "prescription_create" => $prescription_resultpost,
                    "prescription_order" => $prescription_result
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
        
    }
    
    public function order_status_common($order_id_data, $prescription_id_data,$order_details,$prescription_details,$user_id1)
    {
        $this->load->model('OrderModel');
        date_default_timezone_set('Asia/Kolkata');
        $updated_at       = date('Y-m-d H:i:s');
        $sub_total        = '0';
        $product_price    = '0';
        $product_quantity = '0';
        $action_by        = 'vendor';
        
      
       
        $product_details_new = json_decode($order_details,TRUE);
        $order_id      = $product_details_new['order_id'];
        $delivery_time = $product_details_new['delivery_time'];
        $order_status  = $product_details_new['order_status'];
        $listing_id    = $product_details_new['listing_id'];
        $listing_type  = $product_details_new['listing_type'];
        $order_product_data    = $product_details_new['product_order'];
                   
        
        
        
        foreach ($order_product_data as $result) {
            $product_order_id     = $result['product_order_id'];
            $product_quantity     = $result['product_quantity'];
            $product_price        = $result['product_price'];
            $product_unit         = $result['product_unit'];
            $product_unit_value   = $result['product_unit_value'];
            $product_discount     = $result['product_discount'];
            $product_order_status = $result['product_order_status'];
            $sub_total            = $sub_total + ($product_price * $product_quantity);
           
            $query = $this->db->query("UPDATE user_order_product SET product_quantity='$product_quantity',product_price='$product_price',product_discount='$product_discount',sub_total='$sub_total',order_status='$product_order_status',updated_at='$updated_at',product_unit='$product_unit',product_unit_value='$product_unit_value' where product_id='$product_order_id' and order_id='$order_id_data'");
            
            $sub_total = '0';
        }
        $query = $this->db->query("UPDATE user_order SET order_status='$order_status',delivery_time='$delivery_time',listing_id='$user_id1' where order_id='$order_id_data'");

        $invNoArray = $this->db->query("select `invoice_no` from `user_order` WHERE order_id = '$order_id_data'")->row_array();
        $invno = $invNoArray['invoice_no'];
        $action_by_status = "Pharmacy";
        $orderStatus = "Order confirmed";
        $this->OrderModel->update_status($invno,$orderStatus, $action_by_status);
                
        
        
        
        $prescription_details_new = json_decode($prescription_details,TRUE);
        $order_id           = $prescription_details_new['order_id'];
        $order_status       = $prescription_details_new['order_status'];
        $delivery_time      = $prescription_details_new['delivery_time'];
        $prescription_order = $prescription_details_new['prescription_order'];
             
         foreach ($prescription_order as $result) {
            $prescription_name     = $result['prescription_name'];
            $prescription_quantity = $result['prescription_quantity'];
            $prescription_price    = $result['prescription_price'];
            $prescription_discount = $result['prescription_discount'];
            $prescription_status   = $result['prescription_status'];
            
            $query = $this->db->query("INSERT INTO `prescription_order_list`(`order_id`, `prescription_name`, `prescription_quantity`, `prescription_price`, `prescription_discount`, `prescription_status`) VALUES ('$prescription_id_data', '$prescription_name', '$prescription_quantity', '$prescription_price', '$prescription_discount', '$prescription_status')");
            
        }
        $query = $this->db->query("UPDATE user_order SET order_status='$order_status',delivery_time='$delivery_time',listing_id='$user_id1' where order_id='$prescription_id_data'");
        
        $invNoArray = $this->db->query("select `invoice_no` from `user_order` WHERE order_id = '$prescription_id_data'")->row_array();
        $invno = $invNoArray['invoice_no'];
        $action_by_status = "Pharmacy";
        $orderStatus = "Order confirmed";
        $order_status = $this->OrderModel->update_status( $invno,$orderStatus, $action_by_status);
        
        
        function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id_data, $invoice_no, $name, $listing_name, $agent,$notification_type)
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
                    "notification_type" => $notification_type,
                    "order_status" => $order_status,
                    "order_date" => $updated_at,
                    "order_id" => $order_id_data,
                    "invoice_no" => $invoice_no,
                    "name" => $name,
                    "listing_name" => $listing_name
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
        
        $order_info1    = $this->db->query("SELECT user_id,listing_id,listing_name,delivery_time,invoice_no,name,listing_name,order_total,order_date from user_order where order_id='$order_id_data' or order_id='$prescription_id_data'");
       // $order_info1 = $this->db->query($order_info11);
        $product_count12 = $order_info1->num_rows();
        
       
        if ($product_count12 > 0 ) {
               
            $order_info= $order_info1->row();
            $user_id       = $order_info->user_id;
            $listing_id    = $order_info->listing_id;
            $listing_name  = $order_info->listing_name;
            $delivery_time = $order_info->delivery_time;
            $invoice_no    = $order_info->invoice_no;
            $name          = $order_info->name;
            $listing_name  = $order_info->listing_name;
            $order_total   = $order_info->order_total;
            $order_date     = $order_info->order_date;
            $medical_name = $this->db->select('medical_name')->from('medical_stores')->where('user_id', $listing_id)->get()->row();
            $customer_token = $this->db->select('token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
                if(!empty($customer_token)){
                    $token_status   = $customer_token->token_status;
                } else {
                    $token_status   = "0";
                }
                if ($token_status > 0) {
                    $reg_id    = $customer_token->token;
                    $agent     = $customer_token->agent;
                    $img_url   = 'https://medicalwale.com/img/noti_icon.png';
                    $tag       = 'text';
                    $key_count = '1';
                     if ($order_status == 'Order Confirmed') {
                        $title = 'Order Confirmed';
                        $msg   = 'Confirmed: Order no.'.$invoice_no.' is successfully placed with '.$listing_name.'. Your order will be delivered in '.$delivery_time.'. Thank You.';
                        $notification_type="pharmacy_order_confirm";
                         
                     }
                    if ($order_status == 'Awaiting Delivery Confirmation' || $order_status=='Awaiting Customer Confirmation') {
                        $title = 'Order Reply From ' . $medical_name->medical_name;
                        $msg   = 'Your have received a reply on your order';
                        $notification_type="pharmacy_order_confirm";
                    }
                    if ($order_status == 'Order Delivered') {
                        $title = 'Order Delivered';
                        $msg   = 'Delivered: Order no.'.$invoice_no.' of Rs.'.$order_total.' is delivered. We hope your experience with Medicalwale.com has been satisfactory, for feedback and queries contact us on Medicalwale.com.';
                        $notification_type="pharmacy_order_deliver";
                        
                    }
            
                    $notification_array = array(
                      'title' => $title,
                     'msg'  => $msg,
                       'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => $order_status,
                      'order_date' => $order_date,
                       'order_id'   => $invoice_no,
                      'post_id'  => "",
                     'listing_id'  =>  "",
                      'booking_id'  => "",
                     'invoice_no' => $invoice_no,
                     'user_id'  => $user_id,
                      'notification_type'  => $notification_type,
                      'notification_date'  => date('Y-m-d H:i:s')
                       
            );
            $this->db->insert('All_notification_Mobile', $notification_array);
            
            send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $invoice_no, $name, $listing_name, $agent,$notification_type);
            
        }
        /*$data=array('order_status' => $order_status);
        */
        
        // if order is by night owls then send this notification to mno
        $mno_details = $this->db->query("SELECT u.token, u.agent, mo.mno_id ,uo.* FROM `user_order` as uo left join mno_orders as mo on (uo.`invoice_no` = mo.invoice_no) left join users as u on (mo.mno_id = u.id) WHERE uo.`order_id` = '$order_id_data' and uo.`listing_type` = 44 AND mo.ongoing = 1 AND mo.status = 'accepted'")->row_array();
        if(sizeof($mno_details) > 0){
            $this->load->model('PartnermnoModel');
            $reg_id = $mno_details['token'];
            $agent = $mno_details['agent'];
            $invoice_no = $mno_details['invoice_no'];
            $msg = 'Order #'.$invoice_no.' received by pharmacy and gave price to customer';
            $img_url = 'https://medicalwale.com/img/noti_pharmacy.png';
            $tag = 'text';
            $title = 'Order received by pharmacy and gave price to customer';
            $order_status = 'Awaiting Customer Confirmation';
            $order_date = '';
            $name = '';
            $notification_type = 2;
            
            $this->PartnermnoModel->send_gcm_notify_mno($title, $reg_id, $msg, $img_url, $tag, $agent, $invoice_no,$notification_type);
        }
        return array(
            'status' => 200,
            'message' => 'success',
            'order_status' => $order_status
        );
    }
                    return array(
            'status' => 201,
            'message' => 'fail',
            'order_status' => ""
        );
        
    }
    
    public function order_status($order_id, $delivery_time, $order_status, $listing_id, $listing_type, $order_data_list,$user_id1)
    {
        $this->load->model('OrderModel');
        date_default_timezone_set('Asia/Kolkata');
        $updated_at       = date('Y-m-d H:i:s');
        $sub_total        = '0';
        $product_price    = '0';
        $product_quantity = '0';
        $action_by        = 'vendor';
        foreach ($order_data_list as $result) {
            $product_order_id     = $result['product_order_id'];
            $product_quantity     = $result['product_quantity'];
            $product_price        = $result['product_price'];
            $product_unit         = $result['product_unit'];
            $product_unit_value   = $result['product_unit_value'];
            $product_discount     = $result['product_discount'];
            $product_order_status = $result['product_order_status'];
            $sub_total            = $sub_total + ($product_price * $product_quantity);
            
            $query = $this->db->query("UPDATE user_order_product SET product_quantity='$product_quantity',product_price='$product_price',product_discount='$product_discount',sub_total='$sub_total',order_status='$product_order_status',updated_at='$updated_at',product_unit='$product_unit',product_unit_value='$product_unit_value' where product_id='$product_order_id' and order_id='$order_id_data'");
            
            $sub_total = '0';
        }
        $query = $this->db->query("UPDATE user_order SET order_status='$order_status',delivery_time='$delivery_time',listing_id='$user_id1' where order_id='$order_id'");
        
        $invNoArray = $this->db->query("select `invoice_no` from `user_order` WHERE order_id = '$order_id'")->row_array();
        $invno = $invNoArray['invoice_no'];
        $action_by_status = "Pharmacy";
        $orderStatus = "Order confirmed";
        $this->OrderModel->update_status( $invno,$orderStatus, $action_by_status);

        
        function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $invoice_no, $name, $listing_name, $agent,$notification_type)
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
                    "notification_type" =>  $notification_type,
                    "order_status" => $order_status,
                    "order_date" => $updated_at,
                    "order_id" => $order_id,
                    "invoice_no" => $invoice_no,
                    "name" => $name,
                    "listing_name" => $listing_name
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
        
        $order_info    = $this->db->select('user_id,listing_id,listing_name,delivery_time,invoice_no,name,listing_name,order_total,order_date')->from('user_order')->where('order_id', $order_id)->get()->row();
        $user_id       = $order_info->user_id;
        $listing_id    = $order_info->listing_id;
        $listing_name  = $order_info->listing_name;
        $delivery_time = $order_info->delivery_time;
        $invoice_no    = $order_info->invoice_no;
        $name          = $order_info->name;
        $listing_name  = $order_info->listing_name;
        $order_total   = $order_info->order_total;
        $order_date     = $order_info->order_date;
        $medical_name_new = $this->db->select('medical_name')->from('medical_stores')->where('user_id', $listing_id)->get()->row();
        $customer_token = $this->db->select('token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
         if(!empty($customer_token))
        {
            $token_status   = $customer_token->token_status;
        }
        else
        {
            $token_status   = "0";
        }
        if ($token_status > 0) {
            $reg_id    = $customer_token->token;
            $agent     = $customer_token->agent;
            $img_url   = 'https://medicalwale.com/img/noti_icon.png';
            $tag       = 'text';
            $key_count = '1';
            if ($order_status == 'Order Confirmed') {
                $title = 'Order Confirmed';
                $msg   = 'Confirmed: Order no.'.$invoice_no.' is successfully placed with '.$medical_name_new->medical_name.'. Your order will be delivered in '.$delivery_time.'. Thank You.';
                $notification_type="pharmacy_order_confirm";
                
            }
            if ($order_status == 'Awaiting Delivery Confirmation' ||  $order_status == 'Awaiting Customer Confirmation') {
                $title = 'Order Reply From ' .$medical_name_new->medical_name;
                $msg   = 'Your have received a reply on your order';
                $notification_type="pharmacy_order_confirm";
            }
            if ($order_status == 'Order Delivered') {
                $title = 'Order Delivered';
                $msg   = 'Delivered: Order no.'.$invoice_no.' . We hope your experience with Medicalwale.com has been satisfactory, for feedback and queries contact us on Medicalwale.com.';
                $notification_type="pharmacy_order_deliver";
                
            }
               $notification_array = array(
                      'title' => $title,
                     'msg'  => $msg,
                       'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => $order_status,
                      'order_date' => $order_date,
                       'order_id'   => $invoice_no,
                      'post_id'  => "",
                     'listing_id'  =>  "",
                      'booking_id'  => "",
                     'invoice_no' => $invoice_no,
                     'user_id'  => $user_id,
                      'notification_type'  => $notification_type,
                      'notification_date'  => date('Y-m-d H:i:s')
                       
             );
          $this->db->insert('All_notification_Mobile', $notification_array);
            
            send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $invoice_no, $name, $listing_name, $agent,$notification_type);
        }
        
        return array(
            'status' => 200,
            'message' => 'success',
            'order_status' => $order_status
        );
        
    }
    
    public function order_deliver_cancel($order_id, $cancel_reason, $type, $notification_type)
    {
        $this->load->model('OrderModel');
        date_default_timezone_set('Asia/Kolkata');
        $updated_at = date('Y-m-d H:i:s');
        if ($type == 'Order Cancelled') {
            $query = $this->db->query("UPDATE `user_order` SET `updated_at`='$updated_at',`order_status`='Order Cancelled',`cancel_reason`='$cancel_reason',`action_by`='vendor' WHERE invoice_no='$order_id'");
            
            $action_by_status = "Pharmacy";
        $orderStatus = $type;
        $this->OrderModel->update_status( $order_id,$orderStatus, $action_by_status);
         
            
            
        }
        if ($type == 'Order Delivered') {
            $query = $this->db->query("UPDATE `user_order` SET `updated_at`='$updated_at',`order_status`='Order Delivered',`cancel_reason`='',`action_by`='vendor' WHERE invoice_no='$order_id'");
            
            $action_by_status = "Pharmacy";
        $orderStatus = $type;
        $this->OrderModel->update_status( $order_id,$orderStatus, $action_by_status);
         
            
            
        }
        
        function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $notification_type, $order_status, $updated_at, $order_id, $invoice_no, $name, $listing_name, $agent)
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
                    "notification_type" => $notification_type,
                    "order_status" => $order_status,
                    "order_date" => $updated_at,
                    "order_id" => $order_id,
                    "invoice_no" => $invoice_no,
                    "name" => $name,
                    "listing_name" => $listing_name
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
        
        $order_info    = $this->db->select('user_id,listing_id,listing_name,delivery_time,invoice_no,name,listing_name,sum(order_total) as total,order_date')->from('user_order')->where('invoice_no', $order_id)->get()->row();
        $user_id       = $order_info->user_id;
        $listing_id    = $order_info->listing_id;
        $listing_name  = $order_info->listing_name;
        $delivery_time = $order_info->delivery_time;
        $invoice_no    = $order_info->invoice_no;
        $name          = $order_info->name;
        $listing_name  = $order_info->listing_name;
        $order_total   = $order_info->total;
        $order_date     = $order_info->order_date;
        $customer_token = $this->db->select('token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
         if(!empty($customer_token))
        {
            $token_status   = $customer_token->token_status;
        }
        else
        {
            $token_status   = "0";
        }
        $order_status   = $type;
        if ($token_status > 0) {
            $reg_id    = $customer_token->token;
            $agent     = $customer_token->agent;
            $img_url   = 'https://medicalwale.com/img/noti_icon.png';
            $tag       = 'text';
            $key_count = '1';
            if ($type == 'Order Cancelled') {
                $title = 'Order Cancelled';
                $msg   = 'Cancelled: Order no.'.$invoice_no.'  is cancelled. For any queries contact us on Medicalwale.com';
                $notification_type="pharmacy_order_cancel";
            }
            if ($type == 'Order Delivered') {
                $title = 'Order Delivered';
                $msg   = 'Delivered: Order no.'.$invoice_no.'  is delivered. We hope your experience with Medicalwale.com has been satisfactory, for feedback and queries contact us on Medicalwale.com.';
                $notification_type="pharmacy_order_deliver";
                
            }
            
               $notification_array = array(
                      'title' => $title,
                     'msg'  => $msg,
                       'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => $order_status,
                      'order_date' => $order_date,
                       'order_id'   => $invoice_no,
                      'post_id'  => "",
                     'listing_id'  =>  "",
                      'booking_id'  => "",
                     'invoice_no' => $invoice_no,
                     'user_id'  => $user_id,
                      'notification_type'  => $notification_type,
                      'notification_date'  => date('Y-m-d H:i:s')
                       
             );
          $this->db->insert('All_notification_Mobile', $notification_array);
            
            send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $notification_type, $order_status, $updated_at, $order_id, $invoice_no, $name, $listing_name, $agent);
        }
        
        return array(
            'status' => 200,
            'message' => 'success',
            'order_status' => $type
        );
    }
    
    
    public function prescription_status($order_id, $order_status, $prescription_order, $delivery_time,$user_id1)
    {
        $this->load->model('OrderModel');
        date_default_timezone_set('Asia/Kolkata');
        $updated_at            = date('Y-m-d H:i:s');
        $sub_total             = '0';
        $prescription_price    = '0';
        $prescription_quantity = '0';
        
        //$query = $this->db->query("DELETE FROM `prescription_order_list` WHERE order_id='$order_id'");
        
        foreach ($prescription_order as $result) {
            $prescription_name     = $result['prescription_name'];
            $prescription_quantity = $result['prescription_quantity'];
            $prescription_price    = $result['prescription_price'];
            $prescription_discount = $result['prescription_discount'];
            $prescription_status   = $result['prescription_status'];
            
            $query = $this->db->query("INSERT INTO `prescription_order_list`(`order_id`, `prescription_name`, `prescription_quantity`, `prescription_price`, `prescription_discount`, `prescription_status`) VALUES ('$order_id', '$prescription_name', '$prescription_quantity', '$prescription_price', '$prescription_discount', '$prescription_status')");
            
        }
        $query = $this->db->query("UPDATE user_order SET order_status='$order_status',delivery_time='$delivery_time',listing_id='$user_id1' where order_id='$order_id'");
        
        
        $invNoArray = $this->db->query("select `invoice_no` from `user_order` WHERE order_id = '$order_id'")->row_array();
        $invno = $invNoArray['invoice_no'];
        $action_by_status = "Pharmacy";
        $orderStatus = "Order confirmed";
        $this->OrderModel->update_status( $invno,$orderStatus, $action_by_status);

        
        function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $invoice_no, $name, $listing_name, $agent,$notification_type)
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
                    "notification_type" => $notification_type,
                    "order_status" => $order_status,
                    "order_date" => $updated_at,
                    "order_id" => $order_id,
                    "invoice_no" => $invoice_no,
                    "name" => $name,
                    "listing_name" => $listing_name
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
        
        $order_info    = $this->db->select('user_id,listing_id,listing_name,delivery_time,invoice_no,name,listing_name,order_date')->from('user_order')->where('order_id', $order_id)->get()->row();
        $user_id       = $order_info->user_id;
        $listing_id    = $order_info->listing_id;
        $listing_name  = $order_info->listing_name;
        $delivery_time = $order_info->delivery_time;
        $invoice_no    = $order_info->invoice_no;
        $name          = $order_info->name;
        $order_date    = $order_info->order_date;
        $medical_name = $this->db->select('medical_name')->from('medical_stores')->where('user_id', $listing_id)->get()->row();
        $customer_token = $this->db->select('token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
         if(!empty($customer_token))
        {
            $token_status   = $customer_token->token_status;
        }
        else
        {
            $token_status   = "0";
        }
        if ($token_status > 0) {
             $reg_id    = $customer_token->token;
            $agent     = $customer_token->agent;
            $img_url   = 'https://medicalwale.com/img/noti_icon.png';
            $tag       = 'text';
            $key_count = '1';
            if ($order_status == 'Order Confirmed') {
                $title = 'Order Confirmed';
                $msg   = 'Your order will be delivered in ' . $delivery_time;
                 $notification_type="pharmacy_order_confirm";
            }
            if ($order_status == 'Awaiting Customer Confirmation' || $order_status == 'Awaiting Delivery Confirmation') {
                $title = 'Order Reply From ' . $medical_name->medical_name;
                $msg   = 'Your have received a reply on your order';
                 $notification_type="pharmacy_order_confirm";
            }
            if ($order_status == 'Order Delivered') {
                $title = 'Order Delivered';
                $msg   = 'Your has been delivered';
                 $notification_type="pharmacy_order_deliver";
            }
               $notification_array = array(
                      'title' => $title,
                     'msg'  => $msg,
                       'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => $order_status,
                      'order_date' => $order_date,
                       'order_id'   => $invoice_no,
                      'post_id'  => "",
                     'listing_id'  =>  "",
                      'booking_id'  => "",
                     'invoice_no' => $invoice_no,
                     'user_id'  => $user_id,
                      'notification_type'  => $notification_type,
                      'notification_date'  => date('Y-m-d H:i:s')
                       
             );
          $this->db->insert('All_notification_Mobile', $notification_array);
           
            send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $invoice_no, $name, $listing_name, $agent,$notification_type);
        }
        
        return array(
            'status' => 200,
            'message' => 'success',
            'order_status' => $order_status
        );
    }
    
    
    
    public function add_pharmacy($store_name, $store_manager_name, $store_since, $address_line1, $address_line2, $state, $city, $pincode, $latitude, $longitude, $listing_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        
        
        $q_user = $this->db->select('phone,email')->from('users')->where('id', $listing_id)->get()->row();
        $phone  = $q_user->phone;
        $email  = $q_user->email;
        
        
        $list_data = array(
            'user_id' => $listing_id,
            'reg_date' => $created_at,
            'medical_name' => $store_name,
            'store_manager' => $store_manager_name,
            'lat' => $latitude,
            'lng' => $longitude,
            'address1' => $address_line1,
            'address2' => $address_line2,
            'pincode' => $pincode,
            'city' => $city,
            'state' => $state,
            'store_since' => $store_since,
            'email' => $email,
            'contact_no' => $phone
        );
        
        $success = $this->db->insert('medical_stores', $list_data);
        
        $query = $this->db->query("UPDATE users SET name='$store_name',email='$email',phone='$phone',city='$city' WHERE id='$listing_id'");
        
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    
    
    public function pharmacy_details($listing_id)
    {
        $query = $this->db->query("SELECT medical_stores.reg_date,medical_stores.profile_pic, medical_stores.medical_name, medical_stores.store_manager, medical_stores.lat, medical_stores.lng, medical_stores.address1, medical_stores.address2, medical_stores.pincode, medical_stores.city, medical_stores.state, medical_stores.store_since,users.phone FROM medical_stores INNER JOIN users ON medical_stores.user_id=users.id WHERE medical_stores.user_id='$listing_id'");
        
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                
                $reg_date           = $row['reg_date'];
                $mobile             = $row['phone'];
                $store_name         = $row['medical_name'];
                $store_manager_name = $row['store_manager'];
                $latitude           = $row['lat'];
                $longitude          = $row['lng'];
                $address_line1      = $row['address1'];
                $address_line2      = $row['address2'];
                $pincode            = $row['pincode'];
                $city               = $row['city'];
                $state              = $row['state'];
                $store_since        = $row['store_since'];
                $profile_pic        = $row['profile_pic'];
                if ($row['profile_pic'] != '') {
                    $profile_pic = $row['profile_pic'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.png';
                }
                
                
                $resultpost[] = array(
                    "reg_date" => $reg_date,
                    "store_name" => $store_name,
                    "store_manager_name" => $store_manager_name,
                    "mobile" => $mobile,
                    "profile_pic" => $profile_pic,
                    "latitude" => $latitude,
                    "longitude" => $longitude,
                    "address_line1" => $address_line1,
                    "address_line2" => $address_line2,
                    "pincode" => $pincode,
                    "city" => $city,
                    "state" => $state,
                    "store_since" => $store_since
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
        
    }
    
    
    public function partner_statistics($listing_id, $type)
    {
        $rating        = '5.0';
        $profile_views = '0';
        $reviews       = '0';
        
        $query = $this->db->query("SELECT `profile_pic`,`license_no_status`, `licence_pic_status`, `shop_establish_pic_status`, `delivery_details_status`, `delivery_charges_status`, `payment_details_status` FROM `medical_stores` WHERE user_id='$listing_id'");
        
        
        
        foreach ($query->result_array() as $row) {
            
            $license_no_status         = $row['license_no_status'];
            $licence_pic_status        = $row['licence_pic_status'];
            $shop_establish_pic_status = $row['shop_establish_pic_status'];
            $delivery_details_status   = $row['delivery_details_status'];
            $delivery_charges_status   = $row['delivery_charges_status'];
            $payment_details_status    = $row['payment_details_status'];
            $profile_pic               = $row['profile_pic'];
            if ($license_no_status == '1') {
                $license_no_status = '10';
            } else {
                $license_no_status = '0';
            }
            if ($licence_pic_status == '1') {
                $licence_pic_status = '10';
            } else {
                $licence_pic_status = '0';
            }
            if ($shop_establish_pic_status == '1') {
                $shop_establish_pic_status = '10';
            } else {
                $shop_establish_pic_status = '0';
            }
            if ($delivery_details_status == '1') {
                $delivery_details_status = '10';
            } else {
                $delivery_details_status = '0';
            }
            if ($delivery_charges_status == '1') {
                $delivery_charges_status = '10';
            } else {
                $delivery_charges_status = '0';
            }
            if ($payment_details_status == '1') {
                $payment_details_status = '10';
            } else {
                $payment_details_status = '0';
            }
            
            if ($profile_pic != '') {
                $profile_pic = '15';
            } else {
                $profile_pic = '0';
            }
            
            
            
        }
        
        $step1 = '25';
        
        $profile_completed = $step1 + $profile_pic + $license_no_status + $licence_pic_status + $shop_establish_pic_status + $delivery_details_status + $delivery_charges_status + $payment_details_status;
        
        $followers    = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
        $following    = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
        $resultpost[] = array(
            'rating' => $rating,
            'profile_views' => $profile_views,
            'reviews' => $reviews,
            'profile_completed' => (string) $profile_completed,
            'followers' => (string) $followers,
            'following' => (string) $following
        );
        
        return $resultpost;
        
    }
    
    
    
    
    public function pharmacy_license_no($license_no, $listing_id)
    {
        $query = $this->db->query("UPDATE medical_stores SET license_registration='$license_no',license_no_status='1' WHERE user_id='$listing_id'");
        
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    
    public function pharmacy_delivery_details($reach_area, $day_night_delivery, $free_start_time, $free_end_time, $days_closed, $store_open, $store_close, $is_24hrs_available, $listing_id)
    {
        
        if ($is_24hrs_available == "Yes") {
            $store_open  = '';
            $store_close = '';
            
        }
        
        $query = $this->db->query("UPDATE medical_stores SET reach_area='$reach_area',day_night_delivery='$day_night_delivery',free_start_time='$free_start_time',free_end_time='$free_end_time',days_closed='$days_closed',store_open='$store_open',store_close='$store_close',is_24hrs_available='$is_24hrs_available',delivery_details_status='1' WHERE user_id='$listing_id'");
        
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    
    public function pharmacy_delivery_charges($min_order, $is_min_order_delivery, $min_order_delivery_charge, $night_delivery_charge, $listing_id)
    {
        $query = $this->db->query("UPDATE medical_stores SET min_order='$min_order',is_min_order_delivery='$is_min_order_delivery',min_order_delivery_charge='$min_order_delivery_charge',night_delivery_charge='$night_delivery_charge',delivery_charges_status='1' WHERE user_id='$listing_id'");
        
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    
    
    public function pharmacy_payment_details($payment_type, $listing_id)
    {
        $query = $this->db->query("UPDATE medical_stores SET payment_type='$payment_type',payment_details_status='1' WHERE user_id='$listing_id'");
        
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    
    
    
  public function pharmacy_partner_profile_list($listing_id)
    {
        
        $query = $this->db->query("SELECT `licence_pic`,`logo`, `shop_establish_pic`, `license_registration`,`day_night_delivery`,`days_closed`, `reach_area`, `payment_type`, `store_open`, `store_close`,`is_24hrs_available`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`,`store_since`, `free_start_time`, `free_end_time`, `license_no_status`, `licence_pic_status`, `shop_establish_pic_status`, `delivery_details_status`, `delivery_charges_status`, `payment_details_status`,`is_approval`,`is_active`,`lat`,`lng`  FROM `medical_stores` WHERE user_id='$listing_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            $license_no = '';
            foreach ($query->result_array() as $row) {
                
                $pharmacy_license_no       = array();
                $licence_pic               = array();
                $shop_establish_pic        = array();
                $pharmacy_delivery_details = array();
                $pharmacy_delivery_charges = array();
                $pharmacy_payment_details  = array();
                $approval_and_active       = array();
                
                //pharmacy_license_no  (1)
                $license_no          = str_replace("null", "", $row['license_registration']);
                $license_no_status   = $row['license_no_status'];
                $pharmacy_license_no = array(
                    'status' => $license_no_status,
                    'license_no' => $license_no
                );
                
                
                
                //licence_pic  (2)
                $licence_pic        = $row['licence_pic'];
                $licence_pic        = str_replace(' ', '%20', $licence_pic);
                $licence_pic        = 'https://d2c8oti4is0ms3.cloudfront.net/images/pharmacy_images/' . $licence_pic;
                $licence_pic_status = $row['licence_pic_status'];
                
                $licence_pic = array(
                    'status' => $licence_pic_status,
                    'licence_pic' => $licence_pic
                );
                
                $logo_pic_new        = $row['logo'];
                if(empty($logo_pic_new))
                {
                    $logo_pic_status = "0";
                $logo_pic        = ''; 
                }
                else
                {
                $logo_pic_status = "1";
                $logo_pic        = 'https://d2c8oti4is0ms3.cloudfront.net/images/pharmacy_images/' . $logo_pic_new;
                }
                 $logo_pic_arra = array(
                    'status' => $logo_pic_status,
                    'pharmacy_logo' => $logo_pic
                );
                //shop_establish_pic  (3)
                $shop_establish_pic        = $row['shop_establish_pic'];
                $shop_establish_pic        = str_replace(' ', '%20', $shop_establish_pic);
                $shop_establish_pic        = 'https://d2c8oti4is0ms3.cloudfront.net/images/pharmacy_images/' . $shop_establish_pic;
                $shop_establish_pic_status = $row['shop_establish_pic_status'];
                
                $shop_establish_pic = array(
                    'status' => $shop_establish_pic_status,
                    'shop_establish_pic' => $shop_establish_pic
                );
                
                
                //pharmacy_delivery_details  (4)
                $reach_area         = str_replace("null", "", $row['reach_area']);
                $day_night_delivery = str_replace("null", "", $row['day_night_delivery']);
                $free_start_time2   = str_replace("null", "", $row['free_start_time']);
                $free_end_time2     = str_replace("null", "", $row['free_end_time']);
                $free_start_time    = $this->check_time_format($free_start_time2);
                $free_end_time      = $this->check_time_format($free_end_time2);
                
                $days_closed        = str_replace("null", "", $row['days_closed']);
                $is_24hrs_available = str_replace("null", "", $row['is_24hrs_available']);
                $store_open         = str_replace("null", "", $row['store_open']);
                $store_close        = str_replace("null", "", $row['store_close']);
                
                $delivery_details_status = $row['delivery_details_status'];
                $is_free_delivery        = $this->is_free_delivery_staus($free_start_time, $free_end_time);
                
                $pharmacy_delivery_details = array(
                    'status' => $delivery_details_status,
                    'reach_area' => $reach_area,
                    'day_night_delivery' => $day_night_delivery,
                    'free_start_time' => $free_start_time,
                    'free_end_time' => $free_end_time,
                    'days_closed' => $days_closed,
                    'is_24hrs_available' => $is_24hrs_available,
                    'store_open' => $store_open,
                    'store_close' => $store_close,
                    'is_free_delivery' => $is_free_delivery
                );
                
                //pharmacy_delivery_charges  (5)
                $min_order                 = str_replace("null", "", $row['min_order']);
                $is_min_order_delivery     = str_replace("null", "", $row['is_min_order_delivery']);
                $min_order_delivery_charge = str_replace("null", "", $row['min_order_delivery_charge']);
                $night_delivery_charge     = str_replace("null", "", $row['night_delivery_charge']);
                
                $delivery_charges_status = $row['delivery_charges_status'];
                
                $pharmacy_delivery_charges = array(
                    'status' => $delivery_charges_status,
                    'min_order' => $min_order,
                    'is_min_order_delivery' => $is_min_order_delivery,
                    'min_order_delivery_charge' => $min_order_delivery_charge,
                    'night_delivery_charge' => $night_delivery_charge
                );
                
                //pharmacy_payment_details  (6)
                $payment_type = str_replace("null", "", $row['payment_type']);
                
                $payment_details_status   = $row['payment_details_status'];
                $pharmacy_payment_details = array(
                    'status' => $payment_details_status,
                    'payment_type' => $payment_type
                );
                
                //is_approval is_active
                $is_approval = $row['is_approval'];
                $is_active   = $row['is_active'];
                
                $approval_and_active = array(
                    'is_approval' => $is_approval,
                    'is_active' => $is_active
                );
                
                //lat,log is_active
                $latitude  = $row['lat'];
                $longitude = $row['lng'];
                
                $latitude_and_longitude = array(
                    'latitude' => $latitude,
                    'longitude' => $longitude
                );
                
                
                
                
                $data[] = array(
                    "pharmacy_license_no" => $pharmacy_license_no,
                    "licence_pic" => $licence_pic,
                    "pharmacy_logo" => $logo_pic_arra,
                    "shop_establish_pic" => $shop_establish_pic,
                    "pharmacy_delivery_details" => $pharmacy_delivery_details,
                    "pharmacy_delivery_charges" => $pharmacy_delivery_charges,
                    "pharmacy_payment_details" => $pharmacy_payment_details,
                    "approval_and_active" => $approval_and_active,
                    "latitude_and_longitude" => $latitude_and_longitude
                );
                
                $resultpost = array(
                    "status" => 200,
                    "message" => 'success',
                    "data" => $data
                );
            }
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
    
    
    public function pharmacy_licence_pic($listing_id, $licence_pic_file)
    {
        $query = $this->db->query("UPDATE medical_stores SET licence_pic='$licence_pic_file',licence_pic_status='1' WHERE user_id='$listing_id'");
        
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    
    public function pharmacy_shop_establish_pic($listing_id, $shop_establish_pic_file)
    {
        $query = $this->db->query("UPDATE medical_stores SET shop_establish_pic='$shop_establish_pic_file',shop_establish_pic_status='1' WHERE user_id='$listing_id'");
        
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    
    public function update_pharmacy($mobile, $store_name, $store_manager_name, $store_since, $address_line1, $address_line2, $state, $city, $pincode, $listing_id)
    {
        
        $query = $this->db->query("SELECT id from users WHERE phone='$mobile' AND id <> '$listing_id'");
        $count = $query->num_rows();
        
        
        if ($count > 0) {
            return array(
                'status' => 208,
                'message' => 'Phone number already exist'
            );
        }
        
        else {
            
            $query = $this->db->query("UPDATE medical_stores INNER JOIN users ON medical_stores.user_id=users.id
		SET
		users.phone = '$mobile',
		users.name = '$store_name',
		users.city = '$city',
		medical_stores.contact_no = '$mobile',
		medical_stores.medical_name = '$store_name',
		medical_stores.store_manager = '$store_manager_name',
		medical_stores.address1 = '$address_line1',
		medical_stores.address2 = '$address_line2',
		medical_stores.pincode = '$pincode',
		medical_stores.city = '$city',
		medical_stores.state = '$state',
		medical_stores.store_since = '$store_since'
		WHERE medical_stores.user_id ='$listing_id';");
            
            return array(
                'status' => 200,
                'message' => 'success'
            );
        }
    }
    
    public function pharmacy_profile_pic($listing_id, $profile_pic_file)
    {
        $query = $this->db->query("UPDATE medical_stores SET profile_pic='$profile_pic_file' WHERE user_id='$listing_id'");
        
        $usr_query = $this->db->query("SELECT avatar_id FROM users WHERE id='$listing_id'");
        $get_usr   = $usr_query->row_array();
        $avatar_id = $get_usr['avatar_id'];
        date_default_timezone_set('Asia/Kolkata');
        $updated_at = date('Y-m-d H:i:s');
        
        
        if ($avatar_id == '0') {
            
            $media_data = array(
                'title' => $profile_pic_file,
                'type' => 'image',
                'source' => $profile_pic_file,
                'created_at' => $updated_at,
                'deleted_at' => $updated_at
            );
            
            $media_insert = $this->db->insert('media', $media_data);
            $a_id         = $this->db->insert_id();
            
            $query = $this->db->query("UPDATE users SET avatar_id='$a_id' WHERE id='$listing_id'");
        } else {
            
            $query = $this->db->query("UPDATE media SET title='$profile_pic_file',source='$profile_pic_file' WHERE id='$avatar_id'");
            
        }
        
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    public function pharmacy_is_approval($listing_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $approval_date = date('Y-m-d H:i:s');
        
        $query = $this->db->query("UPDATE medical_stores SET is_approval='1',approval_date='$approval_date' WHERE user_id='$listing_id'");
        
        
        function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent)
        {
            date_default_timezone_set('Asia/Kolkata');
            $approval_send_date = date('j M Y h:i A');
            if (!defined("GOOGLE_GCM_URL"))
                define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
            $fields  = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array(
                    "title" => $title,
                    "message" => $msg,
                    "notification_image" => $img_url,
                    "tag" => $tag,
                    'sound' => 'default',
                    "notification_type" => 'approval_sent',
                    "approval_send_date" => $approval_send_date
                    
                    //public static final String SENT_FOR_APPROVAL = "approval_sent";
                    //public static final String APPROVAL_FROM_ADMIN = "approval_received";
                    //approval_send_date -mobile
                    //approval_receive_date -admin
                )
            );
            $headers = array(
                GOOGLE_GCM_URL,
                'Content-Type: application/json',
                $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
                
                //'Authorization: key=AIzaSyDTr3BzDHKj_6FnUiZXT__VCmamH-_gupM' android users
                //'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' android partner
                //'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE' ios partner       
                
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
            //echo $reg_id;
        }
        
        
        $pharmacy_info = $this->db->select('id,medical_name')->from('medical_stores')->where('user_id', $listing_id)->get()->row();
        $medical_name  = $pharmacy_info->medical_name;
        
        $customer_token = $this->db->select('token,agent,token_status')->from('users')->where('id', $listing_id)->get()->row();
        $token_status   = $customer_token->token_status;
        
        if ($token_status > 0) {
            $agent     = $customer_token->agent;
            $reg_id    = $customer_token->token;
            $img_url   = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/logo.png';
            $tag       = 'text';
            $key_count = '1';
            
            $title = 'Thank you for sending your profile';
            $msg   = 'We will review your profile and update you soon.';
            
            //When active by admin
            //$title = 'Welcome, your pharmacy has been approved.';
            //$msg   = 'Congratulations! Your pharmacy listing has been live now.';
            
            send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent);
            
        }
        
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    public function pharmacy_lat_log($latitude, $longitude, $listing_id)
    {
        $query = $this->db->query("UPDATE medical_stores SET lat='$latitude',lng='$longitude' WHERE user_id='$listing_id'");
        
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    
    public function partner_subcategory($category)
    {
        return $this->db->select('id,category as sub_category,details,image')->from('business_category')->where('category_id', $category)->order_by('category', 'asc')->get()->result();
    }
    
      //**************************************************Login Staff System***************************************************
    public function add_staff_member($user_id, $mobile, $staff_name, $staff_email)
    {
        $query = $this->db->query("SELECT * FROM users WHERE phone='$mobile' AND vendor_id = '13'");
        $count = $query->num_rows();
        if ($count == 0) {
            
                $data = array(
                                'vendor_id' => 13,
                                'name' => $staff_name,
                                'phone' => $mobile,
                                'email' => $staff_email,
                                'staff_hub_user_id' => $user_id,
                                'is_active'=>1
                                );
                                
                $this->db->insert('users',$data);      
                $resultpost =   array(
                                'status' => 200,
                                'message' => 'Success'
                            );
            
        } else {
            $resultpost =   array(
                                'status' => 201,
                                'message' => 'Already Available! Either this user is Hub or Staff'
                            );
        }
        return $resultpost;
        
    }
    
    public function staff_member_list($user_id)
    {
        $query = $this->db->query("SELECT avatar_id,id,name,phone,email FROM users WHERE staff_hub_user_id='$user_id' AND vendor_id='13' AND is_active=1");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                
                $staff_user_id      = $row['id'];
                $mobile             = $row['phone'];
                $staff_name         = $row['name'];
                $staff_email        = $row['email'];
                $avatar_id          = $row['avatar_id'];
                
                $query1 = $this->db->query("SELECT source FROM media WHERE id='$avatar_id'");
                $count1 = $query1->num_rows();
                if ($count1 > 0) {
                    $pro = $query1->row_array();
                    if ($pro['source'] != '') {
                        $profile_pic = $pro['source'];
                        $profile_pic = str_replace(' ', '%20', $profile_pic);
                        $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                    } else {
                        $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.png';
                    }
                }
                else {
                        $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.png';
                    }
                
                
                
                $resultpost[] = array(
                    "staff_user_id" => $staff_user_id,
                    "staff_name" => $staff_name,
                    "mobile" => $mobile,
                    "staff_email" => $staff_email,
                    "profile_pic" => $profile_pic
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
        
    }
    public function delete_staff_member($staff_user_id)
    {
        $query = $this->db->query("SELECT * FROM users WHERE id='$staff_user_id' AND vendor_id = '13' AND staff_hub_user_id IS NOT NULL");
        $count = $query->num_rows();
        if ($count > 0) {
            
                $data = array(
                                'is_active'=>0
                                );
                $this->db->where('id',$staff_user_id);              
                $this->db->update('users',$data);      
                $resultpost =   array(
                                'status' => 200,
                                'message' => 'Success'
                            );
            
        } else {
            $resultpost =   array(
                                'status' => 201,
                                'message' => 'User Not Available OR It is Hub!'
                            );
        }
        return $resultpost;
        
    }
    
      //**************************************************Login Staff System***************************************************
    public function inventory_product_type_list($user_id,$staff_user_id)
    {
       $query = $this->db->query("SELECT * FROM inventory_product_type WHERE user_id='$user_id'");
       $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id                     = $row['id'];
                $user_id                = $row['user_id'];
                $vendor_id              = $row['vendor_id'];
                $type                   = $row['type'];
                $unit                   = $row['unit'];
                $description            = $row['description'];
                
                $resultpost[] = array(
                                'id'        => $id,
                                'user_id'   => $user_id,
                                'vendor_id' => $vendor_id,
                                'type'      => $type,
                                'unit'      => $unit,
                                'description' => $description
                                
                                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
        
    } 
    
    public function inventory_category_list($user_id,$staff_user_id)
    {
       $query = $this->db->query("SELECT * FROM inventory_category WHERE user_id='$user_id'");
       $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $sub_category = array();
                if($row['sub_category'] == 0){
                    $id                     = $row['id'];
                    $user_id                = $row['user_id'];
                    $vendor_id              = $row['vendor_id'];
                    $category               = $row['category'];
                   
                   
                   $query1 = $this->db->query("SELECT * FROM inventory_category WHERE sub_category= '$id' AND user_id='$user_id'");
                   $count1 = $query1->num_rows();
                    if ($count1 > 0) {
                        foreach ($query1->result_array() as $row1) {
                            $sub_category_name               = $row1['category'];
                            $sub_id                          = $row1['id'];
                            $sub_category[]=array(
                                'sub_id' => $sub_id,
                                'category' => $sub_category_name
                            );
                        }
                    }
                    
                 $resultpost[] = array(
                                    'id'            => $id,
                                    'user_id'       => $user_id,
                                    'vendor_id'     => $vendor_id,
                                    'category'      => $category,
                                    'sub_category'  => $sub_category,
                                   
                                    );    
                  
                }
            }
             
        } else {
            $resultpost = array();
        }
        return $resultpost;
        
    } 
    
    public function inventory_product_list($user_id,$staff_user_id)
    {
        $query = $this->db->query("SELECT * FROM inventory_product WHERE user_id='$user_id'");
       $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id                     = $row['id'];
                $user_id                = $row['user_id'];
                $vendor_id              = $row['vendor_id'];
                $pname                  = $row['pname'];
                $inventory_image        = $row['inventory_image'];
                $cost_price             = $row['cost_price'];
                $mrp                    = $row['mrp'];
                $selling_price          = $row['selling_price'];
                $distributor_id         = $row['distributor_id'];
                $manufacture_date       = $row['manufacture_date'];
                $expiry_date            = $row['expiry_date'];
                $barcode                = $row['barcode'];
                $ingredients            = $row['ingredients'];
                $category_type_id       = $row['category_type_id'];
                $sub_category_type_id   = $row['sub_category_type_id'];
                $product_type_id        = $row['product_type_id'];
                $size                   = $row['size'];
               
                //distributor_id
                if(!empty($distributor_id))
                {
                    $dist = $this->db->select('*')->from('inventory_distributor')->where('id',$distributor_id)->get()->row();
                    if(!empty($dist))
                    {
                        $distributor_name = $dist->distributor_name;
                    }
                    else
                    {
                        $distributor_name = "";
                    }
                }
                else
                {
                    $distributor_name = "";
                }
                
                //Category
                if(!empty($category_type_id))
                {
                    $cat = $this->db->select('*')->from('inventory_category')->where('id',$category_type_id)->get()->row();
                    if(!empty($cat))
                    {
                        $category_name = $cat->category;
                    }
                    else
                    {
                        $category_name = "";
                    }
                }
                else
                {
                    $category_name = "";
                }
                
                 //Sub-Category
                if(!empty($sub_category_type_id))
                {
                    $sub_cat = $this->db->select('*')->from('inventory_category')->where('id',$sub_category_type_id)->get()->row();
                    if(!empty($sub_cat))
                    {
                        $sub_category_name = $sub_cat->category;
                    }
                    else
                    {
                        $sub_category_name = "";
                    }
                }
                else
                {
                    $sub_category_name = "";
                }
                
                 //product_type
                if(!empty($product_type_id))
                {
                    $product_type = $this->db->select('*')->from('inventory_product_type')->where('id',$product_type_id)->get()->row();
                    if(!empty($product_type))
                    {
                        $product_type_name = $product_type->type;
                    }
                    else
                    {
                        $product_type_name = "";
                    }
                }
                else
                {
                    $product_type_name = "";
                }
                
                     
                if ($inventory_image != '') {
                    $profile_pic = $inventory_image;
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/Inventory/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/Inventory/product_default.jpg';
                }
                
                $ware = array();
                $sql="SELECT * FROM inventory_warehouse where (user_id='$user_id' OR user_id='$staff_user_id')";
                $warehouse=$this->db->query($sql)->result();
                foreach($warehouse as $q) {
                    if($q->id==0)
        	       	{
        	       	    $warehouse_name="In-House";
        	       	}
        	       	else
        	       	{
        	       	    $w_id = $q->id;
        	       	    $sql="SELECT wname From inventory_warehouse WHERE id  = '".$w_id."'";
		
                		$result = $this->db->query($sql)->row();
                		if(!empty($result))
                		{
                		    $warehouse_name = $result->wname;
                		}
                		else
                		{
                		    $warehouse_name = "";
                		}
        	       	}
        	       	
        	       	$sql1="SELECT sum(stock_quantity) as stock_quantity FROM inventory_stock where user_id='$user_id'  AND product_id='$id' AND warehouse_id='$w_id'";
                    $stock=$this->db->query($sql1)->row();
        
//$stock =  $this->Inventory_master_m->get_warehouse_stock($q->id,$pack->id); 
                                if(!empty($stock))
        	       	            {
        	       	                    
        	       	                    if($stock->stock_quantity !=NULL)
        	       	                    {
        	       	                        $stock_quantity=$stock->stock_quantity;
        	       	                    }
        	       	                    else
        	       	                    {
        	       	                         $stock_quantity = 0;
        	       	                    }
        	       	            }
        	       	            else
        	       	            {
        	       	                    $stock_quantity = 0;
        	       	            }
        	       	            
        	       	 $ware[] = array("$warehouse_name" => $stock_quantity);           
                }
                
                $resultpost[] = array(
                                'id'        => $id,
                                'user_id' => $user_id,
                                'vendor_id' => $vendor_id,
                                'pname' => $pname,
                                'inventory_image' => $profile_pic,
                                'cost_price' => $cost_price,
                                'mrp' => $mrp,
                                'selling_price' => $selling_price,
                                'distributor_name' => $distributor_name,
                                'manufacture_date' => $manufacture_date,
                                'expiry_date' => $expiry_date, 
                                'barcode' => $barcode,
                                'ingredients' => $ingredients,
                                'category_name' => $category_name,
                                'sub_category_name' => $sub_category_name,
                                'product_type_name' => $product_type_name,
                                'size' => $size,
                                'stock_details' => $ware
                                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
        
    }  
    
    public function add_inventory_product($user_id,$staff_user_id, $product_name, $cost_price,$mrp, $selling_price, $manufacture_date, $expiry_date, $distributor_name, $category, $sub_category, $product_type, $barcode, $ingredients, $size)
    {
        $query = $this->db->query("SELECT * FROM inventory_product WHERE barcode='$barcode' AND user_id = '$user_id'");
        $count = $query->num_rows();
        if ($count == 0) {
             $img_format = array("jpg","png","gif","bmp","jpeg","PNG","JPG","JPEG","GIF","BMP");
                include('s3_config.php');
               if (isset($_FILES["image"]) && !empty($_FILES["image"]["name"]))
                {
                    $srt        = explode('.', $_FILES['image']['name']);
                    $ext      = getExtension($_FILES['image']['name']);
                    if (in_array($ext, $img_format)) {
                        $fiel_name = uniqid() . date("YmdHis").".".$ext;
                        $filetype   = "image";
                        $sourcePath = $_FILES['image']['tmp_name'];
                        $targetPath = "images/Inventory/".$fiel_name;
                        $s3->putObjectFile($sourcePath, $bucket, $targetPath, S3::ACL_PUBLIC_READ);
                     }
                }
                else
                {
                   $fiel_name = '';
                }
        
                $data = array(
                'user_id'=>$user_id,
                'inventory_image'=>$fiel_name,
                'vendor_id' => 13,
                'pname' => $product_name,
                'cost_price' => $cost_price,
                'mrp' => $mrp,
                'selling_price' => $selling_price,
                'distributor_id' => $distributor_name,
                'manufacture_date' => $manufacture_date,
                'expiry_date' => $expiry_date,
                'barcode' => $barcode,
                'ingredients' =>$ingredients,
                'size' => $size,
                'category_type_id' => $category,
                'sub_category_type_id' => $sub_category,
                'created_by' => $staff_user_id,
                'created_at' => curr_date()
                );  
                
                $this->db->insert('inventory_product',$data);      
                $resultpost =   array(
                                'status' => 200,
                                'message' => 'Success'
                            );
            
        } else {
            $resultpost =   array(
                                'status' => 201,
                                'message' => 'Product Already Available!'
                            );
        }
        return $resultpost;
        
    }
    
    public function edit_inventory_product($user_id,$staff_user_id,$product_id, $product_name, $cost_price,$mrp, $selling_price, $manufacture_date, $expiry_date, $distributor_name, $category, $sub_category, $product_type, $barcode, $ingredients, $size)
    {
        $query = $this->db->query("SELECT inventory_image FROM inventory_product WHERE id='$product_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            $fiel_name = $query->row()->inventory_image;
             $img_format = array("jpg","png","gif","bmp","jpeg","PNG","JPG","JPEG","GIF","BMP");
                include('s3_config.php');
               if (isset($_FILES["image"]) && !empty($_FILES["image"]["name"]))
                {
                    $srt        = explode('.', $_FILES['image']['name']);
                    $ext      = getExtension($_FILES['image']['name']);
                    if (in_array($ext, $img_format)) {
                        $fiel_name = uniqid() . date("YmdHis").".".$ext;
                        $filetype   = "image";
                        $sourcePath = $_FILES['image']['tmp_name'];
                        $targetPath = "images/Inventory/".$fiel_name;
                        $s3->putObjectFile($sourcePath, $bucket, $targetPath, S3::ACL_PUBLIC_READ);
                     }
                }
               
        
                $data = array(
                'inventory_image'=>$fiel_name,
                'vendor_id' => 13,
                'pname' => $product_name,
                'cost_price' => $cost_price,
                'mrp' => $mrp,
                'selling_price' => $selling_price,
                'distributor_id' => $distributor_name,
                'manufacture_date' => $manufacture_date,
                'expiry_date' => $expiry_date,
                'barcode' => $barcode,
                'ingredients' =>$ingredients,
                'size' => $size,
                'category_type_id' => $category,
                'sub_category_type_id' => $sub_category,
                'updated_by' => $staff_user_id,
                'updated_at'=> curr_date()
                );  
                 $this->db->where('id',$product_id);
                $this->db->update('inventory_product',$data);      
                $resultpost =   array(
                                'status' => 200,
                                'message' => 'Success'
                            );
            
        } else {
            $resultpost =   array(
                                'status' => 201,
                                'message' => 'product Not Available!'
                            );
        }
        return $resultpost;
        
    }
        
    public function inventory_distributor_list($user_id,$staff_user_id)
    {
       $query = $this->db->query("SELECT * FROM inventory_distributor WHERE user_id='$user_id'");
       $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id                     = $row['id'];
                $user_id                = $row['user_id'];
                $vendor_id              = $row['vendor_id'];
                $distributor_name       = $row['distributor_name'];
                $distributor_phone       = $row['distributor_phone'];
                $manufacturer_name      = $row['manufacturer_name'];
                $manufacturer_phone     = $row['manufacturer_phone'];
                $map_location           = $row['map_location'];
                $lat                    = $row['lat'];
                $lng                    = $row['lng'];
             
                $resultpost[] = array(
                                'id'        => $id,
                                'user_id' => $user_id,
                                'vendor_id' => $vendor_id,
                                'distributor_name' => $distributor_name,
                                'distributor_phone' => $distributor_phone,
                                'manufacturer_name' => $manufacturer_name,
                                'manufacturer_phone' => $manufacturer_phone,
                                'map_location' => $map_location,
                                'lat' => $lat,
                                'lng' => $lng
                                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
        
    }   
    
    public function add_inventory_distributor($user_id,$staff_user_id, $distributor_name, $manufacturer_name , $distributor_phone, $manufacturer_phone, $map_location, $lat, $lng)
    {
        $query = $this->db->query("SELECT * FROM inventory_distributor WHERE distributor_phone='$distributor_phone' AND user_id = '$user_id'");
        $count = $query->num_rows();
        if ($count == 0) {
            
                $data = array(
                            'user_id'           => $user_id,
                            'vendor_id'         => 13,
                            'distributor_name'  => $distributor_name,
	                        'manufacturer_name' => $manufacturer_name,
	                        'distributor_phone' => $distributor_phone,
	                        'manufacturer_phone'=> $manufacturer_phone,
	                        'map_location'      => $map_location,
	                        'lat'               => $lat,
	                        'lng'               => $lng,
	                        'created_by'        => $staff_user_id,
                            'created_at'        => curr_date()
                );  
                
                $this->db->insert('inventory_distributor',$data);      
                $resultpost =   array(
                                'status' => 200,
                                'message' => 'Success'
                            );
            
        } else {
            $resultpost =   array(
                                'status' => 201,
                                'message' => 'Distributor Already Available!'
                            );
        }
        return $resultpost;
        
    }
    
    public function edit_inventory_distributor($user_id,$staff_user_id,$distributor_id, $distributor_name, $manufacturer_name , $distributor_phone, $manufacturer_phone, $map_location, $lat, $lng)
    {
        $query = $this->db->query("SELECT * FROM inventory_distributor WHERE id='$distributor_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            
                $data = array(
                            'distributor_name'  => $distributor_name,
	                        'manufacturer_name' => $manufacturer_name,
	                        'distributor_phone' => $distributor_phone,
	                        'manufacturer_phone'=> $manufacturer_phone,
	                        'map_location'      => $map_location,
	                        'lat'               => $lat,
	                        'lng'               => $lng,
	                        'updated_by'        => $staff_user_id,
                            'updated_at'        => curr_date()
                );  
                $this->db->where('id',$distributor_id);
                $this->db->update('inventory_distributor',$data);      
                $resultpost =   array(
                                'status' => 200,
                                'message' => 'Success'
                            );
            
        } else {
            $resultpost =   array(
                                'status' => 201,
                                'message' => 'Distributor Not Available!'
                            );
        }
        return $resultpost;
        
    }
    
    public function inventory_warehouse_list($user_id,$staff_user_id)
    {
       $query = $this->db->query("SELECT * FROM inventory_warehouse WHERE user_id='$user_id'");
       $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id                     = $row['id'];
                $user_id                = $row['user_id'];
                $vendor_id              = $row['vendor_id'];
                $wname                  = $row['wname'];
                $map_location           = $row['map_location'];
                $lat                    = $row['lat'];
                $lng                    = $row['lng'];
             
                $resultpost[] = array(
                                'id'        => $id,
                                'user_id' => $user_id,
                                'vendor_id' => $vendor_id,
                                'wname'     => $wname,
                                'map_location' => $map_location,
                                'lat' => $lat,
                                'lng' => $lng
                                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
        
    }
    
    public function add_inventory_warehouse($user_id,$staff_user_id, $wname, $map_location, $lat, $lng)
    {
        $query = $this->db->query("SELECT * FROM inventory_warehouse WHERE wname='$wname' AND user_id = '$user_id'");
        $count = $query->num_rows();
        if ($count == 0) {
            
                $data = array(
                            'user_id'           =>$staff_user_id,
                            'vendor_id'         => 13,
                            'wname'             => $wname,
	                        'map_location'      => $map_location,
	                        'lat'               => $lat,
	                        'lng'               => $lng,
	                        'created_by'        => $staff_user_id,
                            'created_at'        => curr_date()
                );  
                
                $this->db->insert('inventory_warehouse',$data);      
                $resultpost =   array(
                                'status' => 200,
                                'message' => 'Success'
                            );
            
        } else {
            $resultpost =   array(
                                'status' => 201,
                                'message' => 'Warehouse Already Available!'
                            );
        }
        return $resultpost;
        
    }
    
    public function edit_inventory_warehouse($user_id,$staff_user_id, $warehouse_id, $wname, $map_location, $lat, $lng)
    {
        $query = $this->db->query("SELECT * FROM inventory_warehouse WHERE id='$warehouse_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            
                $data = array(
                            'wname'             => $wname,
	                        'map_location'      => $map_location,
	                        'lat'               => $lat,
	                        'lng'               => $lng,
	                        'created_by'        => $staff_user_id,
                            'created_at'        => curr_date()
                );  
                $this->db->where('id',$warehouse_id);
                $this->db->update('inventory_warehouse',$data);      
                $resultpost =   array(
                                'status' => 200,
                                'message' => 'Success'
                            );
            
        } else {
            $resultpost =   array(
                                'status' => 201,
                                'message' => 'Warehouse Not Available!'
                            );
        }
        return $resultpost;
        
    }
    
    public function inventory_po_list($user_id,$staff_user_id)
    {
       $query = $this->db->query("SELECT * FROM inventory_po WHERE user_id='$user_id' OR user_id='$staff_user_id'");
       $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id                     = $row['id'];
                $user_id                = $row['user_id'];
                $vendor_id              = $row['vendor_id'];
                $po_number              = $row['po_number'];
                $distributor_id         = $row['distributor_id'];
                $warehouse_id           = $row['warehouse_id'];
                $po_date                = $row['po_date'];
                $po_status              = $row['po_status'];
               
               
                if(!empty($distributor_id))
                {
                    $dist = $this->db->select('*')->from('inventory_distributor')->where('id',$distributor_id)->get()->row();
                    if(!empty($dist))
                    {
                        $distributor_name = $dist->distributor_name;
                    }
                    else
                    {
                        $distributor_name = "";
                    }
                }
                else
                {
                    $distributor_name = "";
                }
                
                if(!empty($warehouse_id))
                {
                    $dist = $this->db->select('*')->from('inventory_warehouse')->where('id',$warehouse_id)->get()->row();
                    if(!empty($dist))
                    {
                        $wname = $dist->wname;
                    }
                    else
                    {
                        $wname = "";
                    }
                }
                else
                {
                    $wname = "";
                }
                
                $inventory_po_details = array();
                $query1 = $this->db->query("SELECT * FROM inventory_po_details WHERE po_id='$id'");
                $count1 = $query1->num_rows();
                if ($count1 > 0) {
                    
                    foreach ($query1->result_array() as $row1) {
                        
                        $pid= $row1['product_id'];
                        if(!empty($pid))
                        {
                            $dist = $this->db->select('*')->from('inventory_product')->where('id',$pid)->get()->row();
                            if(!empty($dist))
                            {
                                $pname = $dist->pname;
                            }
                            else
                            {
                                $pname = "";
                            }
                        }
                        else
                        {
                            $pname = "";
                        }
                        $inventory_po_details [] = array('product_id' => $row1['product_id'],
                                                         'product_name' => $pname,
                                                         'quantity' => $row1['quantity'],
                                                         'price_per_quantity' => $row1['price_per_quantity'],
                                                         'total_price' => $row1['total_price']
                                                         );
                    }
                }
                $resultpost[] = array(
                                'id'        => $id,
                                'user_id' => $user_id,
                                'vendor_id' => $vendor_id,
                                'distributor_name' => $distributor_name,
                                'warehouse_name'     => $wname,
                                'po_number'          => $po_number,
                                'po_date'            => $po_date,
                                'po_status'          => $po_status,
                                'product_details' => $inventory_po_details
                                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
        
    }
    
    public function add_inventory_po($user_id,$staff_user_id,$po_number,$po_status,$product_details,$po_date,$warehouse_id,$distributor_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_date = date('Y-m-d H:i:s');
        
        $query = $this->db->query("SELECT * FROM inventory_po WHERE po_number='$po_number' AND ( user_id = '$user_id' OR user_id = '$staff_user_id')");
        $count = $query->num_rows();
        if ($count == 0) {
        
            $data_add=array(        'user_id'           => $staff_user_id,
        	                        'vendor_id'         => '13',
                                    'distributor_id'    => $distributor_id,
                                    'warehouse_id'      => $warehouse_id,
                                    'po_number'         => $po_number,
                                    'po_date'           => $po_date,
                                    'po_status'         => $po_status,
        	                        'created_at'        => curr_date(),
        	                        'created_by'        => $staff_user_id
        	               );
        	                        
        	$this->db->insert('inventory_po',$data_add);
            $type = $this->db->insert_id();   
            
            if(!empty($type))
        	{
        	            foreach($product_details as $details)
        	            {
        	                if(!empty($details['product_id']) && !empty($details['price']))
        	                {
        	                    $details = array('po_id'              => $type,
        	                                    'product_id'            => $details['product_id'],
        	                                    'quantity'              => $details['quantity'],
        	                                    'price_per_quantity'    => $details['price'],
            	                                'total_price'           => $details['total_price']
            	                                    );
        	                     $this->db->insert('inventory_po_details',$details);   
        	                   
            	                      if($po_status == "Received")
            	                     {
            	                       
                	                    $data_add_stoack =array(
                	                        'user_id'           => $staff_user_id,
                	                        'vendor_id'         => '13',
                	                        'po_id'             => $type,
                	                        'warehouse_id'      => $warehouse_id,
                	                        'distributor_id'    => $distributor_id,
                	                        'product_id'        => $details['product_id'],
                	                        'stock_quantity'    => $details['quantity'],
                	                        'po_date'           => $po_date,
                	                        'created_at'        => curr_date(),
                	                        'created_by'        => $staff_user_id
                	                        );
                	                    $this->db->insert('inventory_stock',$data_add_stoack);
            	                    }            
            	                    
        	               }         
        	           }
        	       $resultpost =   array(
                                'status' => 200,
                                'message' => 'Success'
                            ); 
            }else {
             $resultpost =   array(
                                'status' => 201,
                                'message' => 'Please try again later!'
                            );
            }
        }
    	else
    	{
    	    $resultpost =   array(
                                'status' => 201,
                                'message' => 'PO Already Available!'
                            );
    	}
        return $resultpost;           
    }
    
     public function edit_inventory_po($user_id,$staff_user_id,$po_id, $po_number,$po_status,$product_details,$po_date,$warehouse_id,$distributor_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_date = date('Y-m-d H:i:s');
        
        $query = $this->db->query("SELECT * FROM inventory_po WHERE id='$po_id'");
        $count = $query->num_rows();
        if ($count > 0) {
        
            $data_add=array(        
                                    'distributor_id'    => $distributor_id,
                                    'warehouse_id'      => $warehouse_id,
                                    'po_number'         => $po_number,
                                    'po_date'           => $po_date,
                                    'po_status'         => $po_status,
        	                        'updated_at'        => curr_date(),
        	                        'updated_by'        => $staff_user_id
        	               );
        	$this->db->where('id',$po_id);                        
        	$this->db->update('inventory_po',$data_add);
         
            $this->db->where('po_id',$po_id)->delete('inventory_po_details');
    	    
    	        if($po_status == "Received")
        	    {
    	            $this->db->where('po_id',$po_id)->delete('inventory_stock');
        	    } 
        	            foreach($product_details as $details)
        	            {
        	                if(!empty($details['product_id']) && !empty($details['price']))
        	                {
        	                    $details = array('po_id'              => $type,
        	                                    'product_id'            => $details['product_id'],
        	                                    'quantity'              => $details['quantity'],
        	                                    'price_per_quantity'    => $details['price'],
            	                                'total_price'           => $details['total_price']
            	                                    );
        	                     $this->db->insert('inventory_po_details',$details);   
        	                   
            	                      if($po_status == "Received")
            	                     {
            	                       
                	                    $data_add_stoack =array(
                	                        'user_id'           => $staff_user_id,
                	                        'vendor_id'         => '13',
                	                        'po_id'             => $type,
                	                        'warehouse_id'      => $warehouse_id,
                	                        'distributor_id'    => $distributor_id,
                	                        'product_id'        => $details['product_id'],
                	                        'stock_quantity'    => $details['quantity'],
                	                        'po_date'           => $po_date,
                	                        'created_at'        => curr_date(),
                	                        'created_by'        => $staff_user_id
                	                        );
                	                    $this->db->insert('inventory_stock',$data_add_stoack);
            	                    }            
            	                    
        	               }         
        	           }
        	       $resultpost =   array(
                                'status' => 200,
                                'message' => 'Success'
                            ); 
           
        }
    	else
    	{
    	    $resultpost =   array(
                                'status' => 201,
                                'message' => 'PO Not Available!'
                            );
    	}
        return $resultpost;           
    }
     //**************************************************Billing System***************************************************
    public function product_barcode_scanner($user_id,$hub_user_id,$barcode)
    {
        $query = $this->db->query("SELECT * FROM inventory_product WHERE barcode='$barcode' AND (user_id = '$user_id' OR user_id = '$hub_user_id')");
        
        $count = $query->num_rows();
        if ($count > 0) {
                $row = $query->row_array();
                $id                     = $row['id'];
                $user_id                = $row['user_id'];
                $vendor_id              = $row['vendor_id'];
                $pname                  = $row['pname'];
                $inventory_image        = $row['inventory_image'];
                $cost_price             = $row['cost_price'];
                $mrp                    = $row['mrp'];
                $selling_price          = $row['selling_price'];
                $distributor_id         = $row['distributor_id'];
                $manufacture_date       = $row['manufacture_date'];
                $expiry_date            = $row['expiry_date'];
                $barcode                = $row['barcode'];
                $ingredients            = $row['ingredients'];
                $category_type_id       = $row['category_type_id'];
                $sub_category_type_id   = $row['sub_category_type_id'];
                $product_type_id        = $row['product_type_id'];
                $size                   = $row['size'];
               
                //distributor_id
                if(!empty($distributor_id))
                {
                    $dist = $this->db->select('*')->from('inventory_distributor')->where('id',$distributor_id)->get()->row();
                    if(!empty($dist))
                    {
                        $distributor_name = $dist->distributor_name;
                    }
                    else
                    {
                        $distributor_name = "";
                    }
                }
                else
                {
                    $distributor_name = "";
                }
                
                //Category
                if(!empty($category_type_id))
                {
                    $cat = $this->db->select('*')->from('inventory_category')->where('id',$category_type_id)->get()->row();
                    if(!empty($cat))
                    {
                        $category_name = $cat->category;
                    }
                    else
                    {
                        $category_name = "";
                    }
                }
                else
                {
                    $category_name = "";
                }
                
                 //Sub-Category
                if(!empty($sub_category_type_id))
                {
                    $sub_cat = $this->db->select('*')->from('inventory_category')->where('id',$sub_category_type_id)->get()->row();
                    if(!empty($sub_cat))
                    {
                        $sub_category_name = $sub_cat->category;
                    }
                    else
                    {
                        $sub_category_name = "";
                    }
                }
                else
                {
                    $sub_category_name = "";
                }
                
                 //product_type
                if(!empty($product_type_id))
                {
                    $product_type = $this->db->select('*')->from('inventory_product_type')->where('id',$product_type_id)->get()->row();
                    if(!empty($product_type))
                    {
                        $product_type_name = $product_type->type;
                    }
                    else
                    {
                        $product_type_name = "";
                    }
                }
                else
                {
                    $product_type_name = "";
                }
                
                     
                if ($inventory_image != '') {
                    $profile_pic = $inventory_image;
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/Inventory/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/Inventory/product_default.jpg';
                }
                
                $resultpost[] = array(
                                'id'      => $id,
                                'user_id' => $user_id,
                                'staff_user_id' => $hub_user_id,
                                'vendor_id' => $vendor_id,
                                'pname' => $pname,
                                'inventory_image' => $profile_pic,
                                'cost_price' => $cost_price,
                                'mrp' => $mrp,
                                'selling_price' => $selling_price,
                                'distributor_name' => $distributor_name,
                                'manufacture_date' => $manufacture_date,
                                'expiry_date' => $expiry_date, 
                                'barcode' => $barcode,
                                'ingredients' => $ingredients,
                                'category_name' => $category_name,
                                'sub_category_name' => $sub_category_name,
                                'product_type_name' => $product_type_name,
                                'size' => $size
                                );
            
        } else {
            $resultpost = array();
        }
        return $resultpost;
        
    }
    
   public function product_inventory_bill($user_id,$hub_user_id,$date,$invoice_no,$product_details,$total_quantity,$total_price,$discount,$tax,$net_amount,$payment_method,$name,$customer_phone,$email,$customer_address,$doctor_name,$bhc_no)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_date = date('Y-m-d H:i:s');
        //Customer details
       
        if(!empty($bhc_no)) 
        {
            
            $query = $this->db->query("SELECT * FROM user_priviladge_card_new WHERE card_no='$bhc_no'")->row();
            if(!empty($query))
            {
                $cust_user_id = $query->user_id;
                
               $query = $this->db->query("SELECT id,cust_user_id,cemail,address,cname FROM inventory_customer WHERE cmobile='$customer_phone'")->row();
                 if(!empty($query))
                {
                    $cust_id = $query->id;
                    $address = $query->address;
                    $email = $query->cemail;
                    $name = $query->cname;
                    $cust_user_id = $query->cust_user_id;
                    if(empty($cust_user_id))
                    {
                        $query1 = $this->db->query("SELECT name,id,email FROM users WHERE phone='$customer_phone'")->row();
                    
                        if(!empty($query1))
                        {
                            $cust_user_id = $query1->id;
                            $this->db->query("UPDATE inventory_customer SET  cust_user_id='$cust_user_id' WHERE id='$cust_id'");
                        }
                    }
                }
                else
                {
                    $query1 = $this->db->query("SELECT name,id,email FROM users WHERE phone='$customer_phone'")->row();
                    
                    if(!empty($query1))
                    {
                        $cust_user_id = $query1->id;
                        $email = $query1->email;
                        $name = $query1->name;
                        if($email!="")
                        {
                            $email = $query1->email;
                        }
                        if($name!="")
                        {
                            $name = $query1->name;
                        }
                    }
                    else
                    {
                        $cust_user_id = "";
                    }
                    $cust_data = array(
            	                'vendor_id'     => '13',
            	                'user_id'       => $user_id,
            	                'cust_user_id'  => $cust_user_id, 
            	                'cname'         =>  $name,
            	                'cmobile'       =>  $customer_phone,
            	                'cemail'        =>   $email,
            	                'address'       =>   $customer_address,
            	                'bachat_health_card' => $bhc_no,
            	                'created_date'  =>$created_date
            	            );
            	    $this->db->insert('inventory_customer',$cust_data);
                    $cust_id = $this->db->insert_id();         
                }
            }
            else
            {
                $query1 = $this->db->query("SELECT name,id,email FROM users WHERE phone='$customer_phone'")->row();
                
                if(!empty($query1))
                {
                    $cust_user_id = $query1->id;
                    $email = $query1->email;
                    $name = $query1->name;
                    if($email!="")
                    {
                        $email = $query1->email;
                    }
                    if($name!="")
                    {
                        $name = $query1->name;
                    }
                }
                else
                {
                    $cust_user_id = "";
                }
                $cust_data = array(
        	                'vendor_id'     => '13',
        	                'user_id'       => $user_id,
        	                'cust_user_id'  => $cust_user_id, 
        	                'cname'         =>  $name,
        	                'cmobile'       =>  $customer_phone,
        	                'cemail'        =>   $email,
        	                'address'       =>   $customer_address,
        	                'bachat_health_card' => $bhc_no,
        	                'created_date'  =>$created_date
        	            );
        	    $this->db->insert('inventory_customer',$cust_data);
                $cust_id = $this->db->insert_id();         
            }
        }
        else
        {
             $query = $this->db->query("SELECT id,cust_user_id,cemail,address,cname FROM inventory_customer WHERE cmobile='$customer_phone'")->row();
             if(!empty($query))
            {
                $cust_id = $query->id;
                $address = $query->address;
                $email = $query->cemail;
                $name = $query->cname;
                $cust_user_id = $query->cust_user_id;
                if(empty($cust_user_id))
                {
                    $query1 = $this->db->query("SELECT name,id,email FROM users WHERE phone='$customer_phone'")->row();
                
                    if(!empty($query1))
                    {
                        $cust_user_id = $query1->id;
                        $this->db->query("UPDATE inventory_customer SET  cust_user_id='$cust_user_id' WHERE id='$cust_id'");
                    }
                }
            }
            else
            {
                $query1 = $this->db->query("SELECT name,id,email FROM users WHERE phone='$customer_phone'")->row();
                
                if(!empty($query1))
                {
                    $cust_user_id = $query1->id;
                    $email = $query1->email;
                    $name = $query1->name;
                    if($email!="")
                    {
                        $email = $query1->email;
                    }
                    if($name!="")
                    {
                        $name = $query1->name;
                    }
                }
                else
                {
                    $cust_user_id = "";
                }
                $cust_data = array(
        	                'vendor_id'     => '13',
        	                'user_id'       => $user_id,
        	                'cust_user_id'  => $cust_user_id, 
        	                'cname'         =>  $name,
        	                'cmobile'       =>  $customer_phone,
        	                'cemail'        =>   $email,
        	                'address'       =>   $customer_address,
        	                'bachat_health_card' => $bhc_no,
        	                'created_date'  =>$created_date
        	            );
        	    $this->db->insert('inventory_customer',$cust_data);
                $cust_id = $this->db->insert_id();         
            }
        }
        //end Customer details
        
        $data_add=array(        'user_id'           => $user_id,                               
    	                        'vendor_id'         => '13',
    	                        'bill_no'           => $invoice_no,
    	                        'cust_user_id'      => $cust_user_id,
    	                        'cust_id'           => $cust_id,
    	                        'total_amount'      => $total_price,
    	                        'bill_date'         => $date,
    	                        'tax'               => $tax,
    	                        'discount'          => $discount,
    	                        'total_qty'         => $total_quantity,
    	                        'final_amount'      => $net_amount,
    	                        'payment_method'    => $payment_method,
    	                        'doctor_name'       => $doctor_name,
    	                        'created_at'        => $created_date,
    	                        'created_by'        => $hub_user_id
    	                        );
    	$this->db->insert('inventory_bill',$data_add);
        $type = $this->db->insert_id();   
        $product_details_new = json_decode($product_details,TRUE);
        if(!empty($type))
    	{
    	           
                    foreach($product_details_new['product'] as $details)
    	            {
    	                if(!empty($details['product_id']) && !empty($details['price']))
    	                {
    	                    $details = array('bill_id'              => $type,
    	                                    'product_id'            => $details['product_id'],
    	                                    'quantity'              => $details['quantity'],
    	                                    'price_per_quantity'    => $details['price'],
        	                                'total_price'           => $details['total_price']
        	                                    );
    	                     $this->db->insert('inventory_bill_details',$details);   
    	                   
        	                    $data_add_stoack =array(
        	                        'user_id'           => $user_id,
        	                        'vendor_id'         => '13',
        	                        'bill_id'           => $type,
        	                        'product_id'        => $details['product_id'],
        	                        'stock_quantity'    => -$details['quantity'],
        	                        'po_date'           => date('Y-m-d'),
        	                        'created_at'        => $created_date,
        	                        'created_by'        => $user_id
        	                        );
        	                     $this->db->insert('inventory_stock',$data_add_stoack);
    	               }         
    	           }
    	           
    	            $medical_id = $user_id;
    	           
    	            $sql="SELECT * FROM medical_stores WHERE user_id = '".$medical_id."' ";
		            $detail = $this->db->query($sql)->row();
    	            //$detail = $this->post_m->fetch_pharmacy_details($this->session->userdata('id'));
        
                    $bill = $this->db->select('*')->from('inventory_bill')->where('id',$type)->get()->row();
                    
                    $bill_detail = $this->db->select('*')->from('inventory_bill_details')->where('bill_id',$type)->get()->result();
                    
                    $customer = $this->db->select('*')->from('inventory_customer')->where('id',$cust_id)->get()->row();
                    
                    $w ="(user_id = '$user_id' OR user_id = '$hub_user_id')";
                    $product=$this->db->select('*')->from('inventory_product')->where($w)->where('vendor_id',"13")->get()->result();
                   if(empty($detail->logo) )
                   {
                       $image=$detail->medical_name;
                   }
                   else
                   {
                       $image = '<img alt="" src="https://d2c8oti4is0ms3.cloudfront.net/images/pharmacy_images/'.$detail->logo.'" style="max-height: 48px;height: 48px;">';
                   }
    	             
    	            $this->load->library('dpdf');
        $pdf = $this->dpdf->load();
        $pdf->AddPage('A5');
        ini_set('memory_limit', '256M');
       
        $html= '<table  cellpadding="5" >
        <tr>
        <td colspan="2">'.$image.'</td>
        <td colspan="4">
        <h5>'.$detail->medical_name.'</h5>
        <p>'.$detail->address1.','.$detail->address2.',<br>'
        .$detail->city.','.$detail->state.','.$detail->pincode.'
        </p>
        </td>
        <td colspan="2">
        <h5>Invoice No</h5>
        										<p>'.$bill->bill_no.'</p>
        										<h5>Contact</h5>
        										<p>Phone : '.$detail->contact_no.'</p>
        </td>
        
        </tr>
        
        <tr>
        <td colspan="8">
        <hr>
        <h3 class="bot">Patient Details</h3>
        	<h5 class="bottom">'.$customer->cname.' <span style="font-size: 13px;"> </span></h5>
        	<h5 class="bottom">Address : <span style="font-size: 13px;">'.$customer->address.', </span></h5>
        	<h5 class="bottom">Mobile : <span style="font-size: 13px;">'.$customer->cmobile.'</span>&nbsp;&nbsp;&nbsp; Email : <span style="font-size: 13px;">'.$customer->cemail.'</span></h5>
        
            
        </td>
        
        </tr>
        <hr>
        <tr>
        <td colspan="8">
        <center>Medicines</center>
        </td>
        </tr>
        
        <tr >
        
         
         <th colspan="1" style=" border: 1px solid #dddddd;border-right:none;">Sr.No</th>
        												<th  colspan="1" style=" border: 1px solid #dddddd;border-right:none;">Product Name</th>
        												<th  colspan="1" style=" border: 1px solid #dddddd;border-right:none;">Quantity</th>
        												<th  colspan="1" style=" border: 1px solid #dddddd;border-right:none;">Price/Quantity</th>
        												<th  colspan="1" style=" border: 1px solid #dddddd;border-right:none;">Price</th>
           
        </tr>';
        
           $i=1;
                                                         $sums=0;
                            						      foreach($bill_detail as $dat) {
                            						$sums +=$dat->total_price ;
        										  
        										   $html .= '<tr id="row_'.$i.'"> 
                                						    <td>'.$i.'</td>
                                					<td>';
        
        if(!empty($product)) { foreach($product as $f) { if($dat->product_id == $f->id) {$ap= $f->pname; } } }
        
                                					 $html .= $ap.'	</td>            <td>'.$dat->quantity.'</td> 
                                					<td>'.$dat->price_per_quantity.'</td>
                                						        <td>'.$dat->total_price.'</td>
                                						      	      
              						        </tr> ';
        										}
        
        
        $html .= '<tr>
        <td colspan="8">
        <hr>
        </td>
        </table>
        	<table cellpadding="5"> 
                            						  
                            						       
                            						    <tbody> 
                            						   
                                						    <tr> 
                                						    <td style="    font-weight: bold;
            font-size: 16px !important;" colspan="2">Total Quantity</td> 
                                						    <td>'.$bill->total_qty.'</td>
                                						    <td style="   font-weight: bold;
            font-size: 16px !important;" colspan="2">Total Price</td> 
                                						    <td>'.$bill->total_amount.'</td>
                                						   
                                				            </tr> 
                                				             <tr> 
                                						    <td style="   font-weight: bold;
            font-size: 16px !important;" colspan="2">Discount Amount</td> 
                                						    <td>'.$bill->discount.' %</td>
                                						    <td style="    font-weight: bold;
            font-size: 16px !important;" colspan="2">Tax</td> 
                                						    <td>'.$bill->tax.' %</td>
                                						   
                                				            </tr> 
                            						   
                            						   
                            						    </tbody> 
                            						 </table> 
                            						 	<table cellpadding="5"> 
                            						   
                            						    <tbody> 
                            						   
                                						    <tr> 
                                						   
                                						    <td style="font-weight: bold;
            font-size: 16px !important;" colspan="5">Net Amount</td> 
                                						    <td>Rs. '.$bill->final_amount.' /-</td>
                                						   
                                				            </tr> 
                                				           
                            						   
                            						    </tbody> 
                            						 </table> 
        ';
          
        

        // render the view into HTML
        
                $pdf->WriteHTML($html); 
                $output = 'invoice' . date('Ymdhis') . '.pdf';
                $pdfFilePath = "/data/web/vendor.medicalwale.com/pharmacy/bill/".$output;
        
                $pdf->Output($pdfFilePath, 'F'); 
               // $pdf->Output($output, 'F');    
                $data_add_pdf =array(
        	                        'user_id'           => $hub_user_id,
        	                        'customer_user_id'  => $cust_user_id,
        	                        'bill_link'           => $output,
        	                        'created_at'        => $created_date,
        	                        'created_by'        => $hub_user_id
        	                        );
        	                     $this->db->insert('inventory_customer_pdf',$data_add_pdf);
        	                     
        	    $resultpost = array(
                                'bill_link' => "https://pharmacy.medicalwale.com/bill/".$output
                                );  
                return  $resultpost;               
               
    	}
    	else
    	{
    	    return  array();  
    	}
                   
    }
    public function inventory_dashboard($user_id,$hub_user_id,$from_date,$to_date)
    {
        if(!empty($from_date) && !empty($to_date))
        {
            
        }
        else
        {
            $from_date = date('Y-m-d',strtotime('last Monday'));
            $to_date = date('Y-m-d',strtotime('next Sunday'));
        }
        $total_orders = $this->db->query("select count(*) as tot from user_order where listing_id='$user_id' AND order_date>='$from_date' AND order_date<='$to_date'");
        $total_orders1 = $total_orders->row()->tot;
        
        $tot_units = $this->db->query("select sum(total_qty) as units from inventory_bill where user_id='$user_id' AND bill_date>='$from_date' AND bill_date<='$to_date'");
        $total_units = $tot_units->row()->units;
        if($total_units == null)
        {
            $total_units = 0;
        }
        
        $net_amount = $this->db->query("select sum(total_amount) as units from inventory_bill where user_id='$user_id' AND bill_date>='$from_date' AND bill_date<='$to_date'");
        $total_amount = $net_amount->row()->units;
        if($total_amount == null)
        {
            $total_amount = 0;
        }
        
        $max_amount = $this->db->query("select max(total_amount) as units from inventory_bill where user_id='$user_id' AND bill_date>='$from_date' AND bill_date<='$to_date'");
        $total_max_amount = $max_amount->row()->units;
        if($total_max_amount == null)
        {
            $total_max_amount = 0;
        }
        
        $min_amount = $this->db->query("select min(total_amount) as units from inventory_bill where user_id='$user_id' AND bill_date>='$from_date' AND bill_date<='$to_date'");
        $total_min_amount = $min_amount->row()->units;
        if($total_min_amount == null)
        {
            $total_min_amount = 0;
        }
        
        $avg_amount = $this->db->query("select avg(total_amount) as units from inventory_bill where user_id='$user_id' AND bill_date>='$from_date' AND bill_date<='$to_date'");
        $total_avg_amount = $avg_amount->row()->units;
         if($total_avg_amount == null)
        {
            $total_avg_amount = 0;
        }
        
        $gross_amount = $this->db->query("select sum(final_amount) as units from inventory_bill where user_id='$user_id' AND bill_date>='$from_date' AND bill_date<='$to_date'");
        $gross_amount1 = $gross_amount->row()->units;
        if($gross_amount1 == null)
        {
            $gross_amount1 = 0;
        }
        
        $gst_amount = $this->db->query("select total_amount,tax,discount from inventory_bill where user_id='$user_id' AND bill_date>='$from_date' AND bill_date<='$to_date'");
        $gst_amount1 = $gst_amount->result();
        
        $sum_gst=0;
        foreach($gst_amount1 as $gst)
        {
            if($gst->total_amount !=0 && $gst->discount != 0 && $gst->tax != 0)
            {
                $disc = $gst->discount/100;
                $amount = $gst->total_amount*$disc;
                $tot_a = $gst->total_amount-$amount;
                
                $tax = $gst->tax/100;
                $amount = $tot_a*$tax;
                $sum_gst += $amount;
                
            }
            else if($gst->total_amount !=0 && $gst->tax != 0)
            {
                $tax = $gst->tax/100;
                $amount = $gst->total_amount*$tax;
                $sum_gst += $amount;
            }
        }
        
        $discount_amount = $this->db->query("select total_amount,discount from inventory_bill where user_id='$user_id'");
        $discount_amount1 = $discount_amount->result();
        
        $sum_discount=0;
        foreach($discount_amount1 as $discount)
        {
            if($discount->total_amount !=0 && $discount->discount != 0)
            {
                $disc = $discount->discount/100;
                $amount = $discount->total_amount*$disc;
                $sum_discount += $amount;
            }
        }       
        
        
        //----------------------------daywise---------------------------
        for($d=0;$d<7;$d++)
        {
            $date = $from_date;
            $date1 = str_replace('-', '/', $date);
            $tomorrow = date('Y-m-d',strtotime($date1 . "+$d days"));
           // echo "select sum(final_amount) as units from inventory_bill where user_id='$user_id' AND bill_date='$tomorrow'";
            $gross_amount_day = $this->db->query("select sum(final_amount) as units from inventory_bill where user_id='$user_id' AND bill_date='$tomorrow'");
            $gross_amount_day1 = $gross_amount_day->row()->units;
            if($gross_amount_day1 == null)
            {
                $gross_amount_day1 = 0;
            }
            
            $day = date('l',strtotime($tomorrow));
            $daywise[]=array('day' => $day,
                             'gross_amount' => $gross_amount_day1);
        }
        $resultpost[] = array(
                            'from_date' => $from_date,
                            'to_date' => $to_date,
                            'total_orders' => $total_orders1,
                            'total_units'  => $total_units,
                            'net_amount'  => $total_amount,
                            'max_bill'    => $total_max_amount,
                            'min_bill' => $total_min_amount,
                            'avg_bill'  => $total_avg_amount,
                            'gross_amount' => $gross_amount1,
                            'total_gst'   => $sum_gst,
                            'total_disc' => $sum_discount,
                            'daywise' => $daywise
                           
                            );
            
        $final = array(
            'status'=> 200,
             'message' => "success",
            'data'=> $resultpost
                       );
        return $final;
        
    }
    
    public function check_json($product_details)
    {
        $product_details_new = json_decode($product_details,TRUE);
        print_r($product_details_new);
        foreach($product_details_new['product'] as $details)
        {
            echo $details['product_id'];
        }
    }
    
    
    public function monthly_report($user_id,$hub_user_id)
    {
     
        $year = date('Y');
        $month = intval(date('m'));				//force month to single integer if '0x'
    	$suff = array('st','nd','rd','th','th','th'); 		//week suffixes
    	$end = date('t',mktime(0,0,0,$month,1,$year)); 		//last date day of month: 28 - 31
      	$start = date('w',mktime(0,0,0,$month,1,$year)); 	//1st day of month: 0 - 6 (Sun - Sat)
    	$last = 7 - $start; 					//get last day date (Sat) of first week
    	$noweeks = ceil((($end - ($last + 1))/7) + 1);		//total no. weeks in month
    	$output = "";						//initialize string		
    	$monthlabel = str_pad($month, 2, '0', STR_PAD_LEFT);
    	for($x=1;$x<$noweeks+1;$x++){	
    		if($x == 1){
    			$startdate = "$year-$monthlabel-01";
    			$day = $last - 6;
    		}else{
    			$day = $last + 1 + (($x-2)*7);
    			$day = str_pad($day, 2, '0', STR_PAD_LEFT);
    			$startdate = "$year-$monthlabel-$day";
    		}
    		if($x == $noweeks){
    			$enddate = "$year-$monthlabel-$end";
    		}else{
    			$dayend = $day + 6;
    			$dayend = str_pad($dayend, 2, '0', STR_PAD_LEFT);
    			$enddate = "$year-$monthlabel-$dayend";
    		}
    	//	echo "select sum(final_amount) as units from inventory_bill where user_id='$user_id' AND bill_date>='$startdate' AND bill_date<='$enddate'";
    		 $gross_amount = $this->db->query("select sum(final_amount) as units from inventory_bill where user_id='$user_id' AND bill_date>='$startdate' AND bill_date<='$enddate'");
        $gross_amount1 = $gross_amount->row()->units;
        if($gross_amount1 == null)
        {
            $gross_amount1 = 0;
        }
        
        $session1[] = array(
                                        'from_date'=> $startdate,
                                        'to_date' => $enddate,
                                        'price'=>$gross_amount1
                                       );
    	//	$output .= "{$x}{$suff[$x-1]} week -> Start date=$startdate End date=$enddate <br />";	
    	}
    //	echo $output;
                 /*   $final_session1=array();
                    $session1 = array();
                    $session2 = array();
                    $session1[] = array(
                                        'from_date'=>'01-05-2019',
                                        'to_date' => '07-05-2019',
                                        'price'=>'1,500'
                                       );
                    $session1[] = array(
                                        'from_date'=>'08-05-2019',
                                        'to_date' => '14-05-2019',
                                        'price'=>'2,500'
                                       );
                    $session1[] = array(
                                        'from_date'=>'15-05-2019',
                                        'to_date' => '21-05-2019',
                                        'price'=>'5,500'
                                       );  
                    $session1[] = array(
                                        'from_date'=>'22-05-2019',
                                        'to_date' => '29-05-2019',
                                        'price'=>'5,500'
                                       );                 */     
                   return $session1;
    }   
    
     public function get_customer_detail_mobile($mobile)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_date = date('Y-m-d H:i:s');
        $bachat_card = "";
        $data_add = array();
        if(strlen($mobile) == 10)
        {
            $query = $this->db->query("SELECT name,email,id,phone FROM users WHERE phone='$mobile' AND vendor_id=0");
            $count = $query->num_rows();
            if ($count > 0) {
                $row = $query->row();
                $user_id= $row->id;
                
                $query1 = $this->db->query("SELECT * FROM user_priviladge_card_new WHERE user_id='$user_id'");
                $count1 = $query1->num_rows();
                if ($count1 > 0) {
                    $row1 = $query1->row();
                    $bachat_card = $row1->card_no;
                }
                
                 $query2 = $this->db->query("SELECT * FROM user_privilage_card WHERE user_id='$user_id'");
                $count2 = $query2->num_rows();
                if ($count2 > 0) {
                    $row2 = $query2->row();
                    $bachat_card = $row2->card_no;
                }
                    $data_add=array(        
                                            'name'          => $row->name,
                                            'email'         => $row->email,
                                            'bachat_card'   => $bachat_card,
                                            'mobile'        => $row->phone,
                                          
                	               );
                
            }
        }
        
        if(strlen($mobile) == 12)
        {
            $query = $this->db->query("SELECT * FROM user_priviladge_card_new WHERE card_no='$mobile'");
            $count = $query->num_rows();
            if ($count > 0) {
                $row = $query->row();
                $user_id= $row->user_id;
                $bachat_card = $row->card_no;
                
                $query2 = $this->db->query("SELECT name,email,id,phone FROM users WHERE id='$user_id'");
                $count2 = $query2->num_rows();
                if ($count2 > 0) {
                    $row2 = $query2->row();
                  
                     $data_add=array(        
                                            'name'          => $row2->name,
                                            'email'         => $row2->email,
                                            'bachat_card'   => $bachat_card,
                                            'mobile'           => $row2->phone,
                                          
                	               );
                }
                
                   
                
            }
            
             $query = $this->db->query("SELECT * FROM user_privilage_card WHERE card_no='$mobile'");
            $count = $query->num_rows();
            if ($count > 0) {
                $row = $query->row();
                $user_id= $row->user_id;
                $bachat_card = $row->card_no;
                
                  $query2 = $this->db->query("SELECT name,email,id,phone FROM users WHERE id='$user_id'");
                $count2 = $query2->num_rows();
                if ($count2 > 0) {
                    $row2 = $query2->row();
                  
                     $data_add=array(        
                                            'name'          => $row2->name,
                                            'email'         => $row2->email,
                                            'bachat_card'   => $bachat_card,
                                            'mobile'           => $row2->phone,
                                          
                	               );
                }
                
                   
                
            }
        }
        return $data_add;           
    }
      public function stock_inventory_dashboard($user_id,$hub_user_id,$page)
    {
        $limit = 7;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        
        
        $query = $this->db->query("SELECT * FROM inventory_product WHERE user_id='$user_id' LIMIT $start, $limit");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id                     = $row['id'];
                $user_id                = $row['user_id'];
                $vendor_id              = $row['vendor_id'];
                $pname                  = $row['pname'];
                $inventory_image        = $row['inventory_image'];
                $cost_price             = $row['cost_price'];
                $mrp                    = $row['mrp'];
                $selling_price          = $row['selling_price'];
                $distributor_id         = $row['distributor_id'];
                $manufacture_date       = $row['manufacture_date'];
                $expiry_date            = $row['expiry_date'];
                $barcode                = $row['barcode'];
                $ingredients            = $row['ingredients'];
                $category_type_id       = $row['category_type_id'];
                $sub_category_type_id   = $row['sub_category_type_id'];
                $product_type_id        = $row['product_type_id'];
                $size                   = $row['size'];
               
        	       	$sql1="SELECT sum(stock_quantity) as stock_quantity FROM inventory_stock where user_id='$user_id'  AND product_id='$id' AND warehouse_id='0'";
                    $stock=$this->db->query($sql1)->row();
                                if(!empty($stock))
        	       	            {
        	       	                    
        	       	                    if($stock->stock_quantity !=NULL)
        	       	                    {
        	       	                        $stock_quantity=$stock->stock_quantity;
        	       	                    }
        	       	                    else
        	       	                    {
        	       	                         $stock_quantity = "0";
        	       	                    }
        	       	            }
        	       	            else
        	       	            {
        	       	                    $stock_quantity = "0";
        	       	            }
        	       	       $resultpost[] = array(
                                'id'        => $id,
                                'pname' => $pname,
                                'stock' => $stock_quantity
                                );      
        	       	     
                }
                
        } else {
            $resultpost = array();
        }
        
        
        return $resultpost;
    }
	public function pharmacy_logo($listing_id, $licence_pic_file)
   {
       $query = $this->db->query("UPDATE medical_stores SET logo='$licence_pic_file' WHERE user_id='$listing_id'");        return array(
           'status' => 200,
           'message' => 'success'
       );
   }
}

