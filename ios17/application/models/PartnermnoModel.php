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
    
 /*   public function auth() {
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
*/
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

    public function login($phone, $token, $agent, $password, $for_excecutor) {
        $data = array();
        // $encr = '827ccb0eea8a706c4c34a16891f84e7b';
        $given_pass = md5($password);
        
        if($for_excecutor == 1){
            $query = $this->db->query("SELECT u.id,u.password,u.name,u.email,u.phone,u.city,u.vendor_id,u.avatar_id,m.source FROM users as u left join media as m on (u.avatar_id = m.id) left join mno_executors as me on (u.id = me.user_id) WHERE u.phone='$phone' and u.vendor_id='44'")->row_array();
        } else {
            $query = $this->db->query("SELECT u.id,u.password,u.name,u.email,u.phone,u.city,u.vendor_id,u.avatar_id,m.source FROM users as u left join media as m on (u.avatar_id = m.id) WHERE u.phone='$phone' and u.vendor_id='44'")->row_array();    
        }
        
        
        $count = sizeof($query);
        
        
        
        if ($count > 0) {
            
            $user_password = $query['password'];
             $img_file = $query['source'];
               if(!empty($img_file)){
                    $user_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $user_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
            
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
                $data['user_image'] = $user_image;
                
                
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
                        
                         $getMnoLocation = $this->db->query("SELECT * FROM `mno_location` WHERE `mno_id` = '$mno_id' ORDER BY `created_at` DESC ")->row_array();
                            $mno_lat = $getMnoLocation['lat'];
                            $mno_lng = $getMnoLocation['lng'];
                        
                        $assignAnotherOrder = $this->PartnermnoModel->assign_pending_order_to_mno($mno_id, $mno_lat, $mno_lng);
                        
                        return 1;
                        
                        // assign new order if no order is online
                        
                       
                        
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
    
    public function pharmacy_list($mno_id, $lat, $lng, $page_no, $per_page,$pharmacy_id,$search){
        //  echo $pharmacy_id; die();
     //   echo $search; die();
     
      date_default_timezone_set('Asia/Calcutta');
        $insert_id = 0;
        $todayDate = date('Y-m-d H:i:s');
        $todayDay = date('l');
        $currenttime = date('h:i A');
        $current_time = date('h : i A');
       
       
       
     $pharmacy_branch_user_id = "";
        if($per_page != 0 && $page_no != 0){
            $offset = $per_page*($page_no - 1);
            $limit = "LIMIT $per_page OFFSET $offset"; 
        } else {
            $per_page = 10;
            $page_no = 1;
            $offset = $per_page*($page_no - 1);
            $limit = "LIMIT $per_page OFFSET $offset"; 
        }
        if($search != ""){
            $searchWhere = "AND (`medical_name` LIKE '%".$search."%' or `map_location`Like '%$search%' or `address1` Like '%$search%' or `address2` Like '%$search%' or `city` Like '%$search%' or `state` Like '%$search%')";
       
            
            
        } else {
            $searchWhere = "";
        }
     

   //  echo $searchWhere; die();
        // $pharmacy_id = 38292;
        if($pharmacy_id != ""){
            $parmacyWhere = "AND (`user_id` = '$pharmacy_id'  AND (`pharmacy_branch_user_id` = 0 OR `pharmacy_branch_user_id` = null OR `pharmacy_branch_user_id` = '')) OR ((`user_id` != 0 OR `user_id` != null OR `user_id` != '') AND `pharmacy_branch_user_id` = '$pharmacy_id')";
            $days_closed_query = $store_close_query  = $store_open_query = "";
        } else {
            $parmacyWhere = "";
            $days_closed_query = "AND (`days_closed` Not Like '".$todayDay." Closed' && `days_closed` Not Like '".$todayDay." Half Day' )";
            $store_close_query = "AND (`store_close` > '$current_time' AND `store_close` > '$currenttime' or `store_close` = '')";
            $store_open_query = "AND (`store_open` <= '$current_time' AND `store_open` <= '$currenttime' or `store_open` = '')";
   
   
        }
        
        
        /*if($lat != "" or $lng != ""){
          $distancyWhere = ", ( 6371 * acos( cos( radians('$lat') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('$lng') ) + sin( radians('$lat') ) * sin( radians( lat ) ) ) ) AS distance" ;  
        } else {
           $distancyWhere = ", id as distance" ;
        }
        */
        
       
        $radius = 20;
        $start = 0;
        $data = $resultpost=array();
          $sql = "SELECT mba,recommended,certified,`id`,`store_time`, `user_id`,`pharmacy_branch_user_id`,`reach_area`, `medical_name`, `discount`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`,  `day_night_delivery`,  `days_closed`,  `is_24hrs_available`, `payment_type`, `store_open`, `store_close`,    ( 6371 * acos( cos( radians('$lat') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('$lng') ) + sin( radians('$lat') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE   is_active='1'    $days_closed_query  $store_close_query  $store_open_query  $parmacyWhere $searchWhere ORDER BY distance ASC  $limit";
          
          
    //   echo $sql; die();
       $all_pharmacies = $this->db->query("SELECT  ( 6371 * acos( cos( radians('$lat') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('$lng') ) + sin( radians('$lat') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_active <> '0'  $days_closed_query  $store_close_query  $store_open_query   $parmacyWhere $searchWhere ORDER BY distance ASC  ")->result_array();
    
        $pharmacy_count = sizeof($all_pharmacies);

        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if($count>0){
            foreach ($query->result_array() as $row) {
                $mlat = $row['lat'];
                $mlng = $row['lng'];
                $medical_user_id = $row['user_id'];
                $pharmacy_branch_user_id = $row['pharmacy_branch_user_id'];
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
                                } else if($time != "close"){
                                    
                                   
                                    $ot = explode("-",$time);
                                    // $open_till = $ot[1];
                                         $open_till = date('H:i', strtotime($ot[1]));
                                  
                                    
                                } else {
                                    $open_till = null;
                                  
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
                
               /* $medical_user_id
                $pharmacy_branch_user_id*/
                if($pharmacy_branch_user_id == null || $pharmacy_branch_user_id == 0 || $pharmacy_branch_user_id == "" ){
                    $medical_id = $medical_user_id;
                     $chat_id = $medical_id;
                } else {
                    $medical_id = $pharmacy_branch_user_id;
                     $chat_id = $medical_id;
                }
                    
                $resultpost[] = array(
                    'id' => intval($medical_id),
                    'medical_name' => $medical_name,
                    'listing_id' => intval($chat_id),
                    'listing_type' => 13,
                    'latitude' => floatval($mlat),
                    'longitude' => floatval($mlng),
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
   
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $originLat . "," . $originLng . "&destinations=" . $destLat . "," . $destLng . "&mode=driving&language=pl-PL&key=AIzaSyDg9YiklFpUhqkDXlwzuC9Cnip4U-78N60&callback=initMap";
        
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
        if($response_a['rows'][0]['elements'][0]['status'] != 'ZERO_RESULTS' && $response_a['rows'][0]['elements'][0]['status'] != 'NOT_FOUND'){
            // in meter
        //  print_r(); die();
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
    
    public function order_list($mno_id, $page_no, $per_page,$type){
      //  date_default_timezone_set('GMT');
        $allOrdersWithInv = array();
       $order_date1 = $order_date = null;
        $ongoing = false;
        $tracking_status = $typeWHere = "";
        if($type == 1){
            $typeWHere = "AND mo.status = 'accepted'";
        } else if($type == 2){
            $typeWHere = "AND mo.status = 'rejected'";
        } else if($type == 3){
            $typeWHere = "AND mo.ongoing = 1";
        } else {
            $typeWHere = "";
        }
        
        $orderData = $allOrdersWithInv = array();
         $oldInv = $inv = 0;
        // $per_page = 300;
        $offset = $per_page*($page_no - 1);
        $limit = "LIMIT $per_page OFFSET $offset"; 
        $data = array();
// echo "SELECT mo.id,  mo.status,mo.ongoing,uo.order_id,uo.order_type,uo.invoice_no ,uo.name, uo.user_id, u.id, u.name as user_name FROM `mno_orders` as mo left join user_order as uo on (uo.invoice_no = mo.invoice_no) left join users as u on (uo.user_id = u.id) WHERE `mno_id` = '$mno_id' $typeWHere AND uo.listing_type = 44 ORDER BY uo.invoice_no desc $limit"; die();
//        $allOrders = $this->db->query("SELECT mo.id as mno_order_id,mo.updated_at as last_updated,uo.order_date, uo.listing_name,uo.listing_type, mo.status,mo.ongoing,uo.order_id,uo.order_type,uo.invoice_no ,uo.name, uo.user_id, u.id, u.name as user_name FROM `mno_orders` as mo left join user_order as uo on (uo.invoice_no = mo.invoice_no) left join users as u on (uo.user_id = u.id) WHERE mo.`mno_id` = '$mno_id' $typeWHere AND (uo.listing_type = 44 OR uo.listing_type = 13) ORDER BY uo.invoice_no desc $limit")->result_array();
        $allOrders = $this->db->query("SELECT mo.id as mno_order_id,mo.updated_at as last_updated,uo.order_date, uo.listing_name,uo.listing_type, mo.status,mo.ongoing,uo.order_id,uo.order_type,uo.invoice_no ,uo.name, uo.user_id, u.id, u.name as user_name FROM `mno_orders` as mo left join user_order as uo on (uo.invoice_no = mo.invoice_no) left join users as u on (uo.user_id = u.id) WHERE mo.`mno_id` = '$mno_id' $typeWHere AND (uo.listing_type = 44 OR uo.listing_type = 13) ORDER BY mo.created_at desc $limit")->result_array();
        $allOrders_count = $this->db->query("SELECT mo.id FROM `mno_orders` as mo left join user_order as uo on (uo.invoice_no = mo.invoice_no) left join users as u on (uo.user_id = u.id) WHERE mo.`mno_id` = '$mno_id' AND (uo.listing_type = 44 OR uo.listing_type = 13) $typeWHere")->result_array();
        $orders_count = sizeof($allOrders_count);
       
      //  print_r($allOrders); die();
     
        foreach($allOrders as $o){
            $order_date1 = $o['order_date'];
            $order_date = strtotime($o['order_date']);
            $last_updated = $o['last_updated'];
          // send pharmacy name or siggested pharmacy name if order from pharmacy
        
        
          if($o['listing_type'] == 13){
            //   echo "SELECT mo.id as mno_order_id, uo.listing_type, mo.status,mo.ongoing,uo.order_id,uo.order_type,uo.invoice_no ,uo.name, uo.user_id, u.id, u.name as user_name FROM `mno_orders` as mo left join user_order as uo on (uo.invoice_no = mo.invoice_no) left join users as u on (uo.user_id = u.id) WHERE mo.`mno_id` = '$mno_id' $typeWHere AND (uo.listing_type = 44 OR uo.listing_type = 13) ORDER BY uo.invoice_no desc $limit" ; die();
              $pharmacy_name = $o['listing_name'];
          } else {
              $pharmacy_name = null;
          }
     
            $listing_type = intval($o['listing_type']); 
           // print_r($o); die();
           $invoice_no = $o['invoice_no'];
            $order_id = null;
            if( $o['ongoing'] == 1){
                $ongoing = true;
            } else {
                $ongoing = false;
                
            }
            
             if(empty($o['status'])){
                 $o['status'] = 'pending'; 
            } 
            $mno_order_id = $o['mno_order_id'];
            $tracking_status_by_notification = $this->PartnermnoModel->get_latest_msg($invoice_no,$mno_order_id);
            $tracking_status = $tracking_status_by_notification['title'];
            
           /* if(empty($o['status'])){
                 $o['status'] = 'pending'; 
                 $tracking_status = "Pending";
            } else if($o['status'] == 'accepted' && $o['ongoing'] == 1){
                $tracking_status = "On going";
            } else if($o['status'] == 'accepted' && $o['ongoing'] == 0 && !empty($o['cancel_reason_after_accept']) ){
                $tracking_status = "Cancelled";
            } else if($o['status'] == 'accepted' && $o['ongoing'] == 0){
                $tracking_status = "Completed";
            } 
            else if($o['status'] == 'rejected'){
                $tracking_status = "Rejected";
            } */
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
                
                $orderData['tracking_status'] = $tracking_status; 
                $orderData['listing_type'] = $listing_type;
                $orderData['order_date_epoch'] = $order_date;
                $orderData['order_date'] =$order_date1;
                $orderData['last_updated'] = $last_updated;
                
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
                
                // if(!empty($o['pharmacy_name'])){
                    $orderData['pharmacy_name'] = $pharmacy_name;
                // } 
                
                $order_type = $o['order_type']; 
                $orderData['status'] = $o['status'];
                
               
                    $orderData['tracking_status'] = $tracking_status; 
                  $orderData['listing_type'] = $listing_type;
                   $orderData['order_date_epoch'] = $order_date;
                    $orderData['order_date'] =$order_date1;
                    $orderData['last_updated'] = $last_updated;
                
                
                $orderData['is_ongoing'] = $ongoing;
                $order_id = intval($o['order_id']);
                 if($order_type == 'order'){
                    $orderData['order_id'] = $order_id;
                } else if($order_type == 'prescription'){
                    $orderData['prescription_id'] = $order_id;
                }
                
            }
            $oldInv = $in;
        }
        $key = -1;
        if(sizeof($orderData) > 0 && $orderData != 0){
            $allOrdersWithInv[] = $orderData; 
           // print_r($allOrdersWithInv); die();
                 // Comparision function 
      /*      function date_compare($element1, $element2) { 
                $datetime1 = strtotime($element1['last_updated']); 
                $datetime2 = strtotime($element2['last_updated']); 
                return $datetime2 - $datetime1; 
            }  
              
            usort($allOrdersWithInv, 'date_compare'); 
       */
      
        /*    $key = array_search(true, array_column($allOrdersWithInv, 'is_ongoing'));
            if($key > -1){
                $getOngoingOrder[] = $allOrdersWithInv[$key];
                unset($allOrdersWithInv[$key]);
                array_splice( $allOrdersWithInv, 0, 0, $getOngoingOrder );
                
            }*/
              
       
        }
        
        // print_r($orderData); die();
        $last_page = ceil($orders_count/$per_page);
        $data['data_count'] = intval($orders_count);
        $data['per_page'] = $per_page;
        $data['current_page'] = $page_no;
        $data['first_page'] = 1;
        $data['last_page'] = "$last_page";
        $data['orders'] = $allOrdersWithInv;
        // print_r($allOrdersWithInv); die();
        return $data;
    }
    
    public function order_accept_reject($mno_id,$invoice_no, $accepted, $cancel_reason_id,$mno_lat,$mno_lng){
        $this->load->model('OrderModel');
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
                        //  mno reject the order
                         
                        //  $cancel_reason_id
                        $cancelReasond = $this->PartnermnoModel->get_cancel_reason($cancel_reason_id); 
                   
                        if(sizeof($cancelReasond) > 0){
                            $cancel_reason = $cancelReasond['cancel_reason'];
                            
                            
                        $rejectOrder = $this->db->query("UPDATE `mno_orders` SET  `ongoing`='0',`status`='rejected',`cancel_reason`='$cancel_reason' WHERE `mno_id` = '$mno_id' AND `invoice_no` = '$invoice_no'");
                        
                        $existingOrder = $this->db->query("SELECT ml.mno_name as mno_name, mo.*, u.id as user_id, u.phone, u.token,  u.agent FROM `mno_orders` as mo left join user_order as uo on (mo.invoice_no = uo.invoice_no) left join users as u on (uo.user_id = u.id) left join mno_list as ml on (ml.mno_id = mo.mno_id) WHERE mo.`invoice_no` = '$invoice_no' AND mo.`mno_id` = '$mno_id' ")->row_array();
                        
                        $mno_name = $existingOrder['mno_name'];
                         
                        $action_by_status = "Night owl";
                        $orderStatus = "Order rejected by night owl ". $mno_name;
                        $order_status = $this->OrderModel->update_status($invoice_no,$orderStatus, $action_by_status);
                        
                        
                        $title = "Order rejected";
                        $reg_id = $existingOrder['token'];
                        $msg = "Night owl rejected your order, we will redirect your order to another night owl soon";
                        $img = $img_url   = 'https://medicalwale.com/img/noti_icon.png';
                        $tag = "text";
                        $agent = $existingOrder['agent'];
                        $invoice_no = $invoice_no;
                        $notification_type = 3;
                        $res = $this->PartnermnoModel->send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent, $invoice_no,$notification_type);
                         
                        $msg1 = "Order rejected successfully";
                        $mno_order_id = $existingOrder['id'];
                        $this->PartnermnoModel->send_gcm_notification_to_mno($mno_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg1);
                        
                        // assign another mno
                        $oderDeatils = $this->db->query("SELECT uo.`user_id` , uo.`lat` , uo.`lng`, u.lat as user_lat, u.lng as user_lng  FROM `user_order` as uo left join users as u on (uo.user_id = u.id) WHERE uo.`invoice_no` = '$invoice_no'")->row_array();
                        $user_id = $oderDeatils['user_id'];
                        $lat = $oderDeatils['lat'];
                        $lng = $oderDeatils['lng'];
                        if(empty($lat) || empty($lng)){
                            // users latlng
                            $lat = $oderDeatils['user_lat'];
                            $lng = $oderDeatils['user_lng'];
                        }
                        else if(empty($oderDeatils['user_lat']) || empty($oderDeatils['user_lng'])){
                            // default andheri
                            $lat = '19.1267157';
                            $lng = '72.8499786';
                        }
                        
                    if($mno_lat == "" || $mno_lng == ""){
                        $getMnoLocation = $this->db->query("SELECT * FROM `mno_location` WHERE `mno_id` = '$mno_id' ORDER BY `created_at` DESC ")->row_array();
                        $mno_lat = $getMnoLocation['lat'];
                        $mno_lng = $getMnoLocation['lng'];
                    }    
                    
                    $assignAnotherOrder = $this->PartnermnoModel->assign_pending_order_to_mno($mno_id, $mno_lat, $mno_lng);
                    
                    
                        $this->PartnermnoModel->assign_mno($user_id,$invoice_no,$lat,$lng);
                        return 3;
                      
                        } else {
                            $cancel_reason = "";
                            return 8;
                        }
                       
                       
                    } else if($accepted == true) {
                        // mno acceoted the order
                        $acceptedOrder = $this->db->query("UPDATE `mno_orders` SET  `ongoing`='1',`status`='accepted'  WHERE `mno_id` = '$mno_id' AND `invoice_no` = '$invoice_no'");
                        $action_by_status = "Night owl";
                        $orderStatus = "Order accepted";
                        
                     //   $order_status = $this->OrderModel->update_status($invoice_no,$orderStatus, $action_by_status);
                        
                        // night owl action table entry
                        
                        if($mno_lat != "" || $mno_lng != ""){
                            $mno_location_details = $this->db->query("SELECT * FROM `mno_location` WHERE `mno_id` = '$mno_id' ORDER BY `created_at` DESC limit 1 ")->row_array();
                            $mno_lat = $mno_location_details['lat'];
                            $mno_lng = $mno_location_details['lng'];
                        }
                        
                        // mno_notification_types table order accepted by mno type id
                        $action_type = 2;
                        
                        
                        $this->db->query("INSERT INTO `mno_actions`(`mno_id`, `invoice_no`, `lat`, `lng`,`action_type`) VALUES ('$mno_id','$invoice_no','$mno_lat','$mno_lng','$action_type')");
                        
                        // accepted notification to user
                        
                        
                        $existingOrder = $this->db->query("SELECT ml.mno_name as mno_name, mo.*, u.id as user_id, u.phone, u.token,  u.agent FROM `mno_orders` as mo left join user_order as uo on (mo.invoice_no = uo.invoice_no) left join users as u on (uo.user_id = u.id) left join mno_list as ml on (ml.mno_id = mo.mno_id) WHERE mo.`invoice_no` = '$invoice_no' AND mo.`mno_id` = '$mno_id' ")->row_array();
                        
                        $mno_name = $existingOrder['mno_name'];
                         
                        $action_by_status = "Night owl";
                        $orderStatus = "Order accepted by night owl ". $mno_name;
                        $order_status = $this->OrderModel->update_status($invoice_no,$orderStatus, $action_by_status);
                     
                        
                        
                        $msg1 = $title = "Order accepted by night owl";
                        $reg_id = $existingOrder['token'];
                        $msg = "Night owl accepted your order";
                        $img = $img_url   = 'https://medicalwale.com/img/noti_icon.png';
                        $tag = "text";
                        $agent = $existingOrder['agent'];
                        $invoice_no = $invoice_no;
                        $notification_type = "mno"; 
                        $res = $this->PartnermnoModel->send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent, $invoice_no,$notification_type);
                        
                        $orderDetails = $this->db->query("SELECT * FROM `mno_orders`   WHERE `mno_id` = '$mno_id' AND `invoice_no` = '$invoice_no' ")->row_array();
                        $mno_order_id = $orderDetails['id'];
                         $notification_type = 2; 
                        $this->PartnermnoModel->send_gcm_notification_to_mno($mno_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg1);
                        
                        
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
         
        //  removed listing_id = 44 condition on 21st aug 2019 12:43pm
        //  added pincode functionality on 20th sept 2k19 at 07:00pm
        $mno_details = $data = array();
         $res = $mno_orders_id = 0;
         $orderInsertedButNotAssiged = 0;
        $description = $inv_id = $oid = $mno_id = $mnos = $allMnos = '';
        $mno_id = 0;
        $isAlreadyAssigned = array();
        $radius = 20;
        date_default_timezone_set('Asia/Kolkata');
        $order_date = date('Y-m-d H:i:s');
        
        $getPincode = $this->db->query("select pincode from user_order where invoice_no = '$invoice_no'")->row_array();
        
        $address_id = 0;
        $pincode = $getPincode['pincode'];
        $mno_available_delivery = $this->PartnermnoModel->mno_available_delivery($address_id, $pincode);
        if($mno_available_delivery['delivery_available'] == 1){
            $isAlreadyAssigned = $this->db->query("SELECT * FROM `mno_orders` WHERE `invoice_no` = '$invoice_no'  AND `ongoing` = 1")->row_array();
            if(!empty($isAlreadyAssigned) && sizeof($isAlreadyAssigned > 0) && $isAlreadyAssigned['mno_id'] > 0){
                
               
                    $res = 1;
                    $mno_id  = $isAlreadyAssigned['mno_id'];
                    $mno_details = $this->db->query("SELECT u.token,u.agent,ml.* FROM `mno_list` as ml left join users as u on (ml.mno_id = u.id) WHERE ml.`mno_id` =  '$mno_id'")->row_array();
                
            } else if(!empty($isAlreadyAssigned) && sizeof($isAlreadyAssigned > 0) && $isAlreadyAssigned['mno_id'] == 0){
                $orderInsertedButNotAssiged = 1;
                    $res = 1;
            }
            
            // else {
            
            
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
            
                if(!empty($busyMnos) &&  sizeof($busyMnos) > 0 && $mno_id == ''){
                    $sql = sprintf("SELECT ml.`id`, ml.`mno_id`,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance  FROM mno_list  as ml left join mno_track as mt on (ml.mno_id = mt.mno_id) WHERE $mnos  mt.logged_in <= '$order_date' AND mt.logged_out = '0000-00-00 00:00:00'  HAVING distance < '%s' ORDER BY distance LIMIT 20", ($lat), ($lng), ($lat), ($radius));
                   
                    $query = $this->db->query($sql);
                    $count = $query->num_rows();
                    $mno = $query->row_array();
                    
                    $mno_id = $mno['mno_id'];
                    
                    
                    if(!empty($mno_id) &&  sizeof($mno_id) > 0){
                    
                    if($orderInsertedButNotAssiged == 0){
                        $insertInOrder = $this->db->query("INSERT INTO `mno_orders`(`invoice_no`, `mno_id`, `ongoing`, `created_at`, `updated_at`) VALUES ('$invoice_no','$mno_id','1','$order_date','$order_date')");
                        $mno_orders_id = $this->db->insert_id();
                        
                    }     else {
                        $inv_id = $isAlreadyAssigned['invoice_no'];
                        $insertInOrder = $this->db->query("UPDATE `mno_orders` SET `mno_id` = '$mno_id', `updated_at` = '$order_date' WHERE `invoice_no` = $inv_id");
                        $insertedInOrder = $this->db->query("SELECT * from `mno_orders`  WHERE `invoice_no` = $inv_id AND `mno_id` = '$mno_id' AND `mno_id` = '$mno_id'")->row_array();
                        
                        $mno_orders_id = $insertedInOrder['id'];
                    }
                        // $res = $mno_id;
                        $res = 1;
                        $mno_details = $this->db->query("SELECT u.token,u.agent,ml.* FROM `mno_list` as ml left join users as u on (ml.mno_id = u.id) WHERE ml.`mno_id` =  '$mno_id'")->row_array();
                        
                       $this->db->query("UPDATE user_order SET order_deliver_by = 'mno' WHERE invoice_no = $invoice_no"); 
                       
                       
                    //   search for existing order where this order is redirected from some order
                    
                        $getOldOrderDetails  =$this->db->query("select * from mno_orders WHERE `invoice_no` = '$invoice_no' AND `ongoing` = 0  AND `order_cancel` > 0 order by id desc ")->row_array();
                        if(sizeof($getOldOrderDetails) > 0){
                            $old_order_id = $getOldOrderDetails['id'];
                            $this->db->query("UPDATE mno_orders SET redircted_to = '$mno_id' WHERE id = '$old_order_id'"); 
                        }
                       
                        // msg to mno
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
                            $img = $img_url   = 'https://medicalwale.com/img/noti_icon.png';
                        //  mno order received notificarion 1111111111
                        //    $this->send_gcm_notify_mno($title, $reg_id, $msg, $img_url, $tag, $agent, $invoice_no,$notification_type);
                          
                            $this->PartnermnoModel->send_gcm_notification_to_mno($mno_id,$invoice_no,$mno_orders_id ,$notification_type, $img, $title, $msg);
                            
                            $this->PartnermnoModel->send_msg_to_mno_executors($invoice_no);
                            
                        // }
                        
                        // $res = $mno_id;
                       $res = 1; 
                    } else {
                        if($orderInsertedButNotAssiged == 0){
                           $insertInOrder = $this->db->query("INSERT INTO `mno_orders`(`invoice_no`, `mno_id`, `ongoing`, `created_at`, `updated_at`) VALUES ('$invoice_no','$mno_id','1','$order_date','$order_date')");
                        
                        } 
                          
                        // $res = 0;
                        $res = 1; 
                    }
                    
                    
                } else {
                    //   print_r($allMnos); die();
                       if($orderInsertedButNotAssiged == 0){
                           $insertInOrder = $this->db->query("INSERT INTO `mno_orders`(`invoice_no`, `mno_id`, `ongoing`, `created_at`, `updated_at`) VALUES ('$invoice_no','$mno_id','1','$order_date','$order_date')");
                        }  
            
                    // $res = 0; 
                    $res = 1; 
                }
                
            // }
            
        } else {
            // aski vaibhav regarding delivery charges and delivery by values : do we need to remove the inserted record
            $res = 2; // MNO delivery not available
            $description = $mno_available_delivery['message'];
        }
        
        $data['mno_id'] = $mno_id;
        $data['res'] = $res;
        $data['description'] = $description;
        $data['data'] = $mno_details;
          
            return $data;
            
    }
    
    // order_details
    
    public function order_details($mno_id, $invoice_no){
        $resultpost = array();
        $order_date_epoch = null;
        $vendor_payment_option = array();
        // echo  $invoice_no; die();
        // remaining order product and prescription details
        $prescription_result1_all = $prescription_order_final = $cost = $tracking = $pharmacy = array();
        $order_id = $start_time = null;
          $delivery_charges_by_customer  = 0;
                $delivery_charges_by_vendor = 0;
                $delivery_charges_to_mno = 0;
        $order_total_discount = $chc = $delivery_charge = $cash_handling_charges = $discount = $payment_method_id = $payment_method_value = $total = $gst = $subtotal = $order_accepted_time = $end_time = null;
        
        $ongoin = false;
        
        
        $statusdata = $data = array();
        $acceped_query = $this->db->query("SELECT mn.created_at as order_delivery_time,ma.created_at as order_acceped_time, mo.* FROM `mno_orders` as mo left join mno_actions as ma on (mo.mno_id = ma.mno_id && mo.invoice_no = ma.invoice_no && ma.action_type = 2) left join mno_notifications as mn on (mo.mno_id = mn.receiver_id && mo.invoice_no = mn.invoice_no && mn.notification_type = 15) WHERE mo.`invoice_no` = '$invoice_no' AND mo.`mno_id` = '$mno_id'   ")->row_array();
        // print_r($acceped_query); die();
        if($acceped_query['status'] == 'accepted'){
            $mno_orders_id = $acceped_query['id'];
            $start_time = strtotime($acceped_query['order_acceped_time']);
            $end_time = strtotime($acceped_query['order_delivery_time']);
            $ongoin_status = $acceped_query['ongoing'];
            if( $ongoin_status == 1){
                $ongoin = true;
            } else {
                $ongoin = false;
            }
            $query = $this->db->query("select user_id,gst,chc,discount,order_total,action_by,order_id,order_type,delivery_time,listing_id,suggested_pharmacy_id,cancel_reason,action_by,delivery_charge,delivery_charges_by_customer,delivery_charges_by_vendor,delivery_charges_to_mno,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status,rxId from user_order where `invoice_no` = '$invoice_no' group by invoice_no order by order_id desc");
            // print_r($query->result_array()); die();
            // tracker_notifications
            // $statusdata = $this->PartnermnoModel->tracker($invoice_no);
            $order_info = $query->row_array();
            $statusdata = $this->PartnermnoModel->tracker_notifications($invoice_no, $mno_orders_id);
            $pharmacy_id = $order_info['listing_id'];
            $suggested_pharmacy_id = $order_info['suggested_pharmacy_id'];
            $gst = $order_info['gst'];
            
            if($pharmacy_id != "" && $pharmacy_id != 0 && $pharmacy_id != null){
            //   echo $pharmacy_id; die();
                $page_no = 1;
                $per_page = 1;
                $lat = $lng = "";
                $pharmacy_id = $pharmacy_id;
                $search = "";
                 $res = $this->PartnermnoModel->pharmacy_list($mno_id, $lat, $lng, $page_no, $per_page,$pharmacy_id,$search);
                  $vendor_payment_option = $this->PartnermnoModel->vendor_payment_option($pharmacy_id);
                  //print_r($vendor_payment_option); die();
                //  print_r($res); die();
                if(array_key_exists('stores',$res) &&  sizeof($res['stores']) > 0){
                    $pharmacy = $res['stores'][0];
                   // print_r($vendor_payment_option); die();
                     if(sizeof($vendor_payment_option) > 0 && array_key_exists('data',$vendor_payment_option)){
                           $pharmacy['payment_options'] = $vendor_payment_option['data'];
                    } else {
                        $pharmacy['payment_options'] = null;
                    }
                    $pharmacy['in_panel']   = true;  
                } else {
                    $pharmacy = null;
                }
                
                // print_r($pharmacy); die();
                     
                
            } else if($suggested_pharmacy_id != "" && $suggested_pharmacy_id != 0  && $suggested_pharmacy_id != null){
                // print_r($suggested_pharmacy_id); die();
                $page_no = 1;
                $per_page = 1;
                $lat = $lng = "";
                $pharmacy_id = $suggested_pharmacy_id;
                $search = "";
                $res = $this->PartnermnoModel->suggested_pharmacy_list($mno_id,$page_no,$per_page,$gst,$lat,$lng,$pharmacy_id,$search);
                // print_r($res); die();
                 $vendor_payment_option = $this->PartnermnoModel->vendor_payment_option(0); // suggested pharmacy
                if(array_key_exists('stores',$res) &&  sizeof($res['stores']) > 0){
                    $pharmacy = $res['stores'][0];
                   // print_r($pharmacy); die();
                    if(sizeof($vendor_payment_option) > 0 && array_key_exists('data',$vendor_payment_option)){
                           $pharmacy['payment_options'] = $vendor_payment_option['data'];
                    } else {
                        $pharmacy['payment_options'] = null;
                    }
                    $pharmacy['in_panel']   = false;  
                } else {
                    $pharmacy = null;
                }
                
            //    print_r($pharmacy); die();
                
            } else {
                $pharmacy = null;
            }
            
            // print_r($query->result_array()); die();
            
            foreach ($query->result_array() as $row) {
                // print_r($row); die();
                $user_id = $row['user_id'];
                $order_id = intval($row['order_id']);
                $order_type = $row['order_type'];
                $delivery_time = $row['delivery_time'];
                $chc = $row['chc'];
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
                $order_date_epoch = strtotime($order_date);
                $order_date = date('j M Y h:i A', strtotime($order_date));
                
                $delivery_charge = $row['delivery_charge'];
                $delivery_charges_by_customer = $row['delivery_charges_by_customer'];
                $delivery_charges_by_vendor = $row['delivery_charges_by_vendor'];
                $delivery_charges_to_mno = $row['delivery_charges_to_mno'];
                $order_total = $row['order_total'];
                $discount = $row['discount'];
                $order_status = $row['order_status'];
                $order_type = $row['order_type'];
                $action_by = $row['action_by'];
                $rxId = $row['rxId'];
                $orderId = "";
                  $is_cancel = false;
                if($rxId != 'NULL') {
                    $query = $this->db->query("select orderState,orderId from medlife_order where rxID='$rxId' order by id desc");
                    $medlife_order =  $query->row();
                    $medlife_order_num =  $query->num_rows();
                // = $medlife_order->orderState;
                 //    $medlife_order= $this->db->select('orderState')->from('medlife_order')->where('rxID', $rxId)->get()->row();
                 if($medlife_order_num>0){
                     $medlife_status = $medlife_order->orderState;
                     $order_status = $medlife_order->orderState;
                     $orderId = $medlife_order->orderId;
                     if($medlife_status == 'Received') {
                         $is_cancel = true;
                     } else {
                         $is_cancel = false;
                     }
                 } else {
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
                        $product_query = $this->db->query("select id as product_order_id,product_id,product_discount,product_name,product_unit,product_unit_value,product_img,product_quantity,product_price,sub_total,product_status,product_status_type,product_status_value,order_status from user_order_product where order_id='$order_id1'  order by product_order_id asc");
                        $product_count = $product_query->num_rows();
                        if ($product_count > 0) {
                            foreach ($product_query->result_array() as $product_row) {
                               
                                $product_order_id     = intval($product_row['product_order_id']);
                                $product_id           = intval($product_row['product_id']);
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
                                
                                
                                if($product_order_status == 'Available'){
                                    $sub_total_sum1      += $product_price * $product_quantity;
                                    $sub_total_discount +=$product_discount;
                                }
                                
                              
                                $product_resultpost[] = array(
                                    "order_id" => intval($order_id1),
                                    "order_product_id" => intval($product_id),
                                    "name" => $product_name,
                                    "image" => $product_img,
                                    "quantity" => intval($product_quantity),
                                    "price" => floatval($product_price),
                                    "unit" => $product_unit,
                                    "unit_value" => floatval($product_unit_value),
                                    "discount" => floatval($product_discount),
                                    "sub_total" => floatval($sub_total),
                                    "status" => $product_status,
                                    "status_type" => $product_status_type,
                                    "status_value" => $product_status_value,
                                    "order_status" => $product_order_status
                                );
                
                          
                            }
                        }
                       // echo "SELECT id as product_order_id, order_status,prescription_image,prescription_image2,prescription_image3,prescription_image4,original_prescription FROM prescription_order_details WHERE order_id='$order_id1' order by product_order_id asc"; die();
                        $product_query1 = $this->db->query("SELECT id as product_order_id, order_status,prescription_image,prescription_image2,prescription_image3,prescription_image4,original_prescription FROM prescription_order_details WHERE order_id='$order_id1' order by product_order_id asc");
                        $product_count1 = $product_query1->num_rows();
                        if ($product_count1 > 0) {
                            foreach ($product_query1->result_array() as $product_row1) {
                                $prescription_result1_all = array();
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
                                        "order_product_id" => intval($product_order_id1),
                                    "name" => $product_name1,
                                    "image" => $images_1,
                                    // "product_orginal_img" => $images_2,
                                    "quantity" => intval($product_quantity1),
                                    "price" => floatval($product_price1),
                                    "unit" => '',
                                    "unit_value" => '',
                                    "discount" => 0,
                                    "sub_total" => floatval($sub_total1),
                                    "status" => $product_status1,
                                    "status_type" => $product_status_type1,
                                    "status_value" => $product_status_value1,
                                    "order_status" => $product_order_status1
                                );
                
                              
                            }
                            
                            
                            $prescription_query = $this->db->query("SELECT * FROM `prescription_order_list` where order_id='$order_id' order by id desc");
                            $prescription_count = $prescription_query->num_rows();
                            if ($prescription_count > 0) {
                                
                                
                                $items = $prescription_result1 = array();
                                foreach ($prescription_query->result_array() as $prescription_row) {
                                    $finalamt=$prescription_row['prescription_price']*$prescription_row['prescription_quantity'];
                                    
                                    $sub_total_sum2+=$finalamt;
                                    
                                    $order_product_id     = $prescription_row['prescription_id'];
                                    $prescription_name     = $prescription_row['prescription_name'];
                                    $prescription_name     = $prescription_row['prescription_name'];
                                    $prescription_quantity = $prescription_row['prescription_quantity'];
                                    $prescription_price    = $prescription_row['prescription_price'];
                                    $prescription_discount = $prescription_row['prescription_discount'];
                                    $prescription_status   = $prescription_row['prescription_status'];
                                    
                                   $sub_total_discount1 += $prescription_discount;
                                   
                                    $prescription_result1[] = $prescription_result[] = array(
                                        "name" => $prescription_name,
                                        "quantity" => intval($prescription_quantity),
                                        "price" => floatval($prescription_price),
                                        "discount" => floatval($prescription_discount),
                                        "status" => $prescription_status
                                    );
                                    
                                   
                                   
                                    $items[] = array(
                                        "name" => $prescription_name,
                                        "quantity" => intval($prescription_quantity),
                                        "price" => floatval($prescription_price),
                                        "discount" => floatval($prescription_discount),
                                        "status" => $prescription_status
                                    );
                                   
                                }
                                
                                 $po['prescription_id'] = intval($product_order_id1);
                                $po['image'] = $images_1;
                                 $po['items'] = $items;
                                     $prescription_result1_all[] = $po;
                                
     
                                if(sizeof($prescription_result1_all) > 0){
                                    $prescription_order_final[] = array(
                                            "order_id" => $order_id,
                                            "prescription_order" => $prescription_result1_all
                                           
                                        );
                                }
                            }
                            
                        }
                }
        }
        $order_total=$sub_total_sum1+$sub_total_sum2;
        $order_total_discount=$sub_total_discount+$sub_total_discount1;
        if($order_total_discount==""){
            $order_total_discount=0;
        } else {
            $order_total_discount;
        }
        
      /*  if($order_status!="Awaiting Confirmation") {
            $user_info_user = $this->db->query("SELECT medical_name,payment_type FROM medical_stores WHERE (user_id='$listing_id' AND pharmacy_branch_user_id= 0) or (user_id != 0 AND pharmacy_branch_user_id='$listing_id' ) ");
            $getuser_info_user = $user_info_user->row_array();    
           if($getuser_info_user['payment_type']!=null || !empty($getuser_info_user['payment_type']))   {
           $listing_paymode=$getuser_info_user['payment_type'];
           }   else   {
               $listing_paymode="Cash On Delivery";
           }
        } else {
          
           $listing_paymode="Cash On Delivery";  
        }
        */
        if($rxId == "") {
            $rxId=null;
        } else {
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
               $deliveryTimeAndCostMno = $this->PartnermnoModel->get_delivery_time_cost($order_id);
               $estimated_delivery_time = $deliveryTimeAndCostMno['delivery_time'];
                $tracker = array(
                     "start_time" => $start_time,
                    "end_time" => $end_time,
                    "estimated_delivery_time" => $estimated_delivery_time,
                    "steps" => $statusdata,
                    "pharmacy" => $pharmacy,
                    
                   
                );
                
               
               
                
                if($order_total == 0){
                    // select amount from user table
                    // $order_info
                    // print_r($order_info); die();
                    $order_total = $order_info['order_total'];
                    $order_total_discount = $order_info['discount'];
                    
                }
                 // discount after tax calculation
                $gstAmt = 0;
                if($gst > 0){
                    $gstAmt = $order_total * ($gst / 100);
                }
                
              
                 $total = $order_total + $delivery_charges_by_customer + $chc - $order_total_discount + $gstAmt;    
                 $grand_total = $order_total + $delivery_charge + $chc - $order_total_discount + $gstAmt;    
               
               
                
                $cost = array(
                    "cash_handling_charges"  => floatval($chc),
                    "discount"  => floatval($order_total_discount),
                    "payment_method_value"  => $payment_method,
                    "gst"  => floatval($gst),
                    "subtotal"  => floatval($order_total),
                    "total"  => floatval($total),
                    "delivery_charge_by_customer" => floatval($delivery_charges_by_customer),
                    "delivery_charge_by_vendor" => floatval($delivery_charges_by_vendor),
                    "delivery_charges_to_mno" => floatval($delivery_charges_to_mno),
                    "delivery_charge" => floatval($delivery_charges_to_mno), // can remove this key, Just added because in MNO app delivery charges to MNO are handled using delivery_charge key 
                    "total_delivery_charges"  => floatval($delivery_charge),
                    "grand_total" => floatval($grand_total)
                    );    
                        
                $resultpost = array(
                    "invoice_no" => $invoice_no,
                    "on_going"  => $ongoin,
                    "medlife_order_id" => $orderId,
                    "delivery_time" => str_replace('null', '', $delivery_time),
                    "order_type" => $order_type,    
                    "listing_id" => $listing_id,
                    "listing_name" => $listing_name,
                    "listing_type" => $listing_type,
                    "listing_payment_mode" => $payment_method,
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
                    "order_date_epoch" => $order_date_epoch,
                    "order_status" => $order_status,
                    "cancel_reason" => $cancel_reason,
                    "delivery_charge" => floatval($delivery_charge),
                    "product" => $product_resultpost_new,
                    "prescription_create" => $prescription_resultpost_new,
                    // "prescription_order" => $prescription_result,
                    "prescription" => $prescription_order_final,
                    "action_by" => $action_by,
                    "rxid" => $rxId,
                    "is_cancel" => $is_cancel,
                    "cost" => $cost,
                    "tracking" => $tracker
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
                "message" => "No order found for this invoice number and night owl"
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
    
    public function order_status_common($mno_id,$order_id_data, $prescription_id_data,$order_details,$prescription_details)
    {
       
        //amount update in user_order is pending
        $this->load->model('PaymentModel');
         $order_status = 'Awaiting Customer Confirmation';
     //   echo $order_id_data ;die();
        $getInvNoByOrderId = $this->db->query("SELECT uo.invoice_no FROM user_order as uo WHERE uo.order_id = '$order_id_data' ")->row_array();
        
        $invoice_no = $invoiceNo = $getInvNoByOrderId['invoice_no'];
        
        $this->PartnermnoModel->pharmacy_didnot_respond($mno_id,$invoiceNo);
       
        date_default_timezone_set('Asia/Kolkata');
        $updated_at       = date('Y-m-d H:i:s');
        $sub_total        = '0';
        $product_price    = '0';
        $product_quantity = '0';
        $action_by        = 'night owl';
        
        
          $total_price    = '0';
         $total_discount = 0;
         
    //   calculate delivery time and cost
    
      $total_prescription_price    = '0';
         $total_discount = 0;
         
        $deliveryTimeAndCostMno = $this->PartnermnoModel->get_delivery_time_cost($order_id_data);
        
        $delivery_charges = $deliveryTimeAndCostMno['delivery_charges'];
        $delivery_time = $deliveryTimeAndCostMno['delivery_time_in_hrs_mins'];
        
        
       
         
       
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
        
        if(array_key_exists('suggested_pharmacy_id',$product_details_new)){
            $suggested_pharmacy_id    = $product_details_new['suggested_pharmacy_id'];
        } else {
            $suggested_pharmacy_id    = 0;
        }
        
       
        
        $listing_type  = 44;
        $order_product_data    = $product_details_new['product_order'];
                   
        
        
        
        foreach ($order_product_data as $result) {
            
            if(array_key_exists('order_id',$result) ){
                $product_order_id = $result['order_id'];
            }else{
                 $product_order_id = "";
            }
            
            if(array_key_exists('quantity',$result) ){
                $product_quantity = $result['quantity'];
            }else{
                 $product_quantity = "";
            }
            
            if(array_key_exists('price',$result) ){
                $product_price = $result['price'];
            }else{
                 $product_price = "";
            }
            
            if(array_key_exists('unit',$result) ){
                $product_unit = $result['unit'];
            }else{
                 $product_unit = "";
            }
            
            if(array_key_exists('unit_value',$result) ){
                $product_unit_value = $result['unit_value'];
            }else{
                 $product_unit_value = "";
            }
            
            if(array_key_exists('discount',$result) ){
                $product_discount = $result['discount'];
            }else{
                 $product_discount = "";
            }
               
             
            $product_order_status = "Available";
            $sub_total            = $sub_total + ($product_price * $product_quantity);
           $st = $product_price * $product_quantity;
            $query = $this->db->query("UPDATE user_order_product SET product_quantity='$product_quantity',product_price='$product_price',product_discount='$product_discount',sub_total='$sub_total',order_status='$product_order_status',updated_at='$updated_at',product_unit='$product_unit',product_unit_value='$product_unit_value' where product_id='$product_order_id' and order_id='$order_id_data'");
            
            
              $total_price    = $total_price + $st;
                $total_discount = $total_discount + $product_discount;
                $product_discount = $st = $sub_total = '0';
        }
      
           // not available
         $notAvailable = 'Not Available';
            
           $this->db->query("UPDATE `user_order_product` SET `order_status` = '$notAvailable' WHERE `order_id` = '$order_id' AND `order_status` != 'Available'  ");        // Not-Available
        
        $query = $this->db->query("UPDATE user_order SET order_status='$order_status',delivery_time='$delivery_time',delivery_charge = '$delivery_charges' where order_id='$order_id_data'");
        
        
        
        $prescription_details_new = $prescription_details;
        
        $order_id           = $prescription_details_new['order_id'];
        $order_status       = "Awaiting Customer Confirmation";
        $prescription_order = $prescription_details_new['prescription_order'];
        
            
         foreach ($prescription_order as $result) {
               
            $prescription_id = $result['prescription_id'];
            $items = $result['items'];
            foreach($items as $r){
                
                
               if(array_key_exists('name',$r) ){
                    $prescription_name = $r['name'];
                }else{
                     $prescription_name = "";
                }
                
                if(array_key_exists('quantity',$r) ){
                    $prescription_quantity = $r['quantity'];
                }else{
                     $prescription_quantity = 1;
                }
                
                if(array_key_exists('price',$r) ){
                    $prescription_price = $r['price'];
                }else{
                     $prescription_price = "";
                }
                
                if(array_key_exists('discount',$r) ){
                    $prescription_discount = $r['discount'];
                }else{
                     $prescription_discount = "";
                }
              
                    
                /*    
                $prescription_name     = !empty($r['name']) ? $r['name'] : "";
                $prescription_quantity = !empty($r['quantity']) ? $r['quantity'] : "";  
                $prescription_price    = !empty($r['price']) ? $r['price'] : "";  
                $prescription_discount = !empty($r['discount']) ? $r['discount'] : "";  
                */
                
                $prescription_status   =  "Available";  
                //print_r($prescription_name); die();
                 $st = $prescription_price * $prescription_quantity;
                 $total_price    = $total_price + $st;
                $total_discount = $total_discount + $prescription_discount;
                $prescription_discount = 0;
                $st = 0;
                 
                $query = $this->db->query("INSERT INTO `prescription_order_list`(`order_id`,`prescription_id`, `prescription_name`, `prescription_quantity`, `prescription_price`, `prescription_discount`, `prescription_status`) VALUES ('$prescription_id_data', '$prescription_id','$prescription_name', '$prescription_quantity', '$prescription_price', '$prescription_discount', '$prescription_status')");
            }
        }
        $query = $this->db->query("UPDATE user_order SET  order_status='$order_status',delivery_time='$delivery_time',delivery_charge = '$delivery_charges' where order_id='$prescription_id_data'");
        
        
        // update user order 
         $this->db->query("UPDATE user_order SET `actual_cost` = '$total_price', `order_total` = '$total_price', `discount` =  '$total_discount' WHERE `invoice_no` = '$invoice_no'");
         // call delivery charge api
        $offer_id = 0;
        $delivery_charges_by_customer = $delivery_charges;
        $addDeliveryCharges  = $this->PaymentModel->add_delivery_charges($delivery_charges, $invoice_no, $delivery_charges_by_customer, $offer_id);
         
        if($addDeliveryCharges['status'] == 1){
             $added_delivery_charges = $addDeliveryCharges['data'];
             $delivery_charges = $added_delivery_charges['delivery_charges'];
                $delivery_charges_by_customer = $added_delivery_charges['delivery_charges_by_customer'];
                $delivery_charges_by_vendor = $added_delivery_charges['delivery_charges_by_vendor'];
                $delivery_charges_by_mw = $added_delivery_charges['delivery_charges_by_mw'];
                $delivery_charges_to_mno = $added_delivery_charges['delivery_charges_to_mno'];
        } else {
            $delivery_charges = $delivery_charges;
            $delivery_charges_by_customer = $delivery_charges;
            $delivery_charges_by_vendor = 0;
            $delivery_charges_by_mw = 0;
            $delivery_charges_to_mno = $delivery_charges;
        }
       
         
         
        
        
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
                $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
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
        
        // a111
     //   echo "SELECT user_id,listing_id,suggested_pharmacy_id,listing_name,delivery_time,invoice_no,name,listing_name,order_total from user_order where order_id='$order_id_data'"; die();
        $order_info1    = $this->db->query("SELECT user_id,listing_id,suggested_pharmacy_id,listing_name,delivery_time,invoice_no,name,listing_name,order_total from user_order where order_id='$order_id_data'");
        $product_count12 = $order_info1->num_rows();
    if ($product_count12 > 0) {
                       
        $order_info= $order_info1->row();
        $user_id       = $order_info->user_id;
        $listing_id    = $order_info->listing_id;
         $suggested_pharmacy_id = $order_info->suggested_pharmacy_id;
         
        if($listing_id != 0 && $listing_id != null){
            $listing_id = $listing_id;
        } else if($suggested_pharmacy_id != 0 && $suggested_pharmacy_id != null){
           $listing_id = $suggested_pharmacy_id;
        } else {
            $listing_id = 0;
        }
        
        //a11 ends 
        $listing_name  = $order_info->listing_name;
        $delivery_time = $order_info->delivery_time;
        $invoice_no    = $order_info->invoice_no;
        $name          = $order_info->name;
        $listing_name  = $order_info->listing_name;
        $order_total   = $order_info->order_total;
        
        // a12
        
        if(($suggested_pharmacy_id == 0 || $suggested_pharmacy_id == null) && $listing_id != 0){
            $medical_name = $this->db->select('medical_name')->from('medical_stores')->where('user_id', $listing_id)->get()->row();
        }  else if($suggested_pharmacy_id != 0 || $suggested_pharmacy_id != null){
            $medical_name = $this->db->select('medical_name')->from('mno_suggested_pharmacies')->where('id', $suggested_pharmacy_id)->get()->row();
           
        } else {
            $medical_name = array();
        }
        
        // a12 ends
        
        
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
                $msg   = 'Your have received a reply on your order';
                $notification_type="pharmacy_order_confirm";
                
                // a13
                
            if(sizeof($medical_name) > 0){
                 $title = 'Order Reply';
               
                 } else {
                      $title = 'Order Reply From ' . $medical_name->medical_name;
               
                     
                 }
                 
                //  a13 ends
                 
                 send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $invoice_no, $name, $listing_name, $agent,$notification_type);

               
            }
          
        }
        
        
             //  quote sent to customer
          //  $orderDetails = $this->db->query("SELECT * FROM `mno_orders` WHERE `invoice_no` LIKE '$invoice_no' AND `mno_id` = '$mno_id' ")->row_array();
            $orderDetails = $this->db->query("SELECT ml.mno_name, mo.*, u.id as user_id, u.phone, u.token, u.agent FROM `mno_orders` as mo left join user_order as uo on (mo.invoice_no = uo.invoice_no) left join users as u on (uo.user_id = u.id) left join mno_list as ml on (ml.mno_id = mo.mno_id) WHERE mo.`invoice_no` = '$invoice_no' AND mo.`mno_id` = '$mno_id' AND mo.`ongoing` = 1 AND mo.`status` = 'accepted'")->row_array();
    
            $mno_order_id = $orderDetails['id'];
            $notification_type = 7;
            $title = "Order quote sent to customer" ;
            $msg = "Order quote sent to customer of order id $invoice_no.";
            $img = 'https://medicalwale.com/img/noti_icon.png';
            $this->PartnermnoModel->send_gcm_notification_to_mno($mno_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
                   
                   
               
    
                $title1 = "Order quote received";
                $msg1 = "Order quote received please confirm the amount";
                $reg_id = $orderDetails['token'];
                $img = $img_url   = 'https://medicalwale.com/img/noti_icon.png';
                $tag = "text";
                $agent = $orderDetails['agent'];
                $invoice_no = $invoice_no;
                $notification_type = "mno"; 
                $res = $this->PartnermnoModel->send_gcm_notify_user($title1, $reg_id, $msg1, $img_url, $tag, $agent, $invoice_no,$notification_type);
               
        
        
        /*$data=array('order_status' => $order_status);
        */
        
         $order_details = $this->PartnermnoModel->order_details($mno_id , $invoice_no);
     //    echo $invoice_no;
        //  print_r($order_details);
        //  die();
         
         if(array_key_exists('data',$order_details)){
             return array(
                'status' => 200,
                'message' => 'success',
                'order_status' => $order_status,
                'data' => $order_details['data']
            );
         } else {
             return array(
                'status' => 201,
                'message' => 'Not assign to this night owl',
                'order_status' => ""
            );
         }
        
    }    return array(
            'status' => 400,
            'message' => 'fail',
            'order_status' => ""
        );
        
    }
    
    public function order_price($mno_id,$order_id, $listing_type, $order_data_list){
        //amount update in user_order is pending
        
         $this->load->model('PaymentModel');
          $order_status = 'Awaiting Customer Confirmation';
        $getOrderIds = array();
        $getInvNoByOrderId = $this->db->query("SELECT uo.invoice_no FROM user_order as uo WHERE uo.order_id = '$order_id' ")->row_array();
          
        $invoice_no = $invoiceNo = $getInvNoByOrderId['invoice_no'];
        
        $this->PartnermnoModel->pharmacy_didnot_respond($mno_id,$invoiceNo);
          $total_price    = '0';
         $total_discount = 0;
        
        date_default_timezone_set('Asia/Kolkata');
        $updated_at       = date('Y-m-d H:i:s');
        $sub_total        = '0';
        $product_price    = '0';
        $product_quantity = '0';
        $action_by        = 'night owl';
        
        
          $deliveryTimeAndCostMno = $this->PartnermnoModel->get_delivery_time_cost($order_id);
        
        
         $delivery_charges = $deliveryTimeAndCostMno['delivery_charges'];
         $delivery_time = $deliveryTimeAndCostMno['delivery_time_in_hrs_mins'];
         
        
        
        foreach ($order_data_list as $result) {
            $product_order_id     = $result['order_id'];
          //  $product_quantity     = $result['quantity'];
           // $product_price        = $result['price'];
         //   $product_unit         = $result['unit'];
            
          //  $product_unit_value   = $result['unit_value'];
        //    $product_discount     = $result['discount'];
         //   $product_order_status = $result['order_status'];
            
            
           
            
            if(array_key_exists('quantity',$result) ){
                $product_quantity = $result['quantity'];
            }else{
                 $product_quantity = 1;
            }
            
            if(array_key_exists('price',$result) ){
                $product_price = $result['price'];
            }else{
                 $product_price = "";
            }
            
            if(array_key_exists('unit',$result) ){
                $product_unit = $result['unit'];
            }else{
                 $product_unit = "";
            }
            
            if(array_key_exists('unit_value',$result) ){
                $product_unit_value = $result['unit_value'];
            }else{
                 $product_unit_value = "";
            }
            
            if(array_key_exists('discount',$result) ){
                $product_discount = $result['discount'];
            }else{
                 $product_discount = "";
            }
               
            if(array_key_exists('order_status',$result) ){
                $product_order_status = "";
            }else{
                 $product_order_status = "Available";
            }
               
            
            $sub_total            = $sub_total + ($product_price * $product_quantity);
            
            $query = $this->db->query("UPDATE user_order_product SET product_quantity='$product_quantity',product_price='$product_price',product_discount='$product_discount',sub_total='$sub_total',order_status='$product_order_status',updated_at='$updated_at',product_unit='$product_unit',product_unit_value='$product_unit_value' where product_id='$product_order_id' and order_id='$order_id'");
            
            $total_price = $total_price + $sub_total;
            $total_discount = $total_discount + $product_discount;
            
            $sub_total = 0;
            $getOrderIds[] = $product_order_id;
            
            

           
        }
        // not available
         $notAvailable = 'Not Available';
            
           $this->db->query("UPDATE `user_order_product` SET `order_status` = '$notAvailable' WHERE `order_id` = '$order_id' AND `order_status` != 'Available'  ");        // Not-Available
        
        $order_status = 'Awaiting Customer Confirmation';
    //    echo "UPDATE user_order SET `discount` = '$total_discount', `order_total` = '$total_price', `actual_cost` = '$total_price',order_status='$order_status',delivery_time='$delivery_time',delivery_charge = '$delivery_charges' where order_id='$order_id'"; die();
        $query = $this->db->query("UPDATE user_order SET `discount` = '$total_discount', `order_total` = '$total_price', `actual_cost` = '$total_price',order_status='$order_status',delivery_time='$delivery_time',delivery_charge = '$delivery_charges' where order_id='$order_id'");
        
        
        
          // call delivery charge api
        $offer_id = 0;
        $delivery_charges_by_customer = $delivery_charges;
        $addDeliveryCharges  = $this->PaymentModel->add_delivery_charges($delivery_charges, $invoice_no, $delivery_charges_by_customer, $offer_id);
         
        if($addDeliveryCharges['status'] == 1){
             $added_delivery_charges = $addDeliveryCharges['data'];
             $delivery_charges = $added_delivery_charges['delivery_charges'];
                $delivery_charges_by_customer = $added_delivery_charges['delivery_charges_by_customer'];
                $delivery_charges_by_vendor = $added_delivery_charges['delivery_charges_by_vendor'];
                $delivery_charges_by_mw = $added_delivery_charges['delivery_charges_by_mw'];
                $delivery_charges_to_mno = $added_delivery_charges['delivery_charges_to_mno'];
        } else {
            $delivery_charges = $delivery_charges;
            $delivery_charges_by_customer = $delivery_charges;
            $delivery_charges_by_vendor = 0;
            $delivery_charges_by_mw = 0;
            $delivery_charges_to_mno = $delivery_charges;
        }
        
        
        
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
                $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
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
        
        // b1
        
        $order_info    = $this->db->select('user_id,listing_id,suggested_pharmacy_id,listing_name,delivery_time,invoice_no,name,listing_name,order_total')->from('user_order')->where('order_id', $order_id)->get()->row();
        $user_id       = $order_info->user_id;
        $listing_id    = $order_info->listing_id;
        $listing_name  = $order_info->listing_name;
        $delivery_time = $order_info->delivery_time;
        $invoice_no    = $order_info->invoice_no;
        $name          = $order_info->name;
        $listing_name  = $order_info->listing_name;
        $order_total   = $order_info->order_total;
          $suggested_pharmacy_id = $order_info->suggested_pharmacy_id;
       
        
         if($listing_id != 0 && $listing_id != null){
            $listing_id = $listing_id;
        } else if($suggested_pharmacy_id != 0 && $suggested_pharmacy_id != null){
           $listing_id = $suggested_pharmacy_id;
        } else {
            $listing_id = 0;
        }
        
        // b12
        
          if(($suggested_pharmacy_id == 0 || $suggested_pharmacy_id == null) && $listing_id != 0){
            $medical_name_new = $this->db->select('medical_name')->from('medical_stores')->where('user_id', $listing_id)->get()->row();
        }  else if($suggested_pharmacy_id != 0 || $suggested_pharmacy_id != null){
            $medical_name_new = $this->db->select('medical_name')->from('mno_suggested_pharmacies')->where('id', $suggested_pharmacy_id)->get()->row();
           
        } else {
            $medical_name_new = array();
        }
        
        // b12 ends
        
        
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
                
                if(sizeof($medical_name_new) > 0){
                 $title = 'Order Reply';
               
                 } else {
                      $title = 'Order Reply From ' .$medical_name_new->medical_name;
                
                     
                 }
                 
                $msg   = 'Your have received a reply on your order';
                $notification_type="pharmacy_order_confirm";
            }
    
            if($order_status == 'Awaiting Customer Confirmation'){
                $title = 'Price quotation received from night owl';
                $msg   = 'Price quotation received from night owl : Order no.'.$invoice_no.' . Please confirm the order.';
                $notification_type="pharmacy_order_quote_sent";
                
            }
            send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $invoice_no, $name, $listing_name, $agent,$notification_type);
        }
        
            //  quote sent to customer
           // $orderDetails = $this->db->query("SELECT * FROM `mno_orders` WHERE `invoice_no` LIKE '$invoice_no' AND `mno_id` = '$mno_id' ")->row_array();
                      $orderDetails = $this->db->query("SELECT ml.mno_name, mo.*, u.id as user_id, u.phone, u.token, u.agent FROM `mno_orders` as mo left join user_order as uo on (mo.invoice_no = uo.invoice_no) left join users as u on (uo.user_id = u.id) left join mno_list as ml on (ml.mno_id = mo.mno_id) WHERE mo.`invoice_no` = '$invoice_no' AND mo.`mno_id` = '$mno_id' AND mo.`ongoing` = 1 AND mo.`status` = 'accepted'")->row_array();
    
            $mno_order_id = $orderDetails['id'];
            $notification_type = 7;
            $title = "Order quote sent to customer" ;
            $msg = "Order quote sent to customer of order id $invoice_no.";
            $img = 'https://medicalwale.com/img/noti_icon.png';
            $this->PartnermnoModel->send_gcm_notification_to_mno($mno_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
            
               
    
                $title1 = "Order quote received";
                $msg1 = "Order quote received please confirm the amount";
                $reg_id = $orderDetails['token'];
                $img = $img_url   = 'https://medicalwale.com/img/noti_icon.png';
                $tag = "text";
                $agent = $orderDetails['agent'];
                $invoice_no = $invoice_no;
                $notification_type = "mno"; 
                $res = $this->PartnermnoModel->send_gcm_notify_user($title1, $reg_id, $msg1, $img_url, $tag, $agent, $invoice_no,$notification_type);
               
                   
          $order_details = $this->PartnermnoModel->order_details($mno_id , $invoice_no);
       
         if(array_key_exists('data',$order_details)){
             return array(
                'status' => 200,
                'message' => 'success',
                'order_status' => $order_status,
                'data' => $order_details['data']
            );
         } else {
                return array(
                    'status' => 200,
                    'message' => 'success',
                    'order_status' => $order_status,
                    'data' => null
                );
                     
         }
         
        
    }
    
    public function prescription_price($mno_id,$order_id, $prescription_order){
        //amount update in user_order is pending
         $this->load->model('PaymentModel');
        $order_status = 'Awaiting Customer Confirmation';
        $getInvNoByOrderId = $this->db->query("SELECT uo.invoice_no FROM user_order as uo WHERE uo.order_id = '$order_id' ")->row_array();
             
        $invoice_no = $invoiceNo = $getInvNoByOrderId['invoice_no'];
        
        $this->PartnermnoModel->pharmacy_didnot_respond($mno_id,$invoiceNo);
        
        date_default_timezone_set('Asia/Kolkata');
        $updated_at            = date('Y-m-d H:i:s');
        $sub_total             = '0';
        $prescription_price    = '0';
        $prescription_quantity = '0';
         $total_prescription_price    = '0';
         $total_discount = 0;
          $deliveryTimeAndCostMno = $this->PartnermnoModel->get_delivery_time_cost($order_id);
        
        
         $delivery_charges = $deliveryTimeAndCostMno['delivery_charges'];
         $delivery_time = $deliveryTimeAndCostMno['delivery_time_in_hrs_mins'];
         
         
        
        //$query = $this->db->query("DELETE FROM `prescription_order_list` WHERE order_id='$order_id'");
      //  print_r($prescription_order); die();
        foreach ($prescription_order as $result) {
            $prescription_id = $result['prescription_id'];
            $items = $result['items'];
            foreach ($items as $r) {
                
                if(array_key_exists('name',$r) ){
                    $prescription_name = $r['name'];
                }else{
                     $prescription_name = "";
                }
                
                if(array_key_exists('quantity',$r) ){
                    $prescription_quantity = $r['quantity'];
                }else{
                     $prescription_quantity = 1;
                }
                
                if(array_key_exists('price',$r) ){
                    $prescription_price = $r['price'];
                }else{
                     $prescription_price = "";
                }
                
                if(array_key_exists('discount',$r) ){
                    $prescription_discount = $r['discount'];
                }else{
                     $prescription_discount = "";
                }
              
                
             /*   $prescription_name     = !empty($r['name']) ? $r['name'] : "";
                $prescription_quantity = !empty($r['quantity']) ? $r['quantity'] : "";
                $prescription_price    = !empty($r['price']) ? $r['price'] : "";
                $prescription_discount = !empty($r['discount']) ? $r['discount'] : "";
                */
                
                $sub_total = $prescription_price * $prescription_quantity ;
                
                $prescription_status   = "Available";
                
                $query = $this->db->query("INSERT INTO `prescription_order_list`(`order_id`,`prescription_id` ,`prescription_name`, `prescription_quantity`, `prescription_price`, `prescription_discount`, `prescription_status`) VALUES ('$order_id','$prescription_id', '$prescription_name', '$prescription_quantity', '$prescription_price', '$prescription_discount', '$prescription_status')");
                
                $total_prescription_price = $total_prescription_price + $sub_total ;
                $total_discount = $total_discount + $prescription_discount ;
                $sub_total = 0;
                
            }   
        }
         $order_status = 'Awaiting Customer Confirmation';
        //  echo "UPDATE user_order SET order_status='$order_status', `order_total` ='$total_prescription_price', `actual_cost`='$total_prescription_price' , `discount` = '$total_discount',delivery_time='$delivery_time',delivery_charge = '$delivery_charges' where order_id='$order_id'"; die();
        $query = $this->db->query("UPDATE user_order SET order_status='$order_status', `order_total` ='$total_prescription_price', `actual_cost`='$total_prescription_price' , `discount` = '$total_discount',delivery_time='$delivery_time',delivery_charge = '$delivery_charges' where order_id='$order_id'");
        
        
          // call delivery charge api
        $offer_id = 0;
        $delivery_charges_by_customer = $delivery_charges;
        $addDeliveryCharges  = $this->PaymentModel->add_delivery_charges($delivery_charges, $invoice_no, $delivery_charges_by_customer, $offer_id);
         
        if($addDeliveryCharges['status'] == 1){
             $added_delivery_charges = $addDeliveryCharges['data'];
             $delivery_charges = $added_delivery_charges['delivery_charges'];
                $delivery_charges_by_customer = $added_delivery_charges['delivery_charges_by_customer'];
                $delivery_charges_by_vendor = $added_delivery_charges['delivery_charges_by_vendor'];
                $delivery_charges_by_mw = $added_delivery_charges['delivery_charges_by_mw'];
                $delivery_charges_to_mno = $added_delivery_charges['delivery_charges_to_mno'];
        } else {
            $delivery_charges = $delivery_charges;
            $delivery_charges_by_customer = $delivery_charges;
            $delivery_charges_by_vendor = 0;
            $delivery_charges_by_mw = 0;
            $delivery_charges_to_mno = $delivery_charges;
        }
        
        
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
                $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
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
        
        // c111
        
        $order_info    = $this->db->select('user_id,listing_id,listing_name,suggested_pharmacy_id,delivery_time,invoice_no,name,listing_name')->from('user_order')->where('order_id', $order_id)->get()->row();
        $user_id       = $order_info->user_id;
        $listing_id    = $order_info->listing_id;
        $listing_name  = $order_info->listing_name;
        $delivery_time = $order_info->delivery_time;
        $invoice_no    = $order_info->invoice_no;
        $name          = $order_info->name;
        
          $suggested_pharmacy_id = $order_info->suggested_pharmacy_id;
          
        if($listing_id != 0 && $listing_id != null){
            $listing_id = $listing_id;
        } else if($suggested_pharmacy_id != 0 && $suggested_pharmacy_id != null){
           $listing_id = $suggested_pharmacy_id;
        } else {
            $listing_id = 0;
        }
        
        //   c111 ends
        
        //   c12
             if(($suggested_pharmacy_id == 0 || $suggested_pharmacy_id == null) && $listing_id != 0){
            $medical_name = $this->db->select('medical_name')->from('medical_stores')->where('user_id', $listing_id)->get()->row();
        }  else if($suggested_pharmacy_id != 0 || $suggested_pharmacy_id != null){
            $medical_name = $this->db->select('medical_name')->from('mno_suggested_pharmacies')->where('id', $suggested_pharmacy_id)->get()->row();
           
        } else {
            $medical_name = array();
        }
        
        // c12 ends
        
       
       
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
                // $title = 'Order Reply From ' . $medical_name->medical_name;
                
                if(sizeof($medical_name) == 0){
                    $title = 'Order Reply';
               
                 } else {
                      $title = 'Order Reply From ' . $medical_name->medical_name;
                
                 }
                 
                 
                $msg   = 'Your have received a reply on your order';
                 $notification_type="pharmacy_order_confirm";
            }
        }
    
                //  quote sent to customer
            //$orderDetails = $this->db->query("SELECT * FROM `mno_orders` WHERE `invoice_no` LIKE '$invoice_no' AND `mno_id` = '$mno_id' ")->row_array();
            $orderDetails = $this->db->query("SELECT ml.mno_name, mo.*, u.id as user_id, u.phone, u.token, u.agent FROM `mno_orders` as mo left join user_order as uo on (mo.invoice_no = uo.invoice_no) left join users as u on (uo.user_id = u.id) left join mno_list as ml on (ml.mno_id = mo.mno_id) WHERE mo.`invoice_no` = '$invoice_no' AND mo.`mno_id` = '$mno_id' AND mo.`ongoing` = 1 AND mo.`status` = 'accepted'")->row_array();
    
            $mno_order_id = $orderDetails['id'];
            $notification_type = 7;
            $title = "Order quote sent to customer" ;
            $msg = "Order quote sent to customer of order id $invoice_no.";
            $img = 'https://medicalwale.com/img/noti_icon.png';
            $this->PartnermnoModel->send_gcm_notification_to_mno($mno_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
    
                $title1 = "Order quote received";
                $msg1 = "Order quote received please confirm the amount";
                $reg_id = $orderDetails['token'];
                $img = $img_url   = 'https://medicalwale.com/img/noti_icon.png';
                $tag = "text";
                $agent = $orderDetails['agent'];
                $invoice_no = $invoice_no;
                $notification_type = "mno";
                $res = $this->PartnermnoModel->send_gcm_notify_user($title1, $reg_id, $msg1, $img_url, $tag, $agent, $invoice_no,$notification_type);
             
        
        $order_details = $this->PartnermnoModel->order_details($mno_id , $invoice_no);
       
         if(array_key_exists('data',$order_details)){
             return array(
                'status' => 200,
                'message' => 'success',
                'order_status' => $order_status,
                'data' => $order_details['data']
            );
         } else {
                return array(
                    'status' => 200,
                    'message' => 'success',
                    'order_status' => $order_status,
                    'data' => null
                );
                     
         }
    }
    
    // suggested_pharmacy_list
    public function suggested_pharmacy_list($mno_id,$page_no,$per_page,$gst,$lat,$lng,$pharmacy_id,$search){
        $Where = "";
            $offset = $per_page*($page_no - 1);
            $limit = "LIMIT $per_page OFFSET $offset"; 
            if(!empty($gst)){
                $Where .= "AND `gst`='$gst'";
            }
            
            if($pharmacy_id != ""){
                $Where .= " AND `id` = '$pharmacy_id'";
            }
            
            if($search != ""){
                $Where .= "AND (`medical_name` LIKE '%".$search."%' or `map_location`Like '%$search%' or `address1` Like '%$search%' or `address2` Like '%$search%' or `city` Like '%$search%' or `state` Like '%$search%' or `gst` Like '%$search%')";
            } 
    //        echo $Where; die();
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
    public function order_to_pharmacy($mno_id, $invoice_no, $pharmacy_id, $suggested_pharmacy_id){
        $this->load->model('PaymentModel');
        $data = array();
        if($pharmacy_id > 0){
            $store_info = $this->db->query("SELECT `id`, `token`,`agent`, `web_token`, `name`, `phone`, `email`, `vendor_id`, `avatar_id`, `lat`, `lng`  FROM users WHERE `id` = '$pharmacy_id' AND `vendor_id` = 13 ")->row_array();
     
            if(sizeof($store_info) > 0){
                $data['store_exists'] = 1;
                
                $name = $store_info['name'];
                
                $this->db->query("UPDATE user_order set `listing_id` = '$pharmacy_id', `suggested_pharmacy_id` = 0, `listing_name` = '$name' WHERE `invoice_no` = '$invoice_no'");
                
                
                // print_r($store_info); die();
                $reg_id = $store_info['token'];
                $agent = $store_info['agent'];
                
                $customer_phone =  $store_info['phone'];
                
                $msg = 'You have received one order of order id '.$invoice_no.' please accept it';
                $img = $img_url = 'https://medicalwale.com/img/noti_pharmacy.png';
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
                 
                //  orderToPharma
                $orderDetails = $this->db->query("SELECT * FROM `mno_orders` WHERE `invoice_no` LIKE '$invoice_no' AND `mno_id` = '$mno_id' ")->row_array();
                
                $mno_order_id = $orderDetails['id'];
                $notification_type = 4;
                $title = "Order sent to pharmacy" ;
                $msg = "Order sent to pharmacy of order id $invoice_no.";
                $this->PartnermnoModel->send_gcm_notification_to_mno($mno_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
            } else {
                $data['store_exists'] = 0; 
            } 
        } else if($suggested_pharmacy_id > 0){
            $sp = $this->db->query("SELECT * FROM `mno_suggested_pharmacies` WHERE `id` = '$suggested_pharmacy_id'")->row_array();
            if(sizeof($sp) > 0){
                $spName = $sp['medical_name'];
                $this->db->query("UPDATE user_order set `listing_id` = 0, `suggested_pharmacy_id` = '$suggested_pharmacy_id', `listing_name` = '$spName' WHERE `invoice_no` = '$invoice_no'");
                $data['store_exists'] = 1; 
            } else {
                 $data['store_exists'] = 0; 
            }
        } else {
            $data['store_exists'] = 0; 
        } 
         //   calculate delivery time and cost
    
        if( $data['store_exists'] == 1  ){
            $get_user_order = $this->db->query("SELECT order_id from user_order where `invoice_no` = '$invoice_no'")->row_array();
            $order_id_data = $get_user_order['order_id'];
            $deliveryTimeAndCostMno = $this->PartnermnoModel->get_delivery_time_cost($order_id_data);
            $delivery_charges = $deliveryTimeAndCostMno['delivery_charges'];
            $delivery_time = $deliveryTimeAndCostMno['delivery_time_in_hrs_mins'];
            
              // call delivery charge api
            $offer_id = 0;
            $delivery_charges_by_customer = $delivery_charges;
            $addDeliveryCharges  = $this->PaymentModel->add_delivery_charges($delivery_charges, $invoice_no, $delivery_charges_by_customer, $offer_id);
           
        }
        
        
        
             $order_details = $this->PartnermnoModel->order_details($mno_id , $invoice_no);
             $data['order_details'] = $order_details['data'];
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
//                "notification_type" => $notification_type,
                "notification_type" => 'mno',
                "notification_date" => $date,
                "invoice_no" => $invoice_no
            )
        );
        
        
        //  INSERT INTO `All_notification_Mobile`(`user_id`, `listing_id`, `order_date`, `order_status`,   `invoice_no`, `notification_type`, `notification_date`, `title`, `msg`, `img_url`, `tag`, `noti_type`,`created_at`) VALUES ([value-2],  [value-4],[value-5],[value-6],[value-7],[value-8],[value-9],[value-10],[value-11],[value-12],[value-13],[value-14],[value-15],[value-16],[value-17],[value-18],[value-19],[value-20],[value-21],[value-22],[value-23],[value-24],[value-25],[value-26],[value-27],[value-28],[value-29],[value-30])
        
        
        
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
            $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
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
       $before_accept_reasons = $after_accept_reasons =  $reason = $data = array();
        
        $selectReasons = $this->db->query("SELECT * FROM mno_cancel_reasons where `reasons_for` = 1")->result_array();
        foreach($selectReasons as $sr){
            if($sr['before_accept'] == 1 ){
                $before_accept_reasons[] = $sr;
            } 
            if($sr['after_accept'] == 1){
                $after_accept_reasons[] = $sr;
            }
        }
        
        if($before_accept === true){
            foreach($before_accept_reasons as $bar){
                $d['id'] = $bar['id'];
                $d['reason'] = $bar['cancel_reason'];
                $data['type'] = 'before';
                $data['reasons'][] = $d;
            }
            
        } else if($before_accept === false){
            
            
            foreach($after_accept_reasons as $aar){
                $d['id'] = $aar['id'];
                $d['reason'] = $aar['cancel_reason'];
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
    public function dashboard1($reqData){
        
        $lastDay = $day  = "";
        $all_orders = 0;                 
        $billing = 0;
        $pending = $amount =  $from_date = $to_date = $net_billing = $total_orders = $accepted_orders = $rejected_orders = $fastest_order = $slowest_order = $online_time = 0;
        $per_day = $orders_per_day   =  $data = array();
        $diff = 0;
        // remaining
        $online_time_1 = "";
$total_orders_1 = "";
$fastest_order_1 = "";
$slowest_order_1 = "";
// remaining 
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
            // print_r($od); die();
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
                    
                    $per_day['online_time'] = $online_time_1;
                    $per_day['total_orders'] = $total_orders_1;
                    $per_day['fastest_order'] = $fastest_order_1;
                    $per_day['slowest_order'] = $slowest_order_1;
                    
                    
                   
                    
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
    
    public function dashboard($reqData){
        // print_r($reqData); die();
        $mno_start_date = "";
        $finalTrack = $orderTrack = $ordersFinal = array();
        $lastLoggedOut = $lastDay = $day  = "";
        $all_orders = 0;                 
        $billing = 0;
        
        $pending = $amount =  $from_date = $to_date = $net_billing = $total_orders = $accepted_orders = $rejected_orders = $fastest_order = $slowest_order = $online_time = 0;
        $per_day = $orders_per_day   =  $data = array();
        $timeDiff = $diff = 0;
        // remaining
        $online_time_1 = "";
        $total_orders_1 = "";
        $fastest_order_1 = "";
        $slowest_order_1 = "";
        // remaining 

        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d');
        $currentDate = date('Y-m-d H:i:s');
        
        
        $mno_id = $reqData['mno_id'];
        $per_page = $reqData['per_page']; 
        $page_no = $reqData['page_no']; 
        
        $given_from_date = $reqData['from_date'];
        $given_to_date  = $reqData['to_date'];
        
        if($given_from_date != "" && $given_to_date != ""){
            $from_date = $given_from_date;
            $to_date = $given_to_date;
        } else {
            $startDateCalc = $page_no * $per_page - 1;
            $per_page_forto = $per_page - 1;
            
            $from_date = date('Y-m-d', strtotime($date. ' - '.$startDateCalc.' days'));
            $to_date = date('Y-m-d', strtotime($from_date. ' + '.$per_page_forto.' days'));
        }
        
        $date1 = date_create($to_date);
        $date2 = date_create($from_date);
        $diff=date_diff($date2,$date1);
        $totalDaysCounted = $diff->format("%a");
        // die();
        $onlineTrack  = $this->db->query("SELECT * FROM `mno_track` WHERE `mno_id` = '$mno_id' AND `logged_in` >= '$from_date' AND `logged_out` <= '$to_date'")->result_array();
        
        $orderDetails = $this->db->query("SELECT uo.order_total,uo.actual_cost, uo.discount, mo.* FROM `mno_orders` as mo left join user_order as uo on (mo.`invoice_no` = uo.invoice_no)  WHERE mo.`mno_id` = '$mno_id' AND mo.`created_at` >= '$from_date' AND mo.`created_at` <= '$to_date' ORDER BY mo.created_at ASC")->result_array();
        
        
        $maxMinOrderTime = $this->db->query("SELECT uot.status,uot.created_at,uot.invoice_no, mo.created_at as mno_assiged_time FROM `mno_orders` as mo left join user_order_tracking as uot on (mo.invoice_no = uot.invoice_no) WHERE mo.`mno_id` = '$mno_id' AND uot.`created_at` >= '$from_date' AND (uot.status = 'Order delivered' || uot.status = 'Order accepted')  ORDER BY uot.invoice_no, uot.status ASC")->result_array();
        
      
        $oldInvoice_no = "";
        $m = 0;
        $mt = $finalMT = array();
        $oldmno_assiged_time = $mno_assiged_time = 0;
        foreach($maxMinOrderTime as $mot){
            $status = $mot['status'];
            $created_at = $mot['created_at'];
            $invoice_no = $mot['invoice_no'];
            $mno_assiged_time = $mot['mno_assiged_time'];
            
            if($oldInvoice_no == $invoice_no){
                if($status == 'Order accepted'){
                    $mt['invoice_no'] = $invoice_no;
                    $mt['accepted_at'] = $created_at;
                    
                } else if($status == 'Order delivered'){
                    $mt['invoice_no'] = $invoice_no;
                    $mt['delivered_at'] = $created_at;
                   
                }
                
            } else {
                if($m != 0){
                    $mt['mno_assiged_time'] = $oldmno_assiged_time;
                    $finalMT[] = $mt;
                    $mt = array();
                }
                
                if($status == 'Order accepted'){
                    $mt['invoice_no'] = $invoice_no;
                    $mt['accepted_at'] = $created_at;
                    
                } else if($status == 'Order delivered'){
                    $mt['invoice_no'] = $invoice_no;
                    $mt['delivered_at'] = $created_at;
                    
                }
            }
           $oldmno_assiged_time = $mno_assiged_time;
            $oldInvoice_no = $invoice_no ;
            $m++;
           
        }
        
        if($m != 0){
            $finalMT[] = $mt;
           
        }
        $finalMaxMinTime = array();
        $oldorderDate = $diffmmt = $mmt = $minTime = $maxTime = 0;
        foreach($finalMT as $fmt){
            $onlyAcceptedDate = date("Y-m-d", strtotime($fmt['accepted_at']));
            
            if(array_key_exists('accepted_at',$fmt)){
                $orderAcceptedDate = date("Y-m-d H:i:s", strtotime($fmt['accepted_at']));
            }  else {
                $orderAcceptedDate = date("Y-m-d H:i:s", strtotime($fmt['mno_assiged_time']));
            }
            
            if(array_key_exists('delivered_at',$fmt)){
                $orderDeliveredDate = date("Y-m-d H:i:s", strtotime($fmt['delivered_at']));
            }  else {
                $orderDeliveredDate = date("Y-m-d H:i:s");
            }
            
            $diffmmt = strtotime($orderDeliveredDate) - strtotime($orderAcceptedDate); 
            if($mmt == 0){
                $minTime = $diffmmt;
                $maxTime = $diffmmt;
            }
            
            if($onlyAcceptedDate == $oldorderDate){
                if($minTime > $diffmmt){
                    $minTime = $diffmmt;
                } 
                if($maxTime < $diffmmt){
                    $maxTime = $diffmmt;
                }
                
                
            } else {
                if($mmt != 0){
                    $ommt['date'] = $oldorderDate;
                    $ommt['min_order'] = $minTime;
                    $ommt['max_order'] = $maxTime;
                    
                    $finalMaxMinTime[] = $ommt;
                    $minTime = $diffmmt;
                    $maxTime = $diffmmt;
                    
                }
                
                if($minTime > $diffmmt){
                    $minTime = $diffmmt;
                } 
                if($maxTime < $diffmmt){
                    $maxTime = $diffmmt;
                }
                
            }
            
            $oldorderDate = $onlyAcceptedDate;
            $mmt++;
        }
        
        if($mmt != 0){
            $ommt['date'] = $oldorderDate;
            $ommt['min_order'] = $minTime;
            $ommt['max_order'] = $maxTime;
            
            $finalMaxMinTime[] = $ommt;
            
            
        }
        
        // print_r($finalMaxMinTime); die();
           
        
        if(sizeof($onlineTrack) == 0){
            // check whether last one is not loggedout yet
            $onlineTrack  = $this->db->query("SELECT * FROM `mno_track` WHERE `mno_id` = '$mno_id' AND `logged_out` = '0000-00-00 00:00:00'")->result_array();
        // print_r($onlineTrack); die();
        
        }
     
            $last_date = "";
            foreach($onlineTrack as $ot){
                $lastLoggedIn = $logged_in = $ot['logged_in'];
                $lastLoggedOut = $logged_out = $ot['logged_out'];
                
                if($logged_out == '0000-00-00 00:00:00'){
                    $logged_out = $currentDate;
                }
                
               
                $start_date = date("Y-m-d", strtotime($logged_in));
                $end_date = date("Y-m-d", strtotime($logged_out));
                
                if($start_date == $last_date ){
                    
                    if($start_date == $end_date){
                        $timeDiff = $timeDiff + (strtotime($logged_out) - strtotime($logged_in));
                        // echo $timeDiff; die();
                    } else {
                        $dayEndDate = date("Y-m-d 23:59:59", strtotime($start_date));
                        $anotherdayStartDate = date("Y-m-d 00:00:00", strtotime($end_date));
                      
                        $timeDiff = $timeDiff + (strtotime($dayEndDate) - strtotime($logged_in));
                        
                        $time['date'] = $start_date;
                        $time['online_time'] = $timeDiff;
                        
                        
                        $finalTrack[] = $time;
                        $timeDiff = 0;
                        
                        $timeDiff = $timeDiff + (strtotime($logged_out) - strtotime($anotherdayStartDate));
                        
                        $start_date = $end_date;
                    }
                    
                } else {
                        
                        
                        if($last_date != ""){
                            $time['date'] = $last_date;
                            $time['online_time'] = $timeDiff;
                            
                            $finalTrack[] = $time;
                            $timeDiff = 0;
                        }
                        
                    
                    if($start_date == $end_date){
                        $timeDiff = $timeDiff + (strtotime($logged_out) - strtotime($logged_in));
                    } else {
                        $dayEndDate = date("Y-m-d 23:59:59", strtotime($start_date));
                        $anotherdayStartDate = date("Y-m-d 00:00:00", strtotime($end_date));
                      
                        $timeDiff = $timeDiff + (strtotime($dayEndDate) - strtotime($logged_in));
                        
                        $time['date'] = $start_date;
                        $time['online_time'] = $timeDiff;
                        
                        
                        $finalTrack[] = $time;
                        $timeDiff = 0;
                        
                        $timeDiff = $timeDiff + (strtotime($logged_out) - strtotime($anotherdayStartDate));
                        
                        $start_date = $end_date;
                        
                    }
                    
                }
                 
                
                $last_date = $start_date;
                
            }
            /*if(sizeof($time) > 0){
                 $finalTrack[] = $time;
            }*/
            
            $todayDate = date("Y-m-d");
            if($lastLoggedOut == '0000-00-00 00:00:00') {
                    // $lastLoggedIn
                
                $nextDay = date("Y-m-d", strtotime("$lastLoggedIn + 1 day") );
                
                
               
                while($todayDate >= $nextDay)  {
                   if($todayDate != $nextDay){
                        $time['date'] = $nextDay;
                        $time['online_time'] = 36000; // 10 hrs
                        $finalTrack[] = $time;
                    } else {
                        $currentDateTime = date("Y-m-d H:i:s");
                        $currentTime = date("H:i:s");
                        if($currentTime > "06:00:00" && $currentTime < "20:00:00" ){ // in between morning 6 to evening 8
                            // echo "between";
                            $onlineTime = 21600; //6 hrs
                        } else if($currentTime < "06:00:00" ){  // before 6am
                            // echo "less: its in morning";
                            $attwelve = date("Y-m-d 00:00:00");
                            $timeDiff = (strtotime($currentDateTime) - strtotime($attwelve));
                      
                            $onlineTime = $timeDiff;
                            
                        } else if($currentTime > "20:00:00" ){
                            // echo "greter: its in evening";
                            
                            $after8 = date("Y-m-d 20:00:00");
                            // Morning 6 hrs plus 
                            $timeDiff = (strtotime($currentDateTime) - strtotime($after8));
                            
                            
                            $onlineTime = 21600 + $timeDiff;
                            
                            
                        } else {
                            $onlineTime = 0;
                        }
                        
                        
                        $time['date'] = $nextDay;
                        $time['online_time'] = $onlineTime; // 10 hrs
                        $finalTrack[] = $time;
                        
                    } 
                  
                    $nextDay = date("Y-m-d", strtotime("$nextDay + 1 day") );
                    
                } 
  
            }
               
        $dayNumber = $first = 0;
        $date_onlyDate = $lastdate_onlyDate = $lastDay = "";
        
        foreach($orderDetails as $od){
            
            
            $amount = $od['order_total'];
            $created_at = $od['created_at'];
            $day = date('D',strtotime($created_at));
            $dayNumber = date('N',strtotime($created_at));
            
            $date_onlyDate = date('Y-m-d',strtotime($created_at));
            
             if($day == $lastDay){
                $billing = $billing  + $amount; 
                $all_orders = $all_orders + 1;
                // print_r($od); die();
                
            } else {
               
                if($first > 0){
                    $per_day['day'] = intval($dayNumber);
                    $per_day['date'] = $lastdate_onlyDate;
                    $per_day['total_orders'] = $all_orders;
                    $per_day['billing'] = $billing;
                }   
                    
                   
                    
                if(sizeof($per_day) > 0){
                    $orders_per_day[] = $per_day;
                    $billing = 0;
                    $all_orders = 0;
                    $per_day = array();
                
                }
                    $billing = $billing  + $amount; 
                    $all_orders = $all_orders + 1;
                
            }
  
            
            /*if($od['status'] == 'rejected'){
                $rejected_orders = $rejected_orders + 1;
            } else if($od['status'] == 'accepted'){
                $accepted_orders = $accepted_orders + 1;
            } else {
                $pending = $pending + 1;
            }*/
            
            
            $lastDay = $day;
            $lastdate_onlyDate = $date_onlyDate;
               $olddayNumber = $dayNumber;
         
            $first++;
            // $orders_per_day[] = $per_day;
        }
       
        $per_day['day'] = intval($dayNumber);
        $per_day['date'] = $date_onlyDate;
        $per_day['total_orders'] = $all_orders;
        $per_day['billing'] = $billing;

        $orders_per_day[] = $per_day;

       
        $dateInLoop = $from_date;
        $dayLoopNo = $online_time = 0;
        for($d=0;$d<=$totalDaysCounted;$d++){
        
            $dayLoop = date('D',strtotime($dateInLoop));
             $dayLoopNo = date('N',strtotime($dateInLoop));
           
                    
            $getIndexOrder = array_search($dateInLoop, array_column($orders_per_day, 'date'),true);
            $getIndexTrack = array_search($dateInLoop, array_column($finalTrack, 'date'),true);
            $getIndexonlineTime = array_search($dateInLoop, array_column($finalMaxMinTime, 'date'),true);
               
            if($getIndexOrder > -1) {
                $total_orders = $orders_per_day[$getIndexOrder]['total_orders'];
                $billing = $orders_per_day[$getIndexOrder]['billing'];
               
            } else {
                    $billing = $total_orders = 0;
            }
            
            if($getIndexTrack > -1) {
                $online_time = $finalTrack[$getIndexTrack]['online_time'];
               
            } else {
                $online_time = 0;
            }
            if($getIndexonlineTime > -1) {
                $fastest_order = $finalMaxMinTime[$getIndexonlineTime]['max_order'];
                $slowest_order = $finalMaxMinTime[$getIndexonlineTime]['min_order'];
               
            } else {
                $fastest_order = $slowest_order = 0;
            }
            
            
            
            $orderTrack["day"] = (int)$dayLoopNo;
            $orderTrack["date"] = $dateInLoop;
            $orderTrack["total_orders"] = $total_orders;
            $orderTrack["billing"] = $billing;
            $orderTrack["online_time"] =$online_time;
            $orderTrack["fastest_order"] =$fastest_order;
            $orderTrack["slowest_order"] =$slowest_order;
            
            $ordersFinal[] = $orderTrack;
            
            
            $dateInLoop = date('Y-m-d', strtotime($dateInLoop. ' + 1 days'));
        }
        // die();
        
        $firstOrder = $this->db->query("SELECT `created_at` FROM `mno_orders` WHERE `mno_id` = '$mno_id' order by `created_at` ASC limit 1 ")->row_array();
        if(sizeof($firstOrder) > 0){
            $mno_start_date = date("Y-m-d", strtotime($firstOrder['created_at']));
        } else {
            $mno_start_date = date("Y-m-d");
        }
        
        $totalSecs = strtotime(date("Y-m-d")) - strtotime($mno_start_date);
        $dataCount = sizeof($ordersFinal);
        
        $totalDays  = $totalSecs /(60 * 60 * 24);
        
        
        $total_pages = ceil( $totalDays / $dataCount);
        
        $finalData['mno_start_date'] = $mno_start_date;
        $finalData['total_pages'] = $total_pages;
        
        $finalData['ordersFinal'] = array_reverse($ordersFinal);
        
          return $finalData;  
        
    }
    
    // order_pickedup
    // 11th october 2019 10:00am added $paid_to_pharmacy,$paid_to_pharmacy_comment,$received_from_pharmacy,$received_from_pharmacy_comment. WRT this we will do entries in db 
    public function order_pickedup($mno_id,$invoice_no,$paid_to_pharmacy,$paid_to_pharmacy_comment,$received_from_pharmacy,$received_from_pharmacy_comment,$received_including_delivery_charges,$paid_payment_method, $received_payment_method){
        $res = $res1 = $res2 = $res3 = array();
        $this->load->model('OrderModel');
        $existingOrder = $this->db->query("SELECT uo.user_id,uo.delivery_charge,uo.delivery_charges_by_customer, uo.delivery_charges_by_vendor, uo.delivery_charges_by_mw,  uo.total_without_dc,uo.listing_type,uo.payment_method,uo.order_total, uo.listing_id,  uo.delivery_charges_to_mno, uo.suggested_pharmacy_id, ml.mno_name, mo.*, u.id as user_id, u.phone, u.token, u.agent FROM `mno_orders` as mo left join user_order as uo on (mo.invoice_no = uo.invoice_no) left join users as u on (uo.user_id = u.id) left join mno_list as ml on (ml.mno_id = mo.mno_id) WHERE mo.`invoice_no` = '$invoice_no' AND mo.`mno_id` = '$mno_id' AND mo.`ongoing` = 1 AND mo.`status` = 'accepted'")->row_array();
        if($existingOrder > 0){
            //  print_r($existingOrder); die();
            
            $suggested_pharmacy_id = $existingOrder['suggested_pharmacy_id'];
            $order_total = $existingOrder['order_total'];
            $listing_id = $existingOrder['listing_id'];
            
            $listing_type = $existingOrder['listing_type'];
            $total_without_dc = $existingOrder['total_without_dc'];
            $payment_method = $existingOrder['payment_method'];
            $delivery_charges_to_mno = $existingOrder['delivery_charges_to_mno'];
            $delivery_charges_by_customer = $existingOrder['delivery_charges_by_customer'];
            $delivery_charges_by_vendor = $existingOrder['delivery_charges_by_vendor'];
            $delivery_charges_by_mw = $existingOrder['delivery_charges_by_mw'];
            
            if($suggested_pharmacy_id > 0){
                $listing_id = $suggested_pharmacy_id;
                $listing_id_type = 51; // suggested pharmacy vendor type
            } else {
                $listing_id = $listing_id;
                $listing_id_type = 13; // pharmacy vendor type
            }
            
           
            
            // user details 
             $user_id = $existingOrder['user_id'];
             $user_id_type = 0;
             
            // mno details
            $mno_id = $mno_id;
            $mno_id_type = 44;
            
            // phatmacy details
            $phatmacy_id = $listing_id;
            $phatmacy_id_type = $listing_id_type;
            
            // mw details 
            $mw_id = 0;
            $mw_id_type = 0;
            
            
            $invoice_no = $invoice_no; 
            $order_type = 1;
            
           // add for each transaction
            $user_comments = "";
            $mw_comments = "Package picked up";
            $vendor_comments = "";
            $payment_method = "";
            $credit = $total_without_dc;
            $debit = "";
            $transaction_of = 1; // entry for package
            // $payment_method = $paymentMethodsSelected['id'];
            $transaction_id = "";
            $trans_status = 1;
            
            // order picked up : package entry from pharmacy to mno
            
            // WORKING
             $res = $this->LedgerModel->create_ledger($mno_id, $invoice_no, $mno_id_type, $phatmacy_id, $phatmacy_id_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status);
            
            
            // delivery charges debit entry : 
                // -> if order from customer : entry in user_vendor_ledger
                // WORKING
                if($listing_type == 44){
                    $completed = 0;
                    
                    if($delivery_charges_by_customer != 0){
                       
                        $user_comments = "";
                        $mw_comments = "Delivery charges to be paid by customer";
                        $vendor_comments = "";
                        $payment_method = "";
                        $credit = $delivery_charges_by_customer;
                        $debit = "";
                        $transaction_of = 3; // entry for package
                         $transaction_id = "";
                        $trans_status = 1;
            
                        //in user_vendor_ledger against mno
                        // WORKING
                        $res1 = $this->LedgerModel->create_ledger($user_id, $invoice_no, $user_id_type, $mno_id, $mno_id_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status);
                        
                        if($delivery_charges_by_customer == $delivery_charges_to_mno){
                            $completed = 1;
                        }
                        
                    }
                    
                    
                    // if transactions not completed then check for vendor amount to mno
                    
                    if($delivery_charges_by_vendor != 0 && $completed != 1){
                        $user_comments = "";
                        $mw_comments = "Delivery charges to be paid by pharmacy";
                        $vendor_comments = "";
                        $payment_method = "";
                        if($delivery_charges_by_vendor > 0){
                            $credit = "";
                            $debit = $delivery_charges_by_vendor;
                        } else {
                            $credit = -($delivery_charges_by_vendor);
                            $debit = "";
                        }
                        
                        $transaction_of = 3; // entry for package
                         $transaction_id = "";
                        $trans_status = 1;
            
                        // WORKING
                        $res2 = $this->LedgerModel->create_ledger($mno_id, $invoice_no, $mno_id_type, $phatmacy_id, $phatmacy_id_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status);
                        
                    
                        
                        $total_from_cust_ven = $delivery_charges_by_customer + $delivery_charges_by_vendor;
                        
                        if($delivery_charges_to_mno == $total_from_cust_ven){
                            $completed = 1;
                        }
                        
                        
                    }
                    
                    // if transactions not completed then check for mw amount to mno
                    
                    if($delivery_charges_by_mw != 0 && $completed != 1){
                        $user_comments = "";
                        $mw_comments = "Delivery charges to be paid by medicalwale";
                        $vendor_comments = "";
                        $payment_method = "";
                        $credit = "";
                        $debit = $delivery_charges_by_mw;
                        $transaction_of = 3; // entry for package
                         $transaction_id = "";
                        $trans_status = 1;
            
                        // WORKING
                        $res3 = $this->LedgerModel->create_ledger($mno_id, $invoice_no, $mno_id_type, $mw_id, $mw_id_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status);
                        
                    }

                }
                 
                // -> if order from pharmacy : entry in vendor_vendor_ledger
                if($listing_type == 13){
                    $completed = 0;
                    
                    
                    if($delivery_charges_by_vendor != 0){
                        $user_comments = "";
                        $mw_comments = "Delivery charges to be paid by pharmacy";
                        $vendor_comments = "";
                        $payment_method = "";
                        if($delivery_charges_by_vendor >= 0){
                            $credit = "";
                            $debit = $delivery_charges_by_vendor;
                        } else {
                            $credit = -($delivery_charges_by_vendor);
                            $debit = "";
                        }
                        
                        $transaction_of = 3; // entry for package
                         $transaction_id = "";
                        $trans_status = 1;
            
                        // WORKING
                        $res1 = $this->LedgerModel->create_ledger($mno_id, $invoice_no, $mno_id_type, $phatmacy_id, $phatmacy_id_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status);
                        
                    
                        
                        if($delivery_charges_to_mno == $delivery_charges_by_vendor){
                            $completed = 1;
                        }
                        
                        
                    }
                    
                    if($delivery_charges_by_customer != 0 && $completed != 1){
                       
                        $user_comments = "";
                        $mw_comments = "Delivery charges to be paid by customer";
                        $vendor_comments = "";
                        $payment_method = "";
                        $credit = $delivery_charges_by_customer;
                        $debit = "";
                        $transaction_of = 3; // entry for package
                         $transaction_id = "";
                        $trans_status = 1;
            
                        //in user_vendor_ledger against mno
                        // WORKING
                        $res2 = $this->LedgerModel->create_ledger($user_id, $invoice_no, $user_id_type, $mno_id, $mno_id_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status);
                        
                        $total_from_cust_ven = $delivery_charges_by_customer + $delivery_charges_by_vendor;
                        
                        if($delivery_charges_to_mno == $total_from_cust_ven){
                            $completed = 1;
                        }
                        
                    }
                    
                     // if transactions not completed then check for mw amount to mno
                    
                    if($delivery_charges_by_mw != 0 && $completed != 1){
                        $user_comments = "";
                        $mw_comments = "Delivery charges to be paid by medicalwale";
                        $vendor_comments = "";
                        $payment_method = "";
                        $credit = "";
                        $debit = $delivery_charges_by_mw;
                        $transaction_of = 3; // entry for package
                         $transaction_id = "";
                        $trans_status = 1;
            
                        // WORKING
                        $res3 = $this->LedgerModel->create_ledger($mno_id, $invoice_no, $mno_id_type, $mw_id, $mw_id_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status);
                        
                    }
                    
                    
                }
                
                
            // amount entry if any transactions happend in  between pharmacy and mno  : i.e. if $paid_to_pharmacy or $received_from_pharmacy is greter than 0
           
            if($paid_to_pharmacy > 0){
                
                $user_comments = "";
                $mw_comments = $paid_to_pharmacy_comment != "" ? $paid_to_pharmacy_comment : "Night owl paid to  pharmacy";
                $vendor_comments = "";
            
                $payment_method = $paid_payment_method;
                $credit = "";
                $debit = $paid_to_pharmacy;
                
                $transaction_of = 2; // entry for package
                 $transaction_id = "";
                $trans_status = 1; // default success
            
                // not- WORKING
                $res1 = $this->LedgerModel->create_ledger($mno_id, $invoice_no, $mno_id_type, $phatmacy_id, $phatmacy_id_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status);
                        
                        
            }
            
            if($received_from_pharmacy > 0){
                $user_comments = "";
                $mw_comments = $received_from_pharmacy_comment != "" ? $received_from_pharmacy_comment : "Night owl received from pharmacy";
                $vendor_comments = $received_including_delivery_charges == true ? "Amount is including delivery charges" : "Amount is excluding including delivery charges" ;
                $payment_method = $received_payment_method;
                $credit = $received_from_pharmacy;
                $debit = "";
                
                $transaction_of = 2; // entry for package
                 $transaction_id = "";
                $trans_status = 1; // default success
            
                //  WORKING
                $res1 = $this->LedgerModel->create_ledger($mno_id, $invoice_no, $mno_id_type, $phatmacy_id, $phatmacy_id_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status);
                        
            }

            $mno_name = $existingOrder['mno_name'];
            $action_by_status = "Night owl";
            $orderStatus = "Order pickedup by night owl ".$mno_name;
            $order_status = $this->OrderModel->update_status($invoice_no,$orderStatus, $action_by_status);
            
            $title = "Order pickedup";
            $reg_id = $existingOrder['token'];
            $msg = "Night owl pickedup order from pharmacy store and night owl is out for delivery";
            $img_url   = 'https://medicalwale.com/img/noti_icon.png';
            $tag = "text";
            $agent = $existingOrder['agent'];
            $invoice_no = $invoice_no;
            $notification_type = 'mno_pickedup_order';
            $res = $this->PartnermnoModel->send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent, $invoice_no,$notification_type);
            
            $receiver_id = $mno_id;
            $invoice_no = $invoice_no;
            $notification_type = 12; //OUT_FOR_DELIVERY refer mno_notification_types
            $img = "https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png";
            $mno_order_id = $existingOrder['id'];
            $title = "Order pickedup ";
            $msg = "Order pickedup of order id ".$invoice_no." and out for delivery";
    
            $response = $this->PartnermnoModel->send_gcm_notification_to_mno($receiver_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
        
            
            
            if($order_status == 1){
                return 1;
            } else {
                return 3;
            }
        } else {
            return 2;
        }
    }
    
    
    public function order_delivered($mno_id,$invoice_no){
        $this->load->model('OrderModel');
       $existingOrder = $this->db->query("SELECT uo.user_id,uo.delivery_charge,uo.delivery_charges_by_customer, uo.delivery_charges_by_vendor, uo.delivery_charges_by_mw,  uo.total_without_dc,uo.listing_type,uo.payment_method,uo.order_total, uo.listing_id,  uo.delivery_charges_to_mno, uo.suggested_pharmacy_id, ml.mno_name, mo.*, u.id as user_id, u.phone, u.token, u.agent FROM `mno_orders` as mo left join user_order as uo on (mo.invoice_no = uo.invoice_no) left join users as u on (uo.user_id = u.id) left join mno_list as ml on (ml.mno_id = mo.mno_id) WHERE mo.`invoice_no` = '$invoice_no' AND mo.`mno_id` = '$mno_id' AND mo.`ongoing` = 1 AND mo.`status` = 'accepted'")->row_array();
       
        if($existingOrder > 0 ){
            $mno_name = $existingOrder['mno_name'];
            $action_by_status = "Night owl";
            $orderStatus = "Order delivered by night owl ".$mno_name;
            $order_status = $this->OrderModel->update_status($invoice_no,$orderStatus, $action_by_status);
            
            // after status update insert in ledger
            
            
            //add package delivered in ledger table
            
            $suggested_pharmacy_id = $existingOrder['suggested_pharmacy_id'];
            $order_total = $existingOrder['order_total'];
            $listing_id = $existingOrder['listing_id'];
            
            $listing_type = $existingOrder['listing_type'];
            $total_without_dc = $existingOrder['total_without_dc'];
            $payment_method = $existingOrder['payment_method'];
            $delivery_charges_to_mno = $existingOrder['delivery_charges_to_mno'];
            $delivery_charges_by_customer = $existingOrder['delivery_charges_by_customer'];
            $delivery_charges_by_vendor = $existingOrder['delivery_charges_by_vendor'];
            $delivery_charges_by_mw = $existingOrder['delivery_charges_by_mw'];
            
            if($suggested_pharmacy_id > 0){
                $listing_id = $suggested_pharmacy_id;
                $listing_id_type = 51; // suggested pharmacy vendor type
            } else {
                $listing_id = $listing_id;
                $listing_id_type = 13; // pharmacy vendor type
            }
            
           
            
            // user details 
             $user_id = $existingOrder['user_id'];
             $user_id_type = 0;
             
            // mno details
            $mno_id = $mno_id;
            $mno_id_type = 44;
            
            // phatmacy details
            $phatmacy_id = $listing_id;
            $phatmacy_id_type = $listing_id_type;
            
            // mw details 
            $mw_id = 0;
            $mw_id_type = 0;
            
            
            $invoice_no = $invoice_no; 
            $order_type = 1;
            
           // add for each transaction
            $user_comments = "";
            $mw_comments = "Package delivered to customer";
            $vendor_comments = "";
            $payment_method = "";
            $credit = $total_without_dc;
            $debit = "";
            $transaction_of = 1; // entry for package
            $transaction_id = "";
            $trans_status = 1;
            //print_r($total_without_dc); die();
            
            //  WORKING
           // $user_id, $user_id_type, $mno_id, $mno_id_type
            $res = $this->LedgerModel->create_ledger($user_id, $invoice_no, $user_id_type, $mno_id, $mno_id_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status);
            
            
            // settle user
            $res = $this->LedgerModel->settle_ledger($user_id,$user_id_type, $invoice_no);
         
            
            // settle MNO
            $res = $this->LedgerModel->settle_ledger($mno_id,$mno_id_type, $invoice_no);
            
            // settle Pharmacy
            $res = $this->LedgerModel->settle_ledger($phatmacy_id,$phatmacy_id_type, $invoice_no);
            
            // END ledger entries and settle
            
            $title = "Order delivered";
            $reg_id = $existingOrder['token'];
            $msg = "Night owl delivered your order";
            $img_url   = 'https://medicalwale.com/img/noti_icon.png';
            $tag = "text";
            $agent = $existingOrder['agent'];
            $invoice_no = $invoice_no;
            $notification_type = 'mno_delivered_order';
            $res = $this->PartnermnoModel->send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent, $invoice_no,$notification_type);
            
            
            $changeOrderStatus = $this->db->query("UPDATE `mno_orders` SET `ongoing`= 0 WHERE `invoice_no` = '$invoice_no' AND `mno_id` = '$mno_id' AND `ongoing` = 1 AND `status` = 'accepted'");
            $updateUserOrderTable = $this->db->query("UPDATE user_order set `order_status` = 'Order Delivered', `cancel_reason`='',`action_by`='night owl'  WHERE `invoice_no` = '$invoice_no'");
            
            // 
            
             $receiver_id = $mno_id;
            $invoice_no = $invoice_no;
            $notification_type = 15; //ORDER_DELIVERED refer mno_notification_types
            $img = "https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png";
            $mno_order_id = $existingOrder['id'];
            $title = "Order delivered ";
            $msg = "Order delivered of order id $invoice_no";
    
            $response = $this->PartnermnoModel->send_gcm_notification_to_mno($receiver_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
        
            $getMnoLocation = $this->db->query("SELECT * FROM `mno_location` WHERE `mno_id` = '$mno_id' ORDER BY `created_at` DESC ")->row_array();
            
            $lat = $getMnoLocation['lat'];
            $lng = $getMnoLocation['lng'];
            
            $assignAnotherOrder = $this->PartnermnoModel->assign_pending_order_to_mno($mno_id, $lat, $lng);
           
        
        
            if($order_status == 1){
                return 1;
            } else {
                return 3;
            }
        } else {
            return 2;
        }
    }
    
    
    
    
     // mno_out_for_pickup
    
    public function mno_out_for_pickup($mno_id,$invoice_no){
        $this->load->model('OrderModel');
         $existingOrder = $this->db->query("SELECT ml.mno_name, mo.*, u.id as user_id, u.phone, u.token, u.agent FROM `mno_orders` as mo left join user_order as uo on (mo.invoice_no = uo.invoice_no) left join users as u on (uo.user_id = u.id) left join mno_list as ml on (ml.mno_id = mo.mno_id) WHERE mo.`invoice_no` = '$invoice_no' AND mo.`mno_id` = '$mno_id' AND mo.`ongoing` = 1 AND mo.`status` = 'accepted'")->row_array();
       
        if($existingOrder > 0){
            // echo $order_pickedup; die();
            $mno_name = $existingOrder['mno_name'];
            $action_by_status = "Night owl";
            $orderStatus = "Night owl ". $mno_name ." is on its way to pickup the order";
           
            $order_status = $this->OrderModel->update_status($invoice_no,$orderStatus, $action_by_status);
           
            
            $title = "On the way to pickedup";
            $reg_id = $existingOrder['token'];
            $msg = "Night owl is on the way to pickedup the order from pharmacy store.";
            $img_url   = 'https://medicalwale.com/img/noti_icon.png';
            $tag = "text";
            $agent = $existingOrder['agent'];
            $invoice_no = $invoice_no;
            $notification_type = 'mno_out_for_pickup';
            $res = $this->PartnermnoModel->send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent, $invoice_no,$notification_type);
            
            
            
            $receiver_id = $mno_id;
            $invoice_no = $invoice_no;
            $notification_type = 10; //OUT_FOR_PICKUP refer mno_notification_types
            $img = "https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png";
            $mno_order_id = $existingOrder['id'];
            $title = "On the way to pickedup";
            $msg = "On the way to pickedup for order id $invoice_no";
    
            $response = $this->PartnermnoModel->send_gcm_notification_to_mno($receiver_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
        
        
            
                return 1;
            
        } else {
            return 2;
        }
    }
    
    // at_store
    
    public function at_store($mno_id,$invoice_no){
        $this->load->model('OrderModel');
          $existingOrder = $this->db->query("SELECT ml.mno_name, mo.*, u.id as user_id, u.phone, u.token, u.agent FROM `mno_orders` as mo left join user_order as uo on (mo.invoice_no = uo.invoice_no) left join users as u on (uo.user_id = u.id) left join mno_list as ml on (ml.mno_id = mo.mno_id) WHERE mo.`invoice_no` = '$invoice_no' AND mo.`mno_id` = '$mno_id' AND mo.`ongoing` = 1 AND mo.`status` = 'accepted'")->row_array();
          
        if($existingOrder > 0 ){
            
            $mno_name = $existingOrder['mno_name'];
            $action_by_status = "Night owl";
            $orderStatus = "Night owl ".$mno_name. " reached at store";
           
            $order_status = $this->OrderModel->update_status($invoice_no,$orderStatus, $action_by_status);
           
            
            // $order_status = $this->OrderModel->update_status($invoice_no,$orderStatus, $action_by_status);
            
            $action_by_status = "Night owl";
            $orderStatus = "Night owl at store";
            
            $title = "Night owl at pharmacy";
            $reg_id = $existingOrder['token'];
            $msg = "Night owl reached at pharmacy store";
            $img_url   = 'https://medicalwale.com/img/noti_icon.png';
            $tag = "text";
            $agent = $existingOrder['agent'];
            $invoice_no = $invoice_no;
            $notification_type = 'MNO_AT_STORE';
            $res = $this->PartnermnoModel->send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent, $invoice_no,$notification_type);
            
            
            $receiver_id = $mno_id;
            $invoice_no = $invoice_no;
            $notification_type = 11; //ARRIVED_AT_PHARMACY refer mno_notification_types
            $img = "https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png";
            $mno_order_id = $existingOrder['id'];
            $title = "Reached at store";
            $msg = "Night owl arrived at pharmacy";
    
            $response = $this->PartnermnoModel->send_gcm_notification_to_mno($receiver_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
            
            return 1;
            
        } else {
            return 2;
        }
    }
    
    
    // at_doorstep
    
    public function at_doorstep($mno_id,$invoice_no){
        $this->load->model('OrderModel');
        $existingOrder = $this->db->query("SELECT ml.mno_name, mo.*, u.id as user_id,uo.name as user_name, u.phone, u.token, u.agent FROM `mno_orders` as mo left join user_order as uo on (mo.invoice_no = uo.invoice_no) left join users as u on (uo.user_id = u.id) left join mno_list as ml on (ml.mno_id = mo.mno_id) WHERE mo.`invoice_no` = '$invoice_no' AND mo.`mno_id` = '$mno_id' AND mo.`ongoing` = 1 AND mo.`status` = 'accepted'")->row_array();
       
        if($existingOrder > 0 ){
            $mno_name = $existingOrder['mno_name'];
            $user_name = $existingOrder['user_name'];
            
            $action_by_status = "Night owl";
            $orderStatus = "Night owl ". $mno_name ." has reached at "  .$user_name.  " location";
             $order_status = $this->OrderModel->update_status($invoice_no,$orderStatus, $action_by_status);
            
          
            
            $title = "Night owl at doorstep";
            $reg_id = $existingOrder['token'];
            $msg = "Night owl reached at doorstep";
            $img_url   = 'https://medicalwale.com/img/noti_icon.png';
            $tag = "text";
            $agent = $existingOrder['agent'];
            $invoice_no = $invoice_no;
            $notification_type = 'MNO_AT_DOORSTEP';
            $res = $this->PartnermnoModel->send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent, $invoice_no,$notification_type);
            
            
            $receiver_id = $mno_id;
            $invoice_no = $invoice_no;
            $notification_type = 13; //ARRIVED_AT_DELIVERY_LOCATION refer mno_notification_types
            $img = "https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png";
            $mno_order_id = $existingOrder['id'];
            $title = "Reached at doorstep";
            $msg = "Night owl arrived at doorstep";
    
            $response = $this->PartnermnoModel->send_gcm_notification_to_mno($receiver_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
                    
            return 1;
            
        } else {
            return 2;
        }
    }
    
    // payment_accepted 
    
    public function payment_accepted($mno_id,$invoice_no, $payment_id,$amount){
            $this->load->model('OrderModel');
            // dont know what to do with amount =>  done
            // used to add in ledger entries
            
            $existingOrder = $this->db->query("SELECT uo.user_id,uo.delivery_charge,uo.delivery_charges_by_customer, uo.delivery_charges_by_vendor, uo.delivery_charges_by_mw,  uo.total_without_dc,uo.listing_type,uo.payment_method,uo.order_total, uo.listing_id,  uo.delivery_charges_to_mno, uo.suggested_pharmacy_id,  ml.mno_name, mo.*, u.id as user_id,uo.listing_id,uo.name as user_name, u.phone, u.token, u.agent FROM `mno_orders` as mo left join user_order as uo on (mo.invoice_no = uo.invoice_no) left join users as u on (uo.user_id = u.id) left join mno_list as ml on (ml.mno_id = mo.mno_id) WHERE mo.`invoice_no` = '$invoice_no' AND mo.`mno_id` = '$mno_id' AND mo.`ongoing` = 1 AND mo.`status` = 'accepted'")->row_array();
        
            
        $pharmacyDetails = array();
        if($payment_id != "" &&  $amount > 0){
        
            $paymentMethods = $this->db->query("SELECT * FROM `payment_method` WHERE `parent_id` = '$payment_id'")->result_array(); 
            if(sizeof($paymentMethods) > 0){
                return 4; //please send subtype id
            } else {
                
                $paymentMethodsSelected = $this->db->query("SELECT * FROM `payment_method` WHERE `id` = '$payment_id'")->row_array(); 
                
                if(sizeof($paymentMethodsSelected) > 0){
                   
                    $paymentMethosName = $paymentMethodsSelected['payment_method'];
                    
                    // echo $paymentMethosName; die();
                    
                    
           
                    if(sizeof($existingOrder) > 0 ){
                        
                        
                        $listing_id = $existingOrder['listing_id'];
                        
                        $mno_name = $existingOrder['mno_name'];
                        $action_by_status = "Night owl";
                        
                        $orderStatus = "Night owl ".$mno_name." has accepted the payment of Rs. ". $amount ." via " .$paymentMethosName ;
                        $order_status = $this->OrderModel->update_status($invoice_no,$orderStatus, $action_by_status);
                        
                        
                        //call ledger api here
                        // accepted payment and payment method option
                        
                       // print_r($existingOrder); die();
                        
                        $suggested_pharmacy_id = $existingOrder['suggested_pharmacy_id'];
                        $listing_id = $existingOrder['listing_id'];
                        $listing_type = $existingOrder['listing_type'];
                        /*$delivery_charges_to_mno = $existingOrder['delivery_charges_to_mno'];
                        $delivery_charges_by_customer = $existingOrder['delivery_charges_by_customer'];
                        $delivery_charges_by_vendor = $existingOrder['delivery_charges_by_vendor'];
                        $delivery_charges_by_mw = $existingOrder['delivery_charges_by_mw'];
                        */
                        if($suggested_pharmacy_id > 0){
                            $listing_id = $suggested_pharmacy_id;
                            $listing_id_type = 51; // suggested pharmacy vendor type
                        } else {
                            $listing_id = $listing_id;
                            $listing_id_type = 13; // pharmacy vendor type
                        }
                        
                       
                        
                        // user details 
                         $user_id = $existingOrder['user_id'];
                         $user_id_type = 0;
                         
                        // mno details
                        $mno_id = $mno_id;
                        $mno_id_type = 44;
                        
                        // phatmacy details
                        $phatmacy_id = $listing_id;
                        $phatmacy_id_type = $listing_id_type;
                        
                        // mw details 
                        $mw_id = 0;
                        $mw_id_type = 0;
                        
                        
                        $invoice_no = $invoice_no; 
                        $order_type = 1;
                        
                       // add for each transaction
                        $user_comments = "";
                        $mw_comments = "Payment accepted from user Rs. ". $amount ." via " .$paymentMethosName. " by night owl";
                        $vendor_comments = "";
                        $payment_method = $payment_id;
                        $credit = "";
                        $debit = $amount;
                        $transaction_of = 2; // entry for amount
                         $transaction_id = "";
                        $trans_status = 1; // default success
                
                        // payment accepted entry 
                        
                            // == if cap means given to MNO
                        if($payment_method == 3){ // payment in CAP: refer payment_method
                            // WORKING
                            // $user_id , $user_id_type , $mno_id, $mno_id_type
                            $transaction_id = "";
                            $trans_status = 1;
                
                             
                            $res = $this->LedgerModel->create_ledger($user_id, $invoice_no, $user_id_type, $mno_id, $mno_id_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status);
                            
                        } else {
                            
                            //== if not cap means given to pharmacy
                            
                            // WORKING
                            // $user_id , $user_id_type , $phatmacy_id, $phatmacy_id_type
                             $mw_comments = "Payment accepted from user by night owl";
                             
                            $res = $this->LedgerModel->create_ledger($user_id, $invoice_no, $user_id_type, $phatmacy_id, $phatmacy_id_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status);
                            
                        }
                        // 
                        
                        $title = "Payment accepted";
                        $reg_id = $existingOrder['token'];
                        // $msg = "Night owl accepted payment of order id $invoice_no using $paymentMethosName";
                        $msg = "Night owl ".$mno_name." has accepted the payment of Rs. ". $amount ." via " .$paymentMethosName ;
                        $img_url   = 'https://medicalwale.com/img/noti_icon.png';
                        $tag = "text";
                        $agent = $existingOrder['agent'];
                        $invoice_no = $invoice_no;
                        $notification_type = 'mno';
                        $res = $this->PartnermnoModel->send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent, $invoice_no,$notification_type);
                        
                        
                        $changeOrderPaymentMethod = $this->db->query("UPDATE `user_order` SET `payment_method`= '$paymentMethosName' WHERE `invoice_no` = '$invoice_no' ");
                        
                      
                        if($listing_id != "" && $listing_id != 0 && $listing_id != ""){
                            $pharmacyDetails = $this->db->query("SELECT agent as ph_agent, token as ph_token from users where id = '$listing_id'")->row_array();
                            
                            $ph_agent = $pharmacyDetails['ph_agent'];
                            $ph_token = $pharmacyDetails['ph_token'];
                            
                              //send notificaion to vendor
                        
                            $title = "Payment accepted by night owl";
                            // $msg = "Night owl accepted  payment using $paymentMethosName";
                            $msg = "Night owl ".$mno_name." has accepted the payment of Rs. ". $amount ." via " .$paymentMethosName ;
                            $img_url = 'https://medicalwale.com/img/noti_icon.png';
                            $tag = "";
                            $notification_type = "payment_accepted";
                            $this->send_gcm_notify_pharmacy($title, $ph_token, $msg, $img_url, $tag, $ph_agent, $invoice_no,$notification_type);
                            
                        }
                        
                        $receiver_id = $mno_id;
                        $invoice_no = $invoice_no;
                        $notification_type = 14; //ORDER_DELIVERED refer mno_notification_types
                        $img = "https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png";
                        $mno_order_id = $existingOrder['id'];
                        $title = "Payment accepted";
                        $msg = "Payment accepted of by night owl of order id $invoice_no";
                
                        $response = $this->PartnermnoModel->send_gcm_notification_to_mno($receiver_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
                    
                        
                    
                          return 1;
                        
                    } else {
                        
                        return 2;  // no existing order
                    }
                } else {
                    return 5;
                }    
            }
        } else {
            if(sizeof($existingOrder) > 0){ 
                
                
                $listing_id = $existingOrder['listing_id'];
                $mno_name = $existingOrder['mno_name'];
                $action_by_status = "Night owl";
                
                $orderStatus = "Night owl ".$mno_name." did not accepted the payment" ;
                $order_status = $this->OrderModel->update_status($invoice_no,$orderStatus, $action_by_status);
                
                        
                $receiver_id = $mno_id;
                $invoice_no = $invoice_no;
                $notification_type = 14; //PAYMENT_ACCEPTED refer mno_notification_types
                $img = "https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png";
                $mno_order_id = $existingOrder['id'];
                $title = "Payment not accepted";
                $msg = "Night owl did not accept Payment from user of order id $invoice_no";
        
                $response = $this->PartnermnoModel->send_gcm_notification_to_mno($receiver_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
            
                return 1; // amount is 0
            } else {
                return 2; // no existing order
            }
            
        }
    }
    
    
    
    // check_pending_order($user_id,$lat,$lng)
    
    public function check_pending_order($mno_id,$lat,$lng){
        date_default_timezone_set('Asia/Calcutta');
        $ret = "";
        $todayDate = date('Y-m-d H:i:s');
        $query = $this->db->query("SELECT id,otp_code,name,email,phone,city,vendor_id FROM users WHERE id='$mno_id' and vendor_id='44'")->row_array();
        echo "SELECT id,otp_code,name,email,phone,city,vendor_id FROM users WHERE id='$mno_id' and vendor_id='44'"; die();
        if(sizeof($query) > 0){
            
        } else {
            $ret = 3;
        }
        return $ret;
    }
    
    public function tracker($invoice_no) {
        $statusdata = array();
        $getStatuses = $this->db->query("SELECT * FROM `user_order_tracking` WHERE `invoice_no` LIKE '$invoice_no' ORDER BY `created_at` ASC")->result_array();
        
        foreach($getStatuses as $statuses){
            $created_at = date_create($statuses['created_at']);
            $d = date_format($created_at, 'D jS F Y, g:ia');
            // echo $d ; die();
            $action_by = strtolower($statuses['action_by']);
            $t['timestamp'] = $d;
            $t['status'] = $statuses['status'] ." by ". $action_by ; 
            $statusdata[] = $t;
        }
        
        return $statusdata;
        
     }
    
    public function send_gcm_notification_to_mno($receiver_id,$invoice_no, $mno_orders_id, $notification_type, $img, $title, $msg) {
        $order_details = array();
        date_default_timezone_set('Asia/Kolkata');
        $date = date('j M Y h:i A');
    
        $data = $customer_details = $pharmacy_details  = $result = array();
        $getUserInfo = $this->db->query("SELECT `token`,`name`, `phone`, `email`, `vendor_id`,`agent` FROM `users` where id='$receiver_id'")->row_array();
        
        $token = $getUserInfo['token'];
        $agent = $getUserInfo['agent'];
        if($token != ""){
            if (!defined("GOOGLE_GCM_URL")){
               define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
            }
            
    
            $notificationCheck = $this->db->query("SELECT * FROM `mno_notifications` WHERE `invoice_no` LIKE '$invoice_no' AND `mno_orders_id` = '$mno_orders_id' AND `notification_type` = '$notification_type' ORDER BY `id` ASC")->row_array();
            
            
            
            if(sizeof($notificationCheck) == 0){
                $this->db->query("INSERT INTO `mno_notifications`( `receiver_id`, `invoice_no`,`mno_orders_id`,`notification_type`, `img`, `title`, `msg`) VALUES ('$receiver_id','$invoice_no','$mno_orders_id','$notification_type','$img','$title','$msg')");
            }
            /*$getCutomerPharmacyInfo = $this->db->query("SELECT u.name,u.phone, ms.medical_name as store_name,ms.contact_no ,msp.medical_name as suggested_store_name,msp.contact_no as suggested_contact_no, mo.mno_id, uo.invoice_no, uo.listing_id, uo.suggested_pharmacy_id, uo.user_id FROM `user_order` as uo left join mno_orders as mo on (mo.invoice_no = uo.invoice_no && mo.status = 'accepted' && mo.redircted_to = 0) left join users as u on (u.id = uo.user_id) left join medical_stores as ms on (uo.listing_id = ms.user_id and uo.listing_id != '') left join mno_suggested_pharmacies as msp on (uo.suggested_pharmacy_id = msp.id && (uo.suggested_pharmacy_id != 0 || uo.suggested_pharmacy_id != '')) WHERE uo.`invoice_no` LIKE '$invoice_no' group by uo.invoice_no    ")->row_array();
            
            $customer_details= array(
                                    "user_id" => $getCutomerPharmacyInfo['user_id'],
                                    "user_name" => $getCutomerPharmacyInfo['name'],
                                    "user_contact" => $getCutomerPharmacyInfo['phone']
                                );
            $pharmacy_details= array(
                                    "pharmacy_name" => $getCutomerPharmacyInfo['store_name'] ? $getCutomerPharmacyInfo['store_name'] : $getCutomerPharmacyInfo['suggested_store_name'],
                                    "pharmacy_contact" => $getCutomerPharmacyInfo['contact_no'] ? $getCutomerPharmacyInfo['contact_no'] : $getCutomerPharmacyInfo['suggested_contact_no'],
                                );
            
            $tracker = $this->PartnermnoModel->tracker_notifications($invoice_no, $mno_orders_id);
            
            $data['customer_details'] = $customer_details;
            $data['pharmacy_details'] = $pharmacy_details;
            $data['steps'] = $tracker;*/
            
            $order_details = $this->PartnermnoModel->order_details($receiver_id , $invoice_no);
            if(array_key_exists('data',$order_details) && sizeof($order_details['data']) > 0){
                 $detailsData =  $order_details['data'];
            } else {
                $detailsData = null;
            }
           
            $fields = array(
               'to' => $token,
               'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array(
                   "title" => $title,
                   "msg" => $msg,
                   "img" => $img,
                   "sound" => 'default',
                   "notification_type" => intval($notification_type),
                   "invoice_no" => $invoice_no,
                   "data" => $detailsData
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
           
          curl_close($ch);
          $result = $fields['data'];
        }
        return $result;    
   }
   
   
    public function tracker_notifications($invoice_no,$mno_orders_id) {
        $allStatuses = $statusdata = array();
        $getNotificationTypes = $this->db->query("SELECT * FROM `mno_notification_types` WHERE `type_for` = 1 ORDER BY `step_id` ASC")->result_array();
   //     echo "SELECT * FROM `mno_notifications` WHERE `invoice_no` LIKE '$invoice_no' AND `mno_orders_id` = '$mno_orders_id' ORDER BY `id` ASC"; die();
        $getStatuses = $this->db->query("SELECT * FROM `mno_notifications` WHERE `invoice_no` LIKE '$invoice_no' AND `mno_orders_id` = '$mno_orders_id' ORDER BY `id` ASC")->result_array();
        $getOrderInfo = $this->db->query("SELECT uo.suggested_pharmacy_id, uo.listing_id, mo.pharmacy_didnot_respond, uo.listing_type FROM `user_order` as uo left join mno_orders as mo on (uo.invoice_no = mo.invoice_no ) WHERE mo.`invoice_no` LIKE '$invoice_no' ")->row_array();
        // print_r($getStatuses); die();
        $listing_type = $getOrderInfo['listing_type'];
        $oldStepId = 0;
        foreach($getNotificationTypes as $g){
            $step_id = intval($g['step_id']);
            if($oldStepId != $step_id){
                $s['message'] = $g['notification_type_description'];
                $s['step_msg'] = $g['step_description'];
                $s['step_id'] = $g['step_id'];
                $s['message_1'] = null;
                $s['message_2'] = null;
                $s['type'] = intval($g['id']);
                $s['type_1'] = null;
                $s['type_2'] = null;
                $s['time'] = null;
                $s['is_completed'] = 0;
                $statuses[] = $s;
            } else {
              //  print_r($g); 
                $cuurentOne = sizeof($statuses) - 1;
                if($statuses[$cuurentOne]['type_1'] == null){
                    $statuses[$cuurentOne]['type_1'] = intval($g['id']);
                    $statuses[$cuurentOne]['message_1'] = $g['notification_type_description'];
                } else {
                    $statuses[$cuurentOne]['type_2'] = intval($g['id']);
                    $statuses[$cuurentOne]['message_2'] = $g['notification_type_description'];
                }
                
                
            }
            $oldStepId = $step_id;
        }
  
        $pharmacy_id = $getOrderInfo['listing_id'];
        $suggested_pharmacy_id = $getOrderInfo['suggested_pharmacy_id'];
        $pharmacy_didnot_respond = $getOrderInfo['pharmacy_didnot_respond'];
  
        $removedNulls = array();
        $cancelledInCurrentStep = $cancelled = 0;
        if($listing_type == 13){
            foreach($statuses as $st){
                 $cancelled = $cancelledInCurrentStep;
             //    print_r($statuses); die();
                $find1 = $find = -1;
                $type = intval($st['type']);
                $type_1 = intval($st['type_1']);
               
                $find = array_search($type,array_column($getStatuses, 'notification_type'));
                $find1 = array_search($type_1,array_column($getStatuses, 'notification_type'));
                      //  echo $find."===";
                if($type == 27 && $find > -1){
                    $i=0;
                    foreach($allStatuses as $added){
                        if($added['type'] != null){
                            $removedNulls[] =  $added;
                        }
                        $i++;
                    }
                    $cancelledInCurrentStep = 1;
                    $allStatuses = $removedNulls;
                }
                
                
                if($find > -1){
                    // print_r($st);
                    $st['is_completed'] = 1;
                    $st['time'] = strtotime($getStatuses[$find]['created_at']);
                    $st['message'] = $getStatuses[$find]['msg'];
                     
                } else {
                    $st['type'] = null;
                    $st['message'] = $st['step_msg'];
                }
                 if($find1 > -1 && $type == 8){
                            $getSTatusMsg = array_search(6,array_column($statuses, 'step_id'));
                            $currentStatusCount = sizeof($allStatuses) - 1;
                          
                            
                             $st['message'] = $getStatuses[$find1]['title'];
                            $st['type'] = $statuses[$getSTatusMsg]['type_1'];
                            $st['time'] = strtotime($getStatuses[$find1]['created_at']);
                            $cancelledInCurrentStep = 1;
                           // $st['is_completed'] = 1;
                            
                        } 
                
                if($type > 9){
                    
                        $st['step'] = intval($st['step_id']);
                        $st['message'] = $st['message'];
                    
                    if($find1 > -1){
                        if($type_1 == 6){
                           
                             $getSTatusMsg = array_search(5,array_column($statuses, 'type'));
                          //  echo $getSTatusMsg; die();
                      //      print_r($statuses[$getSTatusMsg]['step_msg']); die();
                            $currentStatusCount = sizeof($allStatuses) - 1;
                            $st['message'] = $statuses[$getSTatusMsg]['message_1'];
                            $st['type'] = $statuses[$getSTatusMsg]['type_1'];
                            $st['time'] = strtotime($getStatuses[$find1]['created_at']);
                           // $st['is_completed'] = 1;
                             
                                
                        } 

                    }
                        
                    if($type != 9 ){
                        unset($st['is_completed']);
                        unset($st['step_id']);
                        unset($st['type_1']);
                        unset($st['step_msg']);
                        
                        unset($st['type_1']);
                        unset($st['message_1']);
                        unset($st['time_1']);
                        
                        unset($st['message_2']);
                        unset($st['time_2']);



                        if($type <= 15 && $cancelled == 0){
                            $allStatuses[] = $st;
                        } else  if($type > 15 &&  $st['type'] != ""){
                            $allStatuses[] = $st;
                        } 
                    } 
                   
                    
                }     
            
              
            }
        } else if($suggested_pharmacy_id > 0 && $suggested_pharmacy_id != null && $suggested_pharmacy_id != "" ){
            foreach($statuses as $st){
                $cancelled = $cancelledInCurrentStep;
                $find1 = $find = -1;
                $type = intval($st['type']);
                $type_1 = intval($st['type_1']);
               
                $find = array_search($type,array_column($getStatuses, 'notification_type'));
                $find1 = array_search($type_1,array_column($getStatuses, 'notification_type'));
                      //  echo $find."===";
               
                if($type == 27 && $find > -1){
                    $i=0;
                    foreach($allStatuses as $added){
                        if($added['type'] != null){
                            $removedNulls[] =  $added;
                        }
                        $i++;
                    }
                    $cancelledInCurrentStep = 1;
                    $allStatuses = $removedNulls;
                }
                
                if($find > -1){
                    // print_r($st);
                    $st['is_completed'] = 1;
                    $st['time'] = strtotime($getStatuses[$find]['created_at']);
                    $st['message'] = $getStatuses[$find]['msg'];
                    
                    
                     
                } else {
                    $st['type'] = null;
                    $st['message'] = $st['step_msg'];
                }
           
                if($find1 > -1 && $type == 8){
                    $getSTatusMsg = array_search(6,array_column($statuses, 'step_id'));
                    $currentStatusCount = sizeof($allStatuses) - 1;
                 
                    
                    // $st['message'] = $statuses[$getSTatusMsg]['message_1'];
                    $st['message'] = $getStatuses[$find1]['title'];
                    $st['type'] = $statuses[$getSTatusMsg]['type_1'];
                    $st['time'] = strtotime($getStatuses[$find1]['created_at']);
                    $cancelledInCurrentStep = 1;
                   // $st['is_completed'] = 1;
                    
                } 
                
                    
                if($type > 6 && $type != 10 && $type != 11){
                      $st['step'] = intval($st['step_id']);
                        $st['message'] = $st['message'];
                        
                   
                        unset($st['is_completed']);
                        unset($st['step_id']);
                         unset($st['type_1']);
                        unset($st['step_msg']);
                        unset($st['type_2']);
                         unset($st['type_1']);
                        unset($st['message_1']);
                        unset($st['time_1']);
                        unset($st['message_2']);
                        unset($st['time_2']);
                        
                       
                        if($type <= 15 && $cancelled == 0){
                            $allStatuses[] = $st;
                        } else  if($type > 15 &&  $st['type'] != "" && $cancelled == 0){
                            $allStatuses[] = $st;
                        } 
                        
                   
                }  
            }
        } 
        
        else if($pharmacy_didnot_respond > 0){
            
             foreach($statuses as $st){
                 
                $cancelled = $cancelledInCurrentStep;
              //  print_r($statuses); die();
                $find2 = $find1 = $find = -1;
                $type = intval($st['type']);
                $type_1 = intval($st['type_1']);
                $type_2 = intval($st['type_2']);
                
                $find = array_search($type,array_column($getStatuses, 'notification_type'));
                $find1 = array_search($type_1,array_column($getStatuses, 'notification_type'));
                $find2 = array_search($type_2,array_column($getStatuses, 'notification_type'));
                
                      //  echo $find."===";
                      
                if($type == 27 && $find > -1){
                    $i=0;
                    foreach($allStatuses as $added){
                        if($added['type'] != null){
                            $removedNulls[] =  $added;
                        }
                        $i++;
                    }
                    $cancelledInCurrentStep = 1;
                    $allStatuses = $removedNulls;
                }
                
                
                    
                if($find > -1){
                    // print_r($st);
                    $st['is_completed'] = 1;
                    $st['time'] =  strtotime($getStatuses[$find]['created_at']);
                     //$st['time1'] =  $getStatuses[$find]['created_at'];
                    $st['message'] = $getStatuses[$find]['msg'];
                     
                } else {
                    $st['type'] = null;
                    $st['message'] = $st['step_msg'];
                }
                
                
                    if($type != 1 && $type != 2 && $type != 3){
                    
                        $st['step'] = intval($st['step_id']);
                        $st['message'] = $st['message'];
                    
                        if($find1 > -1 && $type == 5){
                            $getSTatusMsg = array_search(4,array_column($statuses, 'step_id'));
                        
                            $currentStatusCount = sizeof($allStatuses) - 1;
                            
                            $st['message'] = $statuses[$getSTatusMsg]['message_1'];
                            $st['type'] = $statuses[$getSTatusMsg]['type_1'];
                           
                           // $st['is_completed'] = 1;
                            
                        } 
                        
                      
                        
                        if($find2 > -1 && $type == 5){
                            $getSTatusMsg = array_search(4,array_column($statuses, 'step_id'));
                        
                            $currentStatusCount = sizeof($allStatuses) - 1;
                            
                            $st['message'] = $statuses[$getSTatusMsg]['message_2'];
                            $st['type'] = $statuses[$getSTatusMsg]['type_2'];
                            
                           // $st['is_completed'] = 1;
                            
                        }
                        
                         if($find1 > -1 && $type == 8){
                            $getSTatusMsg = array_search(6,array_column($statuses, 'step_id'));
                            $currentStatusCount = sizeof($allStatuses) - 1;
                            
                            /*$st['message'] = $statuses[$getSTatusMsg]['message_1'];
                            $st['type'] = $statuses[$getSTatusMsg]['type_1'];*/
                            $st['message'] = $getStatuses[$find1]['title'];
                            $st['type'] = $statuses[$getSTatusMsg]['type_1'];
                            $st['time'] = strtotime($getStatuses[$find1]['created_at']);
                            $cancelledInCurrentStep = 1;
                           // $st['is_completed'] = 1;
                            
                        }        
                          
                    // }
                        
                    if($type != 9 && $st['step_id'] != 7 && $st['step_id'] != 8){
                        unset($st['is_completed']);
                        unset($st['step_id']);
                        unset($st['type_1']);
                        unset($st['step_msg']);
                        
                        unset($st['type_1']);
                        unset($st['message_1']);
                        unset($st['time_1']);
                        unset($st['message_2']);
                        unset($st['type_2']);
                        
                        



                        if($type <= 15 && $cancelled == 0){
                            $allStatuses[] = $st;
                        } else  if($type > 15 &&  $st['type'] != "" && $cancelled == 0){
                            
                            $allStatuses[] = $st;
                        } 
                    } 
                   
                    
                }   
            }
            
            
            
       
        } 
        
        else {
            foreach($statuses as $st){
           //    print_r($getStatuses); die();
                $cancelled = $cancelledInCurrentStep;
                $find2 = $find1 = $find = -1;
                $type = intval($st['type']);
                $type_1 = intval($st['type_1']);
                $type_2 = intval($st['type_2']);
                
                
                $find = array_search($type,array_column($getStatuses, 'notification_type'));
                $find1 = array_search($type_1,array_column($getStatuses, 'notification_type'));
                $find2 = array_search($type_2,array_column($getStatuses, 'notification_type'));
                if($type == 27 && $find > -1){
                    $i=0;
                    foreach($allStatuses as $added){
                        if($added['type'] != null){
                            $removedNulls[] =  $added;
                        }
                        $i++;
                    }
                    $cancelledInCurrentStep = 1;
                    $allStatuses = $removedNulls;
                }
                
                if($find > -1){
                    $st['is_completed'] = 1;
                     $st['time'] =  strtotime($getStatuses[$find]['created_at']);
                 //    $st['time1'] =  $getStatuses[$find]['created_at'];
                    $st['message'] = $getStatuses[$find]['msg'];
                     
                } else {
                    $st['type'] = null;
                    $st['message'] = $st['step_msg'];
                }
                
                
                if($type != 1 && $type != 2 && $type != 3){
                    
                        $st['step'] = intval($st['step_id']);
                        $st['message'] = $st['message'];
                    
                        
                        if($find1 > -1 && $st['step'] == 4){
                            $getSTatusMsg = array_search(4,array_column($statuses, 'step_id'));
                            $currentStatusCount = sizeof($allStatuses) - 1;
                            $st['message'] = $statuses[$getSTatusMsg]['message_1'];
                            $st['type'] = $statuses[$getSTatusMsg]['type_1'];
                            $st['time'] = strtotime($getStatuses[$find1]['created_at']);
                           // $st['is_completed'] = 1;
                        } 
                        if($find2 > -1 && $st['step'] == 4){
                            $getSTatusMsg = array_search(4,array_column($statuses, 'step_id'));
                            $currentStatusCount = sizeof($allStatuses) - 1;
                            $st['message'] = $statuses[$getSTatusMsg]['message_2'];
                            $st['type'] = $statuses[$getSTatusMsg]['type_2'];
                            $st['time'] = strtotime($getStatuses[$find1]['created_at']);
                           // $st['is_completed'] = 1;
                        } 
                        
                        if($find1 > -1 && $type == 8){
                            $getSTatusMsg = array_search(6,array_column($statuses, 'step_id'));
                            $currentStatusCount = sizeof($allStatuses) - 1;
                          
                            
                             $st['message'] = $getStatuses[$find1]['title'];
                            $st['type'] = $statuses[$getSTatusMsg]['type_1'];
                            $st['time'] = strtotime($getStatuses[$find1]['created_at']);
                            $cancelledInCurrentStep = 1;
                           // $st['is_completed'] = 1;
                            
                        } 

                    
                        
                    // if($type != 9 ){
                        unset($st['is_completed']);
                        unset($st['step_id']);
                        unset($st['type_1']);
                        unset($st['step_msg']);
                        unset($st['type_1']);
                        unset($st['message_1']);
                        unset($st['time_1']);
                        unset($st['message_2']);
                        unset($st['type_2']);
                        if($type <= 15 && $cancelled == 0){
                            $allStatuses[] = $st;
                        } else  if($type > 15 &&  $st['type'] != "" && $cancelled == 0){
                            $allStatuses[] = $st;
                        } 
                    // } 
                   
                    
                }    
            }
        }
        // print_r($allStatuses); die();
        return $allStatuses;
    }
     
     //  send user_order table's order_id
    public function get_delivery_time_cost($order_id_data){
         
         
        // return  delivery_charges , total_distance , delivery_time , delivery_time_in_hrs_mins 
        // from pahramcy to customer
        
        
        //  get currunt time
         date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d');
        $current_time = date('H:i:s');
         
         $data = array();
         $drivingTime = $completedKms = $remainingDist = $distanceInKm = $DeliveryCharges = $drivingDistance = 0;
        
         $getMNODetails = $this->db->query("SELECT uo.pincode,uo.order_id,uo.invoice_no, uo.lat as user_lat, uo.lng as user_lng ,uo.`listing_id`, ms.lat as store_lat, ms.lng as store_lng , uo.`suggested_pharmacy_id`, msp.lat as suggested_pharmacy_lat ,  msp.lng as suggested_pharmacy_lng, mo.mno_id, mo.id as mno_order_id, ma.lat as mno_lat, ma.lng as mno_lng ,u.multi_user_type
            FROM `user_order` as uo 
            left join mno_orders as mo on (uo.invoice_no = mo.invoice_no AND mo.`status` = 'accepted'  ) 
            left join mno_actions as ma on (mo.mno_id=ma.mno_id && mo.invoice_no = ma.invoice_no) 
            left join medical_stores as ms on (uo.listing_id != '' && (uo.listing_id = ms.user_id || uo.listing_id = ms.pharmacy_branch_user_id)) 
            left join mno_suggested_pharmacies as msp on (uo.suggested_pharmacy_id != '' && uo.suggested_pharmacy_id = msp.id ) left join users as u on (uo.listing_id = u.id)
             WHERE uo.`order_id` = '$order_id_data'  GROUP by uo.order_id")->row_array();
             
        // print_r($getMNODetails); die();
        $invoice_no = $getMNODetails['invoice_no'];
        $user_lat = $getMNODetails['user_lat'];
        $user_lng = $getMNODetails['user_lng'];
        $listing_id = $getMNODetails['listing_id'];
        $store_lat = $getMNODetails['store_lat'];
        $store_lng = $getMNODetails['store_lng'];
        $suggested_pharmacy_id = $getMNODetails['suggested_pharmacy_id'];
        $suggested_pharmacy_lat = $getMNODetails['suggested_pharmacy_lat'];
        $suggested_pharmacy_lng = $getMNODetails['suggested_pharmacy_lng'];
        $mno_id = $getMNODetails['mno_id'];
        $mno_order_id = $getMNODetails['mno_order_id'];
        $mno_lat = $getMNODetails['mno_lat'];
        $mno_lng = $getMNODetails['mno_lng'];
        $multi_user_type = $getMNODetails['multi_user_type'];
        $pincode = $getMNODetails['pincode'];
        
        // distance from mno to pharmacy
        
        if($store_lat == "" || $store_lng == ""){
            $store_lat = $suggested_pharmacy_lat;
            $store_lng  = $suggested_pharmacy_lng;   
        }
      
        $get_mno_charges = $this->PartnermnoModel->get_mno_charges($store_lat,$store_lng,$user_lat,$user_lng,$mno_lat,$mno_lng, $listing_id,$pincode);
      
       
        $data['delivery_charges'] = $get_mno_charges['delivery_charges'] ;
        $data['total_distance'] = $get_mno_charges['total_distance'] ;
        $data['delivery_time'] = $get_mno_charges['delivery_time'] ; 
        $data['delivery_time_in_hrs_mins'] =$get_mno_charges['delivery_time_in_hrs_mins'] ;
        $data['customer_lat'] = $get_mno_charges['customer_lat'] ; 
        $data['customer_lng'] = $get_mno_charges['customer_lng'] ;
        $data['store_lat'] = $get_mno_charges['store_lat'] ; 
        $data['store_lng'] = $get_mno_charges['store_lng'] ;
        $data['mno_lat'] = $get_mno_charges['mno_lat'] ;  
        $data['mno_lng'] = $get_mno_charges['mno_lng'] ;
        
        // print_r($data); die();
        return $data;
         
     }
     
     
     
      // payment_methods
    public function payment_methods($mno_id){
        $parent = $subCat = $reason = $data = array();
        
        $paymentMethods = $this->db->query("SELECT * FROM payment_method")->result_array();   
            
        foreach($paymentMethods as $pm){
            $parent_id = $pm['parent_id'];
            if($parent_id == 0){
                $p['id'] = $pm['id'];
                $p['payment_method'] = $pm['payment_method'];
                $p['parent_id'] = $parent_id;
                $p['sub_types'] = null;
                $parent[] = $p;
            } else {
                
                $s['id'] = $pm['id'];
                $s['payment_method'] = $pm['payment_method'];
                $s['parent_id'] = $parent_id;
                $subCat[] = $s;
            }
        }
        
        foreach($subCat as $sc){
            // print_r($sc);
            $p_id = $sc['parent_id'];
            $key = array_search($p_id, array_column($parent, 'id'));
            
            $parent[$key]['sub_types'][] = $sc;
           
        }
        
        $data['payment_method'] = $parent;       
      
        return $data;
    }
    
    // cancel_order
    
    public function cancel_order($mno_id, $reason_id,$invoice_no){
         $this->load->model('OrderModel');
        $get_mno_details = $this->db->query("SELECT ml.* FROM `mno_list` as ml WHERE ml.`mno_id` = '$mno_id'  ")->result_array();
        if(sizeof($get_mno_details) > 0){
             $getAllReasons = $this->db->query("SELECT * FROM `mno_cancel_reasons` WHERE `id` = '$reason_id'")->row_array();
             if(sizeof($getAllReasons) > 0){
                
                $cancel_reason = $getAllReasons['cancel_reason'];
                $before_accept = $getAllReasons['before_accept'];
                $after_accept = $getAllReasons['after_accept'];
                $redirect_to_another_mno = $getAllReasons['redirect_to_another_mno'];
                $cancel_order    = $getAllReasons['cancel_order'];    
                
                    // $order_details = $this->db->query("SELECT * from mno_orders where mno_id = '$mno_id' and invoice_no = '$invoice_no' and ongoing = 1 and status like 'accepted'")->row_array();
                
               $existingOrder = $this->db->query("SELECT ml.mno_name, mo.*, u.id as user_id,uo.name as user_name, u.phone, u.token, u.agent FROM `mno_orders` as mo left join user_order as uo on (mo.invoice_no = uo.invoice_no) left join users as u on (uo.user_id = u.id) left join mno_list as ml on (ml.mno_id = mo.mno_id) WHERE mo.`invoice_no` = '$invoice_no' AND mo.`mno_id` = '$mno_id' AND mo.`ongoing` = 1 AND mo.`status` = 'accepted'")->row_array();
                   
                        
                if(sizeof($existingOrder) > 0){
                    
               
                    $mno_order_id = $existingOrder['id'];
                    // update cancel_reason_after_accept , redirected_to, and order_cancel'
                    $this->db->query("UPDATE `mno_orders` SET cancel_reason_after_accept = '$cancel_reason', `order_cancel` = '$cancel_order', ongoing = 0 where id= '$mno_order_id'");
                    
                    
                      
                        $getMnoLocation = $this->db->query("SELECT * FROM `mno_location` WHERE `mno_id` = '$mno_id' ORDER BY `created_at` DESC ")->row_array();
                        $mno_lat = $getMnoLocation['lat'];
                        $mno_lng = $getMnoLocation['lng'];
                       
                    
                    $assignAnotherOrder = $this->PartnermnoModel->assign_pending_order_to_mno($mno_id, $mno_lat, $mno_lng);
                    
                    
                    
                    if($redirect_to_another_mno == 1){
                        // update tracking
                        $mno_name = $existingOrder['mno_name'];
                        $action_by_status = "Night owl";
                        $orderStatus = "Order cancelled by night owl " .$mno_name;
                        $order_status = $this->OrderModel->update_status($invoice_no,$orderStatus, $action_by_status);
                        
                        // send notification to user and mno
                        
                            $title = "Order cancelled";
                            $reg_id = $existingOrder['token'];
                            $msg = "Night owl rejected your order, we will redirect your order to another night owl soon";
                            $img = $img_url   = 'https://medicalwale.com/img/noti_icon.png';
                            $tag = "text";
                            $agent = $existingOrder['agent'];
                            $invoice_no = $invoice_no;
                            $notification_type = 27;
                            $res = $this->PartnermnoModel->send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent, $invoice_no,$notification_type);
                            
                             $title = "Order cancelled by you";
                            $msg1 = "Order cancelled by you of order id ".$invoice_no;
                            $mno_order_id = $existingOrder['id'];
                            $this->PartnermnoModel->send_gcm_notification_to_mno($mno_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg1);
                            
                        
                        
                        
                        // assign another mno
                        $oderDeatils = $this->db->query("SELECT uo.`user_id` , uo.`lat` , uo.`lng`, u.lat as user_lat, u.lng as user_lng  FROM `user_order` as uo left join users as u on (uo.user_id = u.id) WHERE uo.`invoice_no` = '$invoice_no'")->row_array();
                        $user_id = $oderDeatils['user_id'];
                        $lat = $oderDeatils['lat'];
                        $lng = $oderDeatils['lng'];
                        if(empty($lat) || empty($lng)){
                            // users latlng
                            $lat = $oderDeatils['user_lat'];
                            $lng = $oderDeatils['user_lng'];
                        }
                        else if(empty($oderDeatils['user_lat']) || empty($oderDeatils['user_lng'])){
                            // default andheri
                            $lat = '19.1267157';
                            $lng = '72.8499786';
                        }
                        
                        $redicted_to_mno__info = $this->PartnermnoModel->assign_mno($user_id,$invoice_no,$lat,$lng);
                        
                        $redicted_to_mno_id = $redicted_to_mno__info['mno_id'];
                        
                        if($redicted_to_mno_id > 0){
                            $this->db->query("UPDATE `mno_orders` SET redircted_to = '$redicted_to_mno_id' where id= '$mno_order_id'");
                            
                            
                            $title = "Order redirected successfully";
                            $reg_id = $existingOrder['token'];
                            $msg = "Your order redirected to another night owl.";
                            $img = $img_url   = 'https://medicalwale.com/img/noti_icon.png';
                            $tag = "text";
                            $agent = $existingOrder['agent'];
                            $invoice_no = $invoice_no;
                            $notification_type = 27;
                            $res = $this->PartnermnoModel->send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent, $invoice_no,$notification_type);
                            
                            $msg1 = "Order redirected successfully";
                            $mno_order_id = $existingOrder['id'];
                            $this->PartnermnoModel->send_gcm_notification_to_mno($mno_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg1);
                            
                    
                        }
                        
                        
                        
                        
                        
                        
                    } else if($cancel_order == 1){
                        //  update user_order cancel the order
                        
                        $updateUserOrder = $this->db->query("UPDATE user_order set cancel_reason = '$cancel_reason', `order_status` = 'Order Cancelled' WHERE `invoice_no` = '$invoice_no'");
                        
                        // update status from tacking
                        $mno_name = $existingOrder['mno_name'];
                        $action_by_status = "Night owl";
                        $orderStatus = "Order cancelled by night owl ".$mno_name;
                        $order_status = $this->OrderModel->update_status($invoice_no,$orderStatus, $action_by_status);
                        // notification to user, mno
                        
                        $title = "Order cancelled";
                            $reg_id = $existingOrder['token'];
                            $msg = "Night owl cancelled your order.";
                            $img = $img_url   = 'https://medicalwale.com/img/noti_icon.png';
                            $tag = "text";
                            $agent = $existingOrder['agent'];
                            $invoice_no = $invoice_no;
                            $notification_type = 27;
                            $res = $this->PartnermnoModel->send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent, $invoice_no,$notification_type);
                            
                            $msg1 = "Order cancelled successfully";
                            $mno_order_id = $existingOrder['id'];
                            $this->PartnermnoModel->send_gcm_notification_to_mno($mno_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg1);
                            
                        
                    }
                    
                    return 4;
                
                } else {
                    return 3; //this order is not assigned to given mno
                }
                
                    
             } else {
                 return 2;
             }
        
        } else {
            return 1;
        }
        
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
//   get_ongoing_order

    public function get_ongoing_order($mno_id){
        $invoice_no = "";
        $data = $order_details = array();
        $page_no = 1;   
        $per_page = 10;
        $type = 3; //ongoing 
          $listOfOrders = $this->PartnermnoModel->order_list($mno_id, $page_no, $per_page,$type);
          
          foreach($listOfOrders['orders'] as $o){
              $invoice_no = $o['invoice_no'];
          }
          
          if($invoice_no != ""){
               $order_details  = $this->PartnermnoModel->order_details($mno_id , $invoice_no);
          }
        if(!empty($order_details['data'])){
            $data['data']  = $order_details['data'];
        }
        
        return $data;
    }
    
    public function pharmacy_didnot_respond($mno_id,$invoice_no){
        
        $data = array();
        $mnoOrderDetails = $this->db->query("SELECT mo.*, uo.listing_id, uo.suggested_pharmacy_id from mno_orders as mo left join user_order as uo on (mo.invoice_no = uo.invoice_no) WHERE mo.`mno_id` = '$mno_id' and mo.`invoice_no` = '$invoice_no' and mo.ongoing = 1 and mo.status = 'accepted' GROUP BY uo.invoice_no ")->row_array();
       
        
        if(sizeof($mnoOrderDetails) > 0){
            $mno_order_id = $mnoOrderDetails['id'];
            $updateMnoOrders = $this->db->query("UPDATE `mno_orders` SET `pharmacy_didnot_respond` = 1 WHERE `id` = $mno_order_id ");
            $pharmacy_id = $mnoOrderDetails['listing_id'];
            
            
            // if registered pharmacy then send notification to mno
            
            if($pharmacy_id != "" && $pharmacy_id != 0){
            //    $existingOrder = $this->db->query("SELECT mo.*, u.id as order_user_id, u.phone, u.token,  u.agent FROM `mno_orders` as mo left join user_order as uo on (mo.invoice_no = uo.invoice_no) left join users as u on (uo.user_id = u.id)  WHERE mo.`invoice_no` = '$invoice_no' AND mo.`mno_id` = '$mno_id' AND mo.`ongoing` = 1 AND mo.`status` = 'accepted'")->row_array();
                
                $receiver_id = $mno_id;
                $notification_type = 29; //refer mno_notification_types
                $img = "https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png";
                $mno_order_id = $mnoOrderDetails['id'];
                $title = "Pharmacy did not respond";
                $msg = "Pharmacy did not respond for order id $invoice_no";
        
                $response = $this->PartnermnoModel->send_gcm_notification_to_mno($receiver_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
            
                
            }
            
            
            
            return 1 ; // find and ongoing
        } else {
            return 2; // not assing to this user or already completed the order 
        }
        
   //     return $data;
    }
    
    public function get_latest_msg($invoice_no,$mno_order_id){
      //  echo "SELECT `title` FROM `mno_notifications` WHERE `invoice_no` LIKE '$invoice_no' AND `mno_orders_id` = '$mno_order_id' order by id desc LIMIT 1 "; die();
        $orderStatus = $this->db->query("SELECT `title` FROM `mno_notifications` WHERE `invoice_no` LIKE '$invoice_no' AND `mno_orders_id` = '$mno_order_id' order by id desc LIMIT 1 ")->row_array();
        return $orderStatus;
    }
    
    
    
      
    public function responce_behalf_of_user($mno_id, $invoice_no, $confirm, $cancel_reason_id, $payment_method_id) {
        
        $order_id = "";
        $this->load->model('PharmacypartnerModel');
        $this->load->model('OrderModel');
          
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d');
        $updated_at = date('Y-m-d H:i:s');
        $order_type_query = $this->db->query("select order_type,user_id,listing_type from user_order where invoice_no='$invoice_no' ");
        $get_order_info = $order_type_query->row_array();
        $order_type = $get_order_info['order_type'];
        $user_id = $get_order_info['user_id'];
        $listing_type = $get_order_info['listing_type'];
        
        if ($confirm == true) {
            
            $payment_method = $this->PartnermnoModel->get_payment_method($payment_method_id);
            //print_r($payment_method['payment_method']); die();
                if($payment_method['payment_method'] != ""){ 
                    
                    $paymentMethosName = $payment_method['payment_method'];

                    $order_status = 'Order Confirmed';
                    $update = $this->db->query("UPDATE `user_order` SET `updated_at`='$updated_at',`order_status`='$order_status',`payment_method`='$paymentMethosName',`cancel_reason`='',`action_by`='night owl' WHERE invoice_no='$invoice_no'");
        
                        $existingOrder = $this->db->query("SELECT ml.mno_name, mo.*, u.id as user_id,uo.name as user_name, u.phone, u.token, u.agent FROM `mno_orders` as mo left join user_order as uo on (mo.invoice_no = uo.invoice_no) left join users as u on (uo.user_id = u.id) left join mno_list as ml on (ml.mno_id = mo.mno_id) WHERE mo.`invoice_no` = '$invoice_no' AND mo.`mno_id` = '$mno_id' AND mo.`ongoing` = 1 AND mo.`status` = 'accepted'")->row_array();
                        
                    $receiver_id = $mno_id;
                    $notification_type = 8; //ORDER_DELIVERED refer mno_notification_types
                    $img = "https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png";
                    $mno_order_id = $existingOrder['id'];
                    $mno_name = $existingOrder['mno_name'];
                    $user_name = $existingOrder['user_name'];
                    // $title = "Order confirmed by you behalf of customer ";
                     $title = "Order Confirmed by night owl ".$mno_name." behalf of customer ".$user_name;
                  
                    $msg = "Order Confirmed by night owl ".$mno_name." behalf of customer ".$user_name." of order id ".$invoice_no;
            
                    $response = $this->PartnermnoModel->send_gcm_notification_to_mno($receiver_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
                
                
        
        
                    //$invno = $order_id;
                    $action_by_status = "Night owl";
                    $orderStatus = "Order Confirmed by night owl ".$mno_name." behalf of customer ".$user_name;
                    
                    
                    $this->OrderModel->update_status( $invoice_no,$orderStatus, $action_by_status);
                            
                    
                    
                    $updated_at = date('j M Y h:i A', strtotime($updated_at));
                    $res_order = $this->db->query("select order_id,user_id,listing_id,invoice_no,name,listing_name from user_order where invoice_no='$invoice_no' limit 1");
                    $order_info = $res_order->row_array();
                    $user_id = $order_info['user_id'];
                    $listing_id = $order_info['listing_id'];
                    $invoice_no = $order_info['invoice_no'];
                    $order_invoice_no = $order_info['order_id'];
                    $name = $order_info['name'];
                    $listing_name = $order_info['listing_name'];
                    $updated_at = date('j M Y h:i A', strtotime($updated_at));
                    //user notify starts
                    $order_info = $this->db->select('email,token, agent, token_status')->from('users')->where('id', $user_id)->get()->row();
                //   print_r($order_info); die();
                    
                    if (sizeof($order_info) > 0) {
                        $token_status = $order_info->token_status;
                        $reg_id = $order_info->token;
                        $agent = $order_info->agent;
                        $user_email = $order_info->email;
                        $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                        $tag = 'text';
                        $title = 'Order Confirmed by night owl behalf of you';
                        $msg = 'Your order '.$invoice_no.' has been confirmed';
                        $this->PharmacyPartnerModel->send_gcm_notify_usr($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $invoice_no, $order_invoice_no, $name, $listing_name, $agent, $order_type);
                         
                         $notification_array = array(
                                  'title' => $title,
                                  'msg'  => $msg,
                                  'img_url' => $img_url,
                                  'tag' => $tag,
                                  'order_status' => $order_status,
                                  'order_date' => $updated_at,
                                  'order_id'   => $invoice_no,
                                  'listing_name' => $listing_name,
                                  'post_id'  => "",
                                  'listing_id'  => "",
                                  'booking_id'  => "",
                                  'invoice_no' => $invoice_no,
                                  'user_id'  => $user_id,
                                  'notification_type'  => $order_type,
                                  'notification_date'  => date('Y-m-d H:i:s')
                                   
                        );
                     $this->db->insert('All_notification_Mobile', $notification_array);
                      if(!empty($user_email))
                             {
                                $this->OrderModel->pharmacy_booking_sendmail($user_email, $msg, $order_invoice_no);
                             }
                    }
                    
                    //user notify ends
                    
                    // pharmacy notifications
                    
                  
                    
                        $res_token = $this->db->query("select email, token,token_status,agent,phone from users where id='$listing_id' limit 1");
                        $token_value = $res_token->row_array();
                        $token_status = $token_value['token_status'];
                        $partner_phone = $token_value['phone'];
                        $user_email = $token_value['email'];
                        if ($token_status > 0) {
                            $reg_id = $token_value['token'];
                            $agent = $token_value['agent'];
                            $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                            $tag = 'text';
                            $title = 'Order Confirmed';
                            $msg = 'Kindly deliver the order';
                            //web notification starts
                            $pharmacy_notifications = array(
                                'listing_id' => $listing_id,
                                'order_id' => $invoice_no,
                                'title' => $title,
                                'msg' => $msg,
                                'image' => $img_url,
                                'notification_type' => $order_type,
                                'order_status' => $order_status,
                                'order_date' => $updated_at,
                                'invoice_no' => $invoice_no
                            );
                            $this->db->insert('pharmacy_notifications', $pharmacy_notifications);
                            //web notification ends
                            $this->PharmacyPartnerModel->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $order_invoice_no, $name, $listing_name, $agent, $order_type);
                             
                             $notification_array = array(
                                        'title' => $title,
                                      'msg'  => $msg,
                                      'img_url' => $img_url,
                                      'tag' => $tag,
                                      'order_status' => $order_status,
                                      'order_date' => $updated_at,
                                      'order_id'   => $order_id,
                                      'listing_name' => $listing_name,
                                      'post_id'  => "",
                                      'listing_id'  => "",
                                      'booking_id'  => "",
                                      'invoice_no' => $order_invoice_no,
                                      'user_id'  => $listing_id,
                                      'notification_type'  => $order_type,
                                      'notification_date'  => date('Y-m-d H:i:s')
                                       
                            );
                         $this->db->insert('All_notification_Mobile', $notification_array);
                         if(!empty($user_email))
                                 {
                       $this->OrderModel->pharmacy_booking_sendmail($user_email, $msg, $order_invoice_no);
                                 }
                        }
                        $message = 'Order confirmed by night owl behalf of customer.';
                        $post_data = array('From' => '02233721563', 'To' => $partner_phone, 'Body' => $message);
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
                        'status' => 200,
                        'message' => 'Order Confirmed'
                    );
               }  else {
               
           
                return array(
                    'status' => 400,
                    'message' => 'No payment method found'
                );
           }     
        } else  {
          //  $getAllReasons = $this->db->query("SELECT * FROM `mno_cancel_reasons` WHERE `id` = '$cancel_reason_id'")->row_array();
          
             $getAllReasons = $this->PartnermnoModel->get_cancel_reason($cancel_reason_id); 
            
            if(sizeof($getAllReasons) > 0){
                $cancel_reason = $getAllReasons['cancel_reason'];
                    $order_status = 'Order Cancelled';
                    $res_status = $this->db->query("select order_status from user_order where invoice_no='$invoice_no' limit 1");
                    $o_status = $res_status->row_array();
                    $check_status = $o_status['order_status'];
                    if ($check_status == 'Order Delivered') {
                        return array(
                            'status' => 201,
                            'message' => 'Order already Delivered'
                        );
                    } else {
                        $update = $this->db->query("UPDATE `user_order` SET `updated_at`='$updated_at',`order_status`='Order Cancelled',`cancel_reason`='$cancel_reason',`action_by`='night owl' WHERE invoice_no='$invoice_no'");
                     
                        
                    
                            
                        
                       $existingOrder = $this->db->query("SELECT ml.mno_name, mo.*, u.id as user_id,uo.name as user_name, u.phone, u.token, u.agent FROM `mno_orders` as mo left join user_order as uo on (mo.invoice_no = uo.invoice_no) left join users as u on (uo.user_id = u.id) left join mno_list as ml on (ml.mno_id = mo.mno_id) WHERE mo.`invoice_no` = '$invoice_no' AND mo.`mno_id` = '$mno_id' AND mo.`ongoing` = 1 AND mo.`status` = 'accepted'")->row_array();
                     
                    
                            $mno_name = $existingOrder['mno_name'];
                            $user_name = $existingOrder['user_name'];
                              //  $invno = $order_id;
                            $action_by_status = "Night owl";
                            $orderStatus = "Order cancelled by night owl ".$mno_name." behalf of customer ".$user_name;
                            $this->OrderModel->update_status( $invoice_no,$orderStatus, $action_by_status);
                    
                    
                            $receiver_id = $mno_id;
                            $notification_type = 9; //ORDER_DELIVERED refer mno_notification_types
                            $img = "https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png";
                            $mno_order_id = $existingOrder['id'];
                            // $title = "Order cancelled by you behalf of customer ";
                            $title = "Order cancelled by night owl ".$mno_name." behalf of customer ".$user_name;
                            $msg ="Order cancelled by night owl ".$mno_name." behalf of customer ".$user_name."of order id ".$invoice_no;
                            
                            
                             $upateMNOOrder = $this->db->query("UPDATE `mno_orders` SET cancel_reason_after_accept = '$cancel_reason', ongoing = 0 where id= '$mno_order_id'");
                
                    
                            $response = $this->PartnermnoModel->send_gcm_notification_to_mno($receiver_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
                        
                
                        
                        
                        if ($update) {
                   //         echo "select order_id,listing_id,user_id,invoice_no,name,listing_name from user_order where invoice_no='$order_id' limit 1"; die();
                            $res_order = $this->db->query("select order_id,listing_id,user_id,invoice_no,name,listing_name from user_order where invoice_no='$invoice_no' limit 1");
                            $order_info = $res_order->row_array();
                         //   print_r($order_info); die();
                            
                            $user_id = $order_info['user_id'];
                            $listing_id = $order_info['listing_id'];
                            $invoice_no = $order_info['invoice_no'];
                            $order_invoice_no = $order_info['order_id'];
                            $name = $order_info['name'];
                              $listing_name = $order_info['listing_name'];
                            $updated_at = date('j M Y h:i A', strtotime($updated_at));
                            //user notify starts
                         //   echo $user_id; die();
                            $order_info = $this->db->select('email,token, agent, token_status')->from('users')->where('id', $user_id)->get()->row();
                       //     print_r($order_info); die();
                            $token_status = $order_info->token_status;
                            if ($token_status > 0) {
                                $reg_id = $order_info->token;
                                $agent = $order_info->agent;
                                $user_email=  $order_info->email;
                                $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                                $tag = 'text';
                                $msg = 'Order Cancelled by night owl behalf of you';
                                $title = 'Your order '.$invoice_no.' has been cancelled' ;
                                $this->PharmacyPartnerModel->send_gcm_notify_usr($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $invoice_no, $order_invoice_no, $name, $listing_name, $agent, $order_type);
                                
                        $notification_array = array(
                                        'title' => $title,
                                  'msg'  => $msg,
                                  'img_url' => $img_url,
                                  'tag' => $tag,
                                  'order_status' => $order_status,
                                  'order_date' => $updated_at,
                                  'order_id'   => $invoice_no,
                                  'listing_name' => $listing_name,
                                  'post_id'  => "",
                                  'listing_id'  => "",
                                  'booking_id'  => "",
                                  'invoice_no' => $invoice_no,
                                  'user_id'  => $user_id,
                                  'notification_type'  => $order_type,
                                  'notification_date'  => date('Y-m-d H:i:s')
                                       
                            );
                         $this->db->insert('All_notification_Mobile', $notification_array);
                         if(!empty($user_email))
                             {
                                $this->OrderModel->pharmacy_booking_sendmail($user_email, $msg, $invoice_no);
                             }
                            }
                            //user notify ends
                            
                            // pharmacy notification
                            
                                $res_token = $this->db->query("select email,token,token_status,agent,phone from users where id='$listing_id' limit 1");
                                $token_value = $res_token->row_array();
                                $token_status = $token_value['token_status'];
                                $partner_phone = $token_value['phone'];
                                if ($token_status > 0) {
                                    $reg_id = $token_value['token'];
                                    $agent = $token_value['agent'];
                                    $user_email=  $token_value['email'];
                                    $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                                    $tag = 'text';
                                    $msg = 'Order Cancelled by you behalf of cutomer';
                                    $title = 'Your order '.$invoice_no.' has been cancelled' ;
                                    //web notification starts
                                    $pharmacy_notifications = array(
                                        'listing_id' => $listing_id,
                                        'order_id' => $order_id,
                                        'title' => $title,
                                        'msg' => $msg,
                                        'image' => $img_url,
                                        'notification_type' => $order_type,
                                        'order_status' => $order_status,
                                        'order_date' => $updated_at,
                                        'invoice_no' => $invoice_no
                                    );
                                    $this->db->insert('pharmacy_notifications', $pharmacy_notifications);
                                    //web notification ends
                                    $this->PharmacyPartnerModel->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $updated_at, $order_id, $order_invoice_no, $name, $listing_name, $agent, $order_type);
                             
                              
                                     $notification_array = array(
                                               'title' => $title,
                                      'msg'  => $msg,
                                      'img_url' => $img_url,
                                      'tag' => $tag,
                                      'order_status' => $order_status,
                                      'order_date' => $updated_at,
                                      'order_id'   => $invoice_no,
                                      'listing_name' => $listing_name,
                                      'post_id'  => "",
                                      'listing_id'  => "",
                                      'booking_id'  => "",
                                      'invoice_no' => $invoice_no,
                                      'user_id'  => $listing_id,
                                      'notification_type'  => $order_type,
                                      'notification_date'  => date('Y-m-d H:i:s')
                                               
                                    );
                                 $this->db->insert('All_notification_Mobile', $notification_array);
                                 if(!empty($user_email))
                                 {
                                    $this->OrderModel->pharmacy_booking_sendmail($user_email, $msg, $invoice_no);
                                 }
                       
                                }
                                $message = 'Order cancelled, Your order '.$invoice_no.' has been cancelled' ;
                                $post_data = array('From' => '02233721563', 'To' => $partner_phone, 'Body' => $message);
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
                                //redirect
                            
                                return array(
                                    'status' => 200,
                                    'message' => 'Order Cancelled'
                                );
                        } else {
                    return array(
                        'status' => 400,
                        'message' => 'failed'
                    );
                } 
            }
            } else {
                return array(
                    'status' => 400,
                    'message' => 'No reason found for given reason id'
                );
            }  
            
        }
    }
    
    // check payment method exists or not
    
    public function get_payment_method($payment_id){
           $paymentMethods = $this->db->query("SELECT * FROM `payment_method` WHERE `parent_id` = '$payment_id'  ")->result_array(); 
           $paymentMethosName = "";
           $data = array();
        if(sizeof($paymentMethods) > 0){
         //   return 4; //please send subtype id
         $paymentMethosName ="";
        } else {
            
            $paymentMethodsSelected = $this->db->query("SELECT * FROM `payment_method` WHERE `id` = '$payment_id' ")->row_array(); 
       
            
            if(sizeof($paymentMethodsSelected) > 0){
               
                $paymentMethosName = $paymentMethodsSelected['payment_method'];
                
            }
        }
        
        $data['payment_method'] = $paymentMethosName;
        return $data;
    }
   
       public function vendor_payment_option($pharmacy_id){
        $parent = $subCat = $reason = $data = array();
        
        $payment_methods = $this->db->query("SELECT mpm.id,mpm.payment_method,mpm.icon,   ppm.`user_id` as pharmacy_id, ppm.phone, ppm.`image` FROM `pharmacy_payment_methods` as ppm left join payment_method as mpm on(ppm.payment_method_id = mpm.id) WHERE `user_id` = '$pharmacy_id' AND `active` = 1 ")->result_array();
        foreach($payment_methods as $pm){
            $p['id'] = $pm['id'];
            $p['payment_method'] = $pm['payment_method'];
            $p['phone'] = $pm['phone'];
            $p['image'] = $pm['image'] ? $pm['image'] : null;
            $p['icon'] =  $pm['icon'];
            $data['data'][] = $p;
            
        }    
      // print_r($data); die();
        if(sizeof($data) == 0){
            // default CAP
               $CAP = $this->db->query("SELECT * FROM `payment_method` WHERE `id` = 3 ")->row_array();
            $cp['id'] = $CAP['id'];
            $cp['payment_method'] = $CAP['payment_method'];
            $cp['phone'] = null;
            $cp['image'] =  null;
            $cp['icon'] =  $CAP['icon'];
            $data['data'][] = $cp;
        }      
        return $data;
    }
    
    
      public function get_cancel_reason($cancel_reason_id){
        $getAllReasons = array();
    //    echo "SELECT * FROM `mno_cancel_reasons` WHERE `id` = '$cancel_reason_id'" ; die();
        $getAllReasons = $this->db->query("SELECT * FROM `mno_cancel_reasons` WHERE `id` = '$cancel_reason_id'")->row_array();
        return $getAllReasons;
        
    }
    
    public function assign_pending_order_to_mno($mno_id, $lat, $lng){
        $data = array();
        $invoice_no = "";
        $mno = $this->db->query("SELECT * FROM `mno_list` WHERE `mno_id` = '$mno_id' ")->row_array();
        $radius = 20;
        if(sizeof($mno) > 0){
            $mno_track = $this->db->query("SELECT * FROM `mno_track` WHERE `mno_id` = '$mno_id' AND `logged_out` = '0000-00-00 00:00:00' ")->row_array();
            if(sizeof($mno_track) > 0){
                $mno_orders = $this->db->query("SELECT * FROM `mno_orders` WHERE `mno_id` = '$mno_id' AND `ongoing` = 1 ")->row_array();
                if(sizeof($mno_orders) == 0){
                    // mno exists, online and available
                    $alreadyAssinedInvs = $this->db->query("SELECT `invoice_no` FROM `mno_orders` WHERE `mno_id` = '$mno_id' ")->result_array();
                    // print_r($alreadyAssinedInvs); die();
                    $allinvs = "";
                     foreach($alreadyAssinedInvs as $a){
                         if(!empty($allinvs)){
                             $allinvs .= ',';
                         }
                         $allinvs .= $a['invoice_no'];
                    }
                     $invs = "";
                    if($allinvs != ""){
                      $invs = 'WHERE mo.invoice_no NOT IN ( '. $allinvs .' ) ' ;     
                    }
                    
                    $sql = sprintf("SELECT uo.invoice_no,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance  FROM user_order as uo  join mno_orders as mo on (uo.invoice_no = mo.invoice_no &&  mo.mno_id = 0 && mo.ongoing = 1) $invs  HAVING distance < '10' ORDER BY distance LIMIT 1", ($lat), ($lng), ($lat), ($radius));
                 //   echo $sql; die();
                   
                    $getPendingOrder = $this->db->query($sql)->row_array();
                    $invoice_no = $getPendingOrder['invoice_no'];
                    
                //    print_r($getPendingOrder['invoice_no']); die();
                    if($invoice_no != ""){
                        
                    
                        $updateOrder = $this->db->query("UPDATE `mno_orders` SET `mno_id` = '$mno_id' WHERE `invoice_no` = '$invoice_no' AND (`mno_id` = 0 || `mno_id` = '') AND `ongoing` = 1 " );
                        $getUpdatedDetails = $this->db->query("SELECT * FROM mno_orders WHERE `mno_id` = '$mno_id' AND `invoice_no` = '$invoice_no' AND `ongoing` = 1 ")->row_array();
                        $mno_orders_id = $getUpdatedDetails['id'];
                        // msg to mno
                        
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
                            $img = $img_url   = 'https://medicalwale.com/img/noti_icon.png';
                 
                          
                            $this->PartnermnoModel->send_gcm_notification_to_mno($mno_id,$invoice_no,$mno_orders_id ,$notification_type, $img, $title, $msg);
                     
                            $res = $this->PartnermnoModel->send_msg_to_mno_executors($invoice_no);
                        
                        $data['res'] = 1; // mno exists, online and available
                        $data['invoice_no'] = $invoice_no;
                    } else {
                         $data['res'] = 5; // no pending order found
                    }
                } else {
                    $data['res'] = 4; // mno exists and online but not available
                }
            } else {
                $data['res'] = 3; // mno exists but offline    
            }
            
            
        } else {
            $data['res'] = 2; // no night owl found
        }
        
        return $data;
    }
    
    // send_msg_to_mno_executors
    
    public function send_msg_to_mno_executors($invoice_no){
        $data = array();
      $sent_to = 0;
        
        $mno_executors = $this->db->query("SELECT * FROM `mno_executors` WHERE `send_msg` = 1 AND `active` = 1 ")->result_array();
        
        
        $details = $this->db->query("SELECT ml.mno_id,ml.mno_name,mo.* FROM `mno_orders` as mo left join mno_list as ml on (ml.mno_id = mo.mno_id) WHERE mo.`invoice_no` LIKE '$invoice_no' AND mo.`ongoing` = 1 " )->row_array();
        $mno_id = $details['mno_id'];
        $mno_name = $details['mno_name'];
        
        if($mno_id  != "" && $mno_name != "" && $mno_id  != null && $mno_name != null && $mno_id  > 0 ){
            $message = $mno_name.' have received one order of order id '.$invoice_no;
            $exotel_sid = "aegishealthsolutions";
            $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
            $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
            
            foreach($mno_executors as $me){
                $executor_name = $me['executor_name'];
                $executor_phone = $me['executor_phone'];
                
                
                $post_data = array('From' => '02233721563', 'To' => $executor_phone, 'Body' => $message);
            
            
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
                
                $sent_to++;
                
            }
        }
        $data['sent_to'] = $sent_to;
        return $data;
        
    }
    
    // $offer_on = 1 means delivery charge offer
    // if offer_on is 1 then send $delivery_charges else send it as 0 and also send $order_total
    public function get_offer_details($offer_id, $offer_on, $delivery_charges, $order_total){
        $del_charge_off_for_customer = 0;
        $data = array();
        date_default_timezone_set('Asia/Kolkata');
        $current_date = time('Y-m-d');
       
        $offer_details = $this->db->query("SELECT * FROM `vendor_offers` WHERE `offer_on` = '$offer_on' AND `listing_id` = 44 AND `status` = 1 AND `id` = '$offer_id'  AND `min_amount` <= '$order_total'")->row_array();
       
        if(sizeof($offer_details) > 0){
            $end_date =  strtotime($offer_details['end_date']. '+1 day');
            if($end_date >= $current_date){
                
                $price = $offer_details['price'];
                $save_type = $offer_details['save_type'];
                $max_discound = $offer_details['max_discound'];
                
                if($save_type == 'percent'){
                    $del_charge_off_for_customer = ($delivery_charges * $price) / 100;
                } else if($save_type == 'rupee'){
                    $del_charge_off_for_customer = $delivery_charges - $price;
                } else {
                    $del_charge_off_for_customer = 0;
                } 
                
                if($del_charge_off_for_customer < 0){
                    $del_charge_off_for_customer = 0;
                }
                
                if($del_charge_off_for_customer > $max_discound){
                    $del_charge_off_for_customer = $max_discound;
                }
                
                $data['status'] = true;
                $data['del_charge_off_for_customer'] = $del_charge_off_for_customer;
                
            } else {
                $data['status'] = false;
            }
        } else {
            $data['status'] = false;
        }
        return $data;
        
    }
    
    
    // mno_available_delivery
    
    public function mno_available_delivery($address_id, $pincode){
        $data = array();
        $delivery_available = 0;
        // if pincode not given get address id
        // -get pincode from user_address table 
        // else get pincode directly
        // search in mno_available_pincodes table
        // if pincode exists then return success
        // else send error : "MNO service is not available in this area"
        
        if($pincode == ""){
            $get_address_details = $this->db->query("SELECT * FROM `user_address` WHERE `address_id` = '$address_id' ")->row_array();
            if(sizeof($get_address_details) > 0){
                $pincode = $get_address_details['pincode'];
            } else {
                $data['status'] = 2;
                $data['message'] = "No address found";
                
            }
        }
        
        // here you will get pincode either directly from controller or from above condition
        if($pincode != ""){
            $mno_available_pincodes = $this->db->query("SELECT * FROM mno_available_pincodes WHERE pincode = '$pincode'")->result_array();
            if(sizeof($mno_available_pincodes) > 0){
                $data['status'] = 1;
                $data['message'] = "Night owl delivery available";
                $delivery_available = 1;
            } else {
                $data['status'] = 2;
                $data['message'] = "Night owl delivery not available in given area";
            }
        } else {
            $data['status'] = 2;
            $data['message'] = "No pincode found";
        }
        
        $data['delivery_available'] = $delivery_available;
        return $data;
    }
    
    
    // get charges WRT distance  and vendor id
    public function get_mno_charges($store_lat,$store_lng,$user_lat,$user_lng,$mno_lat,$mno_lng, $listing_id,$pincode){
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d');
        $current_time = date('H:i:s');
        $delivery_available = 0;
        $data = array();
        
        $drivingTime = $completedKms = $remainingDist = $distanceInKm = $DeliveryCharges = $drivingDistance = 0;
        
         
        if($pincode > 0){
            $address_id = 0;
            $mno_available_delivery = $this->PartnermnoModel->mno_available_delivery($address_id, $pincode);
            // print_r($mno_available_delivery); die();
            if($mno_available_delivery['delivery_available'] == 1){
                $delivery_available = 1;
            } else {
                $delivery_available = 0;
            }
        }
         
        /* if($mno_lat != "" && $store_lat != "" && $mno_lng != "" && $store_lng != ""){
            $drivingDistanceMnoToPharma = $this->PartnermnoModel->GetDrivingDistance($mno_lat, $store_lat, $mno_lng, $store_lng );
            $drivingDistance = $drivingDistanceMnoToPharma['distance'];
            $drivingTime = $drivingDistanceMnoToPharma['duration'];
        }*/
        
        $getUserDetails = $this->db->query("SELECT * FROM users WHERE id = '$listing_id' ")->row_array();
        if(sizeof($getUserDetails) > 0){
            $multi_user_type = $getUserDetails['multi_user_type'];    
        } else {
            $multi_user_type = "";
        }
        
   
        if($store_lat != "" && $user_lat!=""  && $store_lng != "" && $user_lng!="" ){
            $drivingDistancePharmaToUser = $this->PartnermnoModel->GetDrivingDistance($store_lat, $user_lat, $store_lng, $user_lng );
            
            $drivingDistance = $drivingDistance + $drivingDistancePharmaToUser['distance'];
            $drivingTime = $drivingTime + $drivingDistancePharmaToUser['duration'];
           
        }
        
        if($drivingDistance > 0){
            $distanceInKm = ceil($drivingDistance / 1000);
            
        //   listing id logic is pending

       
        $chargesTableRows = array();
        $pharmacy_hub_id = "";
        if($listing_id > 0){
            if($multi_user_type == 'branch'){
                $getHubId = $this->db->query("SELECT * FROM `medical_stores` WHERE `pharmacy_branch_user_id` = '$listing_id' ORDER BY `id` DESC ")->row_array();
                if(sizeof($getHubId) > 0){
                     $pharmacy_hub_id = $getHubId['user_id'];
                } else {
                    $pharmacy_hub_id = $listing_id;
                }
               
            } else {
                $pharmacy_hub_id = $listing_id;
            }
            
            $chargesTableRows = $this->db->query("SELECT * FROM `mno_delivery_charges` WHERE `listing_id`  = '$pharmacy_hub_id' " )->result_array();
        }
        
        if(sizeof($chargesTableRows) == 0){
            $pharmacy_hub_id = 0;
        }
        
        if(sizeof($chargesTableRows) == 0){
            $chargesTableRows = $this->db->query("SELECT * FROM `mno_delivery_charges` WHERE `listing_id`  = '0' " )->result_array();
        }
        
            
            $chargesTableDay = $chargesTableNight = $chargesTable = array();
            foreach($chargesTableRows as $ctr){
                 $datetimecurrent = new DateTime($current_time);
               //  $datetimecurrent = new DateTime('22:00:00');
                
                 $datetimestart = new DateTime($ctr['start_time']);
                 $datetimeend = new DateTime($ctr['end_time']);
               
                //  day charges
               if ($datetimecurrent >= $datetimestart && $datetimecurrent < $datetimeend ) {

                    $chargesTableDay[] = $ctr;
                }
                
                // night charges
                
                if (($datetimecurrent <= $datetimestart ||  $datetimecurrent >= $datetimeend) && ($datetimestart > $datetimeend)) {
                    $chargesTableNight[] = $ctr;
                }
            }
            
            if(sizeof($chargesTableDay) > 0){
                $chargesTable = $chargesTableDay;
            } else {
                $chargesTable = $chargesTableNight;
            }
            //  print_r($chargesTable); die();
            
            $first = 1;
            foreach($chargesTable as $ct){
              
                
                
                $charge = $ct['charge'];
                $max_distance = $ct['max_distance'];
                $min_distance = $ct['min_distance'];
                
                if($first == 1 && $max_distance != -1){
                    $DeliveryCharges = $DeliveryCharges + $charge;
                    $remainingDist = $distanceInKm - $max_distance;
                    $completedKms = $completedKms + $max_distance;
                }  else if($max_distance >= $distanceInKm && $min_distance < $distanceInKm){
                    $valueFoeTotalKms = $max_distance - $min_distance;
                    $currentCharges = $valueFoeTotalKms * $charge;
                    $DeliveryCharges = $DeliveryCharges + $currentCharges;
                    $remainingDist = $remainingDist - $valueFoeTotalKms;
                    $completedKms = $completedKms + $valueFoeTotalKms;
                } else if($max_distance == -1 && $min_distance <= $distanceInKm  ){
                     $valueFoeTotalKms = $remainingDist;
                    $currentCharges = $valueFoeTotalKms * $charge;
                    $DeliveryCharges = $DeliveryCharges + $currentCharges;
                    $remainingDist = $remainingDist - $valueFoeTotalKms;
                    $completedKms = $completedKms + $valueFoeTotalKms;
                    
                } else if($first == 1 && $max_distance == -1){
                    $valueFoeTotalKms = $distanceInKm ;
                    $currentCharges = $valueFoeTotalKms * $charge;
                    $DeliveryCharges = $DeliveryCharges + $currentCharges;
                    $remainingDist = $remainingDist - $valueFoeTotalKms;
                    $completedKms = $completedKms + $valueFoeTotalKms;
                } else if($completedKms >= $min_distance && $completedKms < $max_distance) {
                    $valueFoeTotalKms =  $max_distance - $min_distance;
                   
                    $currentCharges = $valueFoeTotalKms * $charge;
                    $DeliveryCharges = $DeliveryCharges + $currentCharges;
                     $remainingDist = $remainingDist - $valueFoeTotalKms;
                      $completedKms = $completedKms + $valueFoeTotalKms;
                    
                }
                
                $first++; 
            }
        }
        
        
        if($drivingTime > 0){
            $delivery_time = $drivingTime + 900; //extra 15 mins
            
        } else {
            $delivery_time = $drivingTime;
        
        }
        
        $delivery_time_hr =   gmdate("H", $delivery_time);
        $delivery_time_min =   gmdate("i", $delivery_time);
        
        
        $delivery_time_hrs = $delivery_time_hr > 0 ? $delivery_time_hr .' hours' : "";
        $delivery_time_mins = $delivery_time_min > 0 ? $delivery_time_min .' mins' : "";
         
        $delivery_time_in_hrs_mins = $delivery_time_hrs .' '.$delivery_time_mins; 
      
         
        $data['delivery_charges'] = $DeliveryCharges;
        $data['total_distance'] = $drivingDistance;
        $data['delivery_time'] = $delivery_time; 
        $data['delivery_time_in_hrs_mins'] = $delivery_time_in_hrs_mins;
        $data['customer_lat'] = $user_lat; 
        $data['customer_lng'] = $user_lng;
        $data['store_lat'] = $store_lat; 
        $data['store_lng'] = $store_lng;
        $data['mno_lat'] = $mno_lat; 
        $data['mno_lng'] = $mno_lng;
        $data['delivery_available'] = $delivery_available;
        
        return $data;
    }
    
    // get_mno_by_invoice
    
    public function get_mno_by_invoice($invoice_no, $pharmacy_id){
        $data = array();
        $sql = "SELECT mo.mno_id, ml.mno_name, ml.mno_name, ml.phone, ml.email FROM `mno_orders` as mo left join mno_list as ml on (mo.mno_id = ml.mno_id) WHERE mo.`invoice_no` LIKE '$invoice_no' AND (mo.`status` = 'accepted'  OR mo.`status` = '') AND ((mo.`order_cancel` = 0 AND mo.`redircted_to` = 0) OR (mo.`order_cancel` > 0 AND mo.`redircted_to` = 0)) ORDER BY mo.id desc  ";
        
        $mno_details = $this->db->query($sql)->row_array();  
        if(sizeof($mno_details) > 0){
            $data['status'] = 1; // data found
            $data['data'] = $mno_details;
        } else {
            $data['status'] = 0; // no data found
            
        }
        return $data; 
    }
    
     public function order_list_executors($excecutor_id,$mno_id, $page_no, $per_page,$type,$order_status,$is_ongoing){
       $final_data = $data = array();  
         
        $executor_info = $this->db->query("SELECT * FROM `mno_executors` WHERE `user_id` = '$excecutor_id' ")->row_array();
        if(sizeof($executor_info) > 0){
            $allOrdersWithInv = array();
            $order_date1 = $order_date = null;
            $ongoing = false;
            $is_ongoingWhere = $order_statusWhere = $mnoWhere = $tracking_status = $typeWHere = "";
            if($type != ""){
                if($type == 'pending'){
                   $typeWHere = "AND mo.status != 'accepted' AND mo.status != 'rejected' ";
                } else {
                    $typeWHere = "AND mo.status = '$type'";
                }
                
            }  else {
                $typeWHere = "";
            }
            
            if($mno_id != ""){
                $mnoWhere = "AND mo.mno_id = '$mno_id'";
            }  else {
                $mnoWhere = "";
            }
            
            if($order_status != ""){
                
                $order_statusWhere = "AND uo.order_status = '$order_status'";
            } else {
                $order_statusWhere = "";
            }
            
            if($is_ongoing == 1){
                $is_ongoingWhere = "AND mo.ongoing = '1'";
            } else {
                $is_ongoingWhere = "";
            }
            
            $orderData = $allOrdersWithInv = array();
            $oldInv = $inv = 0;
            $offset = $per_page*($page_no - 1);
            $limit = "LIMIT $per_page OFFSET $offset"; 
            $data = array();
            
             $sql =  "SELECT mo.id as mno_order_id,mo.mno_id,mo.updated_at as last_updated,uo.order_date ,uo.listing_name,uo.listing_type, mo.status,uo.order_status,mo.ongoing,uo.order_id,uo.order_type,uo.invoice_no ,uo.name, uo.user_id, u.id, u.name as user_name FROM `mno_orders` as mo left join user_order as uo on (uo.invoice_no = mo.invoice_no) left join users as u on (uo.user_id = u.id) WHERE   (uo.listing_type = 44 OR uo.listing_type = 13) $mnoWhere  $typeWHere $order_statusWhere $is_ongoingWhere ORDER BY mo.created_at desc $limit"; 
          //   echo $sql; die();
            $allOrders = $this->db->query($sql)->result_array();
            $allOrders_count = $this->db->query("SELECT mo.id FROM `mno_orders` as mo left join user_order as uo on (uo.invoice_no = mo.invoice_no) left join users as u on (uo.user_id = u.id) WHERE (uo.listing_type = 44 OR uo.listing_type = 13) $mnoWhere  $typeWHere $order_statusWhere $is_ongoingWhere")->result_array();
            $orders_count = sizeof($allOrders_count);
            
            foreach($allOrders as $o){
                $order_date1 = $o['order_date'];
                $order_status_uo = $o['order_status'];
                $order_date = strtotime($o['order_date']);
                $last_updated = $o['last_updated'];
              // send pharmacy name or siggested pharmacy name if order from pharmacy
            
            
              if($o['listing_type'] == 13){
                  $pharmacy_name = $o['listing_name'];
              } else {
                  $pharmacy_name = null;
              }
         
                $listing_type = intval($o['listing_type']); 
               // print_r($o); die();
               $invoice_no = $o['invoice_no'];
                $order_id = null;
                if( $o['ongoing'] == 1){
                    $ongoing = true;
                } else {
                    $ongoing = false;
                    
                }
                
                 if(empty($o['status'])){
                     $o['status'] = 'pending'; 
                } 
                $mno_order_id = $o['mno_order_id'];
                $tracking_status_by_notification = $this->PartnermnoModel->get_latest_msg($invoice_no,$mno_order_id);
                $tracking_status = $tracking_status_by_notification['title'];
              
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
                    
                    $orderData['tracking_status'] = $tracking_status; 
                    $orderData['listing_type'] = $listing_type;
                    $orderData['order_date_epoch'] = $order_date;
                    $orderData['order_date'] =$order_date1;
                    $orderData['order_status'] = $order_status_uo;
                    
                    $orderData['last_updated'] = $last_updated;
                    
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
                    
                    // if(!empty($o['pharmacy_name'])){
                        $orderData['pharmacy_name'] = $pharmacy_name;
                    // } 
                    
                    $order_type = $o['order_type']; 
                    $orderData['status'] = $o['status'];
                    
                   
                        $orderData['tracking_status'] = $tracking_status; 
                      $orderData['listing_type'] = $listing_type;
                       $orderData['order_date_epoch'] = $order_date;
                        $orderData['order_date'] =$order_date1;
                        $orderData['order_status'] = $order_status_uo;
                        $orderData['last_updated'] = $last_updated;
                    
                    
                    $orderData['is_ongoing'] = $ongoing;
                    $order_id = intval($o['order_id']);
                     if($order_type == 'order'){
                        $orderData['order_id'] = $order_id;
                    } else if($order_type == 'prescription'){
                        $orderData['prescription_id'] = $order_id;
                    }
                    
                }
                $oldInv = $in;
            }
            $key = -1;
            if(sizeof($orderData) > 0 && $orderData != 0){
                $allOrdersWithInv[] = $orderData; 
            }
            $last_page = ceil($orders_count/$per_page);
            $data['data_count'] = intval($orders_count);
            $data['per_page'] = $per_page;
            $data['current_page'] = $page_no;
            $data['first_page'] = 1;
            $data['last_page'] = $last_page;
            $data['orders'] = $allOrdersWithInv;
            // print_r($allOrdersWithInv); die();
            $final_data['status'] = 1; // got data
            $final_data['data'] = $data; // got data
        } else {
            $final_data['status'] = 2; // no executor             
        }    
        return $final_data;
    }
    
}
?>
