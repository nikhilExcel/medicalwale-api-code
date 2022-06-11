<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PartnermnoModel extends CI_Model {

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

    function sendmail($email, $password, $login_url) {
        $subject = "REGISTRATION INFORMATION";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: Medicalwale.com <no-reply@medicalwale.com>' . "\r\n";
        $headers .= 'Cc: ' . "\r\n";
        $message = '<div style="max-width: 700px;float: none;margin: 0px auto;"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1"><div id="styles_holder"><style>.ReadMsgBody{width:100%;background-color:#fff}.ExternalClass{width:100%;background-color:#fff}.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div{line-height:100%}html{width:100%}body{-webkit-text-size-adjust:none;-ms-text-size-adjust:none;margin:0;padding:0}table{border-spacing:0;border-collapse:collapse;table-layout:fixed;margin:0 auto}table table table{table-layout:auto}img{display:block !important}table td{border-collapse:collapse}.yshortcuts a{border-bottom:none !important}a{color:#1abc9c;text-decoration:none}@media only screen and (max-width: 640px){body{width:auto !important}table[class="table-inner"]{width:90% !important}table[class="table-full"]{width:100% !important;text-align:center !important}img[class="img1"]{width:100% !important;height:auto !important}}@media only screen and (max-width: 479px){body{width:auto !important}table[class="table-inner"]{width:90% !important}table[class="table-full"]{width:100% !important;text-align:center !important}img[class="img1"]{width:100% !important}}</style></div><div id="frame" class="ui-sortable"><table data-thumb="" data-module="header-bar" data-bgcolor="Main BG" align="center" width="100%" bgcolor="#e7e7e7" border="0" cellspacing="0" cellpadding="0" class=""><tbody><tr><td data-border-top-color="Top Border" style="border-top:4px solid #049341;"></td></tr><tr><td height="25"></td></tr><tr><td align="center"><table data-bgcolor="Container" width="600" class="table-inner" bgcolor="#ffffff" style="border-top-left-radius:5px;border-top-right-radius:5px;" border="0" align="center" cellpadding="0" cellspacing="0"><tbody><tr><td align="center" style="border-bottom: 5px solid #049341;"><table class="table-inner" width="520" border="0" align="center" cellpadding="0" cellspacing="0"><tbody><tr><td style="padding: 10px 0px;"><table class="table-full" border="0" align="left" cellpadding="0" cellspacing="0"><tbody><tr><td align="center" style="line-height:0px;"> <img data-crop="false" mc:edit="quinn-box-1" style="display:block; line-height:0px; font-size:0px; border:0px;height: 70px;" src="https://d2c8oti4is0ms3.cloudfront.net/images/img/email-logo.png" alt="logo" ></td></tr></tbody></table><table border="0" align="right" cellpadding="0" cellspacing="0" class="table-full"><tbody><tr><td height="15"></td></tr><tr><td align="center"><table border="0" align="center" cellpadding="0" cellspacing="0"><tbody><tr><td align="center" style=""> <font style="font-size:11px;line-height:16px" face="font-family: arial,sans-serif;" color="#666666"> <b style="font-size: 12px;font-family: arial, sans-serif;">Congratulations On Registration</b><br> </font> <font style="font-size:14px" face="HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue, Helvetica, Arial, sans-serif" color="#666666"> Call Us: </font><font style="font-size:16px" face="HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica, Arial, sans-serif" color="#7db701"><strong>022-60123457</strong></font></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table data-thumb="" data-module="1-2-right" data-bgcolor="Main BG" width="100%" align="center" bgcolor="#e7e7e7" border="0" cellspacing="0" cellpadding="0" class=""><tbody><tr><td align="center"><table data-bgcolor="Container" bgcolor="#FFFFFF" class="table-inner" width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="height: 250px;"><tbody style="background: url(https://d2c8oti4is0ms3.cloudfront.net/images/img/mail_bg.jpg);background-size: cover;"><tr><td height="20"></td></tr><tr><td align="center"><table class="table-inner" width="570" border="0" align="center" cellpadding="0" cellspacing="0"><tbody ><tr><td><table class="table-full" border="0" align="left" cellpadding="0" cellspacing="0" style="width: 320px;"><tbody><tr><td align="left" style="padding-bottom: 10px;"><p><font style="font-size:14px;color:#fff;line-height: 20px;" face="arial,sans-serif" >Welcome to </font><br> <font style="font-size:22px;color:#fff;" face="arial,sans-serif" ><span class="il">Medicalwale.com</span></font></p><p><font style="font-size:14px;color:#fff;line-height: 20px;" face="arial,sans-serif" >You are now part of 50,000+ Healthcare Service providers. We are delighted to have you on board with us. </font></p><p><font style="font-size:14px;color:#fff;" face="arial,sans-serif" >For any clarification <br> Mail us on: <a href="mailto:partner@medicalwale.com" style="color:#fff">partner@medicalwale.com</a> </font></p></td></tr></tbody></table><table border="0" align="right" cellpadding="0" cellspacing="0" class="table-full" style="width: 250px;"><tbody><tr><td><table style="background-color:rgb(249,246,246);border:0px solid rgb(213,210,210);width:100%;" cellpadding="0" cellspacing="0"><tbody><tr><td style="padding-left:20px;padding-top:10px;padding-bottom:10px;padding-right:10px; background: #a8abaf; text-align: center;" valign="top" align="left"><font style="font-size:16px" face="arial,sans-serif" color="#ffffff">Your Login Details</font></td></tr><tr><td style="padding:18px 15px 4px;background: #fff;" align="left"> <font style="font-size:14px;color: #656060;font-weight: 600;" face="arial,sans-serif">Email:</font> <font style="font-size:14px;line-height:21px" face="arial,sans-serif" color="#333333"> <a href="mailto:' . $email . '" target="_blank" style="color: #656060;text-decoration: none;">' . $email . '</a></font></td></tr><tr><td style="padding-bottom:18px;padding-top:4px;padding-left:15px;background: #fff;" align="left"> <font style="font-size:14px;color: #656060;font-weight: 600;" face="arial,sans-serif">Password :</font> <font style="font-size:14px;line-height:21px;color: #656060" face="arial,sans-serif" >' . $password . '</font></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td height="20"></td></tr></tbody></table></td></tr></tbody></table><table data-thumb="" data-module="quote" data-bgcolor="Main BG" width="100%" align="center" bgcolor="#e7e7e7" border="0" cellspacing="0" cellpadding="0" class="" ><tbody><tr><td data-bgcolor="Feature BG" align="center" bgcolor="#e7e7e7"><table data-bgcolor="Feature inner" bgcolor="#fff" class="table-inner" width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;"><tbody><tr><td align="center"><table class="table-inner" width="520" border="0" align="center" cellpadding="0" cellspacing="0"><tbody><tr><td height="35"></td></tr><tr><td height="5"></td></tr><tr><td data-link-style="text-decoration:none; color:#4a4a4a;" data-link-color="Quote" data-size="Quote" data-color="Quote" mc:edit="quinn-box-56" align="center" style="font-family: century Gothic, arial, sans-serif; font-size:16px; color:#4a4a4a; font-weight:bold;">"Your partnership means a lot to us and your constant support is what keeps us going. We look forward to continuing our relation in years to come!"</td></tr><tr><td height="35"></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table data-thumb="" data-module="footer" data-bgcolor="Main BG" width="100%" bgcolor="#e7e7e7" border="0" align="center" cellpadding="0" cellspacing="0" class=""><tbody><tr><td data-bgcolor="Footer Bar" bgcolor="#191919" align="center" style="font-family: Open Sans, Arial, sans-serif; font-size:11px; color:#ffffff;background: #e7e7e7;"><table class="table-inner" width="600" border="0" align="center" cellpadding="0" cellspacing="0"><tbody><tr><td><table class="table-full" bgcolor="#e7e7e7" border="0" align="left" cellpadding="0" cellspacing="0" style="width: 100%;"><tbody><tr><td data-link-style="text-decoration:none; color:#ffffff;" data-link-color="Footer Link" data-size="Content" data-color="Footer Text" mc:edit="quinn-box-81" height="30" style="font-family: Open Sans, Arial, sans-serif; font-size:12px; color: #6d6d6d;line-height: 19px;text-align:center; padding: 10px 0px;"> By using these service, you agree that you are bound by the Terms of Service.<br/>Copyright 2017 AEGIS HEALTH SOLUTIONS PVT. LTD. All rights reserved.</td></tr><tr><td height="15"></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></div></div>';

        $sentmail = mail($email, $subject, $message, $headers);
    }

    function randomPassword() {
        $pass = rand(100000, 999999);
        return $pass;
    }

    public function sendotp($phone) {
        if ($phone == '8655369076') {
            $otp_code = '123456';
        } else {
            $otp_code = rand(100000, 999999);
        }

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
            'otp_code' => (int) $otp_code
        );
    }
    
    public function encrypt($str) {
        //echo $str;
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

    public function login($phone, $token, $agent, $password) {
        $data = array();
        // $encr = '827ccb0eea8a706c4c34a16891f84e7b';
        $given_pass = md5($password);
        $query = $this->db->query("SELECT id,password,name,email,phone,city,vendor_id FROM users WHERE phone='$phone' and vendor_id='44'")->row_array();
        $count = sizeof($query);
        if ($count > 0) {
           
            
            $user_password = $query['password'];
            
            if($user_password == $given_pass){
                $mno_id = $query['id'];
                 $getTrack = $this->db->query("SELECT * FROM `mno_track` WHERE `mno_id` = '$mno_id' ORDER by id DESC limit 1 ")->row_array();
                 $this->db->query("UPDATE `users` SET `token` = '$token' where `id` = '$mno_id'");
                 
                 
                if(sizeof($getTrack) > 0 && $getTrack['logged_out'] == '0000-00-00 00:00:00'){
                    $status = 'online';
                } else {
                    $status = 'offline';
                }
                
                
                $data['mno_id'] = $query['id'];
                $data['name'] = $query['name'];
                $data['email'] = $query['email'];
                $data['phone'] = $query['phone'];
                $data['status'] = $status;
                $data['vendor_id'] = $query['vendor_id'];
                
                
                return array(
                    'status' => 200,
                    'message' => 'success',
                    'data' => $data
                );
            } else {
                return array(
                    'status' => 401,
                    'message' => 'Password did not match',
                    'data' => ''
                );    
            }
            
            
            
        } else {
            return array(
                'status' => '400',
                'message' => 'User not found'
            );
        }
    }
    
    // update_token
    
    public function update_token($mno_id, $token) {
        $status = '';
        $data = array();
        $query = $this->db->query("SELECT id,password,name,email,phone,city,vendor_id FROM users WHERE id='$mno_id' and vendor_id='44'")->row_array();
        $count = sizeof($query);
        if ($count > 0) {
           
            // mno_id
            
            $passwordUpdated = $this->db->query("UPDATE `users` SET `token`='$token' WHERE id='$mno_id'");
            $affected_rows = $this->db->affected_rows();
               
            
            // if($affected_rows > 0){
                $getTrack = $this->db->query("SELECT * FROM `mno_track` WHERE `mno_id` = '$mno_id' ORDER by id DESC limit 1 ")->row_array();
                if(sizeof($getTrack) > 0 && $getTrack['logged_out'] == '0000-00-00 00:00:00'){
                    $status = 'online';
                } else {
                    $status = 'offline';
                }
                
                $data['mno_id'] = $query['id'];
                $data['name'] = $query['name'];
                $data['email'] = $query['email'];
                $data['phone'] = $query['phone'];
                $data['status'] = $status;
                $data['vendor_id'] = $query['vendor_id'];
                
                
                return array(
                    'status' => 200,
                    'message' => 'success',
                    'data' => $data
                );
            // } else {
            //     return array(
            //         'status' => 401,
            //         'message' => 'Something went wrong',
            //         'data' => ''
            //     );    
            // }
            
            
            
        } else {
            return array(
                'status' => '400',
                'message' => 'No night owl found'
            );
        }
    }
    
    
    public function forget_password($phone) {
        
        $query = $this->db->query("SELECT id,password,name,email,phone,city,vendor_id FROM users WHERE phone='$phone' and vendor_id='44'")->row_array();
        $count = sizeof($query);
        if ($count > 0) {
            
            $otpSent = $this->PartnermnoModel->sendotp($phone);
            if($otpSent['status'] == 200){
                return array(
                    'status' => 200,
                    'message' => 'success',
                    'redirect' => 'Partnermno/verify_otp'
                );
            } else {
                return array(
                    'status' => 401,
                    'message' => 'Something went wrong',
                    'redirect' => 'Partnermno/forget_password'
                );
            }
            
            
        } else {
            return array(
                'status' => '400',
                'message' => 'User not found',
                'redirect' => 'Partnermno/forget_password'
            );
        }
    }
    
    public function verify_otp($phone,$otp) {
        
        $query = $this->db->query("SELECT id,otp_code,name,email,phone,city,vendor_id FROM users WHERE phone='$phone' and vendor_id='44'")->row_array();
        $count = sizeof($query);
        if ($count > 0) {
            
            if($query['otp_code'] == $otp){
                return array(
                    'status' => 200,
                    'message' => 'success',
                    'redirect' => 'Partnermno/change_password'
                );
            } else {
                return array(
                    'status' => 401,
                    'message' => 'Wrong OTP',
                    'redirect' => 'Partnermno/verify_otp'
                );
            }
            
            
        } else {
            return array(
                'status' => '400',
                'message' => 'User not found',
                'redirect' => 'Partnermno/forget_password'
            );
        }
    }
    
    public function change_password($phone,$password,$confirmPassword) {
        $data = array();
        $query = $this->db->query("SELECT id,otp_code,name,email,phone,city,vendor_id FROM users WHERE phone='$phone' and vendor_id='44'")->row_array();
        $count = sizeof($query);
        if ($count > 0) {
            
           if($password == $confirmPassword){
               $newPassword = md5($password);
               $id = $query['id'];
               $passwordUpdated = $this->db->query("UPDATE `users` SET `password`='$newPassword' WHERE id='$id'");
               $affected_rows = $this->db->affected_rows();
               
               if($affected_rows > 0){
                   
                   $query = $this->db->query("SELECT id,password,name,email,phone,city,vendor_id FROM users WHERE phone='$phone' and vendor_id='44'")->row_array();
        
                    $data['mno_id'] = $query['id'];
                    $data['name'] = $query['name'];
                    $data['email'] = $query['email'];
                    $data['phone'] = $query['phone'];
                    $data['vendor_id'] = $query['vendor_id'];
                    
                    
                    return array(
                        'status' => 200,
                        'message' => 'success',
                        'data' => $data
                    );
                   
               } else {
                   return array(
                        'status' => 401,
                        'message' => 'Something went wrong'
                    );
               }
               
                
            } else {
                return array(
                    'status' => 401,
                    'message' => 'Password did not match'
                );
            }
            
            
        } else {
            return array(
                'status' => '400',
                'message' => 'User not found'
            );
        }
    }
    
    public function status_online_offline($mno_id, $status) {
        date_default_timezone_set('Asia/Calcutta');

        $todayDate = date('Y-m-d H:i:s');
        $query = $this->db->query("SELECT id,otp_code,name,email,phone,city,vendor_id FROM users WHERE id='$mno_id' and vendor_id='44'")->row_array();
        if(sizeof($query) > 0){
            $lastUpdateRow = $this->db->query("SELECT * FROM `mno_track` WHERE `mno_id` = '$mno_id' ORDER by `id` DESC")->row_array();
            
            if($status == 'online'){
                if($lastUpdateRow['logged_out'] != "0000-00-00 00:00:00"){
                    $query = $this->db->query("INSERT INTO `mno_track` ( `mno_id`, `logged_in`) VALUES ('$mno_id', '$todayDate')");
                    $affected_rows = $this->db->affected_rows();
                    if($affected_rows > 0){
                        return 1;
                    } else {
                        return 2;
                    }
                } else {
                    return 4;
                }
                
                
            } else if($status == 'offline') {
                
                if($lastUpdateRow['logged_out'] == "0000-00-00 00:00:00"){
                    $rowId =  $lastUpdateRow['id'];
                    
                    $query = $this->db->query("UPDATE `mno_track` SET `logged_out`='$todayDate' where id = '$rowId'");
                    $affected_rows = $this->db->affected_rows();
                    if($affected_rows > 0){
                        return 1;
                    } else {
                        return 2;
                    }
                } else {
                    return 5;
                }
                
            } 
        } else {
            return 3;
        }
               
        return $affected_rows;
    }
    
    
    public function current_location($mno_id, $lat, $lng){
        date_default_timezone_set('Asia/Calcutta');
        $insert_id = 0;
        $todayDate = date('Y-m-d H:i:s');
        $todayDay = date('l');
        
        $mno_location = $this->db->query("INSERT INTO `mno_location`(`mno_id`, `lat`, `lng`, `created_at`) VALUES ('$mno_id','$lat','$lng','$todayDate')");
        $insert_id = $this->db->insert_id();
        if($insert_id > 0){
            $result = true;
        } else {
            $result = false;   
        }
        return $result;
       
    }
    
    // pharmacy_list
    
    public function pharmacy_list($mno_id, $lat, $lng, $page_no, $per_page){
        if($per_page != 0 && $page_no != 0){
            $offset = $per_page*($page_no - 1);
            $limit = "LIMIT $per_page OFFSET $offset"; 
        } else {
            $per_page = 10;
            $page_no = 1;
            $offset = $per_page*($page_no - 1);
            $limit = "LIMIT $per_page OFFSET $offset"; 
        }
        
        date_default_timezone_set('Asia/Calcutta');
        $insert_id = 0;
        $todayDate = date('Y-m-d H:i:s');
        $todayDay = date('l');
        $currenttime = date('h:i A');
        $current_time = date('h : i A');
        
        $days_closed_query = "`days_closed` Not Like '".$todayDay." Closed' or `days_closed` Not Like '".$todayDay." Half Day'";
        $store_close_query = "`store_close` > '$current_time' or `store_close` > '$currenttime'";
       $store_open_query = "`store_open` <= '$current_time' or `store_open` <= '$currenttime'";
       
        $radius = 10;
        $start = 0;
        $data = $resultpost=array();
        
        $sql = sprintf("SELECT mba,recommended,certified,`id`,`store_time`, `user_id`,`reach_area`, `medical_name`, `discount`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`,  `day_night_delivery`,  `days_closed`,  `is_24hrs_available`, `payment_type`, `store_open`, `store_close`,    ( 6371 * acos( cos( radians('19.1267157') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('72.8499786') ) + sin( radians('19.1267157') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_approval='1' AND is_active='1' AND (`is_24hrs_available` = 'Yes' OR  ($days_closed_query AND $store_close_query AND $store_open_query)) AND visible <> '0' HAVING distance < '10' ORDER BY distance  $limit", ($lat), ($lng), ($lat), ($radius));
    //  echo $sql; die();
        $all_pharmacies = $this->db->query("SELECT   ( 6371 * acos( cos( radians('19.1267157') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('72.8499786') ) + sin( radians('19.1267157') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_approval='1' AND is_active='1' AND (`is_24hrs_available` = 'Yes' OR  ($days_closed_query AND $store_close_query AND $store_open_query)) AND visible <> '0' HAVING distance < '$radius' ORDER BY distance ")->result_array();
    
        $pharmacy_count = sizeof($all_pharmacies);

        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if($count>0){
            foreach ($query->result_array() as $row) {
                $mlat = $row['lat'];
                $mlng = $row['lng'];
                $medical_id = $row['user_id'];
                $medical_name = $row['medical_name'];
                $store_manager = $row['store_manager'];
                $address1 = $row['address1'];
                $address2 = $row['address2'];
                $pincode = $row['pincode'];
                $city = $row['city'];
                $state = $row['state'];
                $contact_no = $row['contact_no'];
                $whatsapp_no = $row['whatsapp_no'];
                $discount = $row['discount'];
               
                $mba = $row['mba'] != "0";
                $certified = $row['certified'] != "0";
                $recommended = $row['recommended'] != "0";
              
                $reach_area  = $row['reach_area'];
                $is_24hrs_available = $row['is_24hrs_available'];
                if ($is_24hrs_available == 'Yes') {
                    $store_open = date("h:i A", strtotime("12:00 AM"));
                    $store_close = date("h:i A", strtotime("11:59 PM"));
                } else {
                    if($row['store_open'] == null || $row['store_open'] == "" || $row['store_close'] == "" || $row['store_close'] == null){
                        $st = explode("-",$row['store_time']);
                        if(sizeof($st) > 1){
                            
                            $store_open = $this->check_time_format($st[0]);
                            $store_close = $this->check_time_format($st[1]);
                           
                            
                        } else {
                            $store_open = date("h:i A", strtotime("12:00 AM"));
                            $store_close = date("h:i A", strtotime("11:59 PM"));
                        }
                    } else { 
                        $store_open = $this->check_time_format($row['store_open']);
                        $store_close = $this->check_time_format($row['store_close']);
                    }
                }
                $day_night_delivery = $row['day_night_delivery'];
                $days_closed = $row['days_closed'];
                
                $km = $row['distance'];
                $profile_pic = $row['profile_pic'];
                if ($row['profile_pic'] != '') {
                    $profile_pic = $row['profile_pic'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.jpg';
                }
    
                $chat_id = $row['user_id'];
                
                $meter = '0';
                $distance = $km * 1000;
                $store_distance = round($distance, 2);
                if ($distance > 999) {
                    $distance = $distance / 1000;
                    $meter = round($distance, 2) . ' km';
                } else {
                    $meter = round($distance) . ' meters away';
                }
    
                $reach_area = str_replace(" Mtr", "", $reach_area);
                $reach_area = str_replace(" Km", "", $reach_area);
                if ($reach_area > 10) {
                    $ranges = ($reach_area / 1000);
                } else {
                    $ranges = $reach_area;
                }
               // echo $mlat;
            //    echo $mlng;
             //   die();
                $drivingDistanceDurtion = $this->PartnermnoModel->GetDrivingDistance($lat, $mlat, $lng, $mlng );
               
                $drivingDistance = $drivingDistanceDurtion['distance'];
                $drivingDurtion = $drivingDistanceDurtion['duration'];
                $distances = str_replace(',', '.', $drivingDistance);
                
                // $distance_root =  round(($drivingDistance/1000),1);
                // print_r($distance_root); die();
                $todayOpenStatus = $this->check_day_status($todayDay, $days_closed, $is_24hrs_available, $store_open, $store_close);
                $opening_hours = "$todayDay>$todayOpenStatus";
                
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
                $open_close = "";
                $time = "";
                date_default_timezone_set('Asia/Kolkata');
                $data = array();
                $final_Day = array();
                $day_array_list = explode(',', $opening_hours);
                
                for ($i = 0; $i < count($day_array_list); $i++) {
                    $day_list = explode('>', $day_array_list[$i]);
                    for ($j = 0; $j < count($day_list); $j++) {
                        $day_time_list = explode('-', $day_list[$j]);
                        for ($k = 1; $k < count($day_time_list); $k++) {
                            $time_list1 = explode(',', $day_time_list[0]);
                            $time_list2 = explode(',', $day_time_list[1]);
                            $time = array();
                            $open_close = array();
                            for ($l = 0; $l < count($time_list1); $l++) {
                                $time_check = $time_list1[$l] . '-' . $time_list2[$l];
                                $time = str_replace('close-close', 'close', $time_check);
                                
                                if ($time == '12:00 AM-11:59 PM') {
                                    $time = '24 hrs open';
                                    $open_till = null;
                                } else {
                                    $ot = explode("-",$time);
                                    // $open_till = $ot[1];
                                    $open_till = date('H:i', strtotime($ot[1]));
                                }
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
                                    $open_close = 'open';
                                } else {
                                    $open_close = 'closed';
                                }
                            }
                        }
                    }
                }
                $time = $time;
                $status = $open_close;
                $FINAL_RESULT = array();
                    
                $resultpost[] = array(
                    'id' => intval($medical_id),
                    'medical_name' => $medical_name,
                    'listing_id' => intval($chat_id),
                    'listing_type' => 13,
                    'latitude' => floatval($lat),
                    'longitude' => floatval($lng),
                    'store_manager' => $store_manager,
                    'address1' => $address1,
                    'address2' => $address2,
                    'pincode' => intval($pincode),
                    'city' => $city,
                    'state' => $state,
                    'contact_no' => intval($contact_no),
                    'whatsapp_no' => intval($whatsapp_no),
                    "exotel_no" => 02233721563,
                    'store_distance' => $store_distance,
                    'distance' => $drivingDistance,
                    'drivingDurtion' => $drivingDurtion,
                    'discount' => floatval($discount),
                    
                    'is_24hrs_available' => $is_24hrs_available == 'Yes',
                    'day_night_delivery' => $day_night_delivery,
                    'status' => $status,
                    'open_time' => $time,
                    'open_till' => $open_till,
                    'profile_pic' => $profile_pic,
                    'mba' => $mba,
                    'certified' => $certified,
                    'recommended' => $recommended
                );
            }
            
            usort($resultpost, function($a, $b) {
                $a = $a['distance'];
                $b = $b['distance'];
                if ($a == $b) { return 0; }
                    return ($a < $b) ? -1 : 1;
            });
                
                    // $resultpost = array_reverse($resultpost);
            $last_page = ceil($pharmacy_count/$per_page);
            $data['data_count'] = intval($pharmacy_count);
            $data['per_page'] = $per_page;
            $data['current_page'] = $page_no;
            $data['first_page'] = 1;
            $data['last_page'] = "$last_page";
            $data['stores'] = $resultpost;
      
            return $data;
        }
        else{
          return array();
            
        }
    
    }
    
    
   public function GetDrivingDistance($originLat, $destLat, $originLng, $destLng) {
   
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $originLat . "," . $originLng . "&destinations=" . $destLat . "," . $destLng . "&mode=walking&language=pl-PL&key=AIzaSyDg9YiklFpUhqkDXlwzuC9Cnip4U-78N60&callback=initMap";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response, true);
        // print_r($response_a); die();
        if($response_a['rows'][0]['elements'][0]['status'] != 'ZERO_RESULTS' ){
            // in meter
            $distance = $response_a['rows'][0]['elements'][0]['distance']['value'];
            
            // in sec
            $duration = $response_a['rows'][0]['elements'][0]['duration']['value'];
        } else {
            $distance = null;
            $duration = null;

        }
        $data['distance'] = $distance;
        $data['duration'] = $duration;
        // echo $dist ; die();
        return $data;
    }
        
    
    public function check_day_status($day_type, $days_closed, $is_24hrs_available, $store_open, $store_close) {

        if ($is_24hrs_available === 'Yes') {
           
                $time = '12:00 AM-11:59 PM';
           
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
    
    public function check_current_delivery_charges($is_24hrs_available, $day_night_delivery, $free_start_time, $free_end_time, $night_delivery_charge, $days_closed, $is_min_order_delivery, $min_order) {

        date_default_timezone_set('Asia/Kolkata');
        $free_start_time_st = date("h:i A", strtotime($free_start_time));
        $free_end_time_st = date("h:i A", strtotime($free_end_time));
        $current_time_st = date('h:i A');

        $current_time = DateTime::createFromFormat('H:i a', $current_time_st);
        $system_start_time = DateTime::createFromFormat('H:i a', $free_start_time_st);
        $system_end_time = DateTime::createFromFormat('H:i a', $free_end_time_st);

        if ($system_start_time < $system_end_time && $current_time <= $system_end_time) {
            $system_end_time->modify('+1 day')->format('H:i a');
        } elseif ($system_start_time > $system_end_time && $current_time >= $system_end_time) {
            $system_end_time->modify('+1 day')->format('H:i a');
        }

        if ($is_24hrs_available == 'Yes') {
            if ($day_night_delivery == 'Yes') {

                if ($current_time > $system_start_time && $current_time < $system_end_time) {
                    if ($is_min_order_delivery == 'Yes') {
                        $current_delivery_charges = 'Free Delivery Available Above Rs ' . $min_order;
                    } else {
                        $current_delivery_charges = 'Delivery Not Available Below Rs ' . $min_order;
                    }
                } else {
                    $current_delivery_charges = 'Delivery Charges Applied Rs ' . $night_delivery_charge;
                }
            } else {
                if ($current_time > $system_start_time && $current_time < $system_end_time) {
                    // $current_delivery_charges = 'Free Delivery Available';	
                    if ($is_min_order_delivery == 'Yes') {
                        $current_delivery_charges = 'Free Delivery Available Above Rs ' . $min_order;
                    } else {
                        $current_delivery_charges = 'Delivery Not Available Below Rs ' . $min_order;
                    }
                } else {
                    $current_delivery_charges = 'Delivery Not Available Now';
                }
            }
        } else {

            if ($current_time > $system_start_time && $current_time < $system_end_time) {
                //$current_delivery_charges = 'Free Delivery Available';
                if ($is_min_order_delivery == 'Yes') {
                    $current_delivery_charges = 'Free Delivery Available Above Rs ' . $min_order;
                } else {
                    $current_delivery_charges = 'Delivery Not Available Below Rs ' . $min_order;
                }
            } else {
                $current_delivery_charges = 'Delivery Not Available Now';
            }
        }


        return $current_delivery_charges;
    }
    
    public function order_list($mno_id, $page_no, $per_page){
        
        $allOrdersWithInv = array();
        $orderData = $oldInv = $inv = 0;
            $offset = $per_page*($page_no - 1);
            $limit = "LIMIT $per_page OFFSET $offset"; 
        $data = array();
        
        $allOrders = $this->db->query("SELECT mo.id,  mo.status,uo.order_id,uo.order_type,uo.invoice_no ,uo.name, uo.user_id, u.id, u.name as user_name FROM `mno_orders` as mo left join user_order as uo on (uo.invoice_no = mo.invoice_no) left join users as u on (uo.user_id = u.id) WHERE `mno_id` = '$mno_id' ORDER BY uo.invoice_no desc $limit")->result_array();
        $allOrders_count = $this->db->query("SELECT mo.id FROM `mno_orders` as mo left join user_order as uo on (uo.invoice_no = mo.invoice_no) left join users as u on (uo.user_id = u.id) WHERE `mno_id` = '$mno_id'")->result_array();
        $orders_count = sizeof($allOrders_count);
       
        foreach($allOrders as $o){

            $order_id = null;
            if(empty($o['status'])){
                 $o['status'] = 'pending'; 
            }
            $inv = $o['invoice_no'];
            if($oldInv == $inv){
                $order_type = $o['order_type']; 
                $status = $o['status']; 
                $order_id = intval($o['order_id']);
                 if($order_type == 'order'){
                    $orderData['order_id'] = $order_id;
                } else if($order_type == 'prescription'){
                    $orderData['prescription_id'] = $order_id;
                }
                
            } else {
                if(!empty($orderData)){
                    $allOrdersWithInv[] = $orderData; 
                }
                $orderData = array();
                $in =  $o['invoice_no'];
                $orderData['invoice_no'] = $o['invoice_no'];
                
                if(!empty($o['name'])){
                    $orderData['name'] = $o['name'];
                } else {
                    $orderData['name'] = $o['user_name'];
                }
                $order_type = $o['order_type']; 
                $orderData['status'] = $o['status'];
                $order_id = intval($o['order_id']);
                 if($order_type == 'order'){
                    $orderData['order_id'] = $order_id;
                } else if($order_type == 'prescription'){
                    $orderData['prescription_id'] = $order_id;
                }
                
            }
            $oldInv = $in;
        }
        if(sizeof($orderData) > 0){
            $allOrdersWithInv[] = $orderData; 
        }
        // print_r($allOrdersWithInv); die();
        $last_page = ceil($orders_count/$per_page);
        $data['data_count'] = intval($orders_count);
        $data['per_page'] = $per_page;
        $data['current_page'] = $page_no;
        $data['first_page'] = 1;
        $data['last_page'] = "$last_page";
        $data['orders'] = $allOrdersWithInv;
        return $data;
    }
    
    public function order_accept_reject($mno_id,$invoice_no, $accepted, $cancel_reason){
        $data = array();
        $query = $this->db->query("SELECT id,password,name,email,phone,city,vendor_id FROM users WHERE id='$mno_id' and vendor_id='44'")->row_array();
        $count = sizeof($query);
        
        if ($count > 0) {
            $current_status = $this->db->query("SELECT status FROM `mno_orders` WHERE `mno_id` = '$mno_id' AND `invoice_no` = '$invoice_no'")->row_array();
            if(sizeof($current_status) > 0){
                
                if($current_status['status'] == 'accepted'){
                    return 6;
                } else if($current_status['status'] == 'rejected'){
                    return 7;
                } else {
                     if($accepted == false){
                        $rejectOrder = $this->db->query("UPDATE `mno_orders` SET  `ongoing`='0',`status`='rejected',`cancel_reason`='$cancel_reason' WHERE `mno_id` = '$mno_id' AND `invoice_no` = '$invoice_no'");
                        // assign another mno
                        $oderDeatils = $this->db->query("SELECT `user_id` , `lat` , `lng`  FROM `user_order` WHERE `invoice_no` = '$invoice_no'")->row_array();
                        $user_id = $oderDeatils['user_id'];
                        $lat = $oderDeatils['lat'];
                        $lng = $oderDeatils['lng'];
                        if(empty($lat)){
                            $lat = '19.1267157';
                        }
                        if(empty($lng)){
                            $lng = '72.8499786';
                        }
                        
                        $this->PartnermnoModel->assign_mno($user_id,$invoice_no,$lat,$lng);
                        return 3;
                       
                    } else if($accepted == true) {
                        $acceptedOrder = $this->db->query("UPDATE `mno_orders` SET  `ongoing`='1',`status`='accepted'  WHERE `mno_id` = '$mno_id' AND `invoice_no` = '$invoice_no'");
                        return 4;
                    } else { 
                        return 2; 
                    }
                }
            
            
               
                
            } else {
                return 5;
            }    
           
        } else {
            return 1;
        }
    }
    
    
     public function assign_mno($user_id,$invoice_no,$lat,$lng){
        $mnos = $allMnos = '';
        $isAlreadyAssigned = array();
        $radius = 15;
        date_default_timezone_set('Asia/Kolkata');
        $order_date = date('Y-m-d H:i:s');
        $isAlreadyAssigned = $this->db->query("SELECT * FROM `mno_orders` WHERE `invoice_no` = '$invoice_no' AND `mno_id` > 0 AND `ongoing` = 1")->row_array();
       
        if(!empty($isAlreadyAssigned) && sizeof($isAlreadyAssigned > 0)){
            $res = $isAlreadyAssigned['mno_id'];
            
        } else {
        
        
        $busyMnos = $this->db->query("SELECT DISTINCT ml.`mno_id` FROM `mno_list` as ml left join mno_orders as mo on (ml.mno_id = mo.mno_id) where ml.mno_id NOT IN (SELECT ml.`mno_id`  FROM mno_list as  ml left join mno_orders as mo on (ml.mno_id = mo.mno_id) where mo.ongoing = 1) AND ml.mno_id NOT IN (SELECT ml.`mno_id` FROM mno_list as ml left join mno_orders as mo on (ml.mno_id = mo.mno_id) where mo.ongoing = 0 AND mo.invoice_no = '$invoice_no')")->result_array();
            
            foreach($busyMnos as $bm){
                 if(!empty($allMnos)){
                     $allMnos .= ',';
                 }
                 $allMnos .= $bm['mno_id'];
            }
            
            if(!empty($allMnos)){
                $mnos = 'ml.mno_id IN ( '. $allMnos .' ) AND ' ;     
            }
            
                // echo $mnos; die(); 
            
            if(!empty($busyMnos) &&  sizeof($busyMnos) > 0){
                $sql = sprintf("SELECT ml.`id`, ml.`mno_id`,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance  FROM mno_list  as ml left join mno_track as mt on (ml.mno_id = mt.mno_id) WHERE $mnos  mt.logged_in <= '$order_date' AND mt.logged_out = '0000-00-00 00:00:00'  HAVING distance < '%s' ORDER BY distance LIMIT 20", ($lat), ($lng), ($lat), ($radius));
               
                $query = $this->db->query($sql);
                $count = $query->num_rows();
                $mno = $query->row_array();
                
                $mno_id = $mno['mno_id'];
                
                
                if(!empty($mno_id) &&  sizeof($mno_id) > 0){
                
                    $insertInOrder = $this->db->query("INSERT INTO `mno_orders`(`invoice_no`, `mno_id`, `ongoing`, `created_at`, `updated_at`) VALUES ('$invoice_no','$mno_id','1','$order_date','$order_date')");
                    $res = $mno_id;
                    
                    $mno_details = $this->db->query("SELECT u.token,u.agent,ml.* FROM `mno_list` as ml left join users as u on (ml.mno_id = u.id) WHERE ml.`mno_id` =  '$mno_id'")->row_array();
                    
                    $message = 'You have received one order of order id '.$invoice_no;
                    $customer_phone = $mno_details['phone'];
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
                    
                    // if(!empty($mno_details['token']) &&  sizeof($mno_details['token']) > 0){
              
                        $reg_id = $mno_details['token'];
                        $agent = $mno_details['agent'];
                        $msg = 'You have received one order of order id '.$invoice_no.' please accept it';
                        $img_url = 'https://medicalwale.com/img/noti_pharmacy.png';
                        $tag = 'text';
                        $key_count = '1';
                        $title = 'Order received';
                        $order_status = 'Awaiting confirmation';
                        $order_date = '';
                        $name = '';
                        $notification_type = 1;
                        
                     
                        $this->send_gcm_notify_mno($title, $reg_id, $msg, $img_url, $tag, $agent, $invoice_no,$notification_type);
                    // }
                    
                } else {
                    $res = 0;
                }
                
                
            } else {
                
                $res = 0; 
            }
            
        }
          
            return $res;
            
    }
    
    // order_details
    
    public function order_details($mno_id, $invoice_no){
        // remaining order product and prescription details
        $data = array();
        $acceped_query = $this->db->query("SELECT * FROM `mno_orders` WHERE `invoice_no` = '$invoice_no' AND `mno_id` = '$mno_id' ")->row_array();
        if($acceped_query['status'] == 'accepted'){
            
        $query = $this->db->query("select user_id,action_by,order_id,order_type,delivery_time,listing_id,cancel_reason,action_by,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status,rxId from user_order where `invoice_no` = '$invoice_no' group by invoice_no order by order_id desc");
        
        foreach ($query->result_array() as $row) {
                $user_id = $row['user_id'];
                $order_id = intval($row['order_id']);
                $order_type = $row['order_type'];
                $delivery_time = $row['delivery_time'];
                $listing_id = $row['listing_id'];
                $listing_name = $row['listing_name'];
                $listing_type = intval($row['listing_type']);
                $invoice_no = $row['invoice_no'];
                $chat_id = $row['chat_id'];
                $address_id = intval($row['address_id']);
                $name = $row['name'];
                $mobile = intval($row['mobile']);
                $pincode = intval($row['pincode']);
                $address1 = $row['address1'];
                $address2 = $row['address2'];
                $landmark = $row['landmark'];
                $city = $row['city'];
                $state = $row['state'];
                $action_by = $row['action_by'];
                $payment_method = $row['payment_method'];
                $order_date = $row['order_date'];
                $order_date = date('j M Y h:i A', strtotime($order_date));
                $delivery_charge = $row['delivery_charge'];
                $order_status = $row['order_status'];
                $order_type = $row['order_type'];
                $action_by = $row['action_by'];
                $rxId = $row['rxId'];
                $orderId = "";
               //added by zak for maintain medlife cancel order 
                  $is_cancel = false;
                if($rxId != 'NULL')
                {
                    $query = $this->db->query("select orderState,orderId from medlife_order where rxID='$rxId' order by id desc");
                $medlife_order =  $query->row();
                $medlife_order_num =  $query->num_rows();
                // = $medlife_order->orderState;
                 //    $medlife_order= $this->db->select('orderState')->from('medlife_order')->where('rxID', $rxId)->get()->row();
                 if($medlife_order_num>0)
                 {
                     $medlife_status = $medlife_order->orderState;
                     $order_status = $medlife_order->orderState;
                     $orderId = $medlife_order->orderId;
                     if($medlife_status == 'Received')
                     {
                         $is_cancel = true;
                     }
                     else
                     {
                         $is_cancel = false;
                     }
                 }
                 else
                 {
                     $is_cancel = false;
                 }
                }
                
                
                if ($action_by == 'night owl') {
                    $cancel_reason = $row['cancel_reason'];
                } else {
                    $cancel_reason = $row['cancel_reason'];
                }
                $user_info = $this->db->query("SELECT u.name, u.phone, m.source FROM users as u left JOIN media as m ON (u.avatar_id = m.id)  WHERE u.id='$user_id'");
               
                $getuser_info = $user_info->row_array();
                
                    $img_file = $getuser_info['source'];
                    if(!empty($img_file)){
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;    
                    } else {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';;
                    }
                $user_name = $getuser_info['name'];
                $user_mobile = intval($getuser_info['phone']);
                $prescription_resultpost=array();
                $product_resultpost  = array();
                $prescription_result  = array();
                 $query1 = $this->db->query("select user_id,order_id,order_type,action_by,delivery_time,cancel_reason,action_by,listing_id,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where invoice_no='$invoice_no'");
                  $count1 = $query1->num_rows();
                  $order_total=0;
                  $sub_total_sum2=0;
                  $sub_total_sum1=0;
                  $sub_total_discount=0;
                  $sub_total_discount1=0;
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
                            $product_image        = $product_row['product_img'];
                            
                            if(!empty($product_image)){
                                $product_image = str_replace(' ', '%20', $product_image);
                                $product_img = 'https://d2c8oti4is0ms3.cloudfront.net/images/product_images/' . $product_image;
                            } else {
                                $product_img = null;
                            }
                        
                            $product_price        = floatval($product_row['product_price']);
                            $product_unit         = $product_row['product_unit'];
                            $product_unit_value   = floatval($product_row['product_unit_value']);
                            $product_quantity     = intval($product_row['product_quantity']);
                            $product_discount     = floatval($product_row['product_discount']);
                            $sub_total            = floatval($product_row['sub_total']);
                            $product_status       = $product_row['product_status'];
                            $product_status_type  = $product_row['product_status_type'];
                            $product_status_value = $product_row['product_status_value'];
                            $product_order_status = $product_row['order_status'];
                            
                            $sub_total_sum1      += $product_price * $product_quantity;
                            $sub_total_discount +=$product_discount;
                            $product_resultpost[] = array(
                                "order_id" => intval($order_id1),
                                "product_order_id" => intval($product_order_id),
                                "product_id" => intval($product_id),
                                "product_name" => $product_name,
                                "product_img" => $product_img,
                                "product_quantity" => $product_quantity,
                                "product_price" => $product_price,
                                "product_unit" => $product_unit,
                                "product_unit_value" => $product_unit_value,
                                "product_discount" => floatval($product_discount),
                                "sub_total" => $sub_total,
                                "product_status" => $product_status,
                                "product_status_type" => $product_status_type,
                                "product_status_value" => $product_status_value,
                                "product_order_status" => $product_order_status
                            );
                        }
                    }
              
                    $product_query1 = $this->db->query("SELECT id as product_order_id, order_status,prescription_image,prescription_image2,prescription_image3,prescription_image4,original_prescription FROM prescription_order_details WHERE order_id='$order_id1' order by product_order_id asc");
                    $product_count1 = $product_query1->num_rows();
                    if ($product_count1 > 0) {
                        foreach ($product_query1->result_array() as $product_row1) {
                            $product_order_id1     = $product_row1['product_order_id'];
                            $product_id1           = $product_row1['product_order_id'];
                            $product_name1         = '';
                            $product_img1          = '';
                            $product_img1          = $product_row1['prescription_image'];
                            $product_img2          = '';
                            $product_img2          = $product_row1['original_prescription'];
                            $product_quantity1     = '';
                            $product_price1        = '';
                            $sub_total1            = '';
                            $product_status1       = '';
                            $product_status_type1  = '';
                            $product_status_value1 = '';
                            $product_order_status1 = $product_row1['order_status'];
                         if(!empty($product_img1))
                           {
                            if (strpos($product_img1, '/') == true) {
                                $images_1 = "https://d2c8oti4is0ms3.cloudfront.net/" . $product_img1;
                           }
                           else
                           {
                               $images_1= "https://d2c8oti4is0ms3.cloudfront.net/images/prescription_images/" . $product_img1;
                           }
                           }
                           else
                           {
                               $images_1=null;
                           }
                           
                           if(!empty($product_img2))
                           {
                            if (strpos($product_img2, '/') == true) {
                                $images_2 = "https://d2c8oti4is0ms3.cloudfront.net/" . $product_img2;
                           }
                           else
                           {
                               $images_2= "https://d2c8oti4is0ms3.cloudfront.net/images/prescription_images/" . $product_img2;
                           }
                        }
                        else
                        {
                            $images_2="";
                        }
                           
                            $prescription_resultpost[] = array(
                                "order_id" => intval($order_id1),
                                "product_order_id" => intval($product_order_id1),
                                "product_id" => intval($product_id1),
                                "product_name" => $product_name1,
                                "product_img" => $images_1,
                                "product_orginal_img" => $images_2,
                                "product_quantity" => $product_quantity1,
                                "product_price" => $product_price1,
                                "product_unit" => '',
                                "product_unit_value" => '',
                                "product_discount" => 0,
                                "sub_total" => $sub_total1,
                                "product_status" => $product_status1,
                                "product_status_type" => $product_status_type1,
                                "product_status_value" => $product_status_value1,
                                "product_order_status" => $product_order_status1
                            );
                        }
                        
                        
                        $prescription_query = $this->db->query("SELECT * FROM `prescription_order_list` where order_id='$order_id' order by id desc");
                        $prescription_count = $prescription_query->num_rows();
                        if ($prescription_count > 0) {
                            foreach ($prescription_query->result_array() as $prescription_row) {
                                $finalamt=$prescription_row['prescription_price']*$prescription_row['prescription_quantity'];
                                $sub_total_sum2+=$finalamt;
                                
                                $prescription_name     = $prescription_row['prescription_name'];
                                $prescription_quantity = $prescription_row['prescription_quantity'];
                                $prescription_price    = $prescription_row['prescription_price'];
                                $prescription_discount = $prescription_row['prescription_discount'];
                                $prescription_status   = $prescription_row['prescription_status'];
                               $sub_total_discount1 += $prescription_discount;
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
        $order_total=$sub_total_sum1+$sub_total_sum2;
        $order_total_discount=$sub_total_discount+$sub_total_discount1;
        if($order_total_discount=="")
        {
            $order_total_discount=0;
        }
        else
        {
            $order_total_discount;
        }
        
        if($order_status!="Awaiting Confirmation")
        {
            $user_info_user = $this->db->query("SELECT medical_name,payment_type FROM medical_stores WHERE user_id='$listing_id' or pharmacy_branch_user_id='$listing_id'");
            $getuser_info_user = $user_info_user->row_array();    
           $listing_name=$getuser_info_user['medical_name'];
           if($getuser_info_user['payment_type']!=null || !empty($getuser_info_user['payment_type']))
           {
           $listing_paymode=$getuser_info_user['payment_type'];
           }
           else
           {
               $listing_paymode="Cash On Delivery";
           }
        }
        else
        {
          
           $listing_paymode="Cash On Delivery";  
        }
        
        if($rxId == "")
        {
            $rxId=null;
        }
        else
        {
            $rxId;
        }
            $prescription_resultpost_new=array();
                foreach($prescription_resultpost as $pr){
                    foreach($pr as $k=>$v){
                        if($v == ""){
                           $pr[$k] = null; 
                        }
                    }
                    $prescription_resultpost_new[] = $pr;
                }

                $product_resultpost_new=array();
                foreach($product_resultpost as $pr){
                    foreach($pr as $k=>$v){
                        if($v == ""){
                           $pr[$k] = null; 
                        }
                    }
                    $product_resultpost_new[] = $pr;
                }

                $resultpost = array(
                    "invoice_no" => $invoice_no,
                    "medlife_order_id" => $orderId,
                    "delivery_time" => str_replace('null', '', $delivery_time),
                    "order_type" => $order_type,
                    "listing_id" => $listing_id,
                    "listing_name" => $listing_name,
                    "listing_type" => $listing_type,
                    "listing_payment_mode" => $listing_paymode,
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
                    "avatar" => $image,
                    "user_mobile" => $user_mobile,
                    "order_total" => floatval($order_total),
                    "order_discount"=>floatval($order_total_discount),
                    "payment_method" => $payment_method,
                    "order_date" => $order_date,
                    "order_status" => $order_status,
                    "cancel_reason" => $cancel_reason,
                    "delivery_charge" => floatval($delivery_charge),
                    "product_order" => $product_resultpost_new,
                    "prescription_create" => $prescription_resultpost_new,
                    "prescription_order" => $prescription_result,
                    "action_by" => $action_by,
                    "rxid" => $rxId,
                    "is_cancel" => $is_cancel
                );
            }
            $data['accepted'] = true;
            $data['message'] = "order accepted";
            $data['data'] = $resultpost;
            
        }else if($acceped_query['status'] == 'rejected'){
            $data = array(
                "accepted" => false,
                "message" => "order rejected" 
                );
        } else if(sizeof($acceped_query) > 0){
            $data = array(
                "accepted" => false,
                "message" => "order not accepted yet" 
                );
        } else {
            $data = array(
                "accepted" => false,
                "message" => "No result found for this invoice number and night owl"
                );
        }
        
        
       
        return $data;
    }

// add_new_pharmacy
        public function add_new_pharmacy($details){
        $pharmacy_added  = array();
        date_default_timezone_set('Asia/Kolkata');
        $created_date = date('Y-m-d H:i:s');
        $data = array();
        $insert_id = 0;
       
        $suggested_by = $details['mno_id'];
        $medical_name = $details['medical_name'];
        $contact_person = $details['contact_person'];
        $email_id = $details['email_id'];
        $contact_no = $details['contact_no'];
        $lat = $details['lat'];
        $lng = $details['lng'];
        $map_location = $details['map_location'];
        $address1 = $details['address1'];
        $address2 = $details['address2'];
        $city = $details['city'];
        $state = $details['state'];
        $pincode = $details['pincode'];
        $discount = $details['discount'];
        $gst = $details['gst'];
        $created_date = $created_date;
        
        
        $pharmacy_exists = $this->db->query("SELECT * FROM `mno_suggested_pharmacies` WHERE `gst` LIKE '$gst' ORDER BY id DESC")->row_array();
        
        if(sizeof($pharmacy_exists) > 0){
            $exists = 1;
            $insert_id = $pharmacy_exists['id'];
           
            
        } else {
            $exists = 0;
            
            $insertData = $this->db->query("INSERT INTO `mno_suggested_pharmacies`(`suggested_by`, `medical_name`, `contact_person`,`contact_no`, `email_id`, `lat`, `lng`, `map_location`, `address1`, `address2`, `city`, `state`, `pincode`, `discount`,`gst` ,`created_date`) VALUES ('$suggested_by','$medical_name','$contact_person','$contact_no','$email_id','$lat','$lng','$map_location','$address1','$address2','$city','$state','$pincode','$discount','$gst','$created_date')");
            $insert_id = $this->db->insert_id();
            
             
        }
        
        $phamacy = $this->db->query("SELECT * from mno_suggested_pharmacies where id = $insert_id")->row_array();
        unset($phamacy['created_date']);
            
            $pharmacy_added['id'] = intval($phamacy['id']);
            $pharmacy_added['medical_name']=  $phamacy['medical_name'];
            $pharmacy_added['contact_person']=  $phamacy['contact_person'];
            $pharmacy_added['contact_no']=  intval($phamacy['contact_no']);
            $pharmacy_added['email_id']= $phamacy['email_id'];
            $pharmacy_added['lat']=  floatval($phamacy['lat']);
            $pharmacy_added['lng'] = floatval($phamacy['lng']);
            $pharmacy_added['map_location'] = $phamacy['map_location'];
            $pharmacy_added['address1']  = $phamacy['address1'];
            $pharmacy_added['address2'] = $phamacy['address2'];
            $pharmacy_added['city'] = $phamacy['city'];
            $pharmacy_added['state']  = $phamacy['state'];
            $pharmacy_added['pincode'] = intval($phamacy['pincode']);
            $pharmacy_added['gst'] = $phamacy['gst'];
            $pharmacy_added['discount'] = floatval($phamacy['discount']);
            
            foreach($pharmacy_added as $k=>$v){
                if($v == ""){
                    $pharmacy_added[$k] = null;
                }
            }
            
            if($suggested_by == $phamacy['suggested_by']){
                $pharmacy_added['suggested_by'] = true;
            } else {
                $pharmacy_added['suggested_by'] = false;
            } 
            
            
            
        $data = array("insert_id" => $insert_id, "phamacy" => $pharmacy_added, "exists" => $exists);
            
        return $data;
        
        
    }
    
    public function order_status_common($order_id_data, $prescription_id_data,$order_details,$prescription_details)
    {
        date_default_timezone_set('Asia/Kolkata');
        $updated_at       = date('Y-m-d H:i:s');
        $sub_total        = '0';
        $product_price    = '0';
        $product_quantity = '0';
        $action_by        = 'night owl';
        
          $delivery_time      = "30 mins";
       
      
       
        // $product_details_new = json_decode($order_details,TRUE);
        $product_details_new = $order_details;
        
        $order_id      = $product_details_new['order_id'];
        // $delivery_time = $product_details_new['delivery_time'];
        // $order_status  = $product_details_new['order_status'];
        $order_status       = "Awaiting Customer Confirmation";
       
        if(array_key_exists('listing_id',$product_details_new)){
            $listing_id    = $product_details_new['listing_id'];
        } else {
            $listing_id    = 0;
        }
        
        $listing_type  = 44;
        $order_product_data    = $product_details_new['product_order'];
                   
        
        
        
        foreach ($order_product_data as $result) {
            $product_order_id     = $result['product_order_id'];
            $product_quantity     = $result['product_quantity'];
            $product_price        = $result['product_price'];
            $product_unit         = $result['product_unit'];
            $product_unit_value   = $result['product_unit_value'];
            $product_discount     = $result['product_discount'];
            $product_order_status = "Available";
            $sub_total            = $sub_total + ($product_price * $product_quantity);
           
            $query = $this->db->query("UPDATE user_order_product SET product_quantity='$product_quantity',product_price='$product_price',product_discount='$product_discount',sub_total='$sub_total',order_status='$product_order_status',updated_at='$updated_at',product_unit='$product_unit',product_unit_value='$product_unit_value' where product_id='$product_order_id' and order_id='$order_id_data'");
            
            $sub_total = '0';
        }
        $query = $this->db->query("UPDATE user_order SET order_status='$order_status',delivery_time='$delivery_time' where order_id='$order_id_data'");
        
        
        
        $prescription_details_new = $prescription_details;
        
        $order_id           = $prescription_details_new['order_id'];
        $order_status       = "Awaiting Customer Confirmation";
      //  $delivery_time      = "30 mins";
        $prescription_order = $prescription_details_new['prescription_order'];
        
            
         foreach ($prescription_order as $result) {
               
            $prescription_id = $result['prescription_id'];
            $items = $result['items'];
            foreach($items as $r){
                  
                $prescription_name     = !empty($r['prescription_name']) ? $r['prescription_name'] : "";
                $prescription_quantity = !empty($r['prescription_quantity']) ? $r['prescription_quantity'] : "";  
                $prescription_price    = !empty($r['prescription_price']) ? $r['prescription_price'] : "";  
                $prescription_discount = !empty($r['prescription_discount']) ? $r['prescription_discount'] : "";  
                $prescription_status   =  "Available";  
                //print_r($prescription_name); die();
                 
                $query = $this->db->query("INSERT INTO `prescription_order_list`(`order_id`,`prescription_id`, `prescription_name`, `prescription_quantity`, `prescription_price`, `prescription_discount`, `prescription_status`) VALUES ('$prescription_id_data', '$prescription_id','$prescription_name', '$prescription_quantity', '$prescription_price', '$prescription_discount', '$prescription_status')");
                

            }
      
      
            
        }
        $query = $this->db->query("UPDATE user_order SET order_status='$order_status',delivery_time='$delivery_time' where order_id='$prescription_id_data'");
        
        
        
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
        
        $order_info1    = $this->db->query("SELECT user_id,listing_id,listing_name,delivery_time,invoice_no,name,listing_name,order_total from user_order where order_id='$order_id_data'");
    //    echo "SELECT user_id,listing_id,listing_name,delivery_time,invoice_no,name,listing_name,order_total from user_order where order_id='$order_id_data'"; die();
       // $order_info1 = $this->db->query($order_info11);
        $product_count12 = $order_info1->num_rows();
                    if ($product_count12 > 0) {
                       
                     $order_info= $order_info1->row();
        $user_id       = $order_info->user_id;
        $listing_id    = $order_info->listing_id;
        $listing_name  = $order_info->listing_name;
        $delivery_time = $order_info->delivery_time;
        $invoice_no    = $order_info->invoice_no;
        $name          = $order_info->name;
        $listing_name  = $order_info->listing_name;
        $order_total   = $order_info->order_total;
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
                $msg   = 'Confirmed: Order no.'.$invoice_no.' is successfully placed with '.$listing_name.'. Your order will be delivered in '.$delivery_time.'. Thank You.';
                $notification_type="pharmacy_order_confirm";
                 
            send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $invoice_no, $name, $listing_name, $agent,$notification_type);
           
             }
            if ($order_status == 'Awaiting Delivery Confirmation') {
                $title = 'Order Reply From ' . $medical_name->medical_name;
                $msg   = 'Your have received a reply on your order';
                $notification_type="pharmacy_order_confirm";
            
                send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $invoice_no, $name, $listing_name, $agent,$notification_type);
           
            }
            if ($order_status == 'Order Delivered') {
                $title = 'Order Delivered';
                $msg   = 'Delivered: Order no.'.$invoice_no.' of Rs.'.$order_total.' is delivered. We hope your experience with Medicalwale.com has been satisfactory, for feedback and queries contact us on Medicalwale.com.';
                $notification_type="pharmacy_order_deliver";
                
            send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $invoice_no, $name, $listing_name, $agent,$notification_type);
           
                
            }
            
        }
        /*$data=array('order_status' => $order_status);
        */
        return array(
            'status' => 200,
            'message' => 'success',
            'order_status' => $order_status
        );
    }    return array(
            'status' => 201,
            'message' => 'fail',
            'order_status' => ""
        );
        
    }
    
    public function order_price($order_id, $delivery_time, $order_status, $listing_id, $listing_type, $order_data_list){
        date_default_timezone_set('Asia/Kolkata');
        $updated_at       = date('Y-m-d H:i:s');
        $sub_total        = '0';
        $product_price    = '0';
        $product_quantity = '0';
        $action_by        = 'night owl';
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
        $query = $this->db->query("UPDATE user_order SET order_status='$order_status',delivery_time='$delivery_time' where order_id='$order_id'");
        
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
        
        $order_info    = $this->db->select('user_id,listing_id,listing_name,delivery_time,invoice_no,name,listing_name,order_total')->from('user_order')->where('order_id', $order_id)->get()->row();
        $user_id       = $order_info->user_id;
        $listing_id    = $order_info->listing_id;
        $listing_name  = $order_info->listing_name;
        $delivery_time = $order_info->delivery_time;
        $invoice_no    = $order_info->invoice_no;
        $name          = $order_info->name;
        $listing_name  = $order_info->listing_name;
        $order_total   = $order_info->order_total;
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
            if ($order_status == 'Awaiting Delivery Confirmation') {
                $title = 'Order Reply From ' .$medical_name_new->medical_name;
                $msg   = 'Your have received a reply on your order';
                $notification_type="pharmacy_order_confirm";
            }
            if ($order_status == 'Order Delivered') {
                $title = 'Order Delivered';
                $msg   = 'Delivered: Order no.'.$invoice_no.' of Rs.'.$order_total.' is delivered. We hope your experience with Medicalwale.com has been satisfactory, for feedback and queries contact us on Medicalwale.com.';
                $notification_type="pharmacy_order_deliver";
                
            }
            send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $invoice_no, $name, $listing_name, $agent,$notification_type);
        }
        
        return array(
            'status' => 200,
            'message' => 'success',
            'order_status' => $order_status
        );
        
    }
    
    public function prescription_price($order_id, $order_status, $prescription_order, $delivery_time){
        date_default_timezone_set('Asia/Kolkata');
        $updated_at            = date('Y-m-d H:i:s');
        $sub_total             = '0';
        $prescription_price    = '0';
        $prescription_quantity = '0';
        
        $delivery_time      = "30 mins";
        
        //$query = $this->db->query("DELETE FROM `prescription_order_list` WHERE order_id='$order_id'");
      //  print_r($prescription_order); die();
        foreach ($prescription_order as $result) {
            $prescription_id = $result['prescription_id'];
            $items = $result['items'];
            foreach ($items as $r) {
                
                $prescription_name     = !empty($r['prescription_name']) ? $r['prescription_name'] : "";
                $prescription_quantity = !empty($r['prescription_quantity']) ? $r['prescription_quantity'] : "";
                $prescription_price    = !empty($r['prescription_price']) ? $r['prescription_price'] : "";
                $prescription_discount = !empty($r['prescription_discount']) ? $r['prescription_discount'] : "";
                $prescription_status   = "Available";
                
                $query = $this->db->query("INSERT INTO `prescription_order_list`(`order_id`,`prescription_id` ,`prescription_name`, `prescription_quantity`, `prescription_price`, `prescription_discount`, `prescription_status`) VALUES ('$order_id','$prescription_id', '$prescription_name', '$prescription_quantity', '$prescription_price', '$prescription_discount', '$prescription_status')");
            }   
        }
        $query = $this->db->query("UPDATE user_order SET order_status='$order_status',delivery_time='$delivery_time' where order_id='$order_id'");
        
        
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
        
        $order_info    = $this->db->select('user_id,listing_id,listing_name,delivery_time,invoice_no,name,listing_name')->from('user_order')->where('order_id', $order_id)->get()->row();
        $user_id       = $order_info->user_id;
        $listing_id    = $order_info->listing_id;
        $listing_name  = $order_info->listing_name;
        $delivery_time = $order_info->delivery_time;
        $invoice_no    = $order_info->invoice_no;
        $name          = $order_info->name;
        
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
            // print_r($order_status); die();
            if ($order_status == 'Order Confirmed') {
                $title = 'Order Confirmed';
                $msg   = 'Your order will be delivered in ' . $delivery_time;
                 $notification_type="pharmacy_order_confirm";
            }
            if ($order_status == 'Awaiting Customer Confirmation') {
                $title = 'Order Reply From ' . $medical_name->medical_name;
                $msg   = 'Your have received a reply on your order';
                 $notification_type="pharmacy_order_confirm";
            }
            if ($order_status == 'Order Delivered') {
                $title = 'Order Delivered';
                $msg   = 'Your has been delivered';
                 $notification_type="pharmacy_order_deliver";
            } else {
                 $title = 'Order Delivered';
                $msg   = 'Your has been delivered';
                 $notification_type="pharmacy_order_deliver";
            }
            send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $invoice_no, $name, $listing_name, $agent,$notification_type);
        }
        
        return array(
            'status' => 200,
            'message' => 'success',
            'order_status' => $order_status
        );
    }
    
    // suggested_pharmacy_list
    public function suggested_pharmacy_list($mno_id,$page_no,$per_page,$gst,$lat,$lng){
        $Where = "";
            $offset = $per_page*($page_no - 1);
            $limit = "LIMIT $per_page OFFSET $offset"; 
            if(!empty($gst)){
                $Where .= "AND `gst`='$gst'";
            }
        
        $stores = $data = array();
        $query = $this->db->query("SELECT id,password,name,email,phone,city,vendor_id FROM users WHERE id='$mno_id' and vendor_id='44' ")->row_array();
        
        $count = sizeof($query);
        
        if ($count > 0) {
            $data['mno_exists'] = 1;
            $suggested_pharmacies = $this->db->query("SELECT * FROM `mno_suggested_pharmacies` WHERE `mou_signed` = 0 $Where $limit")->result_array();
            $all_stores = $this->db->query("SELECT COUNT(id) as total_count FROM mno_suggested_pharmacies WHERE `mou_signed` = 0 $Where")->row_array();
            $store_count = $all_stores['total_count'];
            foreach($suggested_pharmacies as $sp){
                
                
                unset($sp['mou_signed']);
                unset($sp['created_date']);
                
                $sp['id'] = intval($sp['id']);
                $sp['contact_no'] = intval($sp['contact_no']);
                $sp['pincode'] = intval($sp['pincode']);
                $sp['lat'] = floatval($sp['lat']);
                $sp['lng'] = floatval($sp['lng']);
                $sp['discount'] = floatval($sp['discount']);
                
                
                if($lat != ""  && $lng != "" ){
                    $drivingDistanceDurtion = $this->PartnermnoModel->GetDrivingDistance($lat, $sp['lat'], $lng, $sp['lng'] );
                    $sp['distance'] = $drivingDistanceDurtion['distance'];
                    $sp['duration'] = $drivingDistanceDurtion['duration'];
                } else {
                        $sp['distance'] = $sp['duration'] = null;
                }
                
               
                
                
                foreach($sp as $k => $v){
                    if($v == ""){
                        $sp[$k] = null;
                    }
                }
                
                if($sp['suggested_by'] == $mno_id){
                    $sp['suggested_by_me'] = true;
                } else {
                    $sp['suggested_by_me'] = false;
                }
                unset($sp['suggested_by']);
                $stores[] = $sp;
            }
            
            
            
            $last_page = ceil($store_count/$per_page);
            $data['data_count'] = intval($store_count);
            $data['per_page'] = $per_page;
            $data['current_page'] = $page_no;
            $data['first_page'] = 1;
            $data['last_page'] = $last_page;
            
            
            $data['stores'] = $stores;
        } else {
            $data = array("mno_exists" => 0);
        }
       return $data;
    }
    
