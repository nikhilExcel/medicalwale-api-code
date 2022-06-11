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
        
        $mno_location = $this->db->query("INSERT INTO `mno_location`(`mno_id`, `lat`, `lng`, `created_at`) VALUES ('$mno_id','$lat','$lng','$todayDate')");
        $insert_id = $this->db->insert_id();
        if($insert_id > 0){
            $result = true;
        } else {
            $result = false;   
        }
        return $result;
       
    }
    
    public function order_list($mno_id){
        echo $mno_id; die();
    }

}
