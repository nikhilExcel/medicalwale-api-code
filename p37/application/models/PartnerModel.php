<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PartnerModel extends CI_Model
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
            
            $query = $this->db->query("SELECT id from users WHERE phone='$phone' ");
            $count = $query->num_rows();
            
            $query2 = $this->db->query("SELECT id from users WHERE email='$email' ");
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
        if ($phone == '8655369076' || $phone=='8983779523' || $phone=='9619073803') {
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
    
    public function forget_sendotp($phone_old)
    {
         $query = $this->db->query("SELECT id,phone,otp_code FROM users WHERE (phone='$phone_old' or email='$phone_old') and vendor_id='5'");
        $count = $query->num_rows();
        if ($count > 0) 
        {
            $otp_code="";
            $get_list        = $query->row_array();
            $id              = $get_list['id'];
            $phone           = $get_list['phone'];
            if ($phone == '7506908285' || $phone == '9619073803')  
                {
                    $otp_code = '123456';
                }
            else {
                    $otp_code = rand(100000, 999999);
		    /*OTP message Integration by nikhil STARTS- 17/04/2021*/
                    $sms_response = $this->my_sms_integration($phone,$otp_code);
                    /*OTP message Integration by nikhil ENDS- 17/04/2021**/
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
        
        
            $querys = $this->db->query("UPDATE `users` SET otp_code='$otp_code' WHERE id='$id'");
		
            
            return array(
                'status' => 200,
            'message' => 'success',
            'otp_code' => (int) $otp_code
            );
            
        } else {
            return array(
                'status' => '208',
                'message' => 'Username not found',
                'otp_code' => 0
            );
        }
        
    }
    
    public function login($phone, $token, $agent)
    {
        $query = $this->db->query("SELECT id,name,gender,email,phone,city,vendor_id,otp_code,password FROM users WHERE phone='$phone' and vendor_id='5'");
        $count = $query->num_rows();
        if ($count > 0) {
            $get_list        = $query->row_array();
            $id              = $get_list['id'];
            $name            = $get_list['name'];
            $gender          = $get_list['gender'];
            $phone           = $get_list['phone'];
            $email           = $get_list['email'];
            $city            = $get_list['city'];
            $vendor_id       = $get_list['vendor_id'];
            $password        = $get_list['password'];
              if(empty($password)){
   $passoword_set=0;
   if ($phone == '7506908285' || $phone == '9619073803')  {
                    $otp_code = '123456';
                }
  else {
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
    $otp_code = '0';
}
             
            $category_query = $this->db->select('vendor_name')->from('vendor_type')->where('vendor_type.id', $vendor_id)->get()->row();
            $category       = $category_query->vendor_name;
            $img_count      = $this->db->select('profile_pic')->from('medical_stores')->where('user_id', $id)->get()->num_rows();
            if ($img_count > 0) {
                $media      = $this->db->select('profile_pic')->from('medical_stores')->where('user_id', $id)->get()->row();
                $img_file = $media->profile_pic;
                $image    = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
            } else {
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.png';
            }                        
            
            $accounts_list[] = array(
                "listing_id" => $id,
                "type" => $vendor_id,
                "type_name" => $category,
                "username" => $name,
                "profile_pic" => $image
                
            );            
            $last_login = date('Y-m-d H:i:s', strtotime('+12 hours'));
            $this->db->where('id', $id)->update('users', array('last_login' => $last_login));
            
            $querys = $this->db->query("UPDATE `users` SET `token`='$token',`agent`='$agent',`token_status`='1',otp_code='$otp_code' WHERE id='$id'");
			$userdata = array(                        
                'email' => $email,
                'id' => $id,
                'v_id' => $vendor_id,
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
            
                $date_array = array(
                'listing_id' => $id,
                'user' => 'old',
                'password_set' => $passoword_set,
                'name' => $name,
                'gender' => $gender,
                'type' => $vendor_id,
                'phone' => $phone,
                'email' => $email,
                'city' => str_replace('null','',$city),
                'otp_code' => (int)$otp_code,
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
    
    
     public function login_v1($phone, $token, $agent,$password_new)
    {
        $new=md5($password_new);
        $query = $this->db->query("SELECT users.id,name,users.gender,users.email,users.phone,users.city,users.vendor_id,users.password,users.otp_doc FROM users 
	LEFT JOIN doctor_list ON users.id = doctor_list.user_id 
	WHERE doctor_list.is_active = 1 and (users.phone='$phone' or users.email='$phone') and users.vendor_id='5'");
        $count = $query->num_rows();
        if ($count > 0) {
            $get_list        = $query->row_array();
            $id              = $get_list['id'];
            $name            = $get_list['name'];
            $gender          = $get_list['gender'];
            $phone           = $get_list['phone'];
            $email           = $get_list['email'];
            $city            = $get_list['city'];
            $vendor_id       = $get_list['vendor_id'];
            $password        = $get_list['password'];
            
            if($new==$password)
            {
            $otp_doc         = $get_list['otp_doc'];
             
            $category_query = $this->db->select('vendor_name')->from('vendor_type')->where('vendor_type.id', $vendor_id)->get()->row();
            $category       = $category_query->vendor_name;
	     /*<=== commented by nikhil ( 16 june 2021)		    
            $img_count      = $this->db->select('profile_pic')->from('medical_stores')->where('user_id', $id)->get()->num_rows();
            if ($img_count > 0) {
                $media      = $this->db->select('profile_pic')->from('medical_stores')->where('user_id', $id)->get()->row();
                $img_file = $media->profile_pic;
                $image    = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
            } else {
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.png';
            }                        
            ====>commented by nikhil STARTS( 16 june 2021)
            */
	    /*added by nikhil ( 16 june 2021)*/
            $img_count = $this->db->select('media.id')->from('media')->join('users', 'users.avatar_id=media.id','LEFT')->where('users.id', $id)->get()->num_rows();

                if ($img_count > 0) {
                    $media = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $id)->get()->row();
                    $img_file = $media->source;
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                    
                } else {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.png';
                }
            /*added by nikhil ENDS( 16 june 2021)*/ 		    
            $accounts_list[] = array(
                "listing_id" => $id,
                "type" => $vendor_id,
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
                'v_id' => $vendor_id,
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
            
                $date_array = array(
                'listing_id' => $id,
                'password_set' => $otp_doc,
                'name' => $name,
                'gender' => $gender,
                'type' => $vendor_id,
                'phone' => $phone,
                'email' => $email,
                'city' => str_replace('null','',$city),
                'accounts_list' => $accounts_list,
                'is_approval' => $is_approval,
                'is_active' => $is_active
            );
            
            return array(
                'status' => 200,
                'message' => 'success',
                'data' => $date_array
            );
            }
            else
            {
                return array(
                'status' => '208',
                'message' => 'Invalid Password'
            );
            }
            
        } else {
            return array(
                'status' => '208',
                'message' => 'Username not found'
            );
        }
        
    }
    
     public function set_password($phone, $password,$cpassword)
    {
        $new=md5($password);
        $query = $this->db->query("SELECT id,name,gender,email,phone,city,vendor_id,password,otp_doc FROM users WHERE (phone='$phone' or email='$phone') and vendor_id='5'");
        $count = $query->num_rows();
        if ($count > 0) {
            $get_list        = $query->row_array();
             $id              = $get_list['id'];
            $name            = $get_list['name'];
            $gender          = $get_list['gender'];
            $phone           = $get_list['phone'];
            $email           = $get_list['email'];
            $city            = $get_list['city'];
            $vendor_id       = $get_list['vendor_id'];
            $password_1      = $get_list['password'];
            
           
            
         
		  
                  $querys = $this->db->query("UPDATE `users` SET `password`='$new',`otp_doc`='1' WHERE id='$id'");
            $otp_doc         = $get_list['otp_doc'];
             
            $category_query = $this->db->select('vendor_name')->from('vendor_type')->where('vendor_type.id', $vendor_id)->get()->row();
            $category       = $category_query->vendor_name;
            $img_count      = $this->db->select('profile_pic')->from('medical_stores')->where('user_id', $id)->get()->num_rows();
            if ($img_count > 0) {
                $media      = $this->db->select('profile_pic')->from('medical_stores')->where('user_id', $id)->get()->row();
                $img_file = $media->profile_pic;
                $image    = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
            } else {
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.png';
            }                        
            
            $accounts_list[] = array(
                "listing_id" => $id,
                "type" => $vendor_id,
                "type_name" => $category,
                "username" => $name,
                "profile_pic" => $image
                
            );            
            $last_login = date('Y-m-d H:i:s', strtotime('+12 hours'));
            $this->db->where('id', $id)->update('users', array('last_login' => $last_login));
            
          // $querys = $this->db->query("UPDATE `users` SET `token`='$token',`agent`='$agent',`token_status`='1' WHERE id='$id'");
			$userdata = array(                        
                'email' => $email,
                'id' => $id,
                'v_id' => $vendor_id,
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
            
                $date_array = array(
                'listing_id' => $id,
                'password_set' => $otp_doc,
                'name' => $name,
                'gender' => $gender,
                'type' => $vendor_id,
                'phone' => $phone,
                'email' => $email,
                'city' => str_replace('null','',$city),
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
    
    
    public function order_list($listing_id, $listing_type, $order_type)
    {
        $query = $this->db->query("select user_id,order_id,order_type,action_by,delivery_time,cancel_reason,action_by,listing_id,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where listing_id='$listing_id' and listing_type='$listing_type' and order_type='$order_type' order by order_id desc");
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
        
    }
    
    public function order_status($order_id, $delivery_time, $order_status, $listing_id, $listing_type, $order_data_list)
    {
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
            
            $query = $this->db->query("UPDATE user_order_product SET product_quantity='$product_quantity',product_price='$product_price',product_discount='$product_discount',sub_total='$sub_total',order_status='$product_order_status',updated_at='$updated_at',product_unit='$product_unit',product_unit_value='$product_unit_value' where id='$product_order_id' and order_id='$order_id'");
            
            $sub_total = '0';
        }
        $query = $this->db->query("UPDATE user_order SET order_status='$order_status',delivery_time='$delivery_time' where order_id='$order_id'");
        
        function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $invoice_no, $name, $listing_name, $agent)
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
                    "notification_type" => "order",
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
        
        $order_info    = $this->db->select('user_id,listing_id,listing_name,delivery_time,invoice_no,name,listing_name')->from('user_order')->where('order_id', $order_id)->get()->row();
        $user_id       = $order_info->user_id;
        $listing_id    = $order_info->listing_id;
        $listing_name  = $order_info->listing_name;
        $delivery_time = $order_info->delivery_time;
        $invoice_no    = $order_info->invoice_no;
        $name          = $order_info->name;
        $listing_name  = $order_info->listing_name;
        
        $customer_token = $this->db->select('token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
        $token_status   = $customer_token->token_status;
        
        if ($token_status > 0) {
            $reg_id    = $customer_token->token;
            $agent     = $customer_token->agent;
            $img_url   = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/logo.png';
            $tag       = 'text';
            $key_count = '1';
            if ($order_status == 'Order Confirmed') {
                $title = 'Order Confirmed';
                $msg   = 'Confirmed: Order no.'.$invoice_no.' is successfully placed with '.$listing_name.'. Your order will be delivered in '.$delivery_time.'Thank You';
            }
            if ($order_status == 'Awaiting Customer Confirmation') {
                $title = 'Order Reply From ' . $listing_name;
                $msg   = 'Your have received a reply on your order';
            }
            if ($order_status == 'Order Delivered') {
                $title = 'Order Delivered';
                $msg   = 'Your order has been delivered';
            }
            send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $invoice_no, $name, $listing_name, $agent);
        }
        
        return array(
            'status' => 200,
            'message' => 'success',
            'order_status' => $order_status
        );
        
    }
    
    public function order_deliver_cancel($order_id, $cancel_reason, $type, $notification_type)
    {
        date_default_timezone_set('Asia/Kolkata');
        $updated_at = date('Y-m-d H:i:s');
        if ($type == 'Order Cancelled') {
            $query = $this->db->query("UPDATE `user_order` SET `updated_at`='$updated_at',`order_status`='Order Cancelled',`cancel_reason`='$cancel_reason',`action_by`='vendor' WHERE order_id='$order_id'");
        }
        if ($type == 'Order Delivered') {
            $query = $this->db->query("UPDATE `user_order` SET `updated_at`='$updated_at',`order_status`='Order Delivered',`cancel_reason`='',`action_by`='vendor' WHERE order_id='$order_id'");
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
        
        $order_info    = $this->db->select('user_id,listing_id,listing_name,delivery_time,invoice_no,name,listing_name')->from('user_order')->where('order_id', $order_id)->get()->row();
        $user_id       = $order_info->user_id;
        $listing_id    = $order_info->listing_id;
        $listing_name  = $order_info->listing_name;
        $delivery_time = $order_info->delivery_time;
        $invoice_no    = $order_info->invoice_no;
        $name          = $order_info->name;
        $listing_name  = $order_info->listing_name;
        
        $customer_token = $this->db->select('token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
        $token_status   = $customer_token->token_status;
        $order_status   = $type;
        if ($token_status > 0) {
            $reg_id    = $customer_token->token;
            $agent     = $customer_token->agent;
            $img_url   = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/logo.png';
            $tag       = 'text';
            $key_count = '1';
            if ($type == 'Order Cancelled') {
                $title = 'Order Cancelled';
                $msg   = 'Your order has been cancelled';
            }
            if ($type == 'Order Delivered') {
                $title = 'Order Delivered';
                $msg   = 'Your order has been delivered';
            }
            send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $notification_type, $order_status, $updated_at, $order_id, $invoice_no, $name, $listing_name, $agent);
        }
        
        return array(
            'status' => 200,
            'message' => 'success',
            'order_status' => $type
        );
    }
    
    
    public function prescription_status($order_id, $order_status, $prescription_order, $delivery_time)
    {
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
        $query = $this->db->query("UPDATE user_order SET order_status='$order_status',delivery_time='$delivery_time' where order_id='$order_id'");
        
        
        function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $invoice_no, $name, $listing_name, $agent)
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
                    "notification_type" => "prescription",
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
        
        $order_info    = $this->db->select('user_id,listing_id,listing_name,delivery_time,invoice_no,name,listing_name')->from('user_order')->where('order_id', $order_id)->get()->row();
        $user_id       = $order_info->user_id;
        $listing_id    = $order_info->listing_id;
        $listing_name  = $order_info->listing_name;
        $delivery_time = $order_info->delivery_time;
        $invoice_no    = $order_info->invoice_no;
        $name          = $order_info->name;
        $listing_name  = $order_info->listing_name;
        
        $customer_token = $this->db->select('token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
        $token_status   = $customer_token->token_status;
        
        if ($token_status > 0) {
            $reg_id    = $customer_token->token;
            $agent     = $customer_token->agent;
            $img_url   = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/logo.png';
            $tag       = 'text';
            $key_count = '1';
            if ($order_status == 'Order Confirmed') {
                $title = 'Order Confirmed';
                $msg   = 'Your order will be delivered in ' . $delivery_time;
            }
            if ($order_status == 'Awaiting Customer Confirmation') {
                $title = 'Order Reply From ' . $listing_name;
                $msg   = 'Your have received a reply on your order';
            }
            if ($order_status == 'Order Delivered') {
                $title = 'Order Delivered';
                $msg   = 'Your has been delivered';
            }
            send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $invoice_no, $name, $listing_name, $agent);
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
        
        $query = $this->db->query("SELECT `licence_pic`, `shop_establish_pic`, `license_registration`,`day_night_delivery`,`days_closed`, `reach_area`, `payment_type`, `store_open`, `store_close`,`is_24hrs_available`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`,`store_since`, `free_start_time`, `free_end_time`, `license_no_status`, `licence_pic_status`, `shop_establish_pic_status`, `delivery_details_status`, `delivery_charges_status`, `payment_details_status`,`is_approval`,`is_active`,`lat`,`lng`  FROM `medical_stores` WHERE user_id='$listing_id'");
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
    
   
       public function add_doctor($doctor_id, $patient_name, $mobile_no, $email, $gender, $dob, $blood_group, $address, $city,$state,$pincode,$medical_profile)
    {
        // date_default_timezone_set('Asia/Kolkata');
        // $created_at = date('Y-m-d H:i:s');
        
        
        // $q_user = $this->db->select('phone,email')->from('users')->where('id', $listing_id)->get()->row();
        // $phone  = $q_user->phone;
        // $email  = $q_user->email;
        
        
        $list_data = array(
            'doctor_id' => $doctor_id ,
            'patient_name' => $patient_name ,
            'contact_no' => $mobile_no,
            'email' => $email,
            'gender' => $gender,
            'date_of_birth' => $dob,
            'blood_group' => $blood_group,
            'address' => $address,
            'city' => $city,
            'state' => $state,
            'pincode' => $pincode,
            'medical_profile' => $medical_profile
            
        );
        
        $success = $this->db->insert('doctor_patient', $list_data);
        
        // $query = $this->db->query("UPDATE users SET name='$store_name',email='$email',phone='$phone',city='$city' WHERE id='$listing_id'");
        if($success){
        return array(
            'status' => 200,
            'message' => 'success'
        );}
        else{
             return array(
            'status' => 201,
            'message' => 'Failure'
        );
        }
    } 
	
    function my_sms_integration($username,$otp_code){
        $user = 'medicalwale';
        $pass = 'f62b2613';
        $senderid = 'MDWALE';
        $template = '1707161769136022843';
        
        // make url to post using cURL
        $url = 'http://makemysms.in/api/sendsms.php?username='.$user.'&password='.$pass.'&sender='.$senderid.'&mobile=91'.$username.'&type=1&product=1&template='.$template.'&message=%20Please%20enter%20this%20One%20Time%20Password%20:%20'.$otp_code.'%20to%20verify%20your%20mobile%20number%20'.$username.'%20Aap%20ke%20Health%20Ka%20Saathi.%20MEDICALWALE';
                     
        // Configure cURL options
        $options = array (CURLOPT_RETURNTRANSFER => true , // return web page
        CURLOPT_HEADER => false , // don't return headers
        CURLOPT_FOLLOWLOCATION => false , // follow redirects
        CURLOPT_ENCODING => "" , // handle compressed
        CURLOPT_USERAGENT => "test" , // who am i
        CURLOPT_AUTOREFERER => true , // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120 , // timeout on connect
        CURLOPT_TIMEOUT => 120 , // timeout on response
        CURLOPT_MAXREDIRS => 10 ); // stop after 10 redirects
                     
        // Send the GET request with cURL
        $ch = curl_init ( $url ); 
        curl_setopt_array ( $ch, $options) ;
        $content = curl_exec ( $ch ); 
        $err = curl_errno ( $ch ); 
        $errmsg = curl_error ( $ch ); 
        $header = curl_getinfo ( $ch ); 
        $httpCode = curl_getinfo ( $ch, CURLINFO_HTTP_CODE ); 
        curl_close ( $ch ); 
                     
        // Receive response
        $header [ 'errno' ] = $err; 
        $header [ 'errmsg' ] = $errmsg; 
        $header [ 'content' ] = $content;
        
        return $header['content'];
    } 	
    
    
    
}