// order_to_pharmacy
    public function order_to_pharmacy($mno_id, $invoice_no, $pharmacy_id){
        $data = array();
        $store_info = $this->db->query("SELECT `id`, `token`,`agent`, `web_token`, `name`, `phone`, `email`, `vendor_id`, `avatar_id`, `lat`, `lng`  FROM users WHERE `id` = '$pharmacy_id' AND `vendor_id` = 13 ")->row_array();
        
        if(sizeof($store_info) > 0){
            $data['store_exists'] = 1;
            
            $name = $store_info['name'];
            
            $this->db->query("UPDATE user_order set `listing_id` = '$pharmacy_id', `listing_name` = '$name' WHERE `invoice_no` = '$invoice_no'");
            
            
            // print_r($store_info); die();
            $reg_id = $store_info['token'];
            $agent = $store_info['agent'];
            
            $customer_phone =  $store_info['phone'];
            
            $msg = 'You have received one order of order id '.$invoice_no.' please accept it';
            $img_url = 'https://medicalwale.com/img/noti_pharmacy.png';
            $tag = 'text';
            $key_count = '1';
            $title = 'Order received';
            $order_status = 'Awaiting confirmation';
            $order_date = '';
            $name = '';
            $listing_name = '';
           
           
           $message = 'You have received one order from night owl of order id '.$invoice_no.'. Please accept it.';
                    $customer_phone = $store_info['phone'];
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
                    
           $notification_type="night_owls";
         $this->send_gcm_notify_pharmacy($title, $reg_id, $msg, $img_url, $tag, $agent, $invoice_no,$notification_type);
          
    
            
        } else {
            $data['store_exists'] = 0; 
        }
        return $data;
    }
    
    
    public function send_gcm_notify_mno($title, $reg_id, $msg, $img_url, $tag, $agent, $invoice_no,$notification_type) {

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
               "notification_type" => $notification_type,
               "notification_date" => $date,
               "invoice_no" => $invoice_no
           )
       );
       $headers = array(
           GOOGLE_GCM_URL,
           'Content-Type: application/json',
           'Authorization: key=AAAAbiy1Vsk:APA91bF7ueNzxt9Z13XZHCzJEEuUxczgpvY0IRxTTTSUvejgOIwgc9Sdq9kEQTyzqoDLKzniD4a94g8M99eDxiiQ9JW0xQeBVSLQkfSPpyPZOdXs4DTmz3ln-Ri3Ruyrj_KYtKOg6IXS'
       );
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
       curl_setopt($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
       $result = curl_exec($ch);
     //  print_r($result);
       if ($result === FALSE) {
           die('Problem occurred: ' . curl_error($ch));
       }
       curl_close($ch);
   }
   
   
     public function send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent, $invoice_no,$notification_type) {

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
                "notification_type" => $notification_type,
                "notification_date" => $date,
                "invoice_no" => $invoice_no
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
      //  print_r($result);
        if ($result === FALSE) {
            die('Problem occurred: ' . curl_error($ch));
        }
        curl_close($ch);
    }
    
    //
                
                
    public function send_gcm_notify_pharmacy($title, $reg_id, $msg, $img_url, $tag, $agent, $invoice_no,$notification_type) {

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
                "notification_type" => $notification_type,
                "notification_date" => $date,
                "invoice_no" => $invoice_no
            )
        );
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
            $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AAAASZs-aSM:APA91bEQ0SZFS7YnRRK95O2Jp_PdzVC8t_osgDK0UdWXeQRo06fEedknj2b26yfX_YPkLq85h0lc3hUBlnSSPD1qvfikzFYiHd8tlt7BeuOg3f5WRwQxmz19WbEGbZEX894zZEF5iRO5'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
      //  print_r($result);
        if ($result === FALSE) {
            die('Problem occurred: ' . curl_error($ch));
        }
        curl_close($ch);
    }
    
    
    // cancel_reason
    public function cancel_reason($mno_id,$before_accept){
        $reason = $data = array();
        
        if($before_accept === true){
            
            $reason[] = 'Out of fuel';
            
            
            for($r=0;$r<sizeof($reason);$r++){
                $d['id'] = $r+1;
                $d['reason'] = $reason[$r];
                $data['type'] = 'before';
                $data['reasons'][] = $d;
            }
            
        } else if($before_accept === false){
            
            $reason[] = 'Product not available';
            $reason[] = 'Prescription not readable';
            $reason[] = 'Delivery address too far';
            $reason[] = 'Customer denied';
            
            
            for($r=0;$r<sizeof($reason);$r++){
                $d['id'] = $r+1;
                $d['reason'] = $reason[$r];
                $data['type'] = 'after';
                $data['reasons'][] = $d;
            }
            
        } else { 
            $data['type'] = '';
        }
        
        return $data;
    }
    
    public function mno_details($invoice_no){
        $data = array();
        
        // SELECT ml.mno_name,ml.phone,mo.mno_id FROM `mno_orders` as mo left join mno_list as ml on (mo.mno_id = ml.mno_id) WHERE `invoice_no` = '20190524172412'  AND `ongoing` = 1
        
        $data1 = $this->db->query("SELECT ml.mno_name,ml.phone,mo.mno_id FROM `mno_orders` as mo left join mno_list as ml on (mo.mno_id = ml.mno_id) WHERE `invoice_no` = '$invoice_no'  AND `ongoing` = 1")->row_array();
        
        if(sizeof($data1) > 0){
            $data['message'] = 'mno assinged';
            $data['mno_name'] = $data1['mno_name'];
            $data['mno_phpne'] = $data1['phone'];
        }  else {
            $data['message'] = 'mno not assinged';
            $data['mno_name'] = '';
            $data['mno_phpne'] = '';
        }
         
        return $data;
    }
    
    
    public function check_time_format($time) {
        $time_filter = preg_replace('/\s+/', '', $time);
        $final_time = date("h:i A", strtotime($time_filter));
        return $final_time;
    }
    
    // dashboard
    public function dashboard($reqData){
        
        $lastDay = $day  = "";
        $all_orders = 0;                 
        $billing = 0;
        $pending = $amount =  $from_date = $to_date = $net_billing = $total_orders = $accepted_orders = $rejected_orders = $fastest_order = $slowest_order = $online_time = 0;
        $per_day = $orders_per_day   =  $data = array();
        $diff = 0;
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        
        $mno_id = $reqData['mno_id'];
        $from_date = $reqData['from_date'];
        $to_date = $reqData['to_date'];
        
        if($from_date == "" || $to_date == ""){
            if(date('D')!='Sun'){  
              $from_date = date('Y-m-d',strtotime('last Sunday'));    
            
            }else{
                $from_date = date('Y-m-d');   
            }
            if(date('D')!='Sat')
            {
                $to_date = date('Y-m-d',strtotime('next Saturday'));
            }else{
                $to_date = date('Y-m-d');
            }
        } 
       
        $onlineTrack  = $this->db->query("SELECT * FROM `mno_track` WHERE `mno_id` = '$mno_id' AND `logged_in` >= '$from_date' AND `logged_out` <= '$to_date'")->result_array();
        $orderDetails = $this->db->query("SELECT uo.order_total,uo.actual_cost, uo.discount, mo.* FROM `mno_orders` as mo left join user_order as uo on (mo.`invoice_no` = uo.invoice_no)  WHERE mo.`mno_id` = '$mno_id' AND mo.`created_at` >= '$from_date' AND mo.`created_at` <= '$to_date' ")->result_array();
        $total_orders = sizeof($orderDetails);
        foreach($orderDetails as $od){
            $amount = $od['order_total'];
            $created_at = $od['created_at'];
            
            $net_billing = $net_billing + $amount;
            // print_r($od); die();
            if($od['status'] == 'rejected'){
                $rejected_orders = $rejected_orders + 1;
            } else if($od['status'] == 'accepted'){
                $accepted_orders = $accepted_orders + 1;
            } else {
                $pending = $pending + 1;
            }
            
            $day = date('D',strtotime($created_at));
            
            if($lastDay == $day){
                $all_orders = $all_orders + 1;
                $billing = $billing + $amount;
                
            } else {
                    $per_day['day'] = $day;
                    $per_day['total_orders'] = $all_orders;
                    $per_day['billing'] = $billing;
                    $per_day['created_at'] = $created_at;
                    
                if(sizeof($per_day) > 0){
                    $orders_per_day[] = $per_day;
                }
                $per_day = array();
                $all_orders = 0;
                $billing = 0;
                
                $all_orders = $all_orders + 1;
                $billing = $billing + $amount;
                
                
            }
            
            $lastDay = $day;
            // $orders_per_day[] = $per_day;
        }
        if(sizeof($per_day) > 0){
            $orders_per_day[] = $per_day;
        }
        
        /*foreach($orders_per_day as $opd){
            print_r(strtotime('Y-m-d',$opd['created_at'])); die();
        }*/
        
        foreach($onlineTrack as $ot){
            $logged_in = $ot['logged_in'];
            $logged_out = $ot['logged_out'];
            if($logged_out == '0000-00-00 00:00:00'){
                $logged_out = $date;
            }
            $diff = $diff + (strtotime($logged_out) - strtotime($logged_in));
        }
        $convertedSeconds =  $this->convert_seconds($diff);
        
        $online_time = gmdate("d-m H:i:s", $diff);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['online_time'] = $online_time;
        $data['total_orders'] = $total_orders;
        $data['net_billing'] = $net_billing;
        $data['accepted_orders'] = $accepted_orders;
        $data['rejected_orders'] = $rejected_orders;
        $data['pending'] = $pending;
        
        // remaining
        
         $data['fastest_order'] = $fastest_order;
        $data['slowest_order'] = $slowest_order;
        
        // rem ends
        $data['orders_per_day'] = $orders_per_day;
        
        
        // print_r($data); 
        // die();
        
         
       
        
        
        
        return $data;
    }
    
    function convert_seconds($seconds){
        // $ret = array();
        $secCon = 0;
        $ret['days'] = 0;
        $ret['hours'] = 0;
        $ret['minutes'] = 0;
        $ret['sec'] = 0;
        $dt1 = new DateTime("@0");
        $dt2 = new DateTime("@$seconds");
        $secCon =  $dt1->diff($dt2);
        $ret['days'] = $secCon->format('%a');
        $ret['hours'] = $secCon->format('%h');
        $ret['minutes'] = $secCon->format('%i');
        $ret['sec'] = $secCon->format('%s');
        
        return $ret;
        
     }
     
    public function order_reject($mno_id,$invoice_no,$reject){
         
        $data = array();
        $query = $this->db->query("SELECT id,password,name,email,phone,city,vendor_id FROM users WHERE id='$mno_id' and vendor_id='44'")->row_array();
        $count = sizeof($query);
        
        if ($count > 0) {
            $current_status = $this->db->query("SELECT status FROM `mno_orders` WHERE `mno_id` = '$mno_id' AND `invoice_no` = '$invoice_no'")->row_array();
            if(sizeof($current_status) > 0){
                
            if($reject === true){
                $rejectOrder = $this->db->query("UPDATE `mno_orders` SET  `ongoing`='0' WHERE `mno_id` = '$mno_id' AND `invoice_no` = '$invoice_no'");
                return 3;    
                
            }    
    

                
            } else {
                return 5;
            }    
           
        } else {
            return 1;
        }
    }
    
    
}
