<?php

/*$check_table_existance = $this->PharmacyPartnerModel->stock_table_existance($user_id);
        	       
        	       if($check_table_existance['status'] == true){
                        $table_name = $check_table_existance['table_name'];
   
        	       } 
        	       */
/*
// insert in table
            $listing_id = '13';
            $this->PharmacyPartnerModel->notification_in_table($title, $msg, $img_url, $listing_id, $invoice_no, $user_id, $notification_type);
            */        	       
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
    
    // Staff list by ghanshyam parihar starts
    public function staffList($user_id)
    {
        $query = $this->db->query("SELECT users.is_active,users.avatar_id,users.id,users.name,users.phone,users.email FROM users INNER JOIN pharmacy_staff ps ON(ps.staff_user_id=users.id) WHERE users.staff_hub_user_id='$user_id' AND users.vendor_id='13' order by users.id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                
                $staff_user_id      = $row['id'];
                $mobile             = $row['phone'];
                $staff_name         = $row['name'];
                $staff_email        = $row['email'];
                $avatar_id          = $row['avatar_id'];
                $is_active          = $row['is_active'];
                
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
                    "profile_pic" => $profile_pic,
                    "is_active" => $is_active
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
        
    }
    // Staff list by ghanshyam parihar starts
    
    public function get_versions(){
        $is_live = true;
        if($is_live == true){
            $folder = "/data/web/vendor.medicalwale.com/pharmacy/";
            $url = "https://pharmacy.medicalwale.com/";
        } else {
            $folder = '/home/h8so2sh3q97n/public_html/vendorsandbox.medicalwale.com/pharmacy.medicalwale.com/';
            $url = 'http://vendorsandbox.medicalwale.com/pharmacy.medicalwale.com/';
        }
        $data['folder'] = $folder;
        $data['url'] = $url;
        return $data;
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
        if ($phone == '8655369076' || $phone == '9619073803' ) {
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
    
     public function login($phone, $token, $agent) 
    {
        $image = "";
        $query = $this->db->query("SELECT id,name,email,phone,city,vendor_id FROM users WHERE phone='$phone' and vendor_id ='13' and is_active=1");
        $count = $query->num_rows();
        if ($count > 0) {

            $get_list = $query->row();
            $vendor_id = $get_list->vendor_id;

            $q_user = $this->db->select('id,lat,lng,name,email,phone,city,vendor_id')->from('users')->where('phone', $phone)->where('vendor_id', $vendor_id)->get()->row();

            $id = $q_user->id;
            $name = $q_user->name;
            $phone = $q_user->phone;
            $email = $q_user->email;
            $city = $q_user->city;
            $lat = $q_user->lat;
            $lng = $q_user->lng;
            $vendor_id = $q_user->vendor_id;

            $multi_user = $this->db->query("SELECT id,name,email,phone,city,vendor_id,staff_hub_user_id,lat,lng FROM `users` WHERE  phone='$phone' AND vendor_id ='13'");


            foreach ($multi_user->result_array() as $row) {
                $id = $row['id'];
                $name = $row['name'];
                $phone = $row['phone'];
                $email = $row['email'];
                $city = $row['city'];
                $vendor_id_multi = $row['vendor_id'];
                $staff_hub_user_id = $row['staff_hub_user_id'];
                $lat1 = $row['lat'];
                $lng1 = $row['lng'];
                
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

            $pbioms_record = $this->db->select('*')->from('pbioms_buy_details')->where('user_id', $id)->where('activate', '1')->get()->result();
            if(!empty($pbioms_record))
            {
                $pbioms =1;
                $pbioms_count = $this->db->select('count(*) as cnt')->from('users')->where('staff_hub_user_id', $id)->get()->row();
                $staff_used = $pbioms_count->cnt ;
                
                $total_admin = 0;
                $admin_used = 0;
                $total_staff = 0;
                
                foreach($pbioms_record as $pbioms_record1)
                {
                    $total_admin += $pbioms_record1->total_admin;
                    $total_staff += $pbioms_record1->total_staff;
                }
            }
            else
            {
                $pbioms = 0;
                $total_admin = 0;
                $admin_used = 0;
                $total_staff = 0;
                $staff_used = 0;
            }
            
            $staff_user_id = $id;
            if($staff_hub_user_id != NULL)
            {
                $id = $staff_hub_user_id;
            }
            
            
            $q_user1 = $this->db->select('id,lat,lng,name,email,phone,city,vendor_id')->from('users')->where('id', $id)->get()->row();

            $lat = $q_user1->lat;
            if($lat == NULL)
            {
                $lat = "";
            }
            $lng = $q_user1->lng;
            if($lng == NULL)
            {
                $lng = "";
            }
            
            $date_array = array(
                'listing_id' => $id,
                'staff_user_id' => $staff_user_id,
                'name' => $name,
                'lat' => $lat,
                'lng' => $lng,
                'type' => $vendor_id,
                'phone' => $phone,
                'email' => $email,
                'city' => $city,
                'profile_pic' => $image,
                'accounts_list' => $accounts_list,
                'is_approval' => $is_approval,
                'is_active' => $is_active,
                'pbioms' => "$pbioms",
                'total_admin' => "$total_admin",
                'admin_used' => "$total_admin",
                'total_staff' => "$total_staff",
                'staff_used' => $staff_used
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
    
     public function login_v1($phone, $token, $agent,$password) 
    {
        $image = "";
        $new=md5($password);
        //echo "SELECT id,lat,lng,name,email,phone,city,vendor_id,otp_doc,password FROM users WHERE (phone='$phone' or email='$phone') and vendor_id ='13' and is_active=1";
        $query = $this->db->query("SELECT id,lat,lng,name,email,phone,city,vendor_id,otp_doc,password FROM users WHERE (phone='$phone' or email='$phone') and vendor_id ='13' and is_active=1");
        $count = $query->num_rows();
        if ($count > 0) {

            $q_user = $query->row();
            //$vendor_id = $get_list->vendor_id;

            //$q_user = $this->db->select('id,lat,lng,name,email,phone,city,vendor_id')->from('users')->where('phone', $phone)->where('vendor_id', $vendor_id)->get()->row();

            $id = $q_user->id;
            $name = $q_user->name;
            $phone = $q_user->phone;
            $email = $q_user->email;
            $city = $q_user->city;
            $lat = $q_user->lat;
            $lng = $q_user->lng;
            $vendor_id = $q_user->vendor_id;
            $password        = $q_user->password; 
            $otp_doc         = $q_user->otp_doc;
            if($new==$password)
            {
            $multi_user = $this->db->query("SELECT id,name,email,phone,city,vendor_id,staff_hub_user_id,lat,lng FROM `users` WHERE  phone='$phone' AND vendor_id ='13'");


            foreach ($multi_user->result_array() as $row) {
                $id = $row['id'];
                $name = $row['name'];
                $phone = $row['phone'];
                $email = $row['email'];
                $city = $row['city'];
                $vendor_id_multi = $row['vendor_id'];
                $staff_hub_user_id = $row['staff_hub_user_id'];
                $lat1 = $row['lat'];
                $lng1 = $row['lng'];
                
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

            $pbioms_record = $this->db->select('*')->from('pbioms_buy_details')->where('user_id', $id)->where('activate', '1')->get()->result();
            if(!empty($pbioms_record))
            {
                $pbioms =1;
                $pbioms_count = $this->db->select('count(*) as cnt')->from('users')->where('staff_hub_user_id', $id)->get()->row();
                $staff_used = $pbioms_count->cnt ;
                
                $total_admin = 0;
                
                $pbioms_count_admin = $this->db->select('count(*) as cnt')->from('users')->where('admin_hub_user_id', $id)->get()->row();
                $admin_used = $pbioms_count_admin->cnt ;
                
                $total_staff = 0;
                
                foreach($pbioms_record as $pbioms_record1)
                {
                    $total_admin += $pbioms_record1->total_admin;
                    $total_staff += $pbioms_record1->total_staff;
                }
            }
            else
            {
                $pbioms = 0;
                $total_admin = 0;
                $admin_used = 0;
                $total_staff = 0;
                $staff_used = 0;
            }
            
            $staff_user_id = $id;
            if($staff_hub_user_id != NULL)
            {
                $id = $staff_hub_user_id;
            }
            
            
            $q_user1 = $this->db->select('id,lat,lng,name,email,phone,city,vendor_id')->from('users')->where('id', $id)->get()->row();

            $lat = $q_user1->lat;
            if($lat == NULL)
            {
                $lat = "";
            }
            $lng = $q_user1->lng;
            if($lng == NULL)
            {
                $lng = "";
            }
            
            $date_array = array(
                'listing_id' => $id,
                'password_set' => $otp_doc,
                'staff_user_id' => $staff_user_id,
                'name' => $name,
                'lat' => $lat,
                'lng' => $lng,
                'type' => $vendor_id,
                'phone' => $phone,
                'email' => $email,
                'city' => $city,
                'profile_pic' => $image,
                'accounts_list' => $accounts_list,
                'is_approval' => $is_approval,
                'is_active' => $is_active,
                'pbioms' => "$pbioms",
                'total_admin' => "$total_admin",
                'admin_used' => "$total_admin",
                'total_staff' => "$total_staff",
                'staff_used' => $staff_used
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
     public function login_v2($phone, $token, $agent,$password) 
    {
        $image = "";
        $new=md5($password);
        //echo "SELECT id,lat,lng,name,email,phone,city,vendor_id,otp_doc,password FROM users WHERE (phone='$phone' or email='$phone') and vendor_id ='13' and is_active=1";
        $query = $this->db->query("SELECT id,lat,lng,name,email,phone,city,vendor_id,otp_doc,password,staff_hub_user_id,admin_hub_user_id FROM users WHERE (phone='$phone' or email='$phone') and vendor_id ='13' and is_active=1");
        $count = $query->num_rows();
        if ($count > 0) {

            $q_user = $query->row();
            //$vendor_id = $get_list->vendor_id;

            //$q_user = $this->db->select('id,lat,lng,name,email,phone,city,vendor_id')->from('users')->where('phone', $phone)->where('vendor_id', $vendor_id)->get()->row();

            $id = $q_user->id;
            $name = $q_user->name;
            $phone = $q_user->phone;
            $email = $q_user->email;
            $city = $q_user->city;
            $lat = $q_user->lat;
            $lng = $q_user->lng;
            $vendor_id = $q_user->vendor_id;
            $password        = $q_user->password; 
            $otp_doc         = $q_user->otp_doc;
            $staff_id         = $q_user->staff_hub_user_id;
            $admin_id         = $q_user->admin_hub_user_id;
            if($new==$password)
            {
                
                  
            $staff_user_id = $id;
           /* if($staff_hub_user_id != NULL)
            {
               $id = $staff_hub_user_id;
            }
            */
            
              $img_count = $this->db->select('profile_pic')->from('medical_stores')->where('user_id', $id)->get()->num_rows();
                    if ($img_count == 0) {
                      
                        if(!empty($staff_id))
                        {
                            $staff_user_id = $id;
                            $id=$staff_id;
                            
                        }
                        if(!empty($admin_id))
                        {
                            $staff_user_id = $id;
                            $id=$admin_id;
                            
                        }
                    }
                    
             //----------------- lat lng---------------  
             $q_user1 = $this->db->select('id,lat,lng,name,email,phone,city,vendor_id')->from('users')->where('id', $id)->get()->row();

            $lat = $q_user1->lat;
            if($lat == NULL)
            {
                $lat = "";
            }
            $lng = $q_user1->lng;
            if($lng == NULL)
            {
                $lng = "";
            }
             //----------------- End lat lng--------------- 
             
            $login_by = "";
            if ($staff_id != NULL || $staff_id != "") {
                    $img_count = $this->db->select('profile_pic')->from('medical_stores')->where('user_id', $id)->get()->num_rows();
                    if ($img_count > 0) {
                         $login_by = "Admin";
                        // $id = $staff_user_id;
                    }
                    else
                    {
                        $login_by = "Staff";
                    }
                }
                else if($admin_id != NULL || $admin_id != "")
                {
                    $img_count = $this->db->select('profile_pic')->from('medical_stores')->where('user_id', $id)->get()->num_rows();
                    if ($img_count > 0) {
                         $login_by = "Admin";
                    }
                    else
                    {
                        $login_by = "Admin";
                    }
                }
            
                    
            $multi_user = $this->db->query("SELECT id,name,email,phone,city,vendor_id,staff_hub_user_id,admin_hub_user_id,lat,lng FROM `users` WHERE  phone='$phone' AND vendor_id ='13'");

            foreach ($multi_user->result_array() as $row) {
               // $id = $row['id'];
                $name = $row['name'];
                $phone = $row['phone'];
                $email = $row['email'];
                $city = $row['city'];
                $vendor_id_multi = $row['vendor_id'];
                $staff_hub_user_id = $row['staff_hub_user_id'];
                $admin_hub_user_id = $row['admin_hub_user_id'];
                $lat1 = $row['lat'];
                $lng1 = $row['lng'];
                
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

            $pbioms_record = $this->db->select('*')->from('pbioms_buy_details')->where('user_id', $id)->where('activate', '1')->get()->result();
            if(!empty($pbioms_record))
            {
                $pbioms =1;
                $pbioms_count = $this->db->select('count(*) as cnt')->from('users')->where('staff_hub_user_id', $id)->get()->row();
                $staff_used = $pbioms_count->cnt ;
                
                $total_admin = 0;
                
                $pbioms_count_admin = $this->db->select('count(*) as cnt')->from('users')->where('admin_hub_user_id', $id)->get()->row();
                //echo $this->db->last_query();
                $admin_used = $pbioms_count_admin->cnt ;
                
                $total_staff = 0;
                
                foreach($pbioms_record as $pbioms_record1)
                {
                    $total_admin += $pbioms_record1->total_admin;
                    $total_staff += $pbioms_record1->total_staff;
                }
            }
            else
            {
                $pbioms = 0;
                $total_admin = 0;
                $admin_used = 0;
                $total_staff = 0;
                $staff_used = 0;
            }
          
                    
            
                
            $date_array = array(
                'listing_id' => $id,
                'password_set' => $otp_doc,
                'staff_user_id' => $staff_user_id,
                "login_by" => $login_by,
                'name' => $name,
                'lat' => $lat,
                'lng' => $lng,
                'type' => $vendor_id,
                'phone' => $phone,
                'email' => $email,
                'city' => $city,
                'profile_pic' => $image,
                'accounts_list' => $accounts_list,
                'is_approval' => $is_approval,
                'is_active' => $is_active,
                'pbioms' => "$pbioms",
                'total_admin' => "$total_admin",
                'admin_used' => "$admin_used",
                'total_staff' => "$total_staff",
                'staff_used' => $staff_used
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
        $query = $this->db->query("SELECT id,lat,lng,name,email,phone,city,vendor_id,otp_doc,password FROM users WHERE (phone='$phone' or email='$phone') and vendor_id='13' and is_active=1");
        $count = $query->num_rows();
        if ($count > 0) {
            $q_user = $query->row();
            $id = $q_user->id;
            $name = $q_user->name;
            $phone = $q_user->phone;
            $email = $q_user->email;
            $city = $q_user->city;
            $lat = $q_user->lat;
            $lng = $q_user->lng;
            $vendor_id = $q_user->vendor_id;
            $password        = $q_user->password; 
            $otp_doc         = $q_user->otp_doc;
           
            
           $querys = $this->db->query("UPDATE `users` SET `password`='$new',`otp_doc`='1' WHERE id='$id'");
		
            
           $multi_user = $this->db->query("SELECT id,name,email,phone,city,vendor_id,staff_hub_user_id,lat,lng FROM `users` WHERE  phone='$phone' AND vendor_id ='13'");


            foreach ($multi_user->result_array() as $row) {
                $id = $row['id'];
                $name = $row['name'];
                $phone = $row['phone'];
                $email = $row['email'];
                $city = $row['city'];
                $vendor_id_multi = $row['vendor_id'];
                $staff_hub_user_id = $row['staff_hub_user_id'];
                $lat1 = $row['lat'];
                $lng1 = $row['lng'];
                
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

            $pbioms_record = $this->db->select('*')->from('pbioms_buy_details')->where('user_id', $id)->where('activate', '1')->get()->result();
            if(!empty($pbioms_record))
            {
                $pbioms =1;
                $pbioms_count = $this->db->select('count(*) as cnt')->from('users')->where('staff_hub_user_id', $id)->get()->row();
                $staff_used = $pbioms_count->cnt ;
                
                $total_admin = 0;
                $admin_used = 0;
                $total_staff = 0;
                
                foreach($pbioms_record as $pbioms_record1)
                {
                    $total_admin += $pbioms_record1->total_admin;
                    $total_staff += $pbioms_record1->total_staff;
                }
            }
            else
            {
                $pbioms = 0;
                $total_admin = 0;
                $admin_used = 0;
                $total_staff = 0;
                $staff_used = 0;
            }
            
            $staff_user_id = $id;
            if($staff_hub_user_id != NULL)
            {
                $id = $staff_hub_user_id;
            }
            
            
            $q_user1 = $this->db->select('id,lat,lng,name,email,phone,city,vendor_id')->from('users')->where('id', $id)->get()->row();

            $lat = $q_user1->lat;
            if($lat == NULL)
            {
                $lat = "";
            }
            $lng = $q_user1->lng;
            if($lng == NULL)
            {
                $lng = "";
            }
            
            $date_array = array(
                'listing_id' => $id,
                'password_set' => $otp_doc,
                'staff_user_id' => $staff_user_id,
                'name' => $name,
                'lat' => $lat,
                'lng' => $lng,
                'type' => $vendor_id,
                'phone' => $phone,
                'email' => $email,
                'city' => $city,
                'profile_pic' => $image,
                'accounts_list' => $accounts_list,
                'is_approval' => $is_approval,
                'is_active' => $is_active,
                'pbioms' => "$pbioms",
                'total_admin' => "$total_admin",
                'admin_used' => "$total_admin",
                'total_staff' => "$total_staff",
                'staff_used' => $staff_used
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
    
     public function forget_sendotp($phone_old)
    {
         $query = $this->db->query("SELECT id,phone,otp_code FROM users WHERE (phone='$phone_old' or email='$phone_old') and vendor_id='13'");
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
    
    public function order_status_count($listing_id)
    {
        $list = $listing_id;
        $sql11= "SELECT user_id FROM medical_stores where user_id = '".$listing_id."' AND pharmacy_branch_user_id=0";
	        $result11 = $this->db->query($sql11)->row();
	        if(!empty($result11))
	        {
                $comp = "SELECT pharmacy_branch_user_id FROM medical_stores WHERE user_id='$listing_id' AND medical_stores.pharmacy_branch_user_id != '0' ORDER BY id DESC";
            	  $medical_store_id = $this->db->query($comp)->result();
            	  $listing_id = "";
            	  foreach($medical_store_id as $ids)
            	  {
            	      $listing_id .= $ids->pharmacy_branch_user_id.",";
            	  }
            	  
            	  $listing_id .= $list;
	        }
	        
        $query1 = $this->db->query("select * from user_order where CONCAT(',', user_order.listing_id, ',') REGEXP CONCAT(',(', REPLACE('$listing_id', ',', '|'), '),') group by invoice_no");
        $count1 = $query1->num_rows();
        
        $query2 = $this->db->query("select * from user_order where CONCAT(',', user_order.listing_id, ',') REGEXP CONCAT(',(', REPLACE('$listing_id', ',', '|'), '),') AND order_status='Awaiting Confirmation' group by invoice_no");
        $count2 = $query2->num_rows();
        
        $query3 = $this->db->query("select * from user_order where CONCAT(',', user_order.listing_id, ',') REGEXP CONCAT(',(', REPLACE('$listing_id', ',', '|'), '),') AND order_status='Awaiting Customer Confirmation' group by invoice_no");
        $count3 = $query3->num_rows();
        
        $query4 = $this->db->query("select * from user_order where CONCAT(',', user_order.listing_id, ',') REGEXP CONCAT(',(', REPLACE('$listing_id', ',', '|'), '),') AND order_status='Order Confirmed' group by invoice_no");
        $count4 = $query4->num_rows();
        
        $query5 = $this->db->query("select * from user_order where CONCAT(',', user_order.listing_id, ',') REGEXP CONCAT(',(', REPLACE('$listing_id', ',', '|'), '),') AND order_status='Order Delivered' group by invoice_no");
        $count5 = $query5->num_rows();
        
        $query6 = $this->db->query("select * from user_order where CONCAT(',', user_order.listing_id, ',') REGEXP CONCAT(',(', REPLACE('$listing_id', ',', '|'), '),') AND order_status='Awaiting Delivery Confirmation' group by invoice_no");
        $count6 = $query6->num_rows();
        
        $query7 = $this->db->query("select * from user_order where CONCAT(',', user_order.listing_id, ',') REGEXP CONCAT(',(', REPLACE('$listing_id', ',', '|'), '),') AND order_status='Order Cancelled' group by invoice_no");
        $count7 = $query7->num_rows();
        
        $array[] = array('status'=> 'All',
                       'count' => ($count2+$count3+$count4+$count5+$count6+$count7));
        $array[] = array('status'=> 'Awaiting Confirmation',
                       'count' => $count2);
        $array[] = array('status'=> 'Awaiting Customer Confirmation',
                       'count' => $count3);
        $array[] = array('status'=> 'Order Confirmed',
                       'count' => $count4);
        $array[] = array('status'=> 'Awaiting Delivery Confirmation',
                       'count' => $count6);
        $array[] = array('status'=> 'Order Delivered',
                       'count' => $count5);
        $array[] = array('status'=> 'Order Cancelled',
                       'count' => $count7);
                       
        return $array;
    }
    public function order_list($status, $listing_id, $listing_type, $order_type, $page,$keyword, $date_from, $date_to)
    {
        $l ="";
        if($page == "")
        {
            $l ="";
        }else
        {
             $limit = 10;
             $start = 0;
            if ($page > 0) {
                if (!is_numeric($page)) {
                    $page = 1;
                }
            }
            $start = ($page - 1) * $limit;
            $l = " LIMIT $start, $limit";
        }
       
        
        $whereListing = "";
        if($listing_type == 0){
            $whereListing = " AND (listing_type='13' or listing_type='44')";
        } else {
            $whereListing = " AND (listing_type='13' or listing_type='44')";
        }
        
          if($keyword!="")
                {
                    $whereListing .=" AND (invoice_no LIKE '%$keyword%' OR order_type LIKE '%$keyword%' OR order_status LIKE '%$keyword%' 
                                          OR name LIKE '%$keyword%' OR mobile LIKE '%$keyword%' OR actual_cost LIKE '%$keyword%') ";
                }
                
         if($date_from!="" && $date_to!="")
                {
                    $whereListing .=" AND (order_date >= '$date_from' AND order_date <= '$date_to') ";
                }        
         if($status != "")
         {
             $whereListing .= " AND order_status='$status' ";
         }
         
         $list = $listing_id;
        $sql11= "SELECT user_id FROM medical_stores where user_id = '".$listing_id."' AND pharmacy_branch_user_id=0";
	        $result11 = $this->db->query($sql11)->row();
	        if(!empty($result11))
	        {
                $comp = "SELECT pharmacy_branch_user_id FROM medical_stores WHERE user_id='$listing_id' AND medical_stores.pharmacy_branch_user_id != '0' ORDER BY id DESC";
            	  $medical_store_id = $this->db->query($comp)->result();
            	  $listing_id = "";
            	  foreach($medical_store_id as $ids)
            	  {
            	      $listing_id .= $ids->pharmacy_branch_user_id.",";
            	  }
            	  
            	  $listing_id .= $list;
	        }
       /* echo "select user_id,order_id,order_type,action_by,delivery_time,cancel_reason,action_by,listing_id,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where FIND_IN_SET('" . $listing_id . "',listing_id) and listing_type='$listing_type' group by invoice_no order by order_id desc";*/
        //$query = $this->db->query("select * from user_order where FIND_IN_SET('" . $listing_id . "',listing_id) $whereListing group by invoice_no order by order_id desc $l ");
      //echo "select * from user_order where CONCAT(',', user_order.listing_id, ',') REGEXP CONCAT(',(', REPLACE('$uid', ',', '|'), '),') $whereListing group by invoice_no order by order_id desc $l ";
       $query = $this->db->query("select * from user_order where CONCAT(',', user_order.listing_id, ',') REGEXP CONCAT(',(', REPLACE('$listing_id', ',', '|'), '),') $whereListing group by invoice_no order by order_id desc $l ");
        $count = $query->num_rows();
      
      if ($count > 0) { 
            foreach ($query->result_array() as $row) {
                $tracker = array();
                $prescription_resultpost=array();
                $product_resultpost1  = array();
                $product_resultpost2  = array();
                
                $order_id            = $row['order_id'];
                // $prescription_doctor = $row['prescription_doctor']; //Hitesh
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
                $order_deliver_by    = $row['order_deliver_by'];
                $lat    = $row['lat'];
                $lng    = $row['lng'];
                if($order_deliver_by == NULL)
                {
                    $order_deliver_by = "";
                }
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
    //   print_r($count1); die;
      if ($count1 > 0) {
            foreach ($query1->result_array() as $row1) {
                    $order_id1            = $row1['order_id'];
                    $product_query = $this->db->query("select id as product_order_id,product_id,product_discount,product_name,product_unit,product_unit_value,product_img,product_quantity,product_price,sub_total,product_status,product_status_type,product_status_value,order_status,stock_id,barcode,batch_no,gst from user_order_product where order_id='$order_id1' order by product_order_id asc");
                    $product_count = $product_query->num_rows();
                    if ($product_count > 0) {
                        foreach ($product_query->result_array() as $product_row) {
                            
                            $product_order_id     = $product_row['product_order_id'];
                            $product_id           = $product_row['product_id'];
                            $product_name         = $product_row['product_name'];
                            $product_img          = "https://d2c8oti4is0ms3.cloudfront.net/images/product_images/".$product_row['product_img'];
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
                            $stock_id             = $product_row['stock_id'];
                            $barcode              = $product_row['barcode'];
                            $batch_no             = $product_row['batch_no'];
                            $gst                  = $product_row['gst'];
                            
                            $final_order_total = $order_total + ($product_price * $product_quantity);
                            $disc = (($product_price * $product_quantity)*$product_discount)/100;
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
                                "product_discount" => "$product_discount",
                                //"product_discount" => "$disc",
                                //"product_discount_rupee" => $product_discount,
                                "sub_total" => $sub_total,
                                "product_status" => $product_status,
                                "product_status_type" => $product_status_type,
                                "product_status_value" => $product_status_value,
                                "product_order_status" => $product_order_status,
                                "stock_id"              =>  $stock_id,
                                "barcode"               =>  $barcode,
                                "batch_no"              =>  $batch_no,
                                "gst"                   =>  $gst
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
                                $stock_id              = $prescription_row['stock_id'];
                                $barcode               = $prescription_row['barcode'];
                                $batch_no              = $prescription_row['batch_no'];
                                $gst                   = $prescription_row['gst'];
                                
                                $final_order_total = $order_total + ($prescription_quantity * $prescription_price); 
                                $disc1 = (($prescription_quantity * $prescription_price)*$prescription_discount)/100;
                                $order_total =$final_order_total-$prescription_discount;
                                $prescription_result[] = array(
                                    "prescription_name" => $prescription_name,
                                    "prescription_quantity" => $prescription_quantity,
                                    "prescription_price" => $prescription_price,
                                    "prescription_discount" => "$prescription_discount",
                                    //"prescription_discount" => "$disc1",
                                    //"product_discount_rupee" => $prescription_discount,
                                    "prescription_status" => $prescription_status,
                                    "stock_id"              =>  $stock_id,
                                    "barcode"               =>  $barcode,
                                    "batch_no"              =>  $batch_no,
                                    "gst"                   =>  $gst
                                );
                                
                            }
                        }
                        
                    }
                
                   
            }
      }
      if($order_total ==0)
      {
          $order_total = $row['order_total'];
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
                    
            $tracker = $this->PharmacyPartnerModel->pharmacy_tracker($user_id_, $invoice_no);   
            
            $res = $mno_orders_id = 0;
            $orderInsertedButNotAssiged = 0;
            $inv_id = $oid = $mno_id = $mnos = $allMnos = '';
            $mno_id = 0;
            $isAlreadyAssigned = array();
            $mno_status ="";
            $isAlreadyAssigned_check = $this->db->query("SELECT * FROM `mno_orders` WHERE `invoice_no` = '$invoice_no'")->row_array();
            if(!empty($isAlreadyAssigned_check))
            {
            $isAlreadyAssigned = $this->db->query("SELECT * FROM `mno_orders` WHERE `invoice_no` = '$invoice_no'  AND `ongoing` = 1")->row_array();
            //    echo "SELECT * FROM `mno_orders` WHERE `invoice_no` = '$invoice_no'  AND `ongoing` = 1"; die();
             //   print_r($isAlreadyAssigned); die();
                if(!empty($isAlreadyAssigned) && sizeof($isAlreadyAssigned > 0) && $isAlreadyAssigned['mno_id'] > 0){
                    
                   
                        $res = 1;
                        $mno_id  = $isAlreadyAssigned['mno_id'];
                        $mno_status = $isAlreadyAssigned['status'];
                    
                    
                } else if(!empty($isAlreadyAssigned) && sizeof($isAlreadyAssigned > 0) && $isAlreadyAssigned['mno_id'] == 0){
                    $orderInsertedButNotAssiged = 1;
                        $res = 1;
                        $mno_status = $isAlreadyAssigned['status'];
                }  
            }
            else
            {
               $mno_status = "Notassigned"; 
            }
            
            $data2= array();
            $this->load->model('PaymentModel');
            $data1 = $this->PaymentModel->get_invoice_costing($invoice_no);
            $data2[] = $data1['data'];
            
            $pharmacy_lat = "";
	        $pharmacy_lng = "";
            $sql_lat= "SELECT lat,lng FROM medical_stores where user_id = '".$listing_id."' AND pharmacy_branch_user_id=0";
	        $result_lat = $this->db->query($sql_lat)->row();
	        if(!empty($result_lat))
	        {
	            $pharmacy_lat = $result_lat->lat;
	            $pharmacy_lng = $result_lat->lng;
	            
	        }
	        else
	        {
	            $sql_lat= "SELECT lat,lng FROM medical_stores where pharmacy_branch_user_id = '".$listing_id."'";
    	        $result_lat = $this->db->query($sql_lat)->row();
    	        if(!empty($result_lat))
    	        {
    	            $pharmacy_lat = $result_lat->lat;
    	            $pharmacy_lng = $result_lat->lng;
    	            
    	        }
	        }
                //$tracker_mno = "";
                $tracker_mno = $this->order_tracker_for_users($invoice_no);
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
                    "tracker" => $tracker,
                    "tracker_mno" => $tracker_mno,
                    "prescription_create" => $prescription_resultpost,
                    "prescription_order" => $prescription_result,
                    "order_deliver_by" => $order_deliver_by,
                    "mno_status" => $mno_status,
                    "lat" => $lat,
                    "lng" => $lng,
                    'pharmacy_lat' => $pharmacy_lat,
                    'pharmacy_lng' => $pharmacy_lng,
                    "final_calculation"=> $data2
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
        
    }
    
    public function elasticsearchsize3($index_id,$keyword,$listing_id){
     
        $returndoctor = array();
        $perc=array();
     $returnresult = $this->elasticsearch->query_all($index_id,$keyword);
     
            if($returnresult['hits']['total'] < 0){
               
                       foreach($returnresult['hits']['hits'] as $hi){
                     
                          $sim = similar_text($hi['_source']['name'], $keyword, $perc[]);
                       }
                    }

            $resul= array("query"=>array("bool"=>array("should"=>array(array("term"=>array("listing_id.keyword"=>"$listing_id")),array("query_string"=>array("default_field"=>"_all","query"=>"$keyword"))))));
           $data1=json_encode($resul);
          //  print_r($data1);
                
             $returnresult = $this->elasticsearch->suggest($index_id,$data1);
            // print_r($returnresult);
               foreach($returnresult['hits']['hits'] as $hi){
                          $returndoctor[] =$hi['_source'];
                       }
         return $returndoctor;
                       
        
}
    
    public function order_list_search($listing_id, $listing_type, $order_type,$keyword)
    {
        
        //  echo "select * from user_order where FIND_IN_SET('" . $listing_id . "',listing_id) $whereListing group by invoice_no order by order_id desc $l";
      // exit();
       /* echo "select user_id,order_id,order_type,action_by,delivery_time,cancel_reason,action_by,listing_id,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where FIND_IN_SET('" . $listing_id . "',listing_id) and listing_type='$listing_type' group by invoice_no order by order_id desc";*/
       // $query = $this->db->query("select * from user_order where FIND_IN_SET('" . $listing_id . "',listing_id)  group by invoice_no order by order_id desc $l ");
        $index_id="pharmacy_order_list";
        $pharmacy_order_list=$this->elasticsearchsize3($index_id,$keyword, $listing_id);
        $pharmacy_order_list_count = count($pharmacy_order_list);
      
      if ($pharmacy_order_list_count > 0) {
            foreach ($pharmacy_order_list as $row) {
                $tracker = array();
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
                $order_deliver_by    = $row['order_deliver_by'];
                $lat    = $row['lat'];
                $lng    = $row['lng'];
                if($order_deliver_by == NULL)
                {
                    $order_deliver_by = "";
                }
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
                            $product_img          = "https://d2c8oti4is0ms3.cloudfront.net/images/product_images/".$product_row['product_img'];
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
                            $disc = (($product_price * $product_quantity)*$product_discount)/100;
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
                                "product_discount" => "$product_discount",
                                //"product_discount" => "$disc",
                                //"product_discount_rupee" => $product_discount,
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
                                $disc1 = (($prescription_quantity * $prescription_price)*$prescription_discount)/100;
                                $order_total =$final_order_total-$prescription_discount;
                                $prescription_result[] = array(
                                    "prescription_name" => $prescription_name,
                                    "prescription_quantity" => $prescription_quantity,
                                    "prescription_price" => $prescription_price,
                                    "prescription_discount" => "$prescription_discount",
                                    //"prescription_discount" => "$disc1",
                                    //"product_discount_rupee" => $prescription_discount,
                                    "prescription_status" => $prescription_status
                                );
                                
                            }
                        }
                        
                    }
                
                   
            }
      }
      if($order_total ==0)
      {
          $order_total = $row['order_total'];
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
                    
            $tracker = $this->PharmacyPartnerModel->pharmacy_tracker($user_id_, $invoice_no);   
            
            $res = $mno_orders_id = 0;
            $orderInsertedButNotAssiged = 0;
            $inv_id = $oid = $mno_id = $mnos = $allMnos = '';
            $mno_id = 0;
            $isAlreadyAssigned = array();
            $mno_status ="";
            $isAlreadyAssigned_check = $this->db->query("SELECT * FROM `mno_orders` WHERE `invoice_no` = '$invoice_no'")->row_array();
            if(!empty($isAlreadyAssigned_check))
            {
            $isAlreadyAssigned = $this->db->query("SELECT * FROM `mno_orders` WHERE `invoice_no` = '$invoice_no'  AND `ongoing` = 1")->row_array();
            //    echo "SELECT * FROM `mno_orders` WHERE `invoice_no` = '$invoice_no'  AND `ongoing` = 1"; die();
             //   print_r($isAlreadyAssigned); die();
                if(!empty($isAlreadyAssigned) && sizeof($isAlreadyAssigned > 0) && $isAlreadyAssigned['mno_id'] > 0){
                    
                   
                        $res = 1;
                        $mno_id  = $isAlreadyAssigned['mno_id'];
                        $mno_status = $isAlreadyAssigned['status'];
                    
                    
                } else if(!empty($isAlreadyAssigned) && sizeof($isAlreadyAssigned > 0) && $isAlreadyAssigned['mno_id'] == 0){
                    $orderInsertedButNotAssiged = 1;
                        $res = 1;
                        $mno_status = $isAlreadyAssigned['status'];
                }  
            }
            else
            {
               $mno_status = "Notassigned"; 
            }
            $data2= array();
            $this->load->model('PaymentModel');
            $data1 = $this->PaymentModel->get_invoice_costing($invoice_no);
            $data2[] = $data1['data'];
            
            
            $pharmacy_lat = "";
	        $pharmacy_lng = "";
            $sql_lat= "SELECT lat,lng FROM medical_stores where user_id = '".$listing_id."' AND pharmacy_branch_user_id=0";
	        $result_lat = $this->db->query($sql_lat)->row();
	        if(!empty($result_lat))
	        {
	            $pharmacy_lat = $result_lat->lat;
	            $pharmacy_lng = $result_lat->lng;
	            
	        }
	        else
	        {
	            $sql_lat= "SELECT lat,lng FROM medical_stores where pharmacy_branch_user_id = '".$listing_id."'";
    	        $result_lat = $this->db->query($sql_lat)->row();
    	        if(!empty($result_lat))
    	        {
    	            $pharmacy_lat = $result_lat->lat;
    	            $pharmacy_lng = $result_lat->lng;
    	            
    	        }
	        }
                //$tracker_mno = "";
                $tracker_mno = $this->order_tracker_for_users($invoice_no);
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
                    "tracker" => $tracker,
                    "tracker_mno" => $tracker_mno,
                    "prescription_create" => $prescription_resultpost,
                    "prescription_order" => $prescription_result,
                    "order_deliver_by" => $order_deliver_by,
                    "mno_status" => $mno_status,
                    "lat" => $lat,
                    "lng" => $lng,
                    'pharmacy_lat' => $pharmacy_lat,
                    'pharmacy_lng' => $pharmacy_lng,
                    "final_calculation"=> $data2
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
        
    }
    
     public function order_details($invoice_no)
    {
        $whereListing = "";
       
       /* echo "select user_id,order_id,order_type,action_by,delivery_time,cancel_reason,action_by,listing_id,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where FIND_IN_SET('" . $listing_id . "',listing_id) and listing_type='$listing_type' group by invoice_no order by order_id desc";*/
        $query = $this->db->query("select lat,lng,order_deliver_by,user_id,order_id,order_type,action_by,delivery_time,cancel_reason,action_by,listing_id,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where  invoice_no = '$invoice_no' group by invoice_no");
       
        $count = $query->num_rows();
      
      if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $tracker = array();
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
                $order_deliver_by    = $row['order_deliver_by'];
                $lat    = $row['lat'];
                $lng    = $row['lng'];
                if($order_deliver_by == NULL)
                {
                    $order_deliver_by = "";
                }
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
                    $product_query = $this->db->query("select id as product_order_id,product_id,product_discount,product_name,product_unit,product_unit_value,product_img,product_quantity,product_price,sub_total,product_status,product_status_type,product_status_value,order_status,stock_id,barcode,batch_no from user_order_product where order_id='$order_id1' order by product_order_id asc");
                    $product_count = $product_query->num_rows();
                    if ($product_count > 0) {
                        foreach ($product_query->result_array() as $product_row) {
                            
                            $product_order_id     = $product_row['product_order_id'];
                            $product_id           = $product_row['product_id'];
                            $product_name         = $product_row['product_name'];
                            $product_img          = "https://d2c8oti4is0ms3.cloudfront.net/images/product_images/".$product_row['product_img'];
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
                            $stock_id               = $product_row['stock_id'];
                            $barcode                = $product_row['barcode'];
                            $batch_no               = $product_row['batch_no'];
                            
                            $final_order_total = $order_total + ($product_price * $product_quantity);
                            $disc = (($product_price * $product_quantity)*$product_discount)/100;
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
                                "product_discount" => "$product_discount",
                                //"product_discount" => "$disc",
                                //"product_discount_rupee" => $product_discount,
                                "sub_total" => $sub_total,
                                "product_status" => $product_status,
                                "product_status_type" => $product_status_type,
                                "product_status_value" => $product_status_value,
                                "product_order_status" => $product_order_status,
                                "stock_id"              => $stock_id,
                                "barcode"               => $barcode,
                                "batch_no"              => $batch_no,
                                "gst"                   => ""
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
                                $stock_id               = $prescription_row['stock_id'];
                                $barcode                = $prescription_row['barcode'];
                                $batch_no               = $prescription_row['batch_no'];
                                
                                $final_order_total = $order_total + ($prescription_quantity * $prescription_price); 
                                $disc1 = (($prescription_quantity * $prescription_price)*$prescription_discount)/100;
                                $order_total =$final_order_total-$prescription_discount;
                                $prescription_result[] = array(
                                    "prescription_name" => $prescription_name,
                                    "prescription_quantity" => $prescription_quantity,
                                    "prescription_price" => $prescription_price,
                                    "prescription_discount" => "$prescription_discount",
                                    //"prescription_discount" => "$disc1",
                                    //"product_discount_rupee" => $prescription_discount,
                                    "prescription_status" => $prescription_status,
                                    "stock_id"              => $stock_id,
                                    "barcode"               => $barcode,
                                    "batch_no"              => $batch_no,
                                    "gst"                   => ""
                                );
                                
                            }
                        }
                        
                    }
                
                   
            }
      }
      if($order_total ==0)
      {
          $order_total = $row['order_total'];
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
                    
            $tracker = $this->PharmacyPartnerModel->pharmacy_tracker($user_id_, $invoice_no);   
            
            $res = $mno_orders_id = 0;
            $orderInsertedButNotAssiged = 0;
            $inv_id = $oid = $mno_id = $mnos = $allMnos = '';
            $mno_id = 0;
            $isAlreadyAssigned = array();
            $mno_status ="";
            $isAlreadyAssigned_check = $this->db->query("SELECT * FROM `mno_orders` WHERE `invoice_no` = '$invoice_no'")->row_array();
            if(!empty($isAlreadyAssigned_check))
            {
            $isAlreadyAssigned = $this->db->query("SELECT * FROM `mno_orders` WHERE `invoice_no` = '$invoice_no'  AND `ongoing` = 1")->row_array();
            //    echo "SELECT * FROM `mno_orders` WHERE `invoice_no` = '$invoice_no'  AND `ongoing` = 1"; die();
             //   print_r($isAlreadyAssigned); die();
                if(!empty($isAlreadyAssigned) && sizeof($isAlreadyAssigned > 0) && $isAlreadyAssigned['mno_id'] > 0){
                    
                   
                        $res = 1;
                        $mno_id  = $isAlreadyAssigned['mno_id'];
                        $mno_status = $isAlreadyAssigned['status'];
                    
                    
                } else if(!empty($isAlreadyAssigned) && sizeof($isAlreadyAssigned > 0) && $isAlreadyAssigned['mno_id'] == 0){
                    $orderInsertedButNotAssiged = 1;
                        $res = 1;
                        $mno_status = $isAlreadyAssigned['status'];
                }  
            }
            else
            {
               $mno_status = "Notassigned"; 
            }
            $data2= array();
            $this->load->model('PaymentModel');
            $data1 = $this->PaymentModel->get_invoice_costing($invoice_no);
            $data2[] = $data1['data'];
            
            $pharmacy_lat = "";
	        $pharmacy_lng = "";
            $sql_lat= "SELECT lat,lng FROM medical_stores where user_id = '".$listing_id."' AND pharmacy_branch_user_id=0";
	        $result_lat = $this->db->query($sql_lat)->row();
	        if(!empty($result_lat))
	        {
	            $pharmacy_lat = $result_lat->lat;
	            $pharmacy_lng = $result_lat->lng;
	            
	        }
	        else
	        {
	            $sql_lat= "SELECT lat,lng FROM medical_stores where pharmacy_branch_user_id = '".$listing_id."'";
    	        $result_lat = $this->db->query($sql_lat)->row();
    	        if(!empty($result_lat))
    	        {
    	            $pharmacy_lat = $result_lat->lat;
    	            $pharmacy_lng = $result_lat->lng;
    	            
    	        }
	        }
                //$tracker_mno = "";
                $tracker_mno = $this->order_tracker_for_users($invoice_no);
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
                    "tracker" => $tracker,
                    "tracker_mno" => $tracker_mno,
                    "prescription_create" => $prescription_resultpost,
                    "prescription_order" => $prescription_result,
                    "order_deliver_by" => $order_deliver_by,
                    "mno_status" => $mno_status,
                    "lat" => $lat,
                    "lng" => $lng,
                    'pharmacy_lat' => $pharmacy_lat,
                    'pharmacy_lng' => $pharmacy_lng,
                    "final_calculation"=> $data2
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
        
    }
    
    public function order_tracker_for_users($invoice_no){
        $statuses = $userTillCurrentStatus = $allStatuses1 = $statusdata = array();
       
        $st = $this->db->query("SELECT * FROM `mno_notifications` WHERE `invoice_no` LIKE '$invoice_no'   ORDER BY `id` DESC LIMIT 1")->row_array();
      
        $s = 0;
        if($st['notification_type'] != 1 && $st['notification_type'] != 4 && $st['notification_type'] != 5 && $st['notification_type'] != 6 && $st['notification_type'] != 29 ){
                   
            $s = intval($st['notification_type']);
                  
        }
            
      
   
      return $s;
    } 
    public function order_list_v2($listing_id, $listing_type, $type,$page,$keyword)
    {
        
         $limit = 10;
         $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        
        $whereListing = "";
        if($listing_type == 0){
            $whereListing = "and (listing_type='13' or listing_type='44')";
        } else {
            $whereListing = "and (listing_type='13' or listing_type='44')";
        }
        $type1 ="";
        if($type == "all"){
            $type1 = "";
        } 
        elseif($type == "ac")
             {
                $type1 = "and order_status ='Awaiting Confirmation'";
                
             }
        elseif($type == "acc")
             {
                $type1 = "and order_status ='Awaiting Customer Confirmation'";
                
             }
        elseif($type == "od")
             {
                $type1 = "and order_status ='Order Delivered'";
               
             }     
        elseif($type == "oc")
             {
                $type1 = "and order_status ='Order Cancelled'";
                
             }      
        elseif($type == "adc")
             {
                $type1 = "and order_status ='Awaiting Delivery Confirmation'";
               
             }  
        else
        {
            $type1="";
           
        }
        $keyword1="";
          if($keyword!="")
                {
                    $keyword1 ="AND (name LIKE '%%$keyword%%' OR mobile LIKE '%%$keyword%%' OR order_date LIKE '%%$keyword%%')";
                }
                else
                {
                    $keyword1 ="";
                }
        
       /* echo "select user_id,order_id,order_type,action_by,delivery_time,cancel_reason,action_by,listing_id,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where FIND_IN_SET('" . $listing_id . "',listing_id) and listing_type='$listing_type' group by invoice_no order by order_id desc";*/
        $query = $this->db->query("select user_id,order_id,order_type,action_by,delivery_time,cancel_reason,action_by,listing_id,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where FIND_IN_SET('" . $listing_id . "',listing_id) $whereListing $type1 $keyword1 group by invoice_no order by order_id desc LIMIT $start, $limit ");
       
        $count = $query->num_rows();
      
      if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $tracker = array();
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
                            $disc = (($product_price * $product_quantity)*$product_discount)/100;
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
                                "product_discount" => "$disc",
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
                                $disc1 = (($prescription_quantity * $prescription_price)*$prescription_discount)/100;
                                $order_total =$final_order_total-$prescription_discount;
                                $prescription_result[] = array(
                                    "prescription_name" => $prescription_name,
                                    "prescription_quantity" => $prescription_quantity,
                                    "prescription_price" => $prescription_price,
                                    "prescription_discount" => "$disc1",
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
                    
            $tracker = $this->PharmacyPartnerModel->pharmacy_tracker($user_id_, $invoice_no);     
                  
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
                    "tracker" => $tracker,
                    "prescription_create" => $prescription_resultpost,
                    "prescription_order" => $prescription_result
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
        
    }
    
     public function awaiting_order_list($listing_id, $listing_type, $order_type)
    {
       /* echo "select user_id,order_id,order_type,action_by,delivery_time,cancel_reason,action_by,listing_id,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where FIND_IN_SET('" . $listing_id . "',listing_id) and listing_type='$listing_type' group by invoice_no order by order_id desc";*/
        $query = $this->db->query("select user_id,order_id,order_type,action_by,delivery_time,cancel_reason,action_by,listing_id,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where FIND_IN_SET('" . $listing_id . "',listing_id) and listing_type='$listing_type' and order_status='Awaiting Confirmation' group by invoice_no order by order_id desc");
       
        $count = $query->num_rows();
      
      if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $tracker = array();
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
                            $disc = (($product_price * $product_quantity)*$product_discount)/100;
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
                                "product_discount" => "$disc",
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
                                $disc1 = (($prescription_quantity * $prescription_price)*$prescription_discount)/100;
                                $order_total =$final_order_total-$prescription_discount;
                                $prescription_result[] = array(
                                    "prescription_name" => $prescription_name,
                                    "prescription_quantity" => $prescription_quantity,
                                    "prescription_price" => $prescription_price,
                                    "prescription_discount" => "$disc1",
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
                    
            $tracker = $this->PharmacyPartnerModel->pharmacy_tracker($user_id_, $invoice_no);     
                  
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
                    "tracker" => $tracker,
                    "prescription_create" => $prescription_resultpost,
                    "prescription_order" => $prescription_result
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
        
    }
     public function booking_details($user_id,$invoice_no)
    {
       /* echo "select user_id,order_id,order_type,action_by,delivery_time,cancel_reason,action_by,listing_id,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where FIND_IN_SET('" . $listing_id . "',listing_id) and listing_type='$listing_type' group by invoice_no order by order_id desc";*/
        $query = $this->db->query("select * from user_order where invoice_no='$invoice_no'");
       
        $count = $query->num_rows();
      
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $tracker = array();
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
                $order_deliver_by    = $row['order_deliver_by'];
                $lat    = $row['lat'];
                $lng    = $row['lng'];
                if($order_deliver_by == NULL)
                {
                    $order_deliver_by = "";
                }
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
                            $product_img          = "https://d2c8oti4is0ms3.cloudfront.net/images/product_images/".$product_row['product_img'];
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
                            $disc = (($product_price * $product_quantity)*$product_discount)/100;
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
                                "product_discount" => "$product_discount",
                                //"product_discount" => "$disc",
                                //"product_discount_rupee" => $product_discount,
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
                                $disc1 = (($prescription_quantity * $prescription_price)*$prescription_discount)/100;
                                $order_total =$final_order_total-$prescription_discount;
                                $prescription_result[] = array(
                                    "prescription_name" => $prescription_name,
                                    "prescription_quantity" => $prescription_quantity,
                                    "prescription_price" => $prescription_price,
                                    "prescription_discount" => "$prescription_discount",
                                    //"prescription_discount" => "$disc1",
                                    //"product_discount_rupee" => $prescription_discount,
                                    "prescription_status" => $prescription_status
                                );
                                
                            }
                        }
                        
                    }
                
                   
            }
      }
      if($order_total ==0)
      {
          $order_total = $row['order_total'];
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
                    
            $tracker = $this->PharmacyPartnerModel->pharmacy_tracker($user_id_, $invoice_no);   
            
            $res = $mno_orders_id = 0;
            $orderInsertedButNotAssiged = 0;
            $inv_id = $oid = $mno_id = $mnos = $allMnos = '';
            $mno_id = 0;
            $isAlreadyAssigned = array();
            $mno_status ="";
            $isAlreadyAssigned_check = $this->db->query("SELECT * FROM `mno_orders` WHERE `invoice_no` = '$invoice_no'")->row_array();
            if(!empty($isAlreadyAssigned_check))
            {
            $isAlreadyAssigned = $this->db->query("SELECT * FROM `mno_orders` WHERE `invoice_no` = '$invoice_no'  AND `ongoing` = 1")->row_array();
            //    echo "SELECT * FROM `mno_orders` WHERE `invoice_no` = '$invoice_no'  AND `ongoing` = 1"; die();
             //   print_r($isAlreadyAssigned); die();
                if(!empty($isAlreadyAssigned) && sizeof($isAlreadyAssigned > 0) && $isAlreadyAssigned['mno_id'] > 0){
                    
                   
                        $res = 1;
                        $mno_id  = $isAlreadyAssigned['mno_id'];
                        $mno_status = $isAlreadyAssigned['status'];
                    
                    
                } else if(!empty($isAlreadyAssigned) && sizeof($isAlreadyAssigned > 0) && $isAlreadyAssigned['mno_id'] == 0){
                    $orderInsertedButNotAssiged = 1;
                        $res = 1;
                        $mno_status = $isAlreadyAssigned['status'];
                }  
            }
            else
            {
               $mno_status = "Notassigned"; 
            }
            $data2= array();
            $this->load->model('PaymentModel');
            $data1 = $this->PaymentModel->get_invoice_costing($invoice_no);
            $data2[] = $data1['data'];
            
            $pharmacy_lat = "";
	        $pharmacy_lng = "";
            $sql_lat= "SELECT lat,lng FROM medical_stores where user_id = '".$listing_id."' AND pharmacy_branch_user_id=0";
	        $result_lat = $this->db->query($sql_lat)->row();
	        if(!empty($result_lat))
	        {
	            $pharmacy_lat = $result_lat->lat;
	            $pharmacy_lng = $result_lat->lng;
	            
	        }
	        else
	        {
	            $sql_lat= "SELECT lat,lng FROM medical_stores where pharmacy_branch_user_id = '".$listing_id."'";
    	        $result_lat = $this->db->query($sql_lat)->row();
    	        if(!empty($result_lat))
    	        {
    	            $pharmacy_lat = $result_lat->lat;
    	            $pharmacy_lng = $result_lat->lng;
    	            
    	        }
	        }
	        
                //$tracker_mno = "";
                $tracker_mno = $this->order_tracker_for_users($invoice_no);
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
                    "tracker" => $tracker,
                    "tracker_mno" => $tracker_mno,
                    "prescription_create" => $prescription_resultpost,
                    "prescription_order" => $prescription_result,
                    "order_deliver_by" => $order_deliver_by,
                    "mno_status" => $mno_status,
                    "lat" => $lat,
                    "lng" => $lng,
                    'pharmacy_lat' => $pharmacy_lat,
                    'pharmacy_lng' => $pharmacy_lng,
                    "final_calculation"=> $data2
                );
            }
        } 
        else {
            $resultpost = array();
        }
        return $resultpost; 
        
    }
    public function pharmacy_tracker($user_id, $invoice_no){
        $data = array();
        $getStatuses = $this->db->query("SELECT * FROM `user_order_tracking` WHERE `invoice_no` LIKE '$invoice_no' ORDER BY `created_at` ASC")->result_array();
        
        foreach($getStatuses as $statuses){
            $created_at = date_create($statuses['created_at']);
            $d = date_format($created_at, 'D jS F Y, g:ia');
            // echo $d ; die();
            $action_by = strtolower($statuses['action_by']);
            $t['timestamp'] = $d;
            $t['status'] = $statuses['status']; 
            $data[] = $t;
        }
        
        return $data;
    }
    public function order_status_common($order_id_data, $prescription_id_data,$order_details,$prescription_details,$delivery_charges,$delivery_charges_by_customer,$sub_all_total,$discount,$gst,$order_deliver_by)
    {
        // echo $order_deliver_by; die();
        $total_discount = $grand_total = 0;
        $title = "";
        $this->load->model('OrderModel');
        $this->load->model('PartnermnoModel');
                   $this->load->model('PaymentModel');
        // $delivery_charges = 0;
        
        date_default_timezone_set('Asia/Kolkata');
        $updated_at       = date('Y-m-d H:i:s');
        $sub_total        = '0';
        $product_price    = '0';
        $product_quantity = '0';
        $action_by        = 'vendor';
        
        
        
        // listing_type
        $userOrderDetails = $this->db->query("SELECT * FROM `user_order` WHERE `order_id` = '$order_id_data' ")->row_array();
        $invoice_no = $invNoOrder = $userOrderDetails['invoice_no'];
         $product_details_new = json_decode($order_details,TRUE);
         if($userOrderDetails['order_deliver_by'] == '' ){
            $this->db->query("UPDATE `user_order` SET order_deliver_by = '$order_deliver_by' WHERE `invoice_no` = '$invoice_no'");
         }  
        if($userOrderDetails['listing_type'] == 44 ){
             //   calculate delivery time and cost
    
            $deliveryTimeAndCostMno = $this->PartnermnoModel->get_delivery_time_cost($order_id_data);
            
            $delivery_charges = $deliveryTimeAndCostMno['delivery_charges'];
            $delivery_time = $deliveryTimeAndCostMno['delivery_time_in_hrs_mins'];
            $delivery_charges_by_customer = $delivery_charges;
        } 
        
         // call delivery charge api
        $offer_id = 0;
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
       
        $order_id      = $product_details_new['order_id'];
       
       
        $order_status  = $product_details_new['order_status'];
        $listing_id    = $product_details_new['listing_id'];
        $listing_type  = $product_details_new['listing_type'];
        $order_product_data    = $product_details_new['product_order'];
                   
        
        
        
        foreach ($order_product_data as $result) {
            $sub_total = 0;
            $product_order_id     = $result['product_order_id'];
            $product_quantity     = $result['product_quantity'];
            $product_price        = $result['product_price'];
            $product_unit         = $result['product_unit'];
            $product_unit_value   = $result['product_unit_value'];
            $product_discount     = $result['product_discount'];
            $product_order_status = $result['product_order_status'];
            
            $product_gst = array_key_exists('gst', $result ) ? $result['gst']:0 ;
            $sub_total            = $product_price * $product_quantity;
           
            $query = $this->db->query("UPDATE user_order_product SET gst = '$product_gst', product_quantity='$product_quantity',product_price='$product_price',product_discount='$product_discount',sub_total='$sub_total',order_status='$product_order_status',updated_at='$updated_at',product_unit='$product_unit',product_unit_value='$product_unit_value' where product_id='$product_order_id' and order_id='$order_id_data'");
            
          if($product_order_status == "Available" || $product_order_status == "available"){
                $grand_total = $grand_total + $sub_total;
                $total_discount = $total_discount + $product_discount;
           }  
            // $grand_total = $grand_total + $sub_total;
            $sub_total = '0';
            
            
        }
       
       $this->db->select('listing_id');
       $this->db->from('user_order');
       $this->db->where('order_id',$order_id_data);
       $listing_ids = $this->db->get()->row()->listing_id;

       $this->db->where('order_id',$order_id_data);
       $this->db->update('user_order',array('copy_listing_id' => $listing_ids));
       
       $this->db->select('medical_name');
       $this->db->from('medical_stores');
       $this->db->where('user_id',$listing_id);
       $medical_name = $this->db->get()->row()->medical_name;

       $this->db->select('listing_name');
       $this->db->from('user_order');
       $this->db->where('order_id',$order_id_data);
       $listing_names = $this->db->get()->row()->listing_name;

       $this->db->where('order_id',$order_id_data);
       $this->db->update('user_order',array('copy_listing_name' => $listing_names));

       $this->db->where('order_id',$order_id_data);
       $this->db->update('user_order',array('listing_name' => $medical_name));
       
        $prescription_details_new = json_decode($prescription_details,TRUE);
        $order_id           = $prescription_details_new['order_id'];
        $order_status       = $prescription_details_new['order_status'];
        $delivery_time      = $prescription_details_new['delivery_time'];
        $prescription_order = $prescription_details_new['prescription_order'];
      
        $query = $this->db->query("UPDATE user_order SET order_status='$order_status',delivery_time='$delivery_time',listing_id='$listing_id',delivery_charge = '$delivery_charges',actual_cost='$grand_total',order_total='$grand_total',discount='$discount',gst='$gst' where order_id='$order_id_data'");
        $invNoArray = $this->db->query("select `invoice_no` from `user_order` WHERE order_id = '$order_id_data'")->row_array();
        $invno = $invNoArray['invoice_no'];
        
        //$action_by_status = "Pharmacy";
        //$orderStatus = "Order confirmed";
        // $this->OrderModel->update_status($invno,$orderStatus, $action_by_status);
           
             
         foreach ($prescription_order as $result) {
             $sub_total = 0;
            $prescription_name     = $result['prescription_name'];
            $prescription_quantity = $result['prescription_quantity'];
            $prescription_price    = $result['prescription_price'];
            $prescription_discount = $result['prescription_discount'];
            $prescription_status   = $result['prescription_status'];
            $prescription_gst = array_key_exists('gst', $result ) ? $result['gst']:0 ;
            $sub_total            = $prescription_price * $prescription_quantity;
             
             
            $query = $this->db->query("INSERT INTO `prescription_order_list`(`order_id`, `prescription_name`, `prescription_quantity`, `prescription_price`, `prescription_discount`, `prescription_status`,`gst`) VALUES ('$prescription_id_data', '$prescription_name', '$prescription_quantity', '$prescription_price', '$prescription_discount', '$prescription_status','$prescription_gst')");
            
           if($prescription_status == "Available" || $prescription_status == "available"){
                $grand_total = $grand_total + $sub_total;
                  $total_discount = $total_discount + $prescription_discount;
           }  
           
            // $grand_total = $grand_total + $prescription_price ; 
            $pre_total = 0;
        }
       
       $this->db->select('listing_id');
       $this->db->from('user_order');
       $this->db->where('order_id',$order_id);
       $listing_ids = $this->db->get()->row()->listing_id;

       $this->db->where('order_id',$order_id);
       $this->db->update('user_order',array('copy_listing_id' => $listing_ids));
      
       $query = $this->db->query("UPDATE user_order SET order_status='$order_status',delivery_time='$delivery_time',delivery_charge = '$delivery_charges',listing_id='$listing_id',actual_cost='$grand_total',order_total='$grand_total',discount='$total_discount',gst='$gst' where `invoice_no`='$invoice_no'");
       
        $invNoArray = $this->db->query("select `invoice_no`, `listing_name` from `user_order` WHERE order_id = '$prescription_id_data'")->row_array();
        $invno = $invNoArray['invoice_no'];
        $listing_name = $invNoArray['listing_name'];
        $action_by_status = "Pharmacy";
        $orderStatus1 = "Order confirmed by ".$listing_name. " pharmacy";
        $update_order_status = $this->OrderModel->update_status( $invno,$orderStatus1, $action_by_status);
        
        
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
            // $medical_name = $this->db->select('medical_name')->from('medical_stores')->where('user_id', $listing_id)->get()->row()->medical_name;
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
                        $msg   = 'Your Order No. '.$invoice_no.' from '.$listing_name.' pharmacy has been confirmed. '.$listing_name.' will deliver the products in '.$delivery_time.' mins.';
                        
                        
                        $notification_type="pharmacy_order_confirm";
                           $listing_ids1 = explode(',',$listing_ids);
                         if(count($listing_ids1) > 1 )
                                {
                                  for($ic =0;$ic<count($listing_ids1);$ic++)
                                    {
                                      if($listing_ids1[$ic] != $this->session->userdata('id'))
                                        {
                                           $customer_token_query   = $this->db->query("SELECT token,token_status,agent,web_token FROM users WHERE id='$listing_ids1[$ic]'");
                                           $customer_token = $customer_token_query->row_array();
                                           $token_status   = $customer_token['token_status'];
                                           if ($token_status > 0) 
                                           {
                                               $agent    = $customer_token['agent'];
                                               $reg_id    = $customer_token['token'];
                                               $web_token    = $customer_token['web_token'];
                                               $img_url   = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                                               $tag       = 'text';
                                               $key_count = '1';
                                               $order_status = "";
                                               $title = 'You Lost Your Order';
                                               $msg   = 'Order ' . $invoice_no.' has been confirmed by '.$listing_name.'.';
                                               send_gcm_notify_vendor($title,$reg_id,$msg,$img_url,$tag,$order_status,$updated_at,$order_id,$invoice_no,$name,$listing_name,$agent);
                                            }
                                        }
                                    }
                                }
                        
                        function send_gcm_notify_vendor($title,$reg_id,$msg,$img_url,$tag,$order_status,$updated_at,$order_id,$invoice_no,$name,$listing_name,$agent)
                               {
                        
                                   $fields  = array(
                                       'to' => $reg_id,
                                       'priority' => "high",
                                        $agent === 'android' ? 'data' : 'notification' => array(
                                           "title" => $title,
                                           "message" => $msg,
                                           "image" => $img_url,
                                           "tag" => $tag,
                                           "notification_type" => "pharmacy_order_cancel",
                                            "order_status"=>$order_status,
                                            "order_date"=>$updated_at,
                                            "order_id"=>$order_id,
                                            "invoice_no"=>$invoice_no,
                                            "name"=>$name,
                                            "listing_name"=>$listing_name
                                       )
                                   );
                                   $headers = array(
                                       GOOGLE_GCM_URL,
                                       'Content-Type: application/json',
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
                                 }
                                 
                                 
                            
                       
                         
                     }
                    if ($order_status == 'Awaiting Delivery Confirmation' || $order_status=='Awaiting Customer Confirmation') {
                        $title = $medical_name .' has confirmed your order, please check the bill and confirm the order.';
                        $msg   = 'Your have received a reply on your order';
                        $notification_type="pharmacy_order_confirm";
                    }
                    if ($order_status == 'Order Delivered') {
                        $title = 'Order Delivered';
                        //$msg   = 'Delivered: Order no.'.$invoice_no.' of Rs.'.$order_total.' is delivered. We hope your experience with Medicalwale.com has been satisfactory, for feedback and queries contact us on Medicalwale.com.';
                        $msg='Your order has been delivered by '.$medical_name .' Thanks for choosing Medicalwale.com';
                        $notification_type="pharmacy_order_deliver";
                        
                    }
            if($title != ""){
                
            
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
        }
        /*$data=array('order_status' => $order_status);
        */
        
        // if order is by night owls then send this notification to mno
      //  $mno_details = $this->db->query("SELECT u.token, u.agent, mo.mno_id ,uo.* FROM `user_order` as uo left join mno_orders as mo on (uo.`invoice_no` = mo.invoice_no) left join users as u on (mo.mno_id = u.id) WHERE uo.`order_id` = '$order_id_data' and uo.`listing_type` = 44 AND mo.ongoing = 1 AND mo.status = 'accepted'")->row_array();
       /* if(sizeof($mno_details) > 0){
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
        }*/
        
        
             // notification to mno
                                
            $getMnoOrdersDetails = $this->db->query("SELECT * FROM `mno_orders` WHERE `invoice_no` LIKE '$invoice_no' AND `ongoing` = 1 AND `status` LIKE 'accepted' ORDER BY id DESC")->row_array();
            if(sizeof($getMnoOrdersDetails) > 0){
            
                $img = 'https://medicalwale.com/img/noti_icon.png';
                $title = "Pharmacy accepted the order";
                
                $msg = "Pharmacy accepted the order, please pickup the order";
    
             //   $invoice_no = $invNoOrder;
                $receiver_id = $getMnoOrdersDetails['mno_id'];
                $mno_order_id = $getMnoOrdersDetails['id'];
                $notification_type = 5; // refer table mno_notification_types
                
                $res = $this->PartnermnoModel->send_gcm_notification_to_mno($receiver_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
                
                
                   //   PHARMACY_ORDER_ACCEPTED AND SEND_QUOTE_TO_CUSTOMER ARE in same step that is why 2 notifications together
               
                $notification_type = 7; // refer table mno_notification_types
                
                $title = "Pharmacy sent quote";
                $msg = "Pharmacy sent quote to customer";
                
                $res = $this->PartnermnoModel->send_gcm_notification_to_mno($receiver_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
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
    
    public function order_status($order_id, $delivery_time, $order_status, $listing_id, $listing_type, $order_data_list,$delivery_charges,$delivery_charges_by_customer,$sub_all_total,$discount,$gst,$order_deliver_by)
    {
        $total_discount = $grand_total = 0;
         $title = "";
       
        $this->load->model('OrderModel');
          $this->load->model('PartnermnoModel');
                     $this->load->model('PaymentModel');
        // $delivery_charges = 0;
      
      
        date_default_timezone_set('Asia/Kolkata');
        $updated_at       = date('Y-m-d H:i:s');
        $sub_total        = '0';
        $product_price    = '0';
        $product_quantity = '0';
        $action_by        = 'vendor';
        
        // listing_type
        $userOrderDetails = $this->db->query("SELECT * FROM `user_order` WHERE `order_id` = '$order_id' ")->row_array();
        $invoice_no = $userOrderDetails['invoice_no'];
        if($userOrderDetails['listing_type'] == 44 ){
             //   calculate delivery time and cost
    
    
             $deliveryTimeAndCostMno = $this->PartnermnoModel->get_delivery_time_cost($order_id);
            
            
             $delivery_charges = $deliveryTimeAndCostMno['delivery_charges'];
             $delivery_time = $deliveryTimeAndCostMno['delivery_time_in_hrs_mins'];
             $delivery_charges_by_customer = $delivery_charges;
        
        } 
        
           // call delivery charge api
        $offer_id = 0;
        if($userOrderDetails['order_deliver_by'] == '' ){
            $this->db->query("UPDATE user_order SET  order_deliver_by = '$order_deliver_by' where order_id='$order_id'");
        }
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
      
        $this->db->where('order_id',$order_id);
        $this->db->delete('user_order_product');
        
        foreach ($order_data_list as $result) {
            $sub_total = 0;
            $product_order_id     = $result['product_order_id'];
            $product_name        = $result['product_name'];
            $product_quantity     = $result['product_quantity'];
            $product_price        = $result['product_price'];
            $product_unit         = $result['product_unit'];
            $product_unit_value   = $result['product_unit_value'];
            $product_discount     = $result['product_discount'];
            $product_order_status = $result['product_order_status'];
            $product_gst = array_key_exists('gst', $result ) ? $result['gst']:0 ;
            $sub_total            = $product_price * $product_quantity;
            
            if(array_key_exists('stock_id',$result)){
                $stock_id              = $result['stock_id'];
            } else {
                $stock_id              = "";
            }
            if(array_key_exists('barcode',$result)){
                $barcode               = $result['barcode'];
            } else {
                $barcode               = "";
            }
            if(array_key_exists('batch_no',$result)){
                $batch_no              = $result['batch_no'];
            } else {
                $batch_no              = "";
            }
           
            
            /*$stock_id              = $result['stock_id'];
            $barcode               = $result['barcode'];
            $batch_no              = $result['batch_no'];*/
            
              
            
            
            $arr = array(
                         'order_id' => $order_id,
                         'product_name' => $product_name,
                         'product_id'=> $product_order_id,
                         'product_quantity' => $product_quantity,
                         'product_price' =>  $product_price,
                         'product_unit' => $product_unit,
                         'product_unit_value' => $product_unit_value,
                         'product_discount' => $product_discount,
                         'order_status' => $product_order_status,
                         'stock_id'              =>  $stock_id,
                         'barcode'               =>  $barcode,
                         'batch_no'              =>  $batch_no,
                         'gst'                  => $product_gst
                         );
            //$query = $this->db->query("UPDATE user_order_product SET product_quantity='$product_quantity',product_price='$product_price',product_discount='$product_discount',sub_total='$sub_total',order_status='$product_order_status',updated_at='$updated_at',product_unit='$product_unit',product_unit_value='$product_unit_value' where product_id='$product_order_id' and order_id='$order_id'");
            $this->db->insert('user_order_product', $arr);
           if($product_order_status == "Available" || $product_order_status == "available"){
                $grand_total = $grand_total + $sub_total;
                $total_discount = $total_discount + $product_discount;
           }
           
             $sub_total = '0';
        }
        
       $this->db->select('listing_id');
       $this->db->from('user_order');
       $this->db->where('order_id',$order_id);
       $listing_ids = $this->db->get()->row()->listing_id;

       $this->db->where('order_id',$order_id);
       $this->db->update('user_order',array('copy_listing_id' => $listing_ids));
     
       $this->db->select('medical_name');
       $this->db->from('medical_stores');
       $this->db->where('user_id',$listing_id);
       $medical_name = $this->db->get()->row()->medical_name;

       $this->db->select('listing_name');
       $this->db->from('user_order');
       $this->db->where('order_id',$order_id);
       $listing_names = $this->db->get()->row()->listing_name;

       $this->db->where('order_id',$order_id);
       $this->db->update('user_order',array('copy_listing_name' => $listing_names));

       $this->db->where('order_id',$order_id);
       $this->db->update('user_order',array('listing_name' => $medical_name));
      
        $query = $this->db->query("UPDATE user_order SET order_status='$order_status',delivery_time='$delivery_time',delivery_charge = '$delivery_charges',listing_id='$listing_id',actual_cost='$grand_total',order_total='$grand_total',discount='$total_discount',gst='$gst' where order_id='$order_id'");
        
        $invNoArray = $this->db->query("select `invoice_no`, `listing_name` from `user_order` WHERE order_id = '$order_id'")->row_array();
        $invno = $invNoArray['invoice_no'];
        $listing_name = $invNoArray['listing_name'];
        $action_by_status = "Pharmacy";
        $orderStatus1 = "Order confirmed by ".$listing_name. " pharmacy";
        $this->OrderModel->update_status( $invno,$orderStatus1, $action_by_status);
       
        
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
       
        $order_info    = $this->db->select('user_id,listing_id,listing_name,delivery_time,delivery_charge,invoice_no,name,listing_name,order_total,order_date')->from('user_order')->where('order_id', $order_id)->get()->row();
        $user_id       = $order_info->user_id;
        $listing_id    = $order_info->listing_id;
        $listing_name  = $order_info->listing_name;
        $delivery_time = $order_info->delivery_time;
        $invoice_no    = $order_info->invoice_no;
        $name          = $order_info->name;
        //$listing_name  = $order_info->listing_name;
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
                        $msg   = 'Your Order No. '.$invoice_no.' from '.$listing_name.' pharmacy has been confirmed. '.$listing_name.' will deliver the products in '.$delivery_time.' mins.';
                        
                        
                        $notification_type="pharmacy_order_confirm";
                       
                        
                        $listing_ids1 = explode(',',$listing_ids);
                         if(count($listing_ids1) > 1 )
                                {
                                  for($ic =0;$ic<count($listing_ids1);$ic++)
                                    {
                                      if($listing_ids1[$ic] != $this->session->userdata('id'))
                                        {
                                           $customer_token_query   = $this->db->query("SELECT token,token_status,agent,web_token FROM users WHERE id='$listing_ids1[$ic]'");
                                           $customer_token = $customer_token_query->row_array();
                                           $token_status   = $customer_token['token_status'];
                                           if ($token_status > 0) 
                                           {
                                               $agent    = $customer_token['agent'];
                                               $reg_id    = $customer_token['token'];
                                               $web_token    = $customer_token['web_token'];
                                               $img_url   = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                                               $tag       = 'text';
                                               $key_count = '1';
                                               $order_status = "";
                                               $title = 'You Lost Your Order';
                                               $msg   = 'Order ' . $invoice_no.' has been confirmed by '.$listing_name.'.';
                                               send_gcm_notify_vendor($title,$reg_id,$msg,$img_url,$tag,$order_status,$updated_at,$order_id,$invoice_no,$name,$listing_name,$agent);
                                            }
                                        }
                                    }
                                }
                        
                        function send_gcm_notify_vendor($title,$reg_id,$msg,$img_url,$tag,$order_status,$updated_at,$order_id,$invoice_no,$name,$listing_name,$agent)
                               {
                        
                                   $fields  = array(
                                       'to' => $reg_id,
                                       'priority' => "high",
                                        $agent === 'android' ? 'data' : 'notification' => array(
                                           "title" => $title,
                                           "message" => $msg,
                                           "image" => $img_url,
                                           "tag" => $tag,
                                           "notification_type" => "pharmacy_order_cancel",
                                            "order_status"=>$order_status,
                                            "order_date"=>$updated_at,
                                            "order_id"=>$order_id,
                                            "invoice_no"=>$invoice_no,
                                            "name"=>$name,
                                            "listing_name"=>$listing_name
                                       )
                                   );
                                   $headers = array(
                                       GOOGLE_GCM_URL,
                                       'Content-Type: application/json',
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
                                 }
                        
                        
                         
                     }
                    if ($order_status == 'Awaiting Delivery Confirmation' || $order_status=='Awaiting Customer Confirmation') {
                        $title = $listing_name .'has confirmed your order, please check the bill and confirm the order.';
                        $msg   = 'Your have received a reply on your order';
                        $notification_type="pharmacy_order_confirm";
                    }
                    if ($order_status == 'Order Delivered') {
                        $title = 'Order Delivered';
                        //$msg   = 'Delivered: Order no.'.$invoice_no.' of Rs.'.$order_total.' is delivered. We hope your experience with Medicalwale.com has been satisfactory, for feedback and queries contact us on Medicalwale.com.';
                        $msg='Your order has been delivered by '.$listing_name .' Thanks for choosing Medicalwale.com';
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
        
             
                          $getMnoOrdersDetails = $this->db->query("SELECT * FROM `mno_orders` WHERE `invoice_no` LIKE '$invoice_no' AND `ongoing` = 1 AND `status` LIKE 'accepted' ORDER BY id DESC")->row_array();
            if(sizeof($getMnoOrdersDetails) > 0){
            
                $img = 'https://medicalwale.com/img/noti_icon.png';
                $title = "Pharmacy accepted the order";
                
                $msg = "Pharmacy accepted the order, please pickup the order";
    
             //   $invoice_no = $invNoOrder;
                $receiver_id = $getMnoOrdersDetails['mno_id'];
                $mno_order_id = $getMnoOrdersDetails['id'];
                $notification_type = 5; // refer table mno_notification_types
                
                $res = $this->PartnermnoModel->send_gcm_notification_to_mno($receiver_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
                
                
                   //   PHARMACY_ORDER_ACCEPTED AND SEND_QUOTE_TO_CUSTOMER ARE in same step that is why 2 notifications together
               
                $notification_type = 7; // refer table mno_notification_types
                
                $title = "Pharmacy sent quote";
                $msg = "Pharmacy sent quote to customer";
                
                $res = $this->PartnermnoModel->send_gcm_notification_to_mno($receiver_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
            }
                   
        
        return array(
            'status' => 200,
            'message' => 'success',
            'order_status' => $order_status
        );
        
    }
    
    public function add_address($user_name,$user_mobile,$dob,$email,$gender,$blood,$address_details,$type,$listing_id,$user_id)
    {
        
        date_default_timezone_set('Asia/Kolkata');
        $date_at            = date('Y-m-d H:i:s');
        $date               = date('Y-m-d');
        if($type=="add")
        {
            $query = $this->db->query("SELECT id FROM users WHERE phone='$user_mobile'");
            $count = $query->num_rows();
            if ($count > 0) 
            {
                $get_list = $query->row();
                $user_id = $get_list->id;
                
                $query = $this->db->query("INSERT INTO `user_pharmacy_detail`(`user_id`, `name`, `mobile`, `dob`, `gender`, `email`, `bloodgroup`, `createdat`, `status`, `pharmacy_id`) VALUES ('$user_id','$user_name','$user_mobile','$dob','$gender','$email','$blood','$date_at','0','$listing_id')");
                
                $address_details_new = json_decode($address_details,TRUE);
               foreach ($address_details_new as $result) 
                      {
                        $address1     = $result['address1'];
                        $address2     = $result['address2'];
                        $landmark     = $result['landmark'];
                        $city         = $result['city'];
                        $state        = $result['state'];
                        $date         = $date; 
                        $lat          = $result['lat'];
                        $lng          = $result['lng'];
                        $full_address = $result['full'];
                        $pincode      = $result['pincode'];
                        $query1 = $this->db->query("INSERT INTO `user_pharmacy_address`(`user_id`, `pincode`, `address1`, `address2`, `landmark`, `city`, `state`, `date`, `lat`, `lng`, `full_address`) VALUES ('$user_id','$pincode','$address1','$address2','$landmark','$city','$state','$date','$lat','$lng','$full_address')");
                    }
                
                
                     return array(
            'status' => 200,
            'message' => 'success');       
                
            }
            else
            {
                
                $query2 = $this->db->query("INSERT INTO `users`(`name`, `phone`, `email`, `vendor_id`,  `gender`, `dob`,`blood_group`, `updated_at`,`agent`, `last_login`) VALUES ('$user_name','$user_mobile','$email','0','$gender','$dob','$blood','$date_at','android','$date_at')");
                
                $user_id=$this->db->insert_id();
                
                $query = $this->db->query("INSERT INTO `user_pharmacy_detail`(`user_id`, `name`, `mobile`, `dob`, `gender`, `email`, `bloodgroup`, `createdat`, `status`, `pharmacy_id`) VALUES ('$user_id','$user_name','$user_mobile','$dob','$gender','$email','$blood','$date_at','0','$listing_id')");
                
                    $address_details_new = json_decode($address_details,TRUE);
                   foreach ($address_details_new as $result) 
                          {
                            $address1     = $result['address1'];
                            $address2     = $result['address2'];
                            $landmark     = $result['landmark'];
                            $city         = $result['city'];
                            $state        = $result['state'];
                            $date         = $date; 
                            $lat          = $result['lat'];
                            $lng          = $result['lng'];
                            $full_address = $result['full'];
                            $pincode      = $result['pincode'];
                            $query1 = $this->db->query("INSERT INTO `user_pharmacy_address`(`user_id`, `pincode`, `address1`, `address2`, `landmark`, `city`, `state`, `date`, `lat`, `lng`, `full_address`) VALUES ('$user_id','$pincode','$address1','$address2','$landmark','$city','$state','$date','$lat','$lng','$full_address')");
                    }
                
                
                     return array(
            'status' => 200,
            'message' => 'success');       
                
                
                
            }
        }
        if($type=="edit")
        {
         //$user_id
          $query = $this->db->query("SELECT id FROM user_pharmacy_detail WHERE user_id='$user_id' and status='0' ");
            $count = $query->num_rows();
            if ($count > 0) 
            {
                $get_list = $query->row();
                //$user_id = $get_list->id;
                
                $query = $this->db->query("UPDATE `user_pharmacy_detail` SET `name`='$user_name',`mobile`='$user_mobile',`dob`='$dob',`gender`='$gender',`email`='$email',`bloodgroup`='$blood' WHERE `user_id`='$user_id' and `pharmacy_id`='$listing_id' and status='0'");
                
                $address_details_new = json_decode($address_details,TRUE);
                foreach ($address_details_new as $result) 
                        {
                        $id           = $result['id'];
                        $address1     = $result['address1'];
                        $address2     = $result['address2'];
                        $landmark     = $result['landmark'];
                        $city         = $result['city'];
                        $state        = $result['state'];
                        $date         = $date; 
                        $lat          = $result['lat'];
                        $lng          = $result['lng'];
                        $full_address = $result['full'];
                        $pincode      = $result['pincode'];
                       
                        $query1 = $this->db->query("UPDATE `user_pharmacy_address` SET `pincode`='$pincode',`address1`='$address1',`address2`='$address2',`landmark`='$landmark',`city`='$city',`state`='$state',`date`='$date',`lat`='$lat',`lng`='$lng',`full_address`='$full_address' WHERE `user_id`='$user_id' and id='$id'");
                    }
                return array(
                                'status' => 200,
                                'message' => 'success'
                            );       
                
            }
            return array(
                                'status' => 201,
                                'message' => 'fail'
                            );
            
        }
    }
    
     public function list_address($listing_id)
    {
        $query = $this->db->query("select * from user_pharmacy_detail where pharmacy_id='$listing_id' and status='0' GROUP BY user_id order by name");
       
        $count = $query->num_rows();
      
      if ($count > 0) {
            foreach ($query->result_array() as $row) {
                    $add_result=array();
                
                $user_id      = $row['user_id'];
                $user_name    = $row['name'];
                $user_mobile  = $row['mobile'];
                $user_dob     = $row['dob'];
                $user_gender  = $row['gender'];
                $user_email   = $row['email'];
                $user_blood   = $row['bloodgroup'];
               
                
                
                $address_query = $this->db->query("SELECT * FROM `user_pharmacy_address` where user_id='$user_id' order by id desc");
                $address_count = $address_query->num_rows();
                if ($address_count > 0) {
                            foreach ($address_query->result_array() as $address_row) {
                                $id              = $address_row['id'];
                                $pincode         = $address_row['pincode'];
                                $address1        = $address_row['address1'];
                                $address2        = $address_row['address2'];
                                $landmark        = $address_row['landmark'];
                                $city            = $address_row['city'];
                                $state           = $address_row['state'];
                                $lat             = $address_row['lat'];
                                $lng             = $address_row['lng'];
                                $full_address    = $address_row['full_address'];
                               
                                $add_result[] = array(
                                    "id" => $id,
                                    "address1" => $address1,
                                    "address2" => $address2,
                                    "landmark" => $landmark,
                                    "city" => $city,
                                    "state" => $state,
                                    "pincode" => $pincode,
                                    "lat" => $lat,
                                    "lng" => $lng,
                                    "full_address" => $full_address
                                  
                                );
                                
                            }
                        }
                        
                   
                
                   
          
      
   
                $resultpost[] = array(
                    
                    "user_id" => $user_id,
                    "user_name" => $user_name,
                    "user_mobile" => $user_mobile,
                    "user_dob" => $user_dob,
                    "user_gender" => $user_gender,
                    "user_email" => $user_email,
                    "user_blood" => $user_blood,
                    "user_address" => $add_result,
                  
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
      public function delete_address($listing_id,$user_id)
    {
       
       $address_query = $this->db->query("UPDATE `user_pharmacy_detail` SET `status`='1' WHERE `user_id`='$user_id' and `pharmacy_id`='$listing_id'");
   
                return array(
            'status' => 200,
            'message' => 'success'
           
        );
            

    }
    
     public function prescription_status($order_id, $order_status, $prescription_order, $delivery_time,$listing_id,$delivery_charges,$delivery_charges_by_customer,$sub_all_total,$discount,$gst,$order_deliver_by)
    {
        $total_discount = $grand_total = 0;
        // echo $order_id; die();
        $this->load->model('OrderModel');
          $this->load->model('PartnermnoModel');
           $this->load->model('PaymentModel');
        // $delivery_charges = 0;
      
      
        date_default_timezone_set('Asia/Kolkata');
        $updated_at            = date('Y-m-d H:i:s');
        $sub_total             = '0';
        $prescription_price    = '0';
        $prescription_quantity = '0';
        
        //$query = $this->db->query("DELETE FROM `prescription_order_list` WHERE order_id='$order_id'");
        
        
        
          // listing_type
        $userOrderDetails = $this->db->query("SELECT * FROM `user_order` WHERE `order_id` = '$order_id' ")->row_array();
        $invoice_no = $userOrderDetails['invoice_no'];
       // echo $invoice_no; die();
        if($userOrderDetails['listing_type'] == 44 ){
             //   calculate delivery time and cost
    
             $deliveryTimeAndCostMno = $this->PartnermnoModel->get_delivery_time_cost($order_id);
            $delivery_charges = $deliveryTimeAndCostMno['delivery_charges'];
            $delivery_time = $deliveryTimeAndCostMno['delivery_time_in_hrs_mins'];
              $delivery_charges_by_customer = $delivery_charges;
        }  
        
            // call delivery charge api
        $offer_id = 0;
         if($userOrderDetails['order_deliver_by'] == '' ){
                $this->db->query("UPDATE user_order SET order_deliver_by = '$order_deliver_by' where order_id='$order_id'");
         }
        $addDeliveryCharges  = $this->PaymentModel->add_delivery_charges($delivery_charges, $invoice_no, $delivery_charges_by_customer, $offer_id);
            // print_r($addDeliveryCharges); die();
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
        
        
        // print_r($prescription_order);
        // die;
        foreach ($prescription_order as $result) {
            $sub_total = 0;
            $prescription_name     = $result['prescription_name'];
            $prescription_quantity = $result['prescription_quantity'];
            $prescription_price    = $result['prescription_price'];
            $prescription_discount = $result['prescription_discount'];
            $prescription_status   = $result['prescription_status'];
            $prescription_gst = array_key_exists('gst', $result ) ? $result['gst']:0 ;
            if(array_key_exists('stock_id',$result)){
                $stock_id              = $result['stock_id'];
            } else {
                $stock_id              = "";
            }
            if(array_key_exists('barcode',$result)){
                $barcode               = $result['barcode'];
            } else {
                $barcode               = "";
            }
            if(array_key_exists('batch_no',$result)){
                $batch_no              = $result['batch_no'];
            } else {
                $batch_no              = "";
            }
             /*$stock_id              = $result['stock_id'];
            $barcode               = $result['barcode'];
            $batch_no              = $result['batch_no'];*/
            $sub_total            = $prescription_price * $prescription_quantity;
            
            $query = $this->db->query("INSERT INTO `prescription_order_list`(`order_id`, `prescription_name`, `prescription_quantity`, `prescription_price`, `prescription_discount`, `prescription_status`,`stock_id`,`barcode`,`batch_no`,`gst`) VALUES ('$order_id', '$prescription_name', '$prescription_quantity', '$prescription_price', '$prescription_discount', '$prescription_status','$stock_id','$barcode','$batch_no','$prescription_gst')");
            // $grand_total = $grand_total + $prescription_price;
            
            if($prescription_status == "Available" || $prescription_status == "available"){
                $grand_total = $grand_total + $sub_total;
                $total_discount = $total_discount + $prescription_discount;
           }  
        }
        
       $this->db->select('listing_id');
       $this->db->from('user_order');
       $this->db->where('order_id',$order_id);
       $listing_ids = $this->db->get()->row()->listing_id;
       
       
       $this->db->select('medical_name');
       $this->db->from('medical_stores');
       $this->db->where('user_id',$listing_id);
       $medical_name = $this->db->get()->row()->medical_name;

       $this->db->select('listing_name');
       $this->db->from('user_order');
       $this->db->where('order_id',$order_id);
       $listing_names = $this->db->get()->row()->listing_name;

       $this->db->where('order_id',$order_id);
       $this->db->update('user_order',array('copy_listing_name' => $listing_names));

       $this->db->where('order_id',$order_id);
       $this->db->update('user_order',array('listing_name' => $medical_name));
       
       $this->db->where('order_id',$order_id);
       $this->db->update('user_order',array('copy_listing_id' => $listing_ids));
      
       $query = $this->db->query("UPDATE user_order SET order_status='$order_status',delivery_time='$delivery_time',delivery_charge = '$delivery_charges',listing_id='$listing_id',actual_cost='$grand_total',order_total='$grand_total',discount='$total_discount',gst='$gst' where order_id='$order_id'");
    
        
        
        $invNoArray = $this->db->query("select `invoice_no` , `listing_name` from `user_order` WHERE order_id = '$order_id'")->row_array();
        $invno = $invNoArray['invoice_no'];
        $listing_name = $invNoArray['listing_name'];
        $action_by_status = "Pharmacy";
        $orderStatus = "Order confirmed by ".$listing_name. " pharmacy";;
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
        
        
        
        $order_info    = $this->db->select('user_id,listing_id,listing_name,delivery_time,delivery_charge,invoice_no,name,listing_name,order_date')->from('user_order')->where('order_id', $order_id)->get()->row();
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
                        $msg   = 'Your Order No. '.$invoice_no.' from '.$listing_name.' pharmacy has been confirmed. '.$listing_name.' will deliver the products in '.$delivery_time.' mins.';
                        $notification_type="pharmacy_order_confirm";
                        
                       $listing_ids1 = explode(',',$listing_ids);
                         if(count($listing_ids1) > 1 )
                                {
                                  for($ic =0;$ic<count($listing_ids1);$ic++)
                                    {
                                      if($listing_ids1[$ic] != $this->session->userdata('id'))
                                        {
                                           $customer_token_query   = $this->db->query("SELECT token,token_status,agent,web_token FROM users WHERE id='$listing_ids1[$ic]'");
                                           $customer_token = $customer_token_query->row_array();
                                           $token_status   = $customer_token['token_status'];
                                           if ($token_status > 0) 
                                           {
                                               $agent    = $customer_token['agent'];
                                               $reg_id    = $customer_token['token'];
                                               $web_token    = $customer_token['web_token'];
                                               $img_url   = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                                               $tag       = 'text';
                                               $key_count = '1';
                                               $order_status = "";
                                               $title = 'You Lost Your Order';
                                               $msg   = 'Order ' . $invoice_no.' has been confirmed by '.$listing_name.'.';
                                               send_gcm_notify_vendor($title,$reg_id,$msg,$img_url,$tag,$order_status,$updated_at,$order_id,$invoice_no,$name,$listing_name,$agent);
                                            }
                                        }
                                    }
                                }
                        
                        function send_gcm_notify_vendor($title,$reg_id,$msg,$img_url,$tag,$order_status,$updated_at,$order_id,$invoice_no,$name,$listing_name,$agent)
                               {
                        
                                   $fields  = array(
                                       'to' => $reg_id,
                                       'priority' => "high",
                                        $agent === 'android' ? 'data' : 'notification' => array(
                                           "title" => $title,
                                           "message" => $msg,
                                           "image" => $img_url,
                                           "tag" => $tag,
                                           "notification_type" => "pharmacy_order_cancel",
                                            "order_status"=>$order_status,
                                            "order_date"=>$updated_at,
                                            "order_id"=>$order_id,
                                            "invoice_no"=>$invoice_no,
                                            "name"=>$name,
                                            "listing_name"=>$listing_name
                                       )
                                   );
                                   $headers = array(
                                       GOOGLE_GCM_URL,
                                       'Content-Type: application/json',
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
                                 }
                    }
                    if ($order_status == 'Awaiting Delivery Confirmation' || $order_status=='Awaiting Customer Confirmation') {
                        $title = $medical_name->medical_name .'has confirmed your order, please check the bill and confirm the order.';
                        $msg   = 'Your have received a reply on your order';
                        $notification_type="pharmacy_order_confirm";
                    }
                    if ($order_status == 'Order Delivered') {
                        $title = 'Order Delivered';
                        //$msg   = 'Delivered: Order no.'.$invoice_no.' of Rs.'.$order_total.' is delivered. We hope your experience with Medicalwale.com has been satisfactory, for feedback and queries contact us on Medicalwale.com.';
                        $msg='Your order has been delivered by '.$medical_name->medical_name .' Thanks for choosing Medicalwale.com';
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
        
        
      $getMnoOrdersDetails = $this->db->query("SELECT * FROM `mno_orders` WHERE `invoice_no` LIKE '$invoice_no' AND `ongoing` = 1 AND `status` LIKE 'accepted' ORDER BY id DESC")->row_array();
            if(sizeof($getMnoOrdersDetails) > 0){
            
                $img = 'https://medicalwale.com/img/noti_icon.png';
                $title = "Pharmacy accepted the order";
                
                $msg = "Pharmacy accepted the order, please pickup the order";
    
             //   $invoice_no = $invNoOrder;
                $receiver_id = $getMnoOrdersDetails['mno_id'];
                $mno_order_id = $getMnoOrdersDetails['id'];
                $notification_type = 5; // refer table mno_notification_types
                
                $res = $this->PartnermnoModel->send_gcm_notification_to_mno($receiver_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
                
                
                   //   PHARMACY_ORDER_ACCEPTED AND SEND_QUOTE_TO_CUSTOMER ARE in same step that is why 2 notifications together
               
                $notification_type = 7; // refer table mno_notification_types
                
                $title = "Pharmacy sent quote";
                $msg = "Pharmacy sent quote to customer";
                
                $res = $this->PartnermnoModel->send_gcm_notification_to_mno($receiver_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
            }               
        return array(
            'status' => 200,
            'message' => 'success',
            'order_status' => $order_status
        );
    }
    
    
    public function order_deliver_cancel($order_id, $cancel_reason, $type, $notification_type,$user_id)
    {
        $this->load->model('LedgerModel');
        $this->load->model('PaymentModel');
        $this->load->model('OrderModel');
        $this->load->model('PartnermnoModel');
        date_default_timezone_set('Asia/Kolkata');
        $updated_at = date('Y-m-d H:i:s');
        $user_order_id = "";
        if ($type == 'Order Cancelled') {
            //mrunal
            $array1 = array('order_status' => $type,
                           'cancel_reason' => $cancel_reason,
                           'action_by' => 'vendor',
                           'updated_at' => $updated_at
                           ); 
               $this->db->select('listing_id,copy_cancel_listing_id');
                $this->db->from('user_order');
                $this->db->where('invoice_no',$order_id);
                $check_id = $this->db->get()->row();
                $listing_id = explode(',', $check_id->listing_id);
                if(!empty($check_id->copy_cancel_listing_id))
                $cancel_list = explode(',', $check_id->copy_cancel_listing_id);
                else
                $cancel_list = array();
                // print_r($cancel_list);
                // die;
                  //$user_id = "5040";
                if(sizeof($listing_id)>1){
                  
                    for($i=0;$i<sizeof($listing_id);$i++){
                        if($listing_id[$i]==$user_id){
                            array_push($cancel_list,$listing_id[$i]);
                            unset($listing_id[$i]);
                        }
                    }
                   
                    $cancel = implode(',',$cancel_list);
                    //print_r($cancel);die;
                    $this->db->set('copy_cancel_listing_id',$cancel);
                
                    $this->db->where('invoice_no',$order_id);
                   
                    if($this->db->update('user_order')){
                       $list_id = implode(',',$listing_id);
                       
                       $this->db->set('listing_id',$list_id);
                       $this->db->where('invoice_no',$order_id);
                       $this->db->update('user_order');
                        if($this->db->affected_rows() > 0){
                           
                           $msg = "booking of pharmacy is cancelled by ".$cancel;
                            $this->db->set('cancel_booking_log',$msg);
                            $this->db->where('invoice_no',$order_id);
                            $this->db->update('user_order');
                           
                        }
                    }
                    
                    $invNoArray = $this->db->query("select  `invoice_no` , `listing_name` from `user_order` WHERE invoice_no = '$order_id'")->row_array();
                    
                    $invno = $invNoArray['invoice_no'];
                    $listing_name = $invNoArray['listing_name'];
          
                    $action_by_status = "Pharmacy";
                    $orderStatus = 'Order cancelled by '.$listing_name.' pharmacy';
                    $this->OrderModel->update_status( $order_id,$orderStatus, $action_by_status);
                }               
                 elseif(sizeof($listing_id)==1){
            
                    $list_id = implode(',',$listing_id);
               
                    array_push($cancel_list,$list_id);
                    
                    $cancel = implode(',',$cancel_list);
                  
                    $this->db->set('copy_cancel_listing_id',$listing_id[0]);
                    
                    $this->db->where('invoice_no',$order_id);
                    $this->db->update('user_order');
                    
                    $this->db->set('listing_id',$listing_id[0]);
                    $this->db->where('invoice_no',$order_id);
                    $this->db->update('user_order');
                    
                    $this->db->where('invoice_no',$order_id);
                    $this->db->update('user_order',$array1);
                    
                     $msg = "booking of pharmacy is cancelled by ".$cancel;
                    $this->db->set('cancel_booking_log',$msg);
                    $this->db->where('invoice_no',$order_id);
                    $this->db->update('user_order');
                    
                    $invNoArray = $this->db->query("select  `invoice_no` , `listing_name` from `user_order` WHERE invoice_no = '$order_id'")->row_array();
                    
                    $invno = $invNoArray['invoice_no'];
                    $listing_name = $invNoArray['listing_name'];
          
                    $action_by_status = "Pharmacy";
                    $orderStatus = 'Order cancelled by '.$listing_name.' pharmacy';
                    $this->OrderModel->update_status( $order_id,$orderStatus, $action_by_status);
                 }
            //end               
            
            //$query = $this->db->query("UPDATE `user_order` SET `updated_at`='$updated_at',`order_status`='Order Cancelled',`cancel_reason`='$cancel_reason',`action_by`='vendor' WHERE invoice_no='$order_id'");

            
            // search in mno_orders
            $mno_orders = $this->db->query("SELECT * FROM `mno_orders` WHERE `invoice_no` LIKE '$order_id' AND ongoing = 1 ")->row_array();
            if(sizeof($mno_orders) > 0){
                $mno_id = $mno_orders['mno_id'];
                $mno_order_id = $mno_orders['id'];
                $this->db->query("UPDATE `mno_orders` SET cancel_reason_after_accept = '$cancel_reason', `order_cancel` = '1', ongoing = '0' where id= '$mno_order_id'");
                $getMnoLocation = $this->db->query("SELECT * FROM `mno_location` WHERE `mno_id` = '$mno_id' ORDER BY `created_at` DESC ")->row_array();
                $mno_lat = $getMnoLocation['lat'];
                $mno_lng = $getMnoLocation['lng'];
                
                $assignAnotherOrder = $this->PartnermnoModel->assign_pending_order_to_mno($mno_id, $mno_lat, $mno_lng);
                    
            }
         
        }
        if ($type == 'Order Delivered') {
            $invNoArray = $this->db->query("select  order_id, `invoice_no` , `listing_id` from `user_order` WHERE invoice_no = '$order_id'")->row_array();
            $user_order_id = $invNoArray['order_id'];
            $invoice_no = $invNoArray['invoice_no'];
             $listing_id = $invNoArray['listing_id'];
            $check_table_existance = $this->PharmacyPartnerModel->stock_table_existance($listing_id);
        	       
        	       if($check_table_existance['status'] == true){
                        $table_name = $check_table_existance['table_name'];
                        
                        
                        $query1 = $this->db->query("select user_id,order_id,order_type,action_by,delivery_time,cancel_reason,action_by,listing_id,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where invoice_no='$invoice_no'");
                        $count1 = $query1->num_rows();
                        if ($count1 > 0) {
                    foreach ($query1->result_array() as $row1) {
                        $order_id            = $row1['order_id'];
                        
                        $product_query = $this->db->query("select product_quantity,stock_id,barcode,batch_no from user_order_product where order_id='$order_id'");
                        $product_count = $product_query->num_rows();
                        if ($product_count > 0) {
                            foreach ($product_query->result_array() as $product_row) {
                                
                                $stock_id           = $product_row['stock_id'];
                                $barcode            = $product_row['barcode'];
                                $batch_no           = $product_row['batch_no'];
                                $product_qunatity   = $product_row['product_quantity'];
                                
                                $quantity_query = $this->db->query("select quantity from $table_name where id='$stock_id' ");
                                if (!empty($quantity_query)) {
                                    $quantity_query = $this->db->query("UPDATE $table_name SET quantity = quantity - $product_qunatity where id='$stock_id' ");
                                }
                               
                            }
                        }
                     
                        $prescription_query = $this->db->query("SELECT * FROM `prescription_order_list` where order_id='$order_id'");
                        $prescription_count = $prescription_query->num_rows();
                        if ($prescription_count > 0) {
                            foreach ($prescription_query->result_array() as $prescription_row) {
                                    
                                    $product_qunatity   = $prescription_row['prescription_quantity'];
                                    $stock_id           = $prescription_row['stock_id'];
                                    $barcode            = $prescription_row['barcode'];
                                    $batch_no           = $prescription_row['batch_no'];
                                    $quantity_query = $this->db->query("select quantity from $table_name where id='$stock_id' ");
                                    if (!empty($quantity_query)) {
                                        $quantity_query = $this->db->query("UPDATE $table_name SET quantity = quantity - $product_qunatity where id='$stock_id' ");
                                    }
                                }
                        }
                    }
                }
                
                
   
        	       } 
        	       
            $query = $this->db->query("UPDATE `user_order` SET `updated_at`='$updated_at',`order_status`='Order Delivered',`cancel_reason`='',`action_by`='vendor' WHERE invoice_no='$invoice_no'");
            $invNoArray = $this->db->query("select `user_id`,`listing_type`,`listing_id`,`invoice_no` , `total_without_dc` , `listing_name` from `user_order` WHERE invoice_no = '$invoice_no'")->row_array();
            
            $invno = $invNoArray['invoice_no'];
            $listing_name = $invNoArray['listing_name'];
            $user_id = $invNoArray['user_id'];
            $user_id_type = 0;
            $listing_type = $invNoArray['listing_type'];
            $listing_id = $invNoArray['listing_id'];
            $action_by_status = "Pharmacy";
            $orderStatus = 'Order delivered by '.$listing_name.' pharmacy';
            $this->OrderModel->update_status( $order_id,$orderStatus, $action_by_status);
            
            /*get final amount*/
            $deliveryTimeAndCostMno = $this->PaymentModel->get_invoice_costing($invoice_no);
            if(sizeof($deliveryTimeAndCostMno) > 0 && array_key_exists('data' , $deliveryTimeAndCostMno )  && array_key_exists('order_found' , $deliveryTimeAndCostMno )  && $deliveryTimeAndCostMno['order_found'] == 1 ){
                $deliveryTimeAndCostMnoData = $deliveryTimeAndCostMno['data'];
            
                // pending add ledger entry -> delivered by pharmacy
                
                $invoice_no = $invno; 
                $order_type = 1;
                
               // add for each transaction
                 $user_comments = "";
                $mw_comments = "Package delivered to customer by pharmacy.";
                $vendor_comments = "";
                $payment_method = "";
                $credit = $deliveryTimeAndCostMnoData['grand_total_customer'];
                $debit = "";
                $transaction_of = 1; // entry for package
                 $transaction_id = "";
                $trans_status = 1; // default success
        
                       // == if cap means given to MNO
         
             
                // $user_id , $user_id_type , $mno_id, $mno_id_type
                $transaction_id = "";
                $trans_status = 1;
    
                 $transaction_date = "";
                 $vendor_id=$listing_type;
                 $array_data=array();
                $res = $this->LedgerModel->create_ledger($user_id, $invoice_no, $user_id_type, $listing_id, $listing_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status,$transaction_date,$vendor_id,$array_data);
            
               
               // add for each transaction
                $user_comments = "";
                $mw_comments = "Amount paid by user";
                $vendor_comments = "";
                $payment_method = 3; // default all CAP
                $credit = "";
                $debit = $deliveryTimeAndCostMnoData['grand_total_customer'];
                $transaction_of = 2; // entry for amount
                 $transaction_id = "";
                $trans_status = 1; // default success
        
                    // == if cap means given to MNO
         
             
                // $user_id , $user_id_type , $mno_id, $mno_id_type
                $transaction_id = "";
                $trans_status = 1;
    
                 $transaction_date = "";
                 $vendor_id=$listing_type;
                 $array_data=array();
                $res = $this->LedgerModel->create_ledger($user_id, $invoice_no, $user_id_type, $listing_id, $listing_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status,$transaction_date,$vendor_id,$array_data);
              
            }   
               
            //   qwertyuio
            
                $existingOrder = $this->db->query("SELECT mlo.lat,mlo.lng,ml.mno_name, mo.*, u.id as user_id,uo.name as user_name, u.phone, u.token, u.agent FROM `mno_orders` as mo left join user_order as uo on (mo.invoice_no = uo.invoice_no) left join users as u on (uo.user_id = u.id) left join mno_list as ml on (ml.mno_id = mo.mno_id) left join mno_location as mlo on (mlo.mno_id = mo.mno_id)  WHERE mo.`invoice_no` = '$invoice_no' AND  mo.`ongoing` > 0 AND mo.`status` = 'accepted'")->row_array();
                if(sizeof($existingOrder) > 0){
                    $mno_id = $existingOrder['mno_id'];
                    $user_name = $existingOrder['user_name'];
                     $mno_lat = $existingOrder['lat'];
                    $mno_lng = $existingOrder['lng'];
                    
                    if($mno_id > 0 && $mno_id != ""){
                        
                    
                        $receiver_id = $mno_id;
                        $notification_type = 15; //ORDER_DELIVERED refer mno_notification_types
                        $img = "https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png";
                        $mno_order_id = $existingOrder['id'];
                        // $title = "Order cancelled by you behalf of customer ";
                        $title = "Order delivered ";
                        $msg = "#" .$invoice_no.  " Order delivered  ";
                        
                        
                        $upateMNOOrder = $this->db->query("UPDATE `mno_orders` SET  ongoing = 0 where id= '$mno_order_id'");
            
                
                        $this->PartnermnoModel->send_gcm_notification_to_mno($receiver_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
                    
                        $assignAnotherOrder = $this->PartnermnoModel->assign_pending_order_to_mno($mno_id, $mno_lat, $mno_lng);
                    }
                }    
            
    
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
        
        $order_info    = $this->db->select('user_id,listing_id,listing_type,listing_name,delivery_time,invoice_no,name,listing_name,sum(order_total) as total,order_date')->from('user_order')->where('invoice_no', $order_id)->get()->row();
        $user_id       = $order_info->user_id;
        $listing_id    = $order_info->listing_id;
        $listing_name  = $order_info->listing_name;
        $delivery_time = $order_info->delivery_time;
        $invoice_no    = $order_info->invoice_no;
        $name          = $order_info->name;
        $listing_name  = $order_info->listing_name;
        $order_total   = $order_info->total;
        $order_date     = $order_info->order_date;
        $listing_type  = $order_info->listing_type;
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
               // $title = 'Order Delivered';
              //  $msg   = 'Delivered: Order no.'.$invoice_no.'  is delivered. We hope your experience with Medicalwale.com has been satisfactory, for feedback and queries contact us on Medicalwale.com.';
                 $title = 'Order Delivered';
                        //$msg   = 'Delivered: Order no.'.$invoice_no.' of Rs.'.$order_total.' is delivered. We hope your experience with Medicalwale.com has been satisfactory, for feedback and queries contact us on Medicalwale.com.';
              //  print_r($medical_name); die();
                if(sizeof($medical_name) > 0){
                    $msg='Your order has been delivered by '.$medical_name->medical_name .' Thanks for choosing Medicalwale.com';
                } else {
                    $msg='Your order has been delivered by pharmacy. Thanks for choosing Medicalwale.com';
                    
                }
                
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
             
             if(($type == 'Order Cancelled' && $listing_type != 44) || $type == 'Order Delivered'){
                $this->db->insert('All_notification_Mobile', $notification_array);
                send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $notification_type, $order_status, $updated_at, $order_id, $invoice_no, $name, $listing_name, $agent);
             }
        }
        
        if(($listing_type == 44 || $listing_type == 13) && $type == 'Order Cancelled'){
            $getMnoOrdersDetails = $this->db->query("SELECT * FROM `mno_orders` WHERE `invoice_no` LIKE '$invoice_no' AND `status` LIKE 'accepted' ORDER BY id DESC")->row_array();
            if(sizeof($getMnoOrdersDetails) > 0){
                
                $img = 'https://medicalwale.com/img/noti_icon.png';
                $title = "Pharmacy rejected the order";
                
                $msg = "Pharmacy rejected the order, please send order to another pharmacy";
    
                $invoice_no = $invoice_no;
                $receiver_id = $getMnoOrdersDetails['mno_id'];
                $mno_order_id = $getMnoOrdersDetails['id'];
                $notification_type = 6; // refer table mno_notification_types this type is PHARMACY_ORDER_REJECTED
                
                $res = $this->PartnermnoModel->send_gcm_notification_to_mno($receiver_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
            }        
        } 
        
        return array(
            'status' => 200,
            'message' => 'success',
            'order_status' => $type
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
        $resultpost = array();
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
            
            $query1 = $this->db->query("SELECT * FROM users WHERE id='$listing_id'");
            $count1 = $query1->num_rows();
            if($count1 > 0) {
                $row = $query1->row_array();
                $hub_id = $row['staff_hub_user_id'];
                $query2 = $this->db->query("SELECT medical_stores.reg_date,medical_stores.profile_pic, medical_stores.medical_name, medical_stores.store_manager, medical_stores.lat, medical_stores.lng, medical_stores.address1, medical_stores.address2, medical_stores.pincode, medical_stores.city, medical_stores.state, medical_stores.store_since,users.phone FROM medical_stores INNER JOIN users ON medical_stores.user_id=users.id WHERE medical_stores.user_id='$hub_id' AND pharmacy_branch_user_id='0'");
                $count2 = $query2->num_rows();
                if($count2 > 0) {
                    $row2 = $query2->row_array();
                    $store_name         = $row2['medical_name'];
                }
                else
                {
                    $store_name = "";
                }
                $resultpost[] = array(
                    "reg_date" => "",
                    "store_name" => $store_name,
                    "store_manager_name" => $row['name'],
                    "mobile" => $row['phone'],
                    "profile_pic" => "https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.png",
                    "latitude" => "",
                    "longitude" => "",
                    "address_line1" => "",
                    "address_line2" => "",
                    "pincode" => "",
                    "city" => "",
                    "state" => "",
                    "store_since" => ""
                );
            }
            
            
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
    
    public function pharmacy_licence_pic($listing_id, $license_no, $licence_pic_file)
    {
        if($licence_pic_file != "")
        {
            $query = $this->db->query("UPDATE medical_stores SET licence_pic='$licence_pic_file',licence_pic_status='1',license_registration='$license_no',license_no_status='1' WHERE user_id='$listing_id'");
        }
        else
        {
            $query = $this->db->query("UPDATE medical_stores SET license_registration='$license_no',license_no_status='1' WHERE user_id='$listing_id'");
        }
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
		WHERE medical_stores.user_id ='$listing_id' and medical_stores.pharmacy_branch_user_id ='0';");
            
            return array(
                'status' => 200,
                'message' => 'success'
            );
        
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
                                'password' => md5('12345'),
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
    public function add_staff_member_v1($user_id, $mobile, $staff_name, $staff_email, $profile, $media_id)
    {
        $query = $this->db->query("SELECT * FROM users WHERE phone='$mobile' AND vendor_id = '13'");
        $count = $query->num_rows();
        if ($count == 0) {
            
                $data = array(
                                'vendor_id' => 13,
                                'name' => $staff_name,
                                'phone' => $mobile,
                                'email' => $staff_email,
                                'avatar_id' => $media_id,
                                'staff_hub_user_id' => $user_id,
                                'password' => md5('12345'),
                                'is_active'=>1
                                );
                                
                $this->db->insert('users',$data);   
                $suser_id = $this->db->insert_id();
                $resultpost =   array(
                                'status' => 200,
                                'message' => 'Success',
                                'user_id' => $suser_id
                            );
            
        } else {
            $resultpost =   array(
                                'status' => 201,
                                'message' => 'Already Available! Either this user is Hub or Staff'
                            );
        }
        return $resultpost;
        
    }
    
    
    
    // Ghanashyam Parihar Starts 17 Feb 2020 starts
    
    public function add_staff_attendance($user_id, $mobile, $staff_name, $staff_email, $profile, $media_id,$data1)
    {
        $query = $this->db->query("SELECT * FROM users WHERE phone='$mobile' AND vendor_id = '13'");
        $count = $query->num_rows();
        if ($count == 0) {
            
                $data = array(
                                'vendor_id' => 13,
                                'name' => $staff_name,
                                'phone' => $mobile,
                                'email' => $staff_email,
                                'avatar_id' => $media_id,
                                'staff_hub_user_id' => $user_id,
                                'password' => md5('12345'),
                                'is_active'=>1
                                );
                                
                $this->db->insert('users',$data);   
                $suser_id = $this->db->insert_id();
                
                
            // print_r($data1);
            $this->db->insert('pharmacy_staff', $data1);
            // echo $str = $this->db->last_query();
            //  die;
          	$b_id = $this->db->insert_id();
        
        
            $this->db->query("UPDATE pharmacy_staff  SET staff_user_id   = '$suser_id' WHERE id = '$b_id'");
        
          
        //     if ($this->db->affected_rows()){    
        //         $data="select users.*,hospital_staff.*,media.* from users inner join hospital_staff on(users.id=hospital_staff.user_id )
        //         inner join media on(users.avatar_id=media.id) where hospital_staff.user_id ='".$id."' ";
        //         $comp = $this->db->query($data)->row();
                
        //         $this->sendmail($comp->email,$comp->name,$comp->password,$comp->phone);  
    	   // }
                
                
                $resultpost =   array(
                                'status' => 200,
                                'message' => 'Success',
                                'user_id' => $suser_id
                            );
            
        } else {
            $resultpost =   array(
                                'status' => 201,
                                'message' => 'Already Available! Either this user is Hub or Staff'
                            );
        }
        return $resultpost;
        
    }
    
    // Ghanashyam Parihar Starts 17 Feb 2020 ends
    
    public function edit_staff_member($staff_id, $mobile, $staff_name, $staff_email)
    {
        $query = $this->db->query("SELECT * FROM users WHERE id='$staff_id'");
        $count = $query->num_rows();
        if ($count > 0) {
                $data = array(
                                'name' => $staff_name,
                                'phone' => $mobile,
                                'email' => $staff_email
                            );
                            
                $this->db->where('id',$staff_id);                
                $this->db->update('users',$data);  
                
                $resultpost =   array(
                                'status' => 200,
                                'message' => 'Success'
                            );
            
        } else {
            $resultpost =   array(
                                'status' => 201,
                                'message' => 'Staff Not Available.'
                            );
        }
        return $resultpost;
        
    }
    public function staff_member_list($user_id)
    {
        $query = $this->db->query("SELECT is_active,avatar_id,id,name,phone,email FROM users WHERE staff_hub_user_id='$user_id' AND vendor_id='13' order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                
                $staff_user_id      = $row['id'];
                $mobile             = $row['phone'];
                $staff_name         = $row['name'];
                $staff_email        = $row['email'];
                $avatar_id          = $row['avatar_id'];
                $is_active          = $row['is_active'];
                
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
                    "profile_pic" => $profile_pic,
                    "is_active" => $is_active
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
        
    }
    public function edit_staff_member_v1($staff_id, $mobile, $staff_name, $staff_email, $profile, $media_id)
    {
        $query = $this->db->query("SELECT * FROM users WHERE id='$staff_id'");
        $count = $query->num_rows();
        if ($count > 0) {
                $data = array(
                                'name' => $staff_name,
                                'phone' => $mobile,
                                'email' => $staff_email,
                                'avatar_id' => $media_id,
                            );
                            
                $this->db->where('id',$staff_id);                
                $this->db->update('users',$data);  
                
                $resultpost =   array(
                                'status' => 200,
                                'message' => 'Success'
                            );
            
        } else {
            $resultpost =   array(
                                'status' => 201,
                                'message' => 'Staff Not Available.'
                            );
        }
        return $resultpost;
        
    }
    public function delete_staff_member($staff_user_id)
    {
        $query = $this->db->query("SELECT * FROM users WHERE id='$staff_user_id' AND vendor_id = '13' AND staff_hub_user_id IS NOT NULL");
        $count = $query->num_rows();
        if ($count > 0) {
               if($query->row()->is_active == '1') {
                $data = array(
                                'is_active'=>0
                                );
                $this->db->where('id',$staff_user_id);              
                $this->db->update('users',$data);     
               }
               if($query->row()->is_active == '0') {
                $data = array(
                                'is_active'=>1
                                );
                $this->db->where('id',$staff_user_id);              
                $this->db->update('users',$data);     
               }
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
    public function add_admin_member_v1($user_id, $mobile, $staff_name, $staff_email, $profile, $media_id)
    {
        $query = $this->db->query("SELECT * FROM users WHERE phone='$mobile' AND vendor_id = '13'");
        $count = $query->num_rows();
        if ($count == 0) {
            
                $data = array(
                                'vendor_id' => 13,
                                'name' => $staff_name,
                                'phone' => $mobile,
                                'email' => $staff_email,
                                'avatar_id' => $media_id,
                                'admin_hub_user_id' => $user_id,
                                'password' => md5('12345'),
                                'is_active'=>1
                                );
                                
                $this->db->insert('users',$data);      
                $suser_id = $this->db->insert_id();
                $resultpost =   array(
                                'status' => 200,
                                'message' => 'Success',
                                'user_id' => $suser_id
                                );
            
        } else {
            $resultpost =   array(
                                'status' => 201,
                                'message' => 'Already Available! Either this user is Hub or Admin'
                            );
        }
        return $resultpost;
        
    }
    public function edit_admin_member_v1($staff_id, $mobile, $staff_name, $staff_email, $profile, $media_id)
    {
        $query = $this->db->query("SELECT * FROM users WHERE id='$staff_id'");
        $count = $query->num_rows();
        if ($count > 0) {
                $data = array(
                                'name' => $staff_name,
                                'phone' => $mobile,
                                'email' => $staff_email,
                                'avatar_id' => $media_id,
                            );
                            
                $this->db->where('id',$staff_id);                
                $this->db->update('users',$data);  
                
                $resultpost =   array(
                                'status' => 200,
                                'message' => 'Success'
                            );
            
        } else {
            $resultpost =   array(
                                'status' => 201,
                                'message' => 'Admin Not Available.'
                            );
        }
        return $resultpost;
        
    }
    public function admin_member_list($user_id)
    {
        $query = $this->db->query("SELECT is_active,avatar_id,id,name,phone,email FROM users WHERE admin_hub_user_id='$user_id' AND vendor_id='13' order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                
                $staff_user_id      = $row['id'];
                $mobile             = $row['phone'];
                $staff_name         = $row['name'];
                $staff_email        = $row['email'];
                $avatar_id          = $row['avatar_id'];
                $is_active          = $row['is_active'];
                
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
                    "profile_pic" => $profile_pic,
                    "is_active" => $is_active
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
        
    }
    public function delete_admin_member($staff_user_id)
    {
        $query = $this->db->query("SELECT * FROM users WHERE id='$staff_user_id' AND vendor_id = '13' AND admin_hub_user_id IS NOT NULL");
        $count = $query->num_rows();
        if ($count > 0) {
              if($query->row()->is_active == '1') {
                $data = array(
                                'is_active'=>0
                                );
                $this->db->where('id',$staff_user_id);              
                $this->db->update('users',$data);     
               }
               if($query->row()->is_active == '0') {
                $data = array(
                                'is_active'=>1
                                );
                $this->db->where('id',$staff_user_id);              
                $this->db->update('users',$data);     
               }
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
    
    public function inventory_rack_list($user_id,$staff_user_id)
    {
       $query = $this->db->query("SELECT * FROM inventory_rack WHERE user_id='$user_id'");
       $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id                     = $row['id'];
                $user_id                = $row['user_id'];
                $rack                   = $row['rack'];
                $details                = $row['details'];
               
                $resultpost[] = array(
                                'id'        => $id,
                                'user_id' => $user_id,
                                'rack' => $rack,
                                'details' => $details
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
       $resultpost[] = array(
                                'id'        => "",
                                'user_id' => $user_id,
                                'vendor_id' => "",
                                'wname'     => "No-Stock",
                                'map_location' => "",
                                'lat' => "",
                                'lng' => ""
                                );
        $resultpost[] = array(
                                'id'        => "0",
                                'user_id' => $user_id,
                                'vendor_id' => "",
                                'wname'     => "In-House",
                                'map_location' => "",
                                'lat' => "",
                                'lng' => ""
                                );
                                
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
    
    
     //**************************************************Billing System***************************************************
    /*USING : 2 TIMES*/
    public function product_barcode_scanner_user_order($user_id,$hub_user_id,$barcode)
    {
        
         $check_table_existance = $this->PharmacyPartnerModel->stock_table_existance($user_id);
       
       if($check_table_existance['status'] == true){
            $table_name = $check_table_existance['table_name'];
       
            $query = $this->db->query("SELECT * FROM $table_name WHERE (barcode='$barcode' or product_id='$barcode') AND user_id = '$user_id'");
            
            $count = $query->num_rows();
            if ($count > 0) {
                    $row = $query->row_array();
                    $id = $row['product_id'];
                    
                    $query1 = $this->db->query("SELECT * FROM product WHERE id='$id'");
                    $count1 = $query1->num_rows();
                    if ($count1 > 0) {
                            $row1 = $query1->row_array();
                   
                	       	$sql1="SELECT sum(quantity) as stock_quantity FROM $table_name where user_id='$user_id'  AND (barcode='$barcode' or product_id='$barcode') AND warehouse_id='0'";
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
                           if($stock_quantity > 0) {
                            $user_id                = $user_id;
                            $vendor_id              = "13";
                            $pname                  = $row1['product_name'];
                            $inventory_image        = $row1['image'];
                            $cost_price             = $row['ptr'];
                            $mrp                    = $row['mrp'];
                            $selling_price          = $row['selling_price'];
                            $distributor_id         = "";
                            $manufacture_date       = $row['mgf_date'];
                            $expiry_date            = $row['expiry_date'];
                            $barcode                = $row['barcode'];
                            $ingredients            = "";
                            $category_type_id       = $row1['category'];
                            $sub_category_type_id   = $row1['sub_category'];
                            $product_type_id        = "";
                            $size                   = "";
                           
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
                                $profile_pic = "https://d2c8oti4is0ms3.cloudfront.net/images/product_images/" . $profile_pic;
                            } else {
                                $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/Inventory/product_default.jpg';
                            }
                            
                            $resultpost1[] = array(
                                            'order_id' => "",
                                            'product_name' => $pname,
                                            'product_img' => $profile_pic,
                                            'product_id' => $id,
                                            'product_quantity' => "1",
                                            'product_discount' => "",
                                            'product_price' => $cost_price,
                                            'sub_total' => "",
                                            'product_status' => "",
                                            'product_status_type' => "",
                                            'product_status_value' => "",
                                            'product_unit' => '',
                                            'product_unit_value' => '',
                                            'order_status' => "",
                                            'product_order_id' => "",
                                            'product_order_status' => ""
                                            );
                                            
                                            
                                   $resultpost = array( 'status' => 200,
                                        'message' => 'Product is in stock!!',
                                        'data'=>$resultpost1);         
                                            
                           }
                           else
                           {
                               $resultpost = array( 'status' => 400,
                                        'message' => 'Product is not in stock!!',
                                        'data'=>array());
                           }
                    }
                     else
                           {
                               $resultpost = array( 'status' => 400,
                                        'message' => 'Product Not Found',
                                        'data'=>array());
                           }
                
            } else {
                 $resultpost = array( 'status' => 400,
                                'message' => 'Product is not in stock!!',
                                'data'=>array());
            } 
       } else {
            $resultpost = array( 'status' => 400,
            'message' => 'Product Not Found',
            'data'=>array());
        }    
        return $resultpost;
        
    }
    
    /*using*/
    public function stock_availability_v1($user_id,$hub_user_id,$product_id,$quantity)
    {
        $check_table_existance = $this->PharmacyPartnerModel->stock_table_existance($user_id);
       
       if($check_table_existance['status'] == true){
            $table_name = $check_table_existance['table_name'];
       
            $query = $this->db->query("SELECT * FROM $table_name WHERE product_id='$product_id' AND user_id = '$user_id'");
            
            $count = $query->num_rows();
            if ($count > 0) {
                    $row = $query->row_array();
                    $id                     = $row['product_id'];
                    
                    $query1 = $this->db->query("SELECT id FROM product WHERE id='$id'");
            
                        $count1 = $query1->num_rows();
                        if ($count1 > 0) {
                          
                	       	$sql1="SELECT sum(stock_quantity) as stock_quantity FROM $table_name where user_id='$user_id'  AND product_id='$id' AND warehouse_id='0'";
                            $stock=$this->db->query($sql1)->row();
                                    if(!empty($stock))
            	       	            {
            	       	                    
            	       	                    if($stock->stock_quantity !=NULL)
            	       	                    {
            	       	                        $stock_quantity=$stock->stock_quantity-$quantity;
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
                   if($stock_quantity <= 0) {
                   
                       $resultpost = array( 'status' => 400,
                                'message' => 'Product is not in stock!!');
                   }
                   else
                   {
                       $resultpost = array( 'status' => 200,
                                'message' => 'Stock Available!!');
                   }
                }
                else {
                    $resultpost = array( 'status' => 400,
                                'message' => 'Product Missing!!');
                }
                
            } else {
               $resultpost = array( 'status' => 400,
                                'message' => 'Product is not in stock!!');
            }
       } else {
           $resultpost = array( 'status' => 400,
                            'message' => 'Products are not added yet');
       }    
        return $resultpost;
        
    }
    public function get_pro_name($id)
    {
       $this->db->select('product_name');
       $this->db->from('product');
       $this->db->where('id',$id);
       return $this->db->get()->row();
    }
    
    /*using*/
    public function order_on_call($delivery_by,$service,$delivery_time,$delivery_charge,$listing_id,$listing_name,$staff_user_id,$product_details,$total_quantity,$total_price,$discount,$net_amount,$payment_method,$gst,$customer_name,$customer_phone,$customer_email,$address1,$address2,$city,$state,$pincode,$lat,$lng,$map_location,$address_id,$delivery_charges_by_customer,$doctor_name,$landmark)
    {
        $user_agent = $user_token= "";
        $get_versions = $this->get_versions();
       
        $folder_link =  $get_versions['folder'];        
        $url_link = $get_versions['url'];
        
        $drivingTime = $DeliveryCharges = 0;
        $delivery_time_in_hrs_mins = "";
        $this->load->model('OrderModel');
        $this->load->model('PartnermnoModel');
        $this->load->model('PaymentModel');
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d');
        $current_time = date('H:i:s');
      
        
        $resultpost = array();
        // -------------------- Customer Details --------------------------------
        $customer_address = "";
        if(!empty($address1))
        {
            $customer_address .= $address1;
        }
        if(!empty($address2))
        {
            $customer_address .= ",".$address2;
        }
        if(!empty($city))
        {
            $customer_address .= ",".$city;
        }
        if(!empty($state))
        {
            $customer_address .= ",".$state;
        }
        if(!empty($pincode))
        {
            $customer_address .= "-".$pincode;
        }
        $query1 = $this->db->query("SELECT name,id,email,token,agent,phone FROM users WHERE phone='$customer_phone' AND vendor_id='0'")->row();
        if(!empty($query1))
        {
            $cust_user_id = $query1->id;
            $email = $query1->email;
            $name = $query1->name;
            $user_token = $query1->token;
            $user_agent = $query1->agent;
            $phone = $query1->phone;
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
            $user_data = array(
                'name'         => $customer_name,
                'phone'        => $customer_phone,
                'email'        => $customer_email,
                'map_location' => $map_location,
                'lat'          => $lat,
                'lng'          => $lng,
                'vendor_id'    => 0
            );
            $this->db->insert('users',$user_data);
            $cust_user_id = $this->db->insert_id();
            
            
            /*msg to new user*/
            $message = 'Please download Medicalwale.com';
            $customer_phone = $customer_phone;
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
            
            
            
            $query1 = $this->db->query("SELECT name,id,email FROM users WHERE id='$cust_user_id'")->row();
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
            
            
        }
        
        if(empty($cust_id))
        {
            $query = $this->db->query("SELECT id FROM inventory_customer WHERE cmobile='$customer_phone'")->row();
            if(empty($query))
            {
                $created_date = date('Y-m-d H:i:s');
                $cust_data = array(
                    'vendor_id'     => '13',
                    'user_id'       =>  $listing_id,
                    'cust_user_id'  =>  $cust_user_id, 
                    'cname'         =>  $customer_name,
                    'cmobile'       =>  $customer_phone,
                    'cemail'        =>  $customer_email,
                    'address'       =>  $map_location,
                    'created_date'  =>  $created_date
                );
                $this->db->insert('inventory_customer',$cust_data);
                $cust_id = $this->db->insert_id();   
            }
        }
        // -------------------- END Customer Details --------------------------------
        
        $invoice_no = date('YmdHis');
        
        if($service == "pbioms")
            $order_type = "order";
        else
            $order_type = "prescription";   
            
        $order_status = "Awaiting Customer Confirmation";
       
        $listing_type = 13;
       
            
        $user_id = $cust_user_id;
        
        if(empty($delivery_charge))
        {
            $delivery_charge = 0;
        }
        
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $created_date = date('Y-m-d H:i:s');
        if(empty($address_id))
        {
            $address_data = array(
                'user_id' => $user_id,
                'name' => $name,
                'address1' => $address1,
                'address2' => $address2,
                'landmark' => $landmark ,
                'mobile' => $customer_phone,
                'city' => $city,
                'state' => $state,
                'pincode' => $pincode,
                'date' => $created_at,
                'full_address' => $map_location,
                'lat' => $lat,
                'lng' => $lng,
            );
            $this->db->insert('user_address', $address_data);
            $address_id = $this->db->insert_id();
        }
        
        $chat_id = "user".$user_id;
        
        $order_date = $created_at;
        
        $action_by = "vendor";
        /*if($discount != "")
        $dicount_in_rupee = round(($total_price*$discount)/100);
        else
        $dicount_in_rupee = 0;*/
        $dicount_in_rupee = $discount;
        if($payment_method != "")
        {
            $paymentMethodsSelected = $this->db->query("SELECT * FROM `payment_method` WHERE `id` = '$payment_method'")->row_array(); 
            if(!empty($paymentMethodsSelected))
            {
                $paymentMethosName = $paymentMethodsSelected['payment_method'];
            }
            else
            {
                $paymentMethosName ="";
            }
        }
        else
        {
            $paymentMethosName ="";
        }

        $product_details_new = json_decode($product_details,TRUE);
        if($doctor_name == null){
            $doctor_name = "";
        } 
        
        $user_order = array(
            'invoice_no'    => $invoice_no,
            'order_type'    => $order_type, 
            'delivery_time' => $delivery_time,
            'delivery_charge' => $delivery_charge,
            'delivery_charges_by_customer' => $delivery_charges_by_customer,
            'order_status' => $order_status,
            'listing_id' => $listing_id,
            'listing_name' => $listing_name,
            'listing_type' => $listing_type,
            'user_id' => $user_id,
            'lat' => $lat,
            'lng' => $lng,
            'address_id' => $address_id,
            'name' => $name,
            'mobile' => $customer_phone,
            'pincode' => $pincode,
            'chat_id' => $chat_id,
            'address1' => $address1,
            'address2' => $address2,
            'landmark' => $landmark,
            'city' => $city,
            'state' => $state,
            'actual_cost'   => $total_price,
            'order_total' =>  $total_price,
            'discount' => $dicount_in_rupee,
            'gst'   => $gst,
            'payment_method' => $paymentMethosName,
            'order_date' => $order_date,
            'action_by' => $action_by,
            'order_generate_from' => "pharmacy",
            'order_deliver_by' => $delivery_by,
            'created_by' => $staff_user_id,
            'prescription_doctor' => $doctor_name
        );
        $this->db->insert('user_order', $user_order);
        $order_id = $this->db->insert_id(); 
        
        
        $action_by_status = "Pharmacy";
        $orderStatus = "Order placed by  ". $listing_name ." pharmacy";
        $this->OrderModel->update_status( $invoice_no,$orderStatus, $action_by_status);
        
        /* notification to user*/
        if($user_token != '' && $user_agent != ''){
            $reg_id = $user_token;
            $user_name = $name;
            $agent = $user_agent;
            $user_email = $email;
            $phone = $phone;
            $msg = 'Thanks for placing order with ' . $listing_name;
            $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
            $tag = 'text';
            $key_count = '1';
            $title = 'Order Placed';
            $order_status = '';    
                $notification_array = array(
                       'title' => $title,
                      'msg'  => $msg,
                      'img_url' => $img_url,
                        'tag' => $tag,
                       'order_status' => $order_status,
                      'order_date' => $order_date,
                      'order_id'   => $invoice_no,
                      'post_id'  => "",
                      'listing_id'  => "",
                     'booking_id'  => "",
                      'invoice_no' => $invoice_no,
                      'user_id'  => $user_id,
                      'notification_type'  => 'order',
                      'notification_date'  => date('Y-m-d H:i:s')
                       
            );
            $this->db->insert('All_notification_Mobile', $notification_array);
        }
         
        
        if(!empty($product_details_new['product'])) {
            
            if($service == "pbioms")
            {
                
                
                $sub_total = '0';
                $product_status = 'Available';
                $product_status_type = '';
                $product_status_value = '';
                $product_order_status = 'Available';
                
                $product_details_new = json_decode($product_details,TRUE);
                
                //print_r($product_details_new);
               
                if(!empty($order_id))
        	    {
        	        
            	        foreach($product_details_new['product'] as $details)
            	        {
            	            
            	           
            	            if(!empty($details['product_id']) && !empty($details['product_price']))
            	            {
            	               // print_r($product_details_new['product']); die();
            	                $product_gst = array_key_exists('gst',$details) ? $details['gst'] : 0;
            	                    $product_order = array(
                                        'order_id' => $order_id,
                                        'product_name' => $details['product_name'],
                                        'product_img' => '',
                                        'product_id' => $details['product_id'],
                                        'product_quantity' => $details['quantity'],
                                        'product_discount' => $details['discount'],
                                        'product_price' => $details['product_price'],
                                        'stock_id' => $details['stock_id'],
                                        'gst' => $product_gst,
                                        'barcode' => $details['barcode'],
                                        'batch_no' => $details['batch_no'],
                                        'sub_total' => $sub_total,
                                        'product_status' => $product_status,
                                        'product_status_type' => $product_status_type,
                                        'product_status_value' => $product_status_value,
                                        'product_unit' => '',
                                        'product_unit_value' => '',
                                        'order_status' => $product_order_status
                                    );
            	                    $this->db->insert('user_order_product', $product_order);   
            	                   
            	                   /*create table*/
                                   	$created_new_table = $this->PharmacyPartnerModel->create_new_table($listing_id);
                                    
                            	       if($created_new_table['table_name'] != ""){
                                            $table_name =                     $created_new_table['table_name'];
                                        
            	                   if($delivery_by == "walkin")
            	                   {
            	                       $check = $this->check_pharmacy($listing_id);
		                                if($check != 0)
		                                {
                                		    
                                		    $query1 = $this->db->query("select user_id,order_id,order_type,action_by,delivery_time,cancel_reason,action_by,listing_id,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where invoice_no='$invoice_no'");
                                            $count1 = $query1->num_rows();
                                      
                                            if ($count1 > 0) {
                                                foreach ($query1->result_array() as $row1) {
                                                    $order_id1            = $row1['order_id'];
                                                    $product_query = $this->db->query("select product_quantity,stock_id,barcode,batch_no from user_order_product where order_id='$order_id1'");
                                                    $product_count = $product_query->num_rows();
                                                    if ($product_count > 0) {
                                                        foreach ($product_query->result_array() as $product_row) {
                                                            $stock_id           = $product_row['stock_id'];
                                                            $barcode            = $product_row['barcode'];
                                                            $batch_no           = $product_row['batch_no'];
                                                            $product_qunatity   = $product_row['product_quantity'];
                                                            
                                                            $quantity_query = $this->db->query("select quantity from $table_name where id='$stock_id' AND barcode='$barcode'AND batch_no='$batch_no'");
                                                            if (!empty($quantity_query)) {
                                                                $quantity_query = $this->db->query("UPDATE $table_name SET quantity = quantity - $product_qunatity where id='$stock_id' AND barcode='$barcode' AND batch_no='$batch_no'");
                                                            }
                                                           
                                                        }
                                                    }
                                                 
                                                    $prescription_query = $this->db->query("SELECT * FROM `prescription_order_list` where order_id='$order_id1'");
                                                    $prescription_count = $prescription_query->num_rows();
                                                    if ($prescription_count > 0) {
                                                        foreach ($prescription_query->result_array() as $prescription_row) {
                                                                
                                                                $product_qunatity   = $prescription_row['prescription_quantity'];
                                                                $stock_id           = $prescription_row['stock_id'];
                                                                $gst           = $prescription_row['gst'];
                                                                $barcode            = $prescription_row['barcode'];
                                                                $batch_no           = $prescription_row['batch_no'];
                                                                $quantity_query = $this->db->query("select quantity from $table_name where id='$stock_id' AND barcode='$barcode' AND batch_no='$batch_no'");
                                                                if (!empty($quantity_query)) {
                                                                    $quantity_query = $this->db->query("UPDATE $table_name SET quantity = quantity - $product_qunatity where id='$stock_id' AND barcode='$barcode' AND batch_no='$batch_no'");
                                                                }
                                                            }
                                                    }
                                                }
                                            }
                            		    }
                                   	   
                            	       } 
                                	    /*
                            	       
                	                   $data_add_stoack =array(
                	                        'user_id'           => $listing_id,
                	                        'vendor_type'         => '13',
                	                        'bill_no'           => $order_id,
                	                        'product_id'        => $details['product_id'],
                	                        'quantity'          => -$details['quantity'],
                	                        'po_date'           => date('Y-m-d'),
                	                        'created_at'        => $created_date,
                	                        'created_by'        => $listing_id
                	                        );
                	                     $this->db->insert($table_name,$data_add_stoack);*/
                            	       } 
            	               }         
            	       }
        	        
                   
        	    }
            }
            else
            {
                $product_order_status = 'Available'; 
                $product_details_new = json_decode($product_details,TRUE);
                if(!empty($order_id))
        	    {
        	        
            	        foreach($product_details_new['product'] as $details)
            	        {
            	            if(!empty($details['product_id']) && !empty($details['product_price']))
            	            {
            	                $product_gst = array_key_exists('gst',$details) ? $details['gst'] : 0 ;
            	                    $product_order = array(
                                        'order_id' => $order_id,
                                        'prescription_name' => $details['product_name'],
                                        'prescription_quantity' => $details['quantity'],
                                        'prescription_price' => $details['product_price'],
                                        'prescription_discount' => $details['discount'],
                                        'stock_id' => $details['stock_id'],
                                        'gst' => $product_gst,
                                        'barcode' => $details['barcode'],
                                        'batch_no' => $details['batch_no'],
                                        'prescription_status' => $product_order_status
                                    );
            	                    $this->db->insert('prescription_order_list', $product_order);   
            	                  
                	                
            	               }         
            	       }
        	        
                   
        	    }
            }
        }
         
        if(!empty($order_id))
    	{
    	     
    	   
    	    
            $drivingDistance = "";
            $this->load->model('PartnermnoModel');
            $orderDetails = $this->db->query("SELECT uo.*, ms.lat as pharma_lat, ms.lng as pharma_lng FROM user_order as uo left join medical_stores as ms on (uo.listing_id = ms.user_id) WHERE uo.invoice_no = '$invoice_no'  GROUP by ms.user_id")->row_array();
       
            $customer_lat = $lat;
            $customer_lng = $lng;
            $pharma_lat = $orderDetails['pharma_lat'];
            $pharma_lng = $orderDetails['pharma_lng'];
            $DeliveryCharges = $orderDetails['delivery_charge'];
            $tax = $orderDetails['gst'];
            $chc = $orderDetails['chc'];
            $estimated_delivery_time = $orderDetails['delivery_charge'];
            $discountInRupees = $orderDetails['discount'];
            
            $drivingDistancePharmaToUser = $this->PartnermnoModel->GetDrivingDistance($pharma_lat, $customer_lat, $pharma_lng, $customer_lng );
            
            $drivingDistance = $drivingDistance + $drivingDistancePharmaToUser['distance'];
            if ($drivingDistance > 999) {
                    $distance = $drivingDistance / 1000;
                    $meter = round($distance, 2) . ' km';
            } else {
                    $meter = round($drivingDistance) . ' meters';
            }
    
            $medical_id = $listing_id;
    	    $bill_url ="";
    	    if(!empty($product_details_new['product'])) {       
    	        $sql="SELECT * FROM medical_stores WHERE user_id = '".$medical_id."' || pharmacy_branch_user_id = '".$medical_id."'";
		            $detail = $this->db->query($sql)->row();
    	            //$detail = $this->post_m->fetch_pharmacy_details($this->session->userdata('id'));
        
                    $bill = $this->db->select('*')->from('user_order')->where('order_id',$order_id)->get()->row();
                    
                    if($service == "pbioms")
                    {
                        $bill_detail = $this->db->select('*')->from('user_order_product')->where('order_id',$order_id)->get()->result();
                    }
                    else
                    {
                        $bill_detail = $this->db->select('*')->from('prescription_order_list')->where('order_id',$order_id)->get()->result();
                    }
                    
                    $customer = $this->db->select('*')->from('users')->where('id',$user_id)->get()->row();
                    
                   
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
        										<p>'.$bill->invoice_no.'</p>
        										<h5>Contact</h5>
        										<p>Phone : '.$detail->mobile.'</p>
        </td>
        
        </tr>
        
        <tr>
        <td colspan="8">
        <hr>
        <h3 class="bot">Patient Details</h3>
        	<h5 class="bottom">'.$customer->name.' <span style="font-size: 13px;"> </span></h5>
        	<h5 class="bottom">Address : <span style="font-size: 13px;">'.$customer->landmark.', </span></h5>
        	<h5 class="bottom">Mobile : <span style="font-size: 13px;">'.$customer->phone.'</span>&nbsp;&nbsp;&nbsp; Email : <span style="font-size: 13px;">'.$customer->email.'</span></h5>
        
            
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
                                $q_sum = 0;
                                if($service == "pbioms")
                                {
                            		foreach($bill_detail as $dat) {
                            		
        								$t_price = 	$dat->product_price * $dat->product_quantity;	  
        									$sums +=$t_price ;
        									$q_sum += $dat->product_quantity;
        								$html .= '<tr id="row_'.$i.'"> 
                                						    <td>'.$i.'</td>
                                					<td>';
        
                                        $ap= $this->get_pro_name($dat->product_id); 
        
                                		$html .= $ap->product_name.'	</td>            <td>'.$dat->product_quantity.'</td> 
                                				<td>'.$dat->product_price.'</td>
                                						        <td>'.$t_price.'</td>
                                						      	      
              						        </tr> ';
        							}
                                }
                                else
                                {
                                    foreach($bill_detail as $dat) {
                            		
        								$t_price = 	$dat->prescription_price * $dat->prescription_quantity;	  
        									$sums +=$t_price ;
        									$q_sum += $dat->product_quantity;
        								$html .= '<tr id="row_'.$i.'"> 
                                						    <td>'.$i.'</td>
                                					<td>';
        
                                   
                                        $ap= $dat->prescription_name; 
                                		$html .= $ap.'	</td>            <td>'.$dat->prescription_quantity.'</td> 
                                				<td>'.$dat->prescription_price.'</td>
                                						        <td>'.$t_price.'</td>
                                						      	      
              						        </tr> ';
        							}
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
                                						    <td>'.$q_sum.'</td>
                                						    <td style="   font-weight: bold;
            font-size: 16px !important;" colspan="2">Total Price</td> 
                                						    <td>'.$sums.'</td>
                                						   
                                				            </tr> 
                                				             <tr> 
                                						    <td style="   font-weight: bold;
            font-size: 16px !important;" colspan="2">Discount Amount</td> 
                                						    <td>'.$bill->discount.' %</td>
                                						    <td style="    font-weight: bold;
            font-size: 16px !important;" colspan="2">Tax</td> 
                                						    <td>'.$bill->gst.' %</td>
                                						   
                                				            </tr> 
                            						   
                            						   
                            						    </tbody> 
                            						 </table> 
                            						 	<table cellpadding="5"> 
                            						   
                            						    <tbody> 
                            						   
                                						    <tr> 
                                						   
                                						    <td style="font-weight: bold;
            font-size: 16px !important;" colspan="5">Net Amount</td> 
                                						    <td>Rs. '.$bill->order_total.' /-</td>
                                						   
                                				            </tr> 
                                				           
                            						   
                            						    </tbody> 
                            						 </table> 
        ';
          
        

        // render the view into HTML
        
                $pdf->WriteHTML($html); 
                $output = 'invoice' . date('Ymdhis') . '.pdf';
                
                
                
                $pdfFilePath = $folder_link."bill/".$output;
        
                $pdf->Output($pdfFilePath, 'F'); 
               // $pdf->Output($output, 'F');    
                $data_add_pdf =array(
        	                        'user_id'           => $listing_id,
        	                        'customer_user_id'  => $cust_user_id,
        	                        'bill_link'           => $output,
        	                        'created_at'        => date('Y-m-d H:i:s'),
        	                        'created_by'        => $listing_id
        	                        );
        	                     $this->db->insert('inventory_customer_pdf',$data_add_pdf);
        	                     
        	   $bill_url = $url_link."bill/".$output;                 
    	    }
    	    
    	   /*if walkin THEN LEDGER ENTRY  and order status DELIVERED*/ 
    	  
    	    if($delivery_by == "walkin"){
        	   
        	    $finalCost  = $this->PaymentModel->get_invoice_costing($invoice_no);
        	   
        	    if($finalCost['order_found'] == 1){
        	        $costing = $finalCost['data'];
        	        $grand_total_customer = $costing['grand_total_customer'];
        	    } else {
        	        $grand_total_customer = 0;
        	    }
        	       // $order_id
        	        $update_user_order = $this->db->query("UPDATE user_order SET order_status = 'Order Delivered' WHERE order_id = '$order_id'");
        	        
        	            $action_by_status = "Customer";
                         $orderStatus = "Order picked up by  customer";
                        $this->OrderModel->update_status( $invoice_no,$orderStatus, $action_by_status);
        	       
        	    /*common*/
        	        $user_id = $user_id;
                    $invoice_no = $invoice_no;
                    $user_id_type = 0;
                    $listing_id = $listing_id;
                    $listing_type = 13;
                    $vendor_comments = "";
                    $user_comments = "";
                    $trans_status = 1;
                    $transaction_date = "";
                    $transaction_id = "";
                    $order_type = 1;
                    
                    if($paymentMethosName != ''){ 
                        $usingPaymenMethod  = ' using '.$paymentMethosName; 
                    } else {
                        $usingPaymenMethod = "";
                    } 
                    
                    
                    /*order picked up*/
                    $mw_comments = "Order picked up by customer";
                    $credit = $grand_total_customer;
                    $debit = 0;
                    $transaction_of = 1; // 1: entry for package 2: amount
                    $payment_method_ledger = $payment_method;
                    $vendor_id=$listing_type;
                    $array_data=array();
                    $res = $this->LedgerModel->create_ledger($user_id, $invoice_no, $user_id_type, $listing_id, $listing_type, $credit, $debit, $payment_method_ledger, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status,$transaction_date,$vendor_id,$array_data);
                   
                    /*amount paid*/
                    $mw_comments = "Amount paid by customer ".$usingPaymenMethod;  
                    $credit = 0;
                    $debit = $grand_total_customer;
                    $transaction_of = 2; // 1: entry for package 2: amount
                    $payment_method_ledger = $payment_method;
                   
                   $res = $this->LedgerModel->create_ledger($user_id, $invoice_no, $user_id_type, $listing_id, $listing_type, $credit, $debit, $payment_method_ledger, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status,$transaction_date,$vendor_id,$array_data);
                   
                   /*pending : notification to user : order picked up bu customer*/
                   
                   if($user_token != '' && $user_agent != ''){
                        $reg_id = $user_token;
                        $user_name = $name;
                        $agent = $user_agent;
                        $user_email = $email;
                        $phone = $phone;
                        $msg = 'Order pickedup from ' . $listing_name;
                        $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                        $tag = 'text';
                        $key_count = '1';
                        $title = 'Order pickedup';
                        $order_status = '';    
                            $notification_array = array(
                                   'title' => $title,
                                  'msg'  => $msg,
                                  'img_url' => $img_url,
                                    'tag' => $tag,
                                   'order_status' => $order_status,
                                  'order_date' => $order_date,
                                  'order_id'   => $invoice_no,
                                  'post_id'  => "",
                                  'listing_id'  => "",
                                 'booking_id'  => "",
                                  'invoice_no' => $invoice_no,
                                  'user_id'  => $user_id,
                                  'notification_type'  => 'order',
                                  'notification_date'  => date('Y-m-d H:i:s')
                                   
                        );
                        $this->db->insert('All_notification_Mobile', $notification_array);
                    }
        	        
    	        
    	       
    	    }
            if($delivery_by == "mno")
        	   {
        	     $only_view = 0;
        	       $resultpost = $this->estimate_cost_mno($listing_id,$invoice_no,$bill_url,$only_view);
        	        // call delivery charge api
                    $offer_id = 0;
                    $delivery_charges = $resultpost['delivery_charge'];
                    $addDeliveryCharges  = $this->PaymentModel->add_delivery_charges($delivery_charges, $invoice_no, $delivery_charges_by_customer, $offer_id);
                    
                    
                   /* if($addDeliveryCharges['status'] == 1){
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
                    
                    $resultpost['delivery_charges_by_customer'] = $delivery_charges_by_customer;
                    $resultpost['grand_total_customer'] =  $resultpost['sub_total'] - $resultpost['discount'] + $delivery_charges_by_customer;
     */
        
                    /*if($order_id > 0){
                        $this->db->query("UPDATE user_order SET `delivery_charges_by_customer`  = '$delivery_charges_by_customer_amt' , `delivery_charges_by_vendor` =  '$delivery_charges_by_vendor_amt' WHERE `order_id` = '$order_id'");    
                    }*/
                    
        	   }
        	   else
        	   {
        	        $data = array(
                                'bill_link' => $bill_url,
                                'estimated_delivery_time' => strval($estimated_delivery_time),
                                'estimated_delivery_time_sec' => "",
                                'approx_distance_meter' => strval($drivingDistance),
                                'sub_total' => strval($total_price),
                                'gst' => strval($tax),
                                'cash_handling_charges' => strval($chc),
                                'discount' => strval($discountInRupees),
                                'delivery_charge' => strval($DeliveryCharges),
                                'delivery_charges_by_customer' => strval($DeliveryCharges),
                                'grand_total_customer' => strval($net_amount),
                                'grand_total' => strval($net_amount),
                                'invoice_no' => strval($invoice_no),
                                'user_id' => strval($cust_user_id),
                                'lat' => strval($customer_lat),
                                'lng' => strval($customer_lng),
                                'order_id' => intval($order_id)

                                );
                    $data1 = $this->PaymentModel->get_invoice_costing($invoice_no);
                    $data2 = $data1['data'];
      
                    $resultpost = array_merge($data,$data2);            
        	   } 
        	   
        	   
    	}
                    return  $resultpost;          
        
    }
    public function check_pharmacy($id){
            $this->db->select('pbioms');
            $this->db->from('medical_stores');
            $this->db->where('user_id',$id);
            $query = $this->db->get()->row();
            
            if(empty($query)){
                $this->db->select('pbioms');
                $this->db->from('medical_stores');
                $this->db->where('pharmacy_branch_user_id',$id);
                $query1 = $this->db->get()->row();
                // echo $this->db->last_query();
                // print_r($query1);die;
                if($query1->pbioms == 0){
                    return '0';
                }
                else{
                    return '1';
                }
            }else{
                if($query->pbioms == 0){
                    return '0';
                }
                else{
                    return '1';
                }
            }
        }
    public function upload_product_csv($user_id,$staff_user_id,$file)
    {
        if(!empty($file))
        {
            $handle = fopen($file, "r");
            $c   = 0;
            $row = 0;
            while (($filesop = fgetcsv($handle, 1000, ",")) !== false) {
                //print_r($filesop);
                $row; 
                if($row > 0){
                    $category                   = $filesop[0];
                    $sub_category               = $filesop[1];
                    $product_name               = $filesop[2];
                    $is_prescription_needed     = $filesop[3];
                    $product_price              = $filesop[4]; 
                    $image                      = $filesop[5]; 
                    $product_description        = $filesop[6]; 
                    $product_type               = $filesop[7]; 
                    $pack                       = $filesop[8]; 
                    $pack_unit                  = $filesop[9]; 
                    $expiry                     = $filesop[10]; 
                    $hsn                        = $filesop[11]; 
                    $gst                        = $filesop[12]; 
                    $company                    = $filesop[13]; 
                    $distributor_id             = $filesop[14]; 
                    $min_stock                  = $filesop[15]; 
                    $max_stock                  = $filesop[16]; 
                    $rack                       = $filesop[17]; 
                    $array= array('category'                => $category,
                                  'sub_category'            => $sub_category,
                                  'product_name'            => $product_name,
                                  'is_prescription_needed'  => $is_prescription_needed,
                                  'product_price'           => $product_price,
                                  'image'                   => $image,
                                  'product_description'     => $product_description,
                                  'product_type'            => $product_type,
                                  'pack'                    => $pack,
                                  'pack_unit'               => $pack_unit,
                                  'expiry'                  => $expiry,
                                  'hsn'                     => $hsn,
                                  'gst'                     => $gst,
                                  'company'                 => $company,
                                  'distributor_id'          => $distributor_id,
                                  'created_at'              => date('Y-m-d'),
                                  'created_by'              => $user_id
                                  );
                    $this->db->insert('product',$array);              
                    $product_id = $this->db->insert_id();
                    $array1 = array('product_id'              => $product_id,
                                    'min_stock'             => $min_stock,
                                  'max_stock'               => $max_stock,
                                  'rack'                    => $rack,
                                  'created_at'              => date('Y-m-d'),
                                  'created_by'              => $user_id
                                  );
                    $this->db->insert('inventory_product_details',$array1);               
                      $c++;
                }
                $row++;
                continue;
            }
        }
        else
        {
            
        }
    }
    
    /*using*/
    public function product_inventory_bill($user_id,$hub_user_id,$date,$invoice_no,$product_details,$total_quantity,$total_price,$discount,$tax,$net_amount,$payment_method,$name,$customer_phone,$email,$customer_address,$doctor_name,$bhc_no)
    {
        $get_versions = $this->get_versions();
       
        $folder_link =  $get_versions['folder'];        
        $url_link = $get_versions['url'];
        
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
    	                   
    	                   /*create table*/
    	                       $created_new_table = $this->PharmacyPartnerModel->create_new_table($user_id);
                                    
                    	    if($created_new_table['table_name'] != ""){
                                $table_name =                     $created_new_table['table_name'];
                               
            	                    $data_add_stoack =array(
            	                        'user_id'           => $user_id,
            	                        'vendor_type'         => '13',
            	                        'bill_no'           => $type,
            	                        'product_id'        => $details['product_id'],
            	                        'quantity'          => -$details['quantity'],
            	                        'po_date'           => date('Y-m-d'),
            	                        'created_at'        => $created_date,
            	                        'created_by'        => $user_id
            	                        );
            	                    // $this->db->insert($table_name,$data_add_stoack);
                            }
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
                $pdfFilePath = $folder_link."bill/".$output;
        
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
                                'bill_link' => $url_link."bill/".$output
                                );  
                return  $resultpost;               
               
    	}
    	else
    	{
    	    return  array();  
    	}
                   
    }
    
    public function generate_invoice($order_id,$invoice_no)
    {
        $this->load->model('OrderModel');
        $this->load->model('PartnermnoModel');
        $this->load->model('PaymentModel');
         $resultpost = array();
         
        $get_versions = $this->get_versions();
       
        $folder_link =  $get_versions['folder'];        
        $url_link = $get_versions['url'];
         
        
        if(!empty($order_id))
    	{
    	   
    	     
            $drivingDistance = "";
            $this->load->model('PartnermnoModel');
            $orderDetails = $this->db->query("SELECT uo.*, ms.lat as pharma_lat, ms.lng as pharma_lng FROM user_order as uo left join medical_stores as ms on (uo.listing_id = ms.user_id) WHERE uo.invoice_no = '$invoice_no'  GROUP by ms.user_id")->row_array();
           
            $orderDetails1 = $this->db->query("SELECT uo.* FROM user_order as uo  WHERE uo.order_id = '$order_id'")->row_array();
            
             $cust_user_id = $orderDetails1['user_id'];
             $order_type = $orderDetails1['order_type'];
            
            $customer = $this->db->select('*')->from('users')->where('id',$cust_user_id)->get()->row();
            $customer_lat = $customer->lat;
            $customer_lng = $customer->lng;
            $listing_id = $orderDetails1['listing_id'];
            $pharma_lat = $orderDetails['pharma_lat'];
            $pharma_lng = $orderDetails['pharma_lng'];
            
            $DeliveryCharges = $orderDetails['delivery_charge'];
            $tax = $orderDetails['gst'];
            $chc = $orderDetails['chc'];
            $estimated_delivery_time = $orderDetails['delivery_charge'];
            $discountInRupees = $orderDetails['discount'];
            
            $drivingDistancePharmaToUser = $this->PartnermnoModel->GetDrivingDistance($pharma_lat, $customer_lat, $pharma_lng, $customer_lng );
            
            $drivingDistance = $drivingDistance + $drivingDistancePharmaToUser['distance'];
            if ($drivingDistance > 999) {
                    $distance = $drivingDistance / 1000;
                    $meter = round($distance, 2) . ' km';
            } else {
                    $meter = round($drivingDistance) . ' meters';
            }
    
            $medical_id = $listing_id;
            $ex = explode(',',$listing_id);
    	    $bill_url ="";
    	    if(count($ex)==1) {
    	        
    	        $sql="SELECT * FROM medical_stores WHERE user_id = '".$medical_id."' || pharmacy_branch_user_id = '".$medical_id."'";
		            $detail = $this->db->query($sql)->row();
    	            //$detail = $this->post_m->fetch_pharmacy_details($this->session->userdata('id'));
        
                    $bill = $this->db->select('*')->from('user_order')->where('order_id',$order_id)->get()->row();
                    
                    if($order_type == "order" || $order_type == "")
                    {
                        $bill_detail = $this->db->select('*')->from('user_order_product')->where('order_id',$order_id)->get()->result();
                    }
                    else
                    {
                        $bill_detail = $this->db->select('*')->from('prescription_order_list')->where('order_id',$order_id)->get()->result();
                    }
                    
                    //print_r($bill_detail);
                    
                   
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
        										<p>'.$bill->invoice_no.'</p>
        										<h5>Contact</h5>
        										<p>Phone : '.$detail->mobile.'</p>
        </td>
        
        </tr>
        
        <tr>
        <td colspan="8">
        <hr>
        <h3 class="bot">Patient Details</h3>
        	<h5 class="bottom">'.$customer->name.' <span style="font-size: 13px;"> </span></h5>
        	<h5 class="bottom">Address : <span style="font-size: 13px;">'.$customer->landmark.', </span></h5>
        	<h5 class="bottom">Mobile : <span style="font-size: 13px;">'.$customer->phone.'</span>&nbsp;&nbsp;&nbsp; Email : <span style="font-size: 13px;">'.$customer->email.'</span></h5>
        
            
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
                                $q_sum = 0;
                                if($service == "pbioms")
                                {
                            		foreach($bill_detail as $dat) {
                            		
        								$t_price = 	$dat->product_price * $dat->product_quantity;	  
        									$sums +=$t_price ;
        									$q_sum += $dat->product_quantity;
        								$html .= '<tr id="row_'.$i.'"> 
                                						    <td>'.$i.'</td>
                                					<td>';
        
                                        $ap= $this->get_pro_name($dat->product_id); 
        
                                		$html .= $ap->product_name.'	</td>            <td>'.$dat->product_quantity.'</td> 
                                				<td>'.$dat->product_price.'</td>
                                						        <td>'.$t_price.'</td>
                                						      	      
              						        </tr> ';
        							}
                                }
                                else
                                {
                                    foreach($bill_detail as $dat) {
                            		
        								$t_price = 	$dat->prescription_price * $dat->prescription_quantity;	  
        									$sums +=$t_price ;
        									$q_sum += $dat->product_quantity;
        								$html .= '<tr id="row_'.$i.'"> 
                                						    <td>'.$i.'</td>
                                					<td>';
        
                                   
                                        $ap= $dat->prescription_name; 
                                		$html .= $ap.'	</td>            <td>'.$dat->prescription_quantity.'</td> 
                                				<td>'.$dat->prescription_price.'</td>
                                						        <td>'.$t_price.'</td>
                                						      	      
              						        </tr> ';
        							}
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
                                						    <td>'.$q_sum.'</td>
                                						    <td style="   font-weight: bold;
            font-size: 16px !important;" colspan="2">Total Price</td> 
                                						    <td>'.$sums.'</td>
                                						   
                                				            </tr> 
                                				             <tr> 
                                						    <td style="   font-weight: bold;
            font-size: 16px !important;" colspan="2">Discount Amount</td> 
                                						    <td>'.$bill->discount.' %</td>
                                						    <td style="    font-weight: bold;
            font-size: 16px !important;" colspan="2">Tax</td> 
                                						    <td>'.$bill->gst.' %</td>
                                						   
                                				            </tr> 
                            						   
                            						   
                            						    </tbody> 
                            						 </table> 
                            						 	<table cellpadding="5"> 
                            						   
                            						    <tbody> 
                            						   
                                						    <tr> 
                                						   
                                						    <td style="font-weight: bold;
            font-size: 16px !important;" colspan="5">Net Amount</td> 
                                						    <td>Rs. '.$bill->order_total.' /-</td>
                                						   
                                				            </tr> 
                                				           
                            						   
                            						    </tbody> 
                            						 </table> 
        ';
          
        

        // render the view into HTML
       
                $pdf->WriteHTML($html); 
                $output = 'invoice' . date('Ymdhis') . '.pdf';
                $pdfFilePath = $folder_link."bill/".$output;
        
                $pdf->Output($pdfFilePath, 'F'); 
               // $pdf->Output($output, 'F');    
                $data_add_pdf =array(
        	                        'user_id'           => $listing_id,
        	                        'customer_user_id'  => $cust_user_id,
        	                        'bill_link'           => $output,
        	                        'created_at'        => date('Y-m-d H:i:s'),
        	                        'created_by'        => $listing_id
        	                        );
        	                     $this->db->insert('inventory_customer_pdf',$data_add_pdf);
        	   $bill_url = $url_link."bill/".$output;                 
    	    
    	   
        	        $data = array(
                                'bill_link' => $bill_url,
                                'estimated_delivery_time' => strval($estimated_delivery_time),
                                'estimated_delivery_time_sec' => "",
                                'approx_distance_meter' => strval($drivingDistance),
                                'sub_total' => strval($total_price),
                                'gst' => strval($tax),
                                'cash_handling_charges' => strval($chc),
                                'discount' => strval($discountInRupees),
                                'delivery_charge' => strval($DeliveryCharges),
                                'delivery_charges_by_customer' => strval($DeliveryCharges),
                                'grand_total_customer' => strval($net_amount),
                                'grand_total' => strval($net_amount),
                                'invoice_no' => strval($invoice_no),
                                'user_id' => strval($cust_user_id),
                                'lat' => strval($customer_lat),
                                'lng' => strval($customer_lng),
                                'order_id' => intval($order_id)

                                );
                    $data1 = $this->PaymentModel->get_invoice_costing($invoice_no);
                    $data2 = $data1['data'];
      
                    $resultpost = array_merge($data,$data2);   
    	    }
        	   
    	}
        return  $resultpost;          
        
    }
    public function generate_invoice_v1($order_id,$invoice_no)
    {
        $get_versions = $this->get_versions();
       
        $folder_link =  $get_versions['folder'];        
        $url_link = $get_versions['url'];
        
         $this->load->model('OrderModel');
        $this->load->model('PartnermnoModel');
        $this->load->model('PaymentModel');
         $resultpost = array();
        if(!empty($order_id))
    	{
    	   
    	     
            $drivingDistance = "";
            $this->load->model('PartnermnoModel');
            $orderDetails = $this->db->query("SELECT uo.*, ms.lat as pharma_lat, ms.lng as pharma_lng FROM user_order as uo left join medical_stores as ms on (uo.listing_id = ms.user_id) WHERE uo.invoice_no = '$invoice_no'  GROUP by ms.user_id")->row_array();
           
            $orderDetails1 = $this->db->query("SELECT uo.* FROM user_order as uo  WHERE uo.order_id = '$order_id'")->row_array();
            
             $cust_user_id = $orderDetails1['user_id'];
             $order_type = $orderDetails1['order_type'];
            
            $customer = $this->db->select('*')->from('users')->where('id',$cust_user_id)->get()->row();
            $customer_lat = $customer->lat;
            $customer_lng = $customer->lng;
            $listing_id = $orderDetails1['listing_id'];
            $pharma_lat = $orderDetails['pharma_lat'];
            $pharma_lng = $orderDetails['pharma_lng'];
            
            $DeliveryCharges = $orderDetails['delivery_charge'];
            $tax = $orderDetails['gst'];
            $chc = $orderDetails['chc'];
            $estimated_delivery_time = $orderDetails['delivery_charge'];
            $discountInRupees = $orderDetails['discount'];
            
            $drivingDistancePharmaToUser = $this->PartnermnoModel->GetDrivingDistance($pharma_lat, $customer_lat, $pharma_lng, $customer_lng );
            
            $drivingDistance = $drivingDistance + $drivingDistancePharmaToUser['distance'];
            if ($drivingDistance > 999) {
                    $distance = $drivingDistance / 1000;
                    $meter = round($distance, 2) . ' km';
            } else {
                    $meter = round($drivingDistance) . ' meters';
            }
    
            $medical_id = $listing_id;
            $ex = explode(',',$listing_id);
    	    $bill_url ="";
    	    if(count($ex)==1) {
    	       // $sql="SELECT ms.* , m.source as logo FROM medical_stores as ms left join users as u on (u.id = '$medical_id') left join media as m on (u.avatar_id = m.id) WHERE ms.user_id = '$medical_id' || ms.pharmacy_branch_user_id = '$medical_id' ";
    	        $sql="SELECT * FROM medical_stores WHERE user_id = '".$medical_id."' || pharmacy_branch_user_id = '".$medical_id."'";
    	            $detail = $this->db->query($sql)->row();
    	            //$detail = $this->post_m->fetch_pharmacy_details($this->session->userdata('id'));
        
                    $bill = $this->db->select('*')->from('user_order')->where('order_id',$order_id)->get()->row();
                    $bill_detail = array();
                    $bill_detail1 = array();
                    if($order_type == "order" || $order_type == "")
                    {
                        $bill_detail1 = $this->db->select('l.*')->from('user_order_product as l')->join('user_order as u','u.order_id=l.order_id','left')->where('l.order_id',$order_id)->where('l.product_status','Available')->get()->result();
                        // echo $this->db->last_query();
                    }
                    else
                    {
                        $bill_detail = $this->db->select('l.*')->from('prescription_order_list as l')->join('user_order as u','u.order_id=l.order_id','left')->where('l.order_id',$order_id)->where('l.prescription_status','Available')->get()->result();
                        //echo $this->db->last_query();
                    }
                    
                    //print_r($bill_detail);
                   if(empty($detail->logo) )
                   {
                       $image=$detail->medical_name;
                       $store_logo = "";
                   }
                   else
                   {
                       $image = '<img alt="" src="https://d2c8oti4is0ms3.cloudfront.net/images/pharmacy_images/'.$detail->logo.'" style="max-height: 48px;height: 48px;">';
                       $store_logo = 'https://d2c8oti4is0ms3.cloudfront.net/images/pharmacy_images/'.$detail->logo;
                      /* $image = '<img alt="" src="https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'.$detail->logo.'" style="max-height: 48px;height: 48px;">';
                       $store_logo = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'.$detail->logo;*/
                   }
                   
                   $this->load->library("sma");
                //   $smaBarcode = $this->sma->load();
                   include('vendor/autoload.php');
                   $barcode_image = $this->sma->barcode($text = $invoice_no, $bcs = 'code128', $height = 40, $stext = 0, $get_be = false, $re = false);
                //   $barcode_image= $this->sma->barcode($text = $invoice_no, $bcs = 'code128', $height = 74, $stext = 0, $get_be = false, $re = false);
    	               
    	   
    	$this->load->library('dpdf');
        $pdf = $this->dpdf->load();
        // $pdf->AddPage('A4');
        $pdf->addPage('L');
        ini_set('memory_limit', '256M');
    //   print_r($barcode_image); die();
         $data['detail'] = $detail;
    	   $data['bill'] = $bill;
    	   $data['customer'] = $customer;
    	   $data['service'] = $service;
    	   $data['bill_detail1'] =$bill_detail1;
    	   $data['bill_detail'] =$bill_detail;
    	   $data['logo_url'] =$store_logo;
    	   $data['barcode'] =$barcode_image;
    	   $get_invoice_costing = $this->PaymentModel->get_invoice_costing($invoice_no);
    	   if(array_key_exists('data',$get_invoice_costing)){
    	       $data['final_calculation'] = $get_invoice_costing['data'];
    	   } else {
    	       $data['final_calculation'] = array();
    	   }
    	   $html= $this->load->view('generate_invoice', $data, true);
    	  
       
        // render the view into HTML
        
                $pdf->WriteHTML($html); 
                $output = 'invoice' . date('Ymdhis') . '.pdf';
                $pdfFilePath = $folder_link."bill/".$output;
        
                $pdf->Output($pdfFilePath, 'F'); 
               // $pdf->Output($output, 'F');    
                $data_add_pdf =array(
        	                        'user_id'           => $listing_id,
        	                        'customer_user_id'  => $cust_user_id,
        	                        'bill_link'           => $output,
        	                        'created_at'        => date('Y-m-d H:i:s'),
        	                        'created_by'        => $listing_id
        	                        );
        	                     $this->db->insert('inventory_customer_pdf',$data_add_pdf);
        	   $bill_url = $url_link."bill/".$output;                 
    	        
    	               $this->db->query("UPDATE `user_order` SET `bill_url`='$bill_url' WHERE `invoice_no` = '$invoice_no'"); 
    	   
        	        $data = array(
                                'bill_link' => $bill_url,
                                'estimated_delivery_time' => strval($estimated_delivery_time),
                                'estimated_delivery_time_sec' => "",
                                'approx_distance_meter' => strval($drivingDistance),
                                'sub_total' => strval($total_price),
                                'gst' => strval($tax),
                                'cash_handling_charges' => strval($chc),
                                'discount' => strval($discountInRupees),
                                'delivery_charge' => strval($DeliveryCharges),
                                'delivery_charges_by_customer' => strval($DeliveryCharges),
                                'grand_total_customer' => strval($net_amount),
                                'grand_total' => strval($net_amount),
                                'invoice_no' => strval($invoice_no),
                                'user_id' => strval($cust_user_id),
                                'lat' => strval($customer_lat),
                                'lng' => strval($customer_lng),
                                'order_id' => intval($order_id)

                                );
                    $data1 = $this->PaymentModel->get_invoice_costing($invoice_no);
                    $data2 = $data1['data'];
      
                    $resultpost = array_merge($data,$data2);   
    	    }
        	   
    	}
        return  $resultpost;          
        
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
        
        $discount_amount = $this->db->query("select total_amount,discount from inventory_bill where user_id='$user_id' AND bill_date>='$from_date' AND bill_date<='$to_date'");
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
            
                           $tot_units = $this->db->query("select sum(total_qty) as units from inventory_bill where user_id='$user_id' AND bill_date='$tomorrow'");
                    $total_units1 = $tot_units->row()->units;
                    if($total_units1 == null)
                    {
                        $total_units1 = 0;
                    }
                    
                    $net_amount = $this->db->query("select sum(total_amount) as units from inventory_bill where user_id='$user_id' AND bill_date='$tomorrow'");
                    $total_amount1 = $net_amount->row()->units;
                    if($total_amount1 == null)
                    {
                        $total_amount1 = 0;
                    }
                    
                    $max_amount = $this->db->query("select max(total_amount) as units from inventory_bill where user_id='$user_id' AND bill_date='$tomorrow'");
                    $total_max_amount1 = $max_amount->row()->units;
                    if($total_max_amount1 == null)
                    {
                        $total_max_amount1 = 0;
                    }
                    
                    $min_amount = $this->db->query("select min(total_amount) as units from inventory_bill where user_id='$user_id' AND bill_date='$tomorrow'");
                    $total_min_amount1 = $min_amount->row()->units;
                    if($total_min_amount1 == null)
                    {
                        $total_min_amount1 = 0;
                    }
                    
                    $avg_amount = $this->db->query("select avg(total_amount) as units from inventory_bill where user_id='$user_id' AND bill_date='$tomorrow'");
                    $total_avg_amount1 = $avg_amount->row()->units;
                     if($total_avg_amount1 == null)
                    {
                        $total_avg_amount1 = 0;
                    }
                   
                    
                    $gst_amount = $this->db->query("select total_amount,tax,discount from inventory_bill where user_id='$user_id' AND bill_date='$tomorrow'");
                    $gst_amount11 = $gst_amount->result();
                    
                    $sum_gst=0;
                    foreach($gst_amount11 as $gst)
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
                    
                    $discount_amount = $this->db->query("select total_amount,discount from inventory_bill where user_id='$user_id' AND bill_date='$tomorrow'");
                    $discount_amount11 = $discount_amount->result();
                    
                    $sum_discount=0;
                    foreach($discount_amount11 as $discount)
                    {
                        if($discount->total_amount !=0 && $discount->discount != 0)
                        {
                            $disc = $discount->discount/100;
                            $amount = $discount->total_amount*$disc;
                            $sum_discount += $amount;
                        }
                    }       
                    
            
            $day = date('l',strtotime($tomorrow));
            $daywise[]=array('day' => $day,
                             'gross_amount' => $gross_amount_day1,
                             'total_units'  => $total_units1,
                            'net_amount'  => $total_amount1,
                            'max_bill'    => $total_max_amount1,
                            'min_bill' => $total_min_amount1,
                            'avg_bill'  => $total_avg_amount1,
                            'total_gst'   => $sum_gst,
                            'total_disc' => $sum_discount,);
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
    public function inventory_dashboard_v1($user_id,$hub_user_id,$from_date,$to_date)
    {
        if(empty($from_date) && empty($to_date))
        {
            $from_date = date('Y-m-d',strtotime('last Monday'));
            $to_date = date('Y-m-d',strtotime('next Sunday'));
        }
        
        $new_from_date = date('Y-m-d',strtotime($from_date . "-1 days"));
        $new_to_date = date('Y-m-d',strtotime($to_date . "+1 days"));
        
        $total_orders = $this->db->query("select count(*) as tot from user_order where listing_id='$user_id' AND order_date between '$new_from_date' AND  '$new_to_date'");
        $total_orders1 = $total_orders->row()->tot;
        
        $gross_amount1  = $this->db->query("select * from user_order where listing_id='$user_id' AND order_date between '$new_from_date' AND  '$new_to_date' AND order_status != 'Order Cancelled'");
        $gross_amount11 = $gross_amount1->result();
        
        $final_amount = 0;
        $final_amount1 = 0;
        $final_discount = 0;
        $final_discount1 = 0;
        $final_quantity = 0;
        $final_quantity1 = 0;
        $final_gst = 0;
        $final_gst1 = 0;
        $final_gross = 0;
        $final_gross1 = 0;
        
        $tot_array = array();
        foreach($gross_amount11 as $det)
        {
            $order_id = $det->order_id;
            $order_type = $det->order_type;
            $delivery_charge = $det->delivery_charge;
            $gst = $det->gst;
            $chc = $det->chc;
           
            if($order_type == "order")
            {
                $gross_amount = $this->db->query("select product_discount,product_quantity,product_price from user_order_product where order_id='$order_id'");
                $gross_amount1 = $gross_amount->row();
                if(!empty($gross_amount1))
                {
                    $product_discount = $gross_amount1->product_discount;
                    $product_quantity = $gross_amount1->product_quantity;
                    $product_price = $gross_amount1->product_price;
                    $final_quantity += $product_quantity;
                    $tot = $product_quantity * $product_price;
                    
                    $final_gross += $tot;
                    if($product_discount != 0 || $product_discount != NULL)
                    {
                        $dic_amount = ($tot*$product_discount)/100;
                        $final_discount += $dic_amount;
                        $tot -= $dic_amount;
                    }
                    
                    if($gst != 0 || $gst != NULL)
                    {
                        $dic_amount = ($tot*$gst)/100;
                        $tot += $dic_amount;
                        
                        $final_gst += $gst;
                    }
                    
                    $final_amount = $tot;
                  
                    if($delivery_charge != 0 || $delivery_charge != NULL)
                    {
                        $final_amount += $delivery_charge;
                    }
                    
                    if($chc != 0 || $chc != NULL)
                    {
                        $final_amount += $chc;
                    }
                    $tot_array[]=$final_amount;
                }
                else
                {
                    $final_amount = 0;
                }
                
            }
            if($order_type == "prescription")
            {
                $gross_amount = $this->db->query("select prescription_discount,prescription_quantity,prescription_price from prescription_order_list where order_id='$order_id'");
                $gross_amount1 = $gross_amount->row();
                if(!empty($gross_amount1))
                {
                    $product_discount = $gross_amount1->prescription_discount;
                    $product_quantity = $gross_amount1->prescription_quantity;
                    $product_price = $gross_amount1->prescription_price;
                    $final_quantity1 += $product_quantity;
                    $tot = $product_quantity * $product_price;
                    $final_gross1 += $tot;
                    if($product_discount != 0 || $product_discount != NULL)
                    {
                        $dic_amount = ($tot*$product_discount)/100;
                        $tot -= $dic_amount;
                        $final_discount1 += $dic_amount;
                    }
                    
                    if($gst != 0 || $gst != NULL)
                    {
                        $dic_amount = ($tot*$gst)/100;
                        $tot += $dic_amount;
                        $final_gst1 += $gst;
                    }
                    
                    $final_amount1 = $tot;
                  
                    if($delivery_charge != 0 || $delivery_charge != NULL)
                    {
                        $final_amount1 += $delivery_charge;
                    }
                    
                    if($chc != 0 || $chc != NULL)
                    {
                        $final_amount1 += $chc;
                    }
                    $tot_array[]=$final_amount1;
                }
                else
                {
                    $final_amount1 = 0;
                }
                
            }
        }
        
        if(!empty($tot_array))
        {
            $max = max($tot_array);
            $min = min($tot_array);
            $avg = round(array_sum($tot_array)/count($tot_array));
        }
        else
        {
            $max = 0;
            $min = 0;
            $avg = 0;
        }
        //----------------------------daywise---------------------------
        for($d=0;$d<7;$d++)
        {
            $date = $from_date;
            $date1 = str_replace('-', '/', $date);
            $tomorrow = date('Y-m-d',strtotime($date1 . "+$d days"));
            //echo "select * from user_order where listing_id='$user_id' AND order_date ='$tomorrow'";
            $gross_amount1_  = $this->db->query("select * from user_order where listing_id='$user_id' AND order_date LIKE '%" .$tomorrow."%' ");
            $gross_amount11_ = $gross_amount1_->result();
            
            $total_orders2 = count($gross_amount11_);
            
            $final_amount_ = 0;
            $final_amount1_ = 0;
            $final_discount_ = 0;
            $final_discount1_ = 0;
            $final_quantity_ = 0;
            $final_quantity1_ = 0;
            $final_gst_ = 0;
            $final_gst1_ = 0;
            $final_gross_ = 0;
            $final_gross1_ = 0;
            
            $tot_array_ = array();
            foreach($gross_amount11_ as $det)
            {
                $order_id_ = $det->order_id;
                $order_type_ = $det->order_type;
                $delivery_charge_ = $det->delivery_charge;
                $gst_ = $det->gst;
                $chc_ = $det->chc;
               
                if($order_type_ == "order")
                {
                    $gross_amount = $this->db->query("select product_discount,product_quantity,product_price from user_order_product where order_id='$order_id_'");
                    $gross_amount1 = $gross_amount->row();
                    if(!empty($gross_amount1))
                    {
                        $product_discount_ = $gross_amount1->product_discount;
                        $product_quantity_ = $gross_amount1->product_quantity;
                        $product_price_ = $gross_amount1->product_price;
                        $final_quantity_ += $product_quantity_;
                        $tot_ = $product_quantity_ * $product_price_;
                        
                        $final_gross_ += $tot_;
                        if($product_discount_ != 0 || $product_discount_ != NULL)
                        {
                            $dic_amount_ = ($tot_*$product_discount_)/100;
                            $final_discount_ += $dic_amount_;
                            $tot_ -= $dic_amount_;
                        }
                        
                        if($gst_ != 0 || $gst_ != NULL)
                        {
                            $dic_amount_ = ($tot_*$gst_)/100;
                            $tot_ += $dic_amount_;
                            
                            $final_gst_ += $gst_;
                        }
                        
                        $final_amount_ = $tot_;
                      
                        if($delivery_charge_ != 0 || $delivery_charge_ != NULL)
                        {
                            $final_amount_ += $delivery_charge_;
                        }
                        
                        if($chc_ != 0 || $chc_ != NULL)
                        {
                            $final_amount_ += $chc_;
                        }
                        $tot_array_[]=$final_amount_;
                    }
                    else
                    {
                        $final_amount_ = 0;
                    }
                    
                }
                if($order_type_ == "prescription")
                {
                    $gross_amount = $this->db->query("select prescription_discount,prescription_quantity,prescription_price from prescription_order_list where order_id='$order_id_'");
                    $gross_amount1 = $gross_amount->row();
                    if(!empty($gross_amount1))
                    {
                        $product_discount_ = $gross_amount1->prescription_discount;
                        $product_quantity_ = $gross_amount1->prescription_quantity;
                        $product_price_ = $gross_amount1->prescription_price;
                        $final_quantity1_ += $product_quantity_;
                        $tot_ = $product_quantity_ * $product_price_;
                        $final_gross1_ += $tot_;
                        if($product_discount_ != 0 || $product_discount_ != NULL)
                        {
                            $dic_amount_ = ($tot_*$product_discount_)/100;
                            $tot_ -= $dic_amount_;
                            $final_discount1_ += $dic_amount_;
                        }
                        
                        if($gst_ != 0 || $gst_ != NULL)
                        {
                            $dic_amount_ = ($tot_*$gst_)/100;
                            $tot_ += $dic_amount_;
                            $final_gst1_ += $gst_;
                        }
                        
                        $final_amount1_ = $tot_;
                      
                        if($delivery_charge_ != 0 || $delivery_charge_ != NULL)
                        {
                            $final_amount1_ += $delivery_charge_;
                        }
                        
                        if($chc_ != 0 || $chc_ != NULL)
                        {
                            $final_amount1_ += $chc_;
                        }
                        $tot_array_[]=$final_amount1_;
                    }
                    else
                    {
                        $final_amount1_ = 0;
                    }
                    
                }
            }
            
             if(!empty($tot_array_))
            {
                $max_ = max($tot_array_);
                $min_ = min($tot_array_);
                $avg_ = round(array_sum($tot_array_)/count($tot_array_));
            }
            else
            {
                $max_ = 0;
                $min_ = 0;
                $avg_ = 0;
            }
        
            $day = date('l',strtotime($tomorrow));
            $daywise[]=array('day' => $day,
                            'total_orders' => $total_orders2,
                            'date' => $tomorrow,  
                            'gross_amount' => $final_amount_+$final_amount1_,
                            'total_units'  => $final_quantity_+$final_quantity1_,
                            'net_amount'  => $final_gross_+$final_gross1_,
                            'max_bill'    => $max_,
                            'min_bill' => $min_,
                            'avg_bill'  => $avg_ ,
                            'total_gst'   => $final_gst_+$final_gst1_,
                            'total_disc' => $final_discount_+$final_discount1_);
        }
        $resultpost[] = array(
                            'from_date' => $from_date,
                            'to_date' => $to_date,
                            'total_orders' => $total_orders1,
                            'total_units'  => $final_quantity+$final_quantity1,
                            'net_amount'  => $final_gross+$final_gross1 ,
                            'max_bill'    => $max,
                            'min_bill' => $min,
                            'avg_bill'  => $avg,
                            'gross_amount' => $final_amount+$final_amount1,
                            'total_gst'   => $final_gst+$final_gst1,
                            'total_disc' => $final_discount+$final_discount1,
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
    
    	}
      
                   return $session1;
    }   
    public function monthly_report_v1($user_id,$hub_user_id)
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
    		
    	$gross_amount1  = $this->db->query("select * from user_order where listing_id='$user_id' AND order_date >= '$startdate' AND order_date <= '$enddate'");
        $gross_amount11 = $gross_amount1->result();
        $final_amount = 0;
        $final_amount1 = 0;
        foreach($gross_amount11 as $det)
        {
            $order_id = $det->order_id;
            $order_type = $det->order_type;
            $delivery_charge = $det->delivery_charge;
            $gst = $det->gst;
            $chc = $det->chc;
           
            if($order_type == "order")
            {
                $gross_amount = $this->db->query("select product_discount,product_quantity,product_price from user_order_product where order_id='$order_id'");
                $gross_amount1 = $gross_amount->row();
                if(!empty($gross_amount1))
                {
                    $product_discount = $gross_amount1->product_discount;
                    $product_quantity = $gross_amount1->product_quantity;
                    $product_price = $gross_amount1->product_price;
                    
                    $tot = $product_quantity * $product_price;
                    
                    if($product_discount != 0 || $product_discount != NULL)
                    {
                        $dic_amount = ($tot*$product_discount)/100;
                        $tot -= $dic_amount;
                    }
                    
                    if($gst != 0 || $gst != NULL)
                    {
                        $dic_amount = ($tot*$gst)/100;
                        $tot += $dic_amount;
                    }
                    
                    $final_amount = $tot;
                  
                    if($delivery_charge != 0 || $delivery_charge != NULL)
                    {
                        $final_amount += $delivery_charge;
                    }
                    
                    if($chc != 0 || $chc != NULL)
                    {
                        $final_amount += $chc;
                    }
                    
                }
                else
                {
                    $final_amount = 0;
                }
            }
            if($order_type == "prescription")
            {
                $gross_amount = $this->db->query("select prescription_discount,prescription_quantity,prescription_price from prescription_order_list where order_id='$order_id'");
                $gross_amount1 = $gross_amount->row();
                if(!empty($gross_amount1))
                {
                    $product_discount = $gross_amount1->prescription_discount;
                    $product_quantity = $gross_amount1->prescription_quantity;
                    $product_price = $gross_amount1->prescription_price;
                    
                    $tot = $product_quantity * $product_price;
                    
                    if($product_discount != 0 || $product_discount != NULL)
                    {
                        $dic_amount = ($tot*$product_discount)/100;
                        $tot -= $dic_amount;
                    }
                    
                    if($gst != 0 || $gst != NULL)
                    {
                        $dic_amount = ($tot*$gst)/100;
                        $tot += $dic_amount;
                    }
                    
                    $final_amount1 = $tot;
                  
                    if($delivery_charge != 0 || $delivery_charge != NULL)
                    {
                        $final_amount1 += $delivery_charge;
                    }
                    
                    if($chc != 0 || $chc != NULL)
                    {
                        $final_amount1 += $chc;
                    }
                    
                }
                else
                {
                    $final_amount1 = 0;
                }
            }
        }
        
    
        
        $session1[] = array(
                                        'from_date'=> $startdate,
                                        'to_date' => $enddate,
                                        'price'=>$final_amount+$final_amount1,
                                        'current_date' => date('Y-m-d')
                                       );
    
    	}
      
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
            $query = $this->db->query("SELECT name,email,id,phone,map_location FROM users WHERE phone='$mobile' AND vendor_id=0");
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
                                            'map_location'  => $row->map_location,
                                          
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
                
                $query2 = $this->db->query("SELECT name,email,id,phone,map_location FROM users WHERE id='$user_id'");
                $count2 = $query2->num_rows();
                if ($count2 > 0) {
                    $row2 = $query2->row();
                  
                     $data_add=array(        
                                            'name'          => $row2->name,
                                            'email'         => $row2->email,
                                            'bachat_card'   => $bachat_card,
                                            'mobile'        => $row2->phone,
                                            'map_location'  => $row2->map_location,
                                          
                	               );
                }
                
                   
                
            }
            
             $query = $this->db->query("SELECT * FROM user_privilage_card WHERE card_no='$mobile'");
            $count = $query->num_rows();
            if ($count > 0) {
                $row = $query->row();
                $user_id= $row->user_id;
                $bachat_card = $row->card_no;
                
                $query2 = $this->db->query("SELECT name,email,id,phone,map_location FROM users WHERE id='$user_id'");
                $count2 = $query2->num_rows();
                if ($count2 > 0) {
                    $row2 = $query2->row();
                  
                     $data_add=array(        
                                            'name'          => $row2->name,
                                            'email'         => $row2->email,
                                            'bachat_card'   => $bachat_card,
                                            'mobile'        => $row2->phone,
                                            'map_location'  => $row2->map_location,
                	               );
                }
            }
        }
        return $data_add;           
    }
    
    /*using*/
    public function stock_inventory_dashboard_v1($user_id,$hub_user_id,$page,$date_from,$date_to)
    {
        $limit = 7;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        
        
        $query = $this->db->query("SELECT * FROM product WHERE created_by='$user_id' OR created_by='0' LIMIT $start, $limit");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id                     = $row['id'];
                $user_id                = $user_id;
                $vendor_id              = "13";
                $pname                  = $row['product_name'];
                $inventory_image        = $row['image'];
                $cost_price             = "";
                $mrp                    = "";
                $selling_price          = "";
                $distributor_id         = "";
                $manufacture_date       = "";
                $expiry_date            = "";
                $barcode                = "";
                $ingredients            = "";
                $category_type_id       = $row['category'];
                $sub_category_type_id   = $row['sub_category'];
                $product_type_id        = "";
                $size                   = "";
                
                $new_from_date = date('Y-m-d',strtotime($date_from . "-1 days"));
                $new_to_date = date('Y-m-d',strtotime($date_to . "+1 days"));
        
                $w = "";
                if(!empty($date_from) && !empty($date_to))
                {
                    $w = " AND created_at BETWEEN '$new_from_date' AND '$new_to_date'";
                }
                
                $check_table_existance = $this->PharmacyPartnerModel->stock_table_existance($user_id);
                if($check_table_existance['status'] == true){
                    $table_name = $check_table_existance['table_name'];
                
                
            	    $sql1="SELECT sum(quantity) as stock_quantity FROM $table_name where user_id='$user_id'  AND product_id='$id' AND warehouse_id='0' $w ";
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
                } else {
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
        $query = $this->db->query("UPDATE medical_stores SET logo='$licence_pic_file' WHERE user_id='$listing_id'");        
        return array(
           'status' => 200,
           'message' => 'success'
        );
    }
    public function pharmacy_feedback($user_id,$feedback,$email)
    {
        $data = array(
                            'user_id'           => $user_id,
                            'email'             => $email,
                            'feed_back'         => $feedback
	                        
                );  
                
                $this->db->insert('users_feedback',$data);      
                $id = $this->db->insert_id();
        if($id != "")        
                $resultpost =   array(
                                'status' => 200,
                                'message' => 'Success'
                            );
            
         else {
            $resultpost =   array(
                                'status' => 201,
                                'message' => 'Failed'
                            );
        }
        return $resultpost;
        
    }
     public function get_user_ledger_details($user_id){
        $debit_list =array();
        $point_list =array();
        $credit_list =array();
        $failure_list = array();
        $bachat_list = array();
        $credit_list_trans = array();
        //echo "SELECT * FROM user_ledger_balance WHERE user_id='$user_id'";
        
          
        $query = $this->db->query("SELECT * FROM user_ledger_balance WHERE user_id='$user_id'");

        $row = $query->row();
  
        $num_row = $query->num_rows();
     
        if ($num_row>0)
        {
                $ledger_balance =  $row->ledger_balance;
                $lock_amount =  $row->lock_amount;
        }else{
                $ledger_balance = 0;
                $lock_amount =  0;
        }
       
        
        $pnts_rate = $this->db->select('rate')->where('id', 1)->get('points_rate')->row();
        if (!empty($pnts_rate)) {
            $rate = $pnts_rate->rate;
        } else {
            $rate = "";
        }    
        
        $query_point = $this->db->query("SELECT * FROM user_ledger WHERE user_id='$user_id' AND (trans_type='4' or trans_type='5') GROUP BY order_id order by id DESC");

        $count_point = $query_point->num_rows();
        // print_r($count_point); die();
        //$ttl_pointds = array();
        if ($count_point > 0) {

            foreach ($query_point->result_array() as $row) {
               $ttl_pointds = array();
                $listing_id = $row['listing_id'];
                
                //added for vendor details 
                $listing_id = $row['listing_id'];
                
                $doctor_details = $this->db->query("SELECT * FROM users WHERE id='$listing_id'");
                $details = $doctor_details->row();
                $doctor_count = $doctor_details->num_rows();
                if($doctor_count>0)
                {
                    $doctor_name = $details->name;
                }
                else
                {
                    $doctor_name = "";
                }
                
                 $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                //end
            
                
                $order_id      = $row['order_id'];
                $trans_type      = $row['trans_type'];
                 
                //echo "SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') order by id DESC";
                // $query_point_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') order by id DESC");
                $query_point_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') and user_id='$user_id' order by id DESC");
                $count_query_point_trans = $query_point_trans->num_rows();
                
                
                //CHECK INVOICE IS ADDED OR NOT
                $query_inovce = $this->db->query("SELECT * FROM tbl_invoice WHERE order_id='$order_id'");
                $count_inovce = $query_inovce->num_rows();
                if($count_inovce > 0){
                    $invoice = '1';
                }else{
                    $invoice = '0';
                }
                 
                if ($count_query_point_trans > 0) {
                    $points_list_trans = array();
                    foreach ($query_point_trans->result_array() as $row_nest) {
                            
                            if($row_nest['trans_type']  == '5'){   
                                $trans_id       = $row_nest['trans_id'];
                                $trans_type     = $row_nest['trans_type'];
                                $trans_mode     = $row_nest['trans_mode'];
                                $amount        = $row_nest['amount'];
                                $amount_saved        = $row_nest['amount_saved'];
                                $trans_time     = $row_nest['trans_time'];
                                
                                
                                $trans_time = date('m/d/Y h:i a', strtotime($trans_time));
                            
                                array_push($ttl_pointds,$row_nest['amount']);
                                $points_list_trans[] = array(
                                    'order_id' => $order_id,
                                    'trans_id' => $trans_id,
                                    'amount' => $amount-$amount_saved,
                                    'trans_mode' => $trans_mode,
                                    'trans_time'=>$trans_time
                                ); 
                            }else if($row_nest['trans_type']  == '4'){
                                $trans_id       = $row_nest['trans_id'];
                                $trans_type     = $row_nest['trans_type'];
                                $trans_mode     = $row_nest['trans_mode'];
                                $amount        = $row_nest['amount'];
                                $amount_saved        = $row_nest['amount_saved'];
                                $trans_time     = $row_nest['trans_time'];
                                $trans_time = date('m/d/Y h:i a', strtotime($trans_time));
                                
                                array_push($ttl_pointds,$row_nest['amount']);
                                $points_list_trans[] = array(
                                    'order_id' => $order_id,
                                    'trans_id' => $trans_id,
                                    'amount' => $amount-$amount_saved,
                                    'trans_mode' => $trans_mode,
                                    'trans_time'=>$trans_time,
                                    'invoice' =>$invoice
                                );
                            }
                        
                    }
                      // print_r($ttl_pointds); 
                    
                }
                if($row_nest['trans_type']  == '5'){        
                     $convert_rs = array_sum($ttl_pointds);
                     $point_list[] = array(
                         'vendor_image' => $vendor_image,
                         'vendor_name' =>$doctor_name,
                        'order_id' => $order_id,
                        'trans_type'=>$trans_type,
                        //'total_points'=>$amount-$amount_saved,
                        'total_points'=>$convert_rs*$rate,
                        'point_rupee'=>array_sum($ttl_pointds),
                        'trans_details'=>$points_list_trans,
                         'invoice' =>$invoice
                    ); 
                }else if($row_nest['trans_type']  == '4'){
                    $convert_rs = round(array_sum($ttl_pointds)/$rate,2);
                     $point_list[] = array(
                         'vendor_image' => $vendor_image,
                         'vendor_name' =>$doctor_name,
                        'order_id' => $order_id,
                        'trans_type'=>$trans_type,
                        'total_points'=>$amount-$amount_saved,
                        'point_rupee'=>$convert_rs,
                        'trans_details'=>$points_list_trans,
                         'invoice' =>$invoice
                        
                    ); 
                }
               
            }
           
           
        }      
        
        
        $query_credit = $this->db->query("SELECT * FROM user_ledger WHERE user_id='$user_id' and (trans_type='0' or trans_type='1' or trans_type='2') GROUP BY order_id order by id DESC");
        
        $count_credit = $query_credit->num_rows();
        if ($count_credit > 0) {

            foreach ($query_credit->result_array() as $row) {
                
                $order_id      = $row['order_id'];
                $listing_id = $row['listing_id'];
                
               //added for vendor details 
                $listing_id = $row['listing_id'];
                
                $doctor_details = $this->db->query("SELECT * FROM users WHERE id='$listing_id'");
                $details = $doctor_details->row();
                $doctor_count = $doctor_details->num_rows();
                if($doctor_count>0)
                {
                    $doctor_name = $details->name;
                }
                else
                {
                    $doctor_name = "";
                }
                
                 $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                //end

                $query_debit_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='0' or trans_type='1' or trans_type = '2') order by id DESC");
                $count_query_debit_trans = $query_debit_trans->num_rows();
                
                //CHECK INVOICE IS ADDED OR NOT
                //echo "SELECT * FROM tbl_invoice WHERE order_id='$order_id'";
                $query_inovce = $this->db->query("SELECT * FROM tbl_invoice WHERE order_id='$order_id'");
                $count_inovce = $query_inovce->num_rows();
                if($count_inovce > 0){
                    $invoice = '1';
                }else{
                    $invoice = '0';
                }
                 
                if ($count_query_debit_trans > 0) {
                    $credit_list_trans = array();
                    foreach ($query_debit_trans->result_array() as $row_nest) {
                        
                            $trans_id       = $row_nest['trans_id'];
                            $trans_type     = $row_nest['trans_type'];
                            $trans_mode     = $row_nest['trans_mode'];
                             $amount         = $row_nest['amount'];
                            $amount_saved        = $row_nest['amount_saved'];
                            
                          
                           
                            $trans_time     = $row_nest['trans_time'];
                            $trans_time = date('m/d/Y h:i a', strtotime($trans_time));
                            $credit_list_trans[] = array(
                                'order_id' => $order_id,
                                'trans_id' => $trans_id,
                                'trans_type' => $trans_type,
                                'trans_mode' => $trans_mode,
                                'amount' => $amount-$amount_saved,
                                'point_earned' => $amount-$amount_saved,
                                'trans_time'=>$trans_time
                            ); 
                         
                    }
                        
                    
                }
                 
                $credit_list[] = array(
                    'vendor_image' => $vendor_image,
                     'vendor_name' =>$doctor_name,
                    'order_id' => $order_id,
                    'trans_details'=>$credit_list_trans,
                    'invoice' =>$invoice
                ); 
                
                
            }
        }  
        
        
        
       
        $query_failure = $this->db->query("SELECT * FROM user_ledger WHERE user_id='$user_id' AND trans_type = '2' order by id DESC");
        $count_failure = $query_failure->num_rows();
        if ($count_failure > 0) {

            foreach ($query_failure->result_array() as $row) {
                //added for vendor details 
                $listing_id = $row['listing_id'];
                
                $doctor_details = $this->db->query("SELECT * FROM users WHERE id='$listing_id'");
                $details = $doctor_details->row();
                $doctor_count = $doctor_details->num_rows();
                if($doctor_count>0)
                {
                    $doctor_name = $details->name;
                }
                else
                {
                    $doctor_name = "";
                }
                
                 $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                //end
                $order_id      = $row['order_id'];
                $trans_id      = $row['trans_id'];
                $trans_type    = $row['trans_type'];
                $amount        = $row['amount'];
                
                //CHECK INVOICE IS ADDED OR NOT
                
                $query_inovce = $this->db->query("SELECT * FROM tbl_invoice WHERE order_id='$order_id'");
                $count_inovce = $query_inovce->num_rows();
                if($count_inovce > 0){
                    $invoice = '1';
                }else{
                    $invoice = '0';
                }
                
                
                
                $failure_list[] = array(
                    'vendor_image' => $vendor_image,
                    'vendor_name' =>$doctor_name,
                    'order_id' => $order_id,
                    'trans_id' => $trans_id,
                    'trans_type' => $trans_type,
                    'amount' => $amount,
                     'invoice' =>$invoice
                );
            }
           
           
        }
     
     
     //added by zak for to show  invoice list in ledger details 
     
     $query_invoice_details = $this->db->query("SELECT * FROM tbl_invoice WHERE user_id='$user_id' ");
     $count_invoice_details = $query_invoice_details->num_rows();
     if($count_invoice_details > 0){
          foreach ($query_invoice_details->result_array() as $row) {
              $id =$row['id'];
              $order_id = $row['order_id'];
              $invoice_no = $row['invoice_no'];
              $comment =$row['comment'];
              $invoice_date = $row['invoice_date'];
              $query_attachment = $this->db->query("select * from tbl_invoice_attachment WHERE invoice_id='$id'");
              
              $attachment_file = array();
              foreach ($query_attachment->result_array() as $row) {
                  $file_name = $row['file_name'];
              
              $attachment_file[] = array(
                        'https://medicalwale.s3.amazonaws.com/images/invoice_images/'.$file_name
                  );
              }
              $invoice_details[] = array(
                   'order_id' => $order_id,
                   'invoice_no' => $invoice_no,
                   'comment' => $comment,
                   'invoice_date' => $invoice_date,
                   'attachment' =>$attachment_file
                  );
          }
     }
     else
     {
         $invoice_details = array();
     }
        
        $query_bachat = $this->db->query("SELECT * FROM user_ledger WHERE user_id='$user_id' AND (trans_type='1') AND amount_saved!='0' GROUP BY order_id order by id DESC");

        $count_bachat = $query_bachat->num_rows();
        // print_r($count_point); die();
        //$ttl_pointds = array();
        if ($count_bachat > 0) {

            foreach ($query_bachat->result_array() as $row) {
                $ttl_bachat = array();
                $order_id      = $row['order_id'];
                $trans_type      = $row['trans_type'];
                 $discount = "";
                 
                 
                 $listing_id = $row['listing_id'];
                
               //added for vendor details 
                $listing_id = $row['listing_id'];
                
                $doctor_details = $this->db->query("SELECT * FROM users WHERE id='$listing_id'");
                $details = $doctor_details->row();
                $doctor_count = $doctor_details->num_rows();
                if($doctor_count>0)
                {
                    $doctor_name = $details->name;
                }
                else
                {
                    $doctor_name = "";
                }
                
                 $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                //end
                // $query_bachat_trans = $this->db->query();
                
                //CHECK INVOICE IS ADDED OR NOT
                $query_inovce = $this->db->query("SELECT * FROM tbl_invoice WHERE order_id='$order_id'");
                $count_inovce = $query_inovce->num_rows();
                if($count_inovce > 0){
                    $invoice = '1';
                }else{
                    $invoice = '0';
                }
                 
                //echo "SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') order by id DESC";
                // $query_point_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') order by id DESC");
                $query_bachat_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='1') order by id DESC");
                
                $count_query_bachat_trans = $query_bachat_trans->num_rows();
                 //print_r($query_bachat_trans->result_array());
                if ($count_query_bachat_trans > 0) {
                    $bachat_list_trans = array();
                    foreach ($query_bachat_trans->result_array() as $row_nest) {
                            
                        if(!empty($row_nest['amount_saved'])){
                            $trans_id       = $row_nest['trans_id'];
                            $trans_type     = $row_nest['trans_type'];
                            $trans_mode     = $row_nest['trans_mode'];
                            $amount         = $row_nest['amount'];
                            $amount_saved   = $row_nest['amount_saved'];
                            $discount       = $row_nest['discount'];
                            $trans_time     = $row_nest['trans_time'];
                               $trans_time = date('m/d/Y h:i a', strtotime($trans_time));
                                
                            array_push($ttl_bachat,$row_nest['amount']);
                            $bachat_list_trans[] = array(
                                'order_id' => $order_id,
                                'trans_id' => $trans_id,
                                'trans_mode' => $trans_mode,
                                'trans_time'=>$trans_time,
                                'invoice' =>$invoice
                            ); 
                        }
                        
                    }
                    // print_r($ttl_pointds); 
                    
                }
                
                $convert_rs = round(array_sum($ttl_bachat)/$rate);
                 $bachat_list[] = array(
                     'vendor_image' => $vendor_image,
                     'vendor_name' =>$doctor_name,
                    'order_id' => $order_id,
                    'trans_type'=>$trans_type,
                    // 'total_points'=>array_sum($ttl_bachat),
                    // 'point_rupee'=>$convert_rs,
                    'amount' => $amount,
                    'amount_saved' => $amount_saved,
                    'discount' => $discount,
                    'trans_details'=>$bachat_list_trans,
                    'invoice' =>$invoice
                ); 
               
            }
           
           
        }
      
                $query = $this->db->query("SELECT SUM(points) as total_points FROM user_points WHERE user_id='$user_id' and status='active' order by id DESC");

                $rows = $query->row();
                
                if (isset($rows))
                {
                        $total_points =  $rows->total_points;
                }else{
                        $total_points = 0;
                }
     
        $ledger_details = array();
        $ledger_details['balance_sheet'] =  $credit_list;
        $ledger_details['points']   =  $point_list;
        $ledger_details['failure'] =  $failure_list;
        $ledger_details['bachat_card'] =  $bachat_list;
      //  if($ledger_balance == $lock_amount)
        $ledger_details['balance'] = $ledger_balance;
        $ledger_details['total_points']   =  $total_points;
        $ledger_details['invoice_details'] = $invoice_details;
        return $ledger_details;
        
    }
    
     /*public function all_order_list($user_id,$staff_user_id,$from_date,$to_date,$filter,$amount_from,$amount_to,$discount_from,$discount_to,$prescription)
    {
                if($filter == "all")
                {   
                    $order_total=0;
                    $sub_total_sum2=0;
                    $sub_total_sum1=0;
                    $sub_total_discount=0;
                    $sub_total_discount1=0;
                    $where1 ="";
                     if($from_date != "" && $to_date != "")
                            {
                                 $where1 .= " AND order_date>='$from_date' AND order_date<='$to_date' ";
                            }
                           if($prescription != "")
                            {
                                 $where1 .= " AND order_type='$prescription' ";
                            }
                    $user_orders = $this->db->query("select * from user_order where listing_id='$user_id' AND order_status='Order Delivered' $where1");
                    $user_orders_rows = $user_orders->num_rows(); 
                    if($user_orders_rows>0)
                    {
                        $resultpost1 = array();
                        foreach ($user_orders->result_array() as $row) {
                            $order_id = $row['order_id'];
                            $order_type = $row['order_type'];
                            $invoice_no = $row['invoice_no'];
                            $name = $row['name'];
                            $mobile = $row['mobile'];
                            $pincode = $row['pincode'];
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
                           
                          
                            $user_info = $this->db->query("SELECT name,phone FROM users WHERE id='$user_id'");
                            $getuser_info = $user_info->row_array();
                            $user_name = $getuser_info['name'];
                            $user_mobile = $getuser_info['phone'];
                            $product_resultpost = array();
                            $prescription_result = array();
                            if ($order_type == 'order') {
                                $order_total = '0';
                                $product_query = $this->db->query("select id as product_order_id,product_unit,product_unit_value,product_id,product_name,product_img,product_quantity,product_discount,product_price,sub_total,product_status,product_status_type,product_status_value,order_status from user_order_product where order_id='$order_id' order by product_order_id asc");
                                $product_count = $product_query->num_rows();
                                if ($product_count > 0) {
                                    foreach ($product_query->result_array() as $product_row) {
                                        $product_order_id = $product_row['product_order_id'];
                                        $product_id = $product_row['product_id'];
                                        $product_name = $product_row['product_name'];
                                        $product_img = "https://d2c8oti4is0ms3.cloudfront.net/images/product_images/" . $product_row['product_img'];
                                        $product_quantity = $product_row['product_quantity'];
                                        $product_discount = $product_row['product_discount'];
                                        $product_price = $product_row['product_price'];
                                        $product_unit = $product_row['product_unit'];
                                        $product_unit_value = $product_row['product_unit_value'];
                                        $sub_total = $product_row['sub_total'];
                                        $product_status = $product_row['product_status'];
                                        $product_status_type = $product_row['product_status_type'];
                                        $product_status_value = $product_row['product_status_value'];
                                        $product_order_status = $product_row['order_status'];
                                        $sub_total_sum1      += $product_price * $product_quantity;
                                         $sub_total_discount +=$product_discount;
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
                                } else {
                                    $product_resultpost = array();
                                }
                            } else {
                                $order_total = '0';
                                $product_query = $this->db->query("SELECT id as product_order_id, order_status,prescription_image FROM prescription_order_details WHERE order_id='$order_id' order by product_order_id asc");
                                $product_count = $product_query->num_rows();
                                if ($product_count > 0) {
                                    foreach ($product_query->result_array() as $product_row) {
                                        $product_order_id = $product_row['product_order_id'];
                                        $product_id = $product_row['product_order_id'];
                                        $product_name = '';
                                        $prescription_image = '';
                                        $product_img = $product_row['prescription_image'];
                                        $images_1 = "";
                                        if (strpos($product_img, '/') == true) {
                                             $images_1 = "https://d2c8oti4is0ms3.cloudfront.net/" . $product_img;
                                        }
                                        else
                                        {
                                            $images_1= "https://d2c8oti4is0ms3.cloudfront.net/images/prescription_images/" . $product_img;
                                        }
                                        
                                        $product_quantity = '';
                                        $product_price = '';
                                        $sub_total = '';
                                        $product_status = '';
                                        $product_status_type = '';
                                        $product_status_value = '';
                                        $product_order_status = $product_row['order_status'];
            
                                        $product_resultpost[] = array(
                                            "product_order_id" => $product_order_id,
                                            "product_id" => $product_id,
                                            "product_name" => $product_name,
                                            "product_img" => $images_1,
                                            "product_quantity" => $product_quantity,
                                            "product_price" => $product_price,
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
                                            $finalamt=$prescription_row['prescription_price']*$prescription_row['prescription_quantity'];
                                            $sub_total_sum2+=$finalamt;
                                            $prescription_name = $prescription_row['prescription_name'];
                                            $prescription_quantity = $prescription_row['prescription_quantity'];
                                            $prescription_price = $prescription_row['prescription_price'];
                                            $prescription_discount = $prescription_row['prescription_discount'];
                                            $prescription_status = $prescription_row['prescription_status'];
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
                            $order_total=$sub_total_sum1+$sub_total_sum2;
                            $order_total_discount=$sub_total_discount+$sub_total_discount1;
                            if($order_total_discount=="")
                            {
                                $order_total_discount=0;
                            }
                            
                         if($amount_from != "" && $amount_to != "" && $discount_from != "" && $discount_to != "")
                            {
                                 if($order_total >= (int)$amount_from && $order_total <= (int)$amount_to && $order_total_discount >= (int)$discount_from && $order_total_discount <= (int)$discount_to)
                                 {
                                     $resultpost1[] = array(
                                        "order_id" => $order_id,
                                        "order_type" => $order_type,
                                        "invoice_no" => $invoice_no,
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
                                        "delivery_charge" => $delivery_charge,
                                        "product_order" => $product_resultpost,
                                        "prescription_order" => $prescription_result
                                    );
                                 }
                            }
                            else  if($amount_from != "" && $amount_to != "") 
                            {
                                 if($order_total >= (int)$amount_from && $order_total <= (int)$amount_to )
                                 {
                                     $resultpost1[] = array(
                                        "order_id" => $order_id,
                                        "order_type" => $order_type,
                                        "invoice_no" => $invoice_no,
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
                                        "delivery_charge" => $delivery_charge,
                                        "product_order" => $product_resultpost,
                                        "prescription_order" => $prescription_result
                                    );
                                 }
                            }
                            else if($discount_from != "" && $discount_to != "")
                            {
                                 if($order_total_discount >= $discount_from && $order_total_discount <= $discount_to )
                                 {
                                      $resultpost1[] = array(
                                        "order_id" => $order_id,
                                        "order_type" => $order_type,
                                        "invoice_no" => $invoice_no,
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
                                        "delivery_charge" => $delivery_charge,
                                        "product_order" => $product_resultpost,
                                        "prescription_order" => $prescription_result
                                    );
                                 }
                            }
                            else
                            {
                                $resultpost1[] = array(
                                    "order_id" => $order_id,
                                    "order_type" => $order_type,
                                    "invoice_no" => $invoice_no,
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
                                    "delivery_charge" => $delivery_charge,
                                    "product_order" => $product_resultpost,
                                    "prescription_order" => $prescription_result
                                );
                            }
                        }
                    } 
                    else {
                            $resultpost1 = array();
                        }
                    
                    $where2 ="";
                     if($from_date != "" && $to_date != "")
                            {
                                 $where2 .= " AND bill_date>='$from_date' AND bill_date<='$to_date' ";
                            }
                            if($amount_from != "" && $amount_to != "")
                            {
                                 $where2 .= " AND (final_amount >= '$amount_from' OR final_amount <= '$amount_to') ";
                            }
                            if($discount_from != "" && $discount_to != "")
                            {
                                 $where2 .= " AND (discount >= '$discount_from' OR discount <= '$discount_to') ";
                            }
                            if($prescription != "")
                            {
                                 $where2 .= " AND order_type='$prescription' ";
                            }
                    $bill_order = $this->db->query("select * from inventory_bill where user_id='$user_id' $where2"); 
                    $bill_orders_rows = $bill_order->num_rows();
                    
                     if($bill_orders_rows>0)
                             {
                      $resultpost2 = array();
                        foreach ($bill_order->result_array() as $row) {
                            $bid =  $row['id'];
                            $order_id = $row['bill_no'];
                            $order_type = "Offline";
                            $invoice_no = $row['bill_no'];
                            $cust_id = $row['cust_id'];
                            
                            $user_info1 = $this->db->query("SELECT * FROM inventory_customer WHERE id='$cust_id'");
                            $user_info1_rows = $user_info1->num_rows();
                            if($user_info1_rows >0)
                            {
                                $getuser_info1 = $user_info1->row_array();
                              
                                $name = $getuser_info1['cname'];
                                $mobile = $getuser_info1['cmobile'];
                                $address1 = $getuser_info1['address'];
                                $address2 = "";
                                $landmark = "";
                                $pincode = $getuser_info1['pincode'];
                                if($pincode==null)
                                {
                                    $pincode ="";
                                }
                                
                                $city = $getuser_info1['city'];
                                if($city==null)
                                {
                                    $city ="";
                                }
                                $state = $getuser_info1['state'];
                                if($state==null)
                                {
                                    $state ="";
                                }
                            }
                            else
                            {
                                 $name = "";
                                $mobile = "";
                                $pincode = "";
                                $address1 = "";
                                $address2 = "";
                                $landmark = "";
                                $city = "";
                                $state ="";
                            }
                            
                            $action_by = "";
                            $payment_method = $row['payment_method'];
                            $order_date = $row['bill_date'];
                            $order_date = date('j M Y h:i A', strtotime($order_date));
                            $delivery_charge = 0;
                            $order_status = "";
                            $order_type = "Offline";
                           
                          
                            $user_info = $this->db->query("SELECT name,phone FROM users WHERE id='$user_id'");
                            $getuser_info = $user_info->row_array();
                            $user_name = $getuser_info['name'];
                            $user_mobile = $getuser_info['phone'];
                            $product_resultpost = array();
                            $prescription_result = array();
                           
                                $order_total = '0';
                                $product_query = $this->db->query("select * from inventory_bill_details where bill_id='$bid' order by id asc");
                                $product_count = $product_query->num_rows();
                                if ($product_count > 0) {
                                    foreach ($product_query->result_array() as $product_row) {
                                        
                                        
                                        $product_order_id = "";
                                        $product_id = $product_row['product_id'];
                                       
                                        
                                        $product = $this->db->query("select * from inventory_product where id='$product_id' order by id asc");
                                        $product_c= $product->num_rows();
                                        if ($product_c > 0) {
                                            $product_result = $product->row_array();
                                            $product_name = $product_result['pname'];
                                            $product_type_id = $product_result['product_type_id'];
                                            $product_img = "https://d2c8oti4is0ms3.cloudfront.net/images/Inventory/" . $product_result['inventory_image'];
                                            $product_units = $this->db->query("select * from inventory_product_type where id='$product_type_id' order by id asc");
                                            $product_u= $product_units->num_rows();
                                            if ($product_u > 0) {
                                                $product_unit_result = $product_units->row_array();
                                                $product_unit = $product_unit_result['unit'];
                                            }
                                            else
                                            {
                                                $product_unit = "";
                                            }
                                        }
                                        else
                                        {
                                            $product_name = "";
                                            $product_img = "";
                                            $product_unit = "";
                                        }
                                        $product_quantity = $product_row['quantity'];
                                        $product_discount = 0;
                                        $product_price = $product_row['price_per_quantity'];
                                       
                                        $product_unit_value = "";
                                        $sub_total = $product_row['total_price'];
                                        $product_status = "";
                                        $product_status_type = "";
                                        $product_status_value = "";
                                        $product_order_status = "";
                                        $sub_total_sum1      += $product_price * $product_quantity;
                                         $sub_total_discount +=$product_discount;
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
                                } else {
                                    $product_resultpost = array();
                                }
                           
                            $order_total=$sub_total_sum1;
                            $order_total_discount=$sub_total_discount;
                            if($order_total_discount=="")
                            {
                                $order_total_discount=0;
                            }
                            else
                            {
                                $order_total_discount;
                            }
                            $resultpost2[] = array(
                                "order_id" => $order_id,
                                "order_type" => $order_type,
                                "invoice_no" => $invoice_no,
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
                                "delivery_charge" => $delivery_charge,
                                "product_order" => $product_resultpost,
                                "prescription_order" => $prescription_result
                            );
                        
                    } 
                    
                   
                    
                }
                        else {
                                $resultpost2 = array();
                            }
                    if(!empty($resultpost1) && !empty($resultpost2))
                    {
                        $resultpost = array_merge($resultpost1,$resultpost2);
                       
                    }  
                    else if(!empty($resultpost1))
                    {
                        $resultpost = $resultpost1;
                    }
                    else if(!empty($resultpost2))
                    {
                        $resultpost = $resultpost2;
                    }        
                }
                else if($filter == "online")
                {
                     $order_total=0;
                          $sub_total_sum2=0;
                          $sub_total_sum1=0;
                          $sub_total_discount=0;
                          $sub_total_discount1=0;
                          $where= "";
                            if($from_date != "" && $to_date != "")
                            {
                                 $where .= " AND order_date>='$from_date' AND order_date<='$to_date' ";
                            }
                            if($prescription != "")
                            {
                                 $where .= " AND order_type='$prescription' ";
                            }
                    $resultpost = array();       
                    $user_orders = $this->db->query("select * from user_order where listing_id='$user_id'  AND order_status='Order Delivered' $where");
                    $user_orders_rows = $user_orders->num_rows(); 
                    if($user_orders_rows>0)
                    {
                        foreach ($user_orders->result_array() as $row) {
                            $order_id = $row['order_id'];
                            $order_type = $row['order_type'];
                            $invoice_no = $row['invoice_no'];
                            $name = $row['name'];
                            $mobile = $row['mobile'];
                            $pincode = $row['pincode'];
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
                           
                          
                            $user_info = $this->db->query("SELECT name,phone FROM users WHERE id='$user_id'");
                            $getuser_info = $user_info->row_array();
                            $user_name = $getuser_info['name'];
                            $user_mobile = $getuser_info['phone'];
                            $product_resultpost = array();
                            $prescription_result = array();
                            if ($order_type == 'order') {
                                $order_total = '0';
                                $product_query = $this->db->query("select id as product_order_id,product_unit,product_unit_value,product_id,product_name,product_img,product_quantity,product_discount,product_price,sub_total,product_status,product_status_type,product_status_value,order_status from user_order_product where order_id='$order_id' order by product_order_id asc");
                                $product_count = $product_query->num_rows();
                                if ($product_count > 0) {
                                    foreach ($product_query->result_array() as $product_row) {
                                        $product_order_id = $product_row['product_order_id'];
                                        $product_id = $product_row['product_id'];
                                        $product_name = $product_row['product_name'];
                                        $product_img = "https://d2c8oti4is0ms3.cloudfront.net/images/product_images/" . $product_row['product_img'];
                                        $product_quantity = $product_row['product_quantity'];
                                        $product_discount = $product_row['product_discount'];
                                        $product_price = $product_row['product_price'];
                                        $product_unit = $product_row['product_unit'];
                                        $product_unit_value = $product_row['product_unit_value'];
                                        $sub_total = $product_row['sub_total'];
                                        $product_status = $product_row['product_status'];
                                        $product_status_type = $product_row['product_status_type'];
                                        $product_status_value = $product_row['product_status_value'];
                                        $product_order_status = $product_row['order_status'];
                                        $sub_total_sum1      += $product_price * $product_quantity;
                                         $sub_total_discount +=$product_discount;
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
                                } else {
                                    $product_resultpost = array();
                                }
                            } else {
                                $order_total = '0';
                                $product_query = $this->db->query("SELECT id as product_order_id, order_status,prescription_image FROM prescription_order_details WHERE order_id='$order_id' order by product_order_id asc");
                                $product_count = $product_query->num_rows();
                                if ($product_count > 0) {
                                    foreach ($product_query->result_array() as $product_row) {
                                        $product_order_id = $product_row['product_order_id'];
                                        $product_id = $product_row['product_order_id'];
                                        $product_name = '';
                                        $prescription_image = '';
                                        $product_img = $product_row['prescription_image'];
                                        $images_1 = "";
                                        if (strpos($product_img, '/') == true) {
                                             $images_1 = "https://d2c8oti4is0ms3.cloudfront.net/" . $product_img;
                                        }
                                        else
                                        {
                                            $images_1= "https://d2c8oti4is0ms3.cloudfront.net/images/prescription_images/" . $product_img;
                                        }
                                        
                                        $product_quantity = '';
                                        $product_price = '';
                                        $sub_total = '';
                                        $product_status = '';
                                        $product_status_type = '';
                                        $product_status_value = '';
                                        $product_order_status = $product_row['order_status'];
            
                                        $product_resultpost[] = array(
                                            "product_order_id" => $product_order_id,
                                            "product_id" => $product_id,
                                            "product_name" => $product_name,
                                            "product_img" => $images_1,
                                            "product_quantity" => $product_quantity,
                                            "product_price" => $product_price,
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
                                            $finalamt=$prescription_row['prescription_price']*$prescription_row['prescription_quantity'];
                                            $sub_total_sum2+=$finalamt;
                                            $prescription_name = $prescription_row['prescription_name'];
                                            $prescription_quantity = $prescription_row['prescription_quantity'];
                                            $prescription_price = $prescription_row['prescription_price'];
                                            $prescription_discount = $prescription_row['prescription_discount'];
                                            $prescription_status = $prescription_row['prescription_status'];
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
                            $order_total=$sub_total_sum1+$sub_total_sum2;
                          
                            $order_total_discount=$sub_total_discount+$sub_total_discount1;
                            if($order_total_discount=="")
                            {
                                $order_total_discount=0;
                            }
                            
                            if($amount_from != "" && $amount_to != "" && $discount_from != "" && $discount_to != "")
                            {
                                 if($order_total >= (int)$amount_from && $order_total <= (int)$amount_to && $order_total_discount >= (int)$discount_from && $order_total_discount <= (int)$discount_to)
                                 {
                                     $resultpost[] = array(
                                        "order_id" => $order_id,
                                        "order_type" => $order_type,
                                        "invoice_no" => $invoice_no,
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
                                        "delivery_charge" => $delivery_charge,
                                        "product_order" => $product_resultpost,
                                        "prescription_order" => $prescription_result
                                    );
                                 }
                            }
                            else  if($amount_from != "" && $amount_to != "") 
                            {
                                 if($order_total >= (int)$amount_from && $order_total <= (int)$amount_to )
                                 {
                                     $resultpost[] = array(
                                        "order_id" => $order_id,
                                        "order_type" => $order_type,
                                        "invoice_no" => $invoice_no,
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
                                        "delivery_charge" => $delivery_charge,
                                        "product_order" => $product_resultpost,
                                        "prescription_order" => $prescription_result
                                    );
                                 }
                            }
                            else if($discount_from != "" && $discount_to != "")
                            {
                                 if($order_total_discount >= $discount_from && $order_total_discount <= $discount_to )
                                 {
                                      $resultpost[] = array(
                                        "order_id" => $order_id,
                                        "order_type" => $order_type,
                                        "invoice_no" => $invoice_no,
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
                                        "delivery_charge" => $delivery_charge,
                                        "product_order" => $product_resultpost,
                                        "prescription_order" => $prescription_result
                                    );
                                 }
                            }
                            else
                            {
                                $resultpost[] = array(
                                    "order_id" => $order_id,
                                    "order_type" => $order_type,
                                    "invoice_no" => $invoice_no,
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
                                    "delivery_charge" => $delivery_charge,
                                    "product_order" => $product_resultpost,
                                    "prescription_order" => $prescription_result
                                );
                            }
                        }
                    } else {
                            $resultpost = array();
                        }
                }
                else if($filter == "offline")
                {
                     $order_total=0;
                          $sub_total_sum2=0;
                          $sub_total_sum1=0;
                          $sub_total_discount=0;
                          $sub_total_discount1=0;
                          $where= "";
                            if($from_date != "" && $to_date != "")
                            {
                                 $where .= " AND bill_date>='$from_date' AND bill_date<='$to_date' ";
                            }
                            if($amount_from != "" && $amount_to != "")
                            {
                                 $where .= " AND (final_amount >= '$amount_from' OR final_amount <= '$amount_to') ";
                            }
                            if($discount_from != "" && $discount_to != "")
                            {
                                 $where .= " AND (discount >= '$discount_from' OR discount <= '$discount_to') ";
                            }
                            if($staff_user_id != "")
                            {
                                 $where .= " AND created_by = '$staff_user_id' ";
                            }
                            if($prescription != "")
                            {
                                 $where .= " AND order_type='$prescription' ";
                            }
                        echo "select * from inventory_bill where user_id='$user_id' $where";
                       $bill_order = $this->db->query("select * from inventory_bill where user_id='$user_id' $where"); 
                        $bill_orders_rows = $bill_order->num_rows();
                    
                     if($bill_orders_rows>0)
                             {
                      
                        foreach ($bill_order->result_array() as $row) {
                            $bid =  $row['id'];
                            $order_id = $row['bill_no'];
                            $order_type = "Offline";
                            $invoice_no = $row['bill_no'];
                            $cust_id = $row['cust_id'];
                            
                            $user_info1 = $this->db->query("SELECT * FROM inventory_customer WHERE id='$cust_id'");
                            $user_info1_rows = $user_info1->num_rows();
                            if($user_info1_rows >0)
                            {
                                $getuser_info1 = $user_info1->row_array();
                              
                                $name = $getuser_info1['cname'];
                                $mobile = $getuser_info1['cmobile'];
                                $address1 = $getuser_info1['address'];
                                $address2 = "";
                                $landmark = "";
                                $pincode = $getuser_info1['pincode'];
                                if($pincode==null)
                                {
                                    $pincode ="";
                                }
                                
                                $city = $getuser_info1['city'];
                                if($city==null)
                                {
                                    $city ="";
                                }
                                $state = $getuser_info1['state'];
                                if($state==null)
                                {
                                    $state ="";
                                }
                            }
                            else
                            {
                                 $name = "";
                                $mobile = "";
                                $pincode = "";
                                $address1 = "";
                                $address2 = "";
                                $landmark = "";
                                $city = "";
                                $state ="";
                            }
                            
                            $action_by = "";
                            $payment_method = $row['payment_method'];
                            $order_date = $row['bill_date'];
                            $order_date = date('j M Y h:i A', strtotime($order_date));
                            $delivery_charge = 0;
                            $order_status = "";
                            $order_type = "Offline";
                           
                          
                            $user_info = $this->db->query("SELECT name,phone FROM users WHERE id='$user_id'");
                            $getuser_info = $user_info->row_array();
                            $user_name = $getuser_info['name'];
                            $user_mobile = $getuser_info['phone'];
                            $product_resultpost = array();
                            $prescription_result = array();
                           
                                $order_total = '0';
                                $product_query = $this->db->query("select * from inventory_bill_details where bill_id='$bid' order by id asc");
                                $product_count = $product_query->num_rows();
                                if ($product_count > 0) {
                                    foreach ($product_query->result_array() as $product_row) {
                                        
                                        
                                        $product_order_id = "";
                                        $product_id = $product_row['product_id'];
                                       
                                        
                                        $product = $this->db->query("select * from inventory_product where id='$product_id' order by id asc");
                                        $product_c= $product->num_rows();
                                        if ($product_c > 0) {
                                            $product_result = $product->row_array();
                                            $product_name = $product_result['pname'];
                                            $product_type_id = $product_result['product_type_id'];
                                            $product_img = "https://d2c8oti4is0ms3.cloudfront.net/images/Inventory/" . $product_result['inventory_image'];
                                            $product_units = $this->db->query("select * from inventory_product_type where id='$product_type_id' order by id asc");
                                            $product_u= $product_units->num_rows();
                                            if ($product_u > 0) {
                                                $product_unit_result = $product_units->row_array();
                                                $product_unit = $product_unit_result['unit'];
                                            }
                                            else
                                            {
                                                $product_unit = "";
                                            }
                                        }
                                        else
                                        {
                                            $product_name = "";
                                            $product_img = "";
                                            $product_unit = "";
                                        }
                                        $product_quantity = $product_row['quantity'];
                                        $product_discount = 0;
                                        $product_price = $product_row['price_per_quantity'];
                                       
                                        $product_unit_value = "";
                                        $sub_total = $product_row['total_price'];
                                        $product_status = "";
                                        $product_status_type = "";
                                        $product_status_value = "";
                                        $product_order_status = "";
                                        $sub_total_sum1      += $product_price * $product_quantity;
                                         $sub_total_discount +=$product_discount;
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
                                } else {
                                    $product_resultpost = array();
                                }
                           
                            $order_total=$sub_total_sum1;
                            $order_total_discount=$sub_total_discount;
                            if($order_total_discount=="")
                            {
                                $order_total_discount=0;
                            }
                            else
                            {
                                $order_total_discount;
                            }
                            $resultpost[] = array(
                                "order_id" => $order_id,
                                "order_type" => $order_type,
                                "invoice_no" => $invoice_no,
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
                                "delivery_charge" => $delivery_charge,
                                "product_order" => $product_resultpost,
                                "prescription_order" => $prescription_result
                            );
                        
                    } 
                    
                   
                    
                }
                        else {
                                $resultpost = array();
                            }
                }
                    
                return $resultpost;    
    }*/
    
    /*using*/
    public function product_find($user_id,$hub_user_id,$find)
    {
        $resultpost = array();
        $check_table_existance = $this->PharmacyPartnerModel->stock_table_existance($user_id);
        	       
        if($check_table_existance['status'] == true){
            $table_name = $check_table_existance['table_name'];
            $query = $this->db->query("SELECT ins.gst,ins.quantity,p.id as id,p.product_name,p.image,ins.barcode,ins.selling_price,ins.id as stock_id, ins.batch_no FROM product as p inner join $table_name as ins on ins.product_id=p.id  WHERE p.product_name LIKE '%" .$find."%'  AND ins.quantity > 0  GROUP BY ins.barcode limit 10 ");
            $count = $query->num_rows();
                if ($count > 0) {
                    foreach ($query->result_array() as $row) {
                        $id                     = $row['id'];
                        $pname                  = $row['product_name'];
                        $inventory_image        = $row['image'];
                        $barcode                = $row['barcode'];
                        $price                  = $row['selling_price'];
                        $stock_id               = $row['stock_id'];
                        $batch_no               = $row['batch_no'];
                        $gst                    = $row['gst'];
                        $quantity               = $row['quantity'];
                        $resultpost[] = array(
                                                'id'        => $id,
                                                'pname' => $pname,
                                                'inventory_image' => $inventory_image,
                                                'barcode'         => $barcode,
                                                'cost_price'           => $price,
                                                'selling_price'           => $price,
                                                'stock_id'          => $stock_id,
                                                'batch_no'          => $batch_no,
                                                'gst'          => $gst,
                                                'quantity'     => $quantity
                                                );
                    }
                }
               
        } else {
            $resultpost = array();
        }
            return $resultpost;
    }
    
    /*using*/
    public function inventory_stock_report_list_v1($page,$user_id,$staff_user_id,$find,$filter,$stock_limit,$brand,$purchase_order,$expiry_date_from,$expiry_date_to,$warehouse_id,$pid_sort,$pname_sort)
    {
        $resultpost = array();
        $limit = 15;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        
        
        $whr ="";
        $sort ="";
        if($filter =="all")
        {   if($find !="")
            {
                $whr .= " AND product_name like '%" .$find."%' ";
            }
            if($pid_sort != "")
            {
                $sort = " order by id '$pid_sort' ";
            }
            if($pname_sort != "")
            {
                $sort = " order by product_name $pname_sort ";
            }
            $query = $this->db->query("SELECT * FROM product WHERE (created_by='$user_id' OR created_by='0') $whr $sort LIMIT $start, $limit");
            $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $id                     = $row['id'];
                    $user_id                = $user_id;
                    $vendor_id              = "13";
                    $pname                  = $row['product_name'];
                    $inventory_image        = $row['image'];
                    $expiry_date            = "";
                    $barcode                = "";
                  
                         
                    if ($inventory_image != '') {
                        $profile_pic = $inventory_image;
                        $profile_pic = str_replace(' ', '%20', $profile_pic);
                        $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/Inventory/' . $profile_pic;
                    } else {
                        $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/Inventory/product_default.jpg';
                    }
                    
                     $w_w = "";
                    if($warehouse_id !="")
                    {
                        $w_w = " AND id='$warehouse_id' ";
                    }
                    $ware = array();
                    $check_count=0;
                    
                     if($warehouse_id == "0" || $warehouse_id == "")
                    {
                       
            	       	   // $q->id=0;
            	       	    $w_id = 0;
            	       	    $warehouse_name="In-House";
            	       	
            	       	
            	       	$where = "";
            	       	if($brand != "")
            	       	{
            	       	    $where = " AND distributor_id IN ($brand) ";
            	       	}
            	       	if($expiry_date_from != "" && $expiry_date_to != "")
            	       	{
            	       	    $where .= " AND expiry_date >= $expiry_date_from AND expiry_date <= $expiry_date_to   ";
            	       	}
            	       	$tot_value ="";
            	       	if($purchase_order != "")
            	       	{
            	       	    
            	       	   $sql2="SELECT * FROM inventory_po where user_id='$user_id' AND warehouse_id='$w_id' AND po_status='Created'";
                           $po_det=$this->db->query($sql2);
                           $count_po = $po_det->num_rows();
                           if($count_po > 0)
                           {
                               foreach($po_det->result() as $pos)
                               {
                                   $pos_id = $pos->id;
                                    $sql3="SELECT * FROM inventory_po_details where po_id='$pos_id' AND product_id='$id'";
                                       $po_det3=$this->db->query($sql3);
                                       $count_po3 = $po_det3->num_rows();
                                       if($count_po3 > 0)
                                       {
                                          $pos3 =  $po_det3->row();
                                          $ware[] = array("warehouse"=>"$warehouse_name",
            	       	                 "stock"=> $pos3->quantity,
            	       	                 "expiry_date" => $pos3->expiry_date); 
            	       	                 
            	       	                    $resultpost[] = array(
                                            'id'        => $id,
                                            'user_id' => $user_id,
                                            'vendor_id' => $vendor_id,
                                            'pname' => $pname,
                                            'inventory_image' => $profile_pic,
                                            'expiry_date' => $expiry_date, 
                                            'barcode' => $barcode,
                                            'stock_details' => $ware
                                            );
                                       }
                               }
                           }
                           
                         
            	       	   
            	       	}
            	       	else
            	       	{
            	       	    $check_table_existance = $this->PharmacyPartnerModel->stock_table_existance($user_id);
        	       
                	        if($check_table_existance['status'] == true){
                                $table_name = $check_table_existance['table_name'];
                                	$sql1="SELECT sum(quantity) as stock_quantity,expiry_date FROM $table_name where user_id='$user_id'  AND product_id='$id' AND warehouse_id='$w_id' $where";
                                $stock=$this->db->query($sql1)->row();
                
                	        } else {
                	            $stock = array();
                	        }
                	       
                	       
      
                                        if(!empty($stock))
                	       	            {
                	       	                     $expiry_date = $stock->expiry_date;
                	       	                    if($stock->stock_quantity !=NULL)
                	       	                    {
                	       	                        $stock_quantity=$stock->stock_quantity;
                	       	                        $check_count=1;
                	       	                    }
                	       	                    else
                	       	                    {
                	       	                         $stock_quantity = "0";
                	       	                         if($expiry_date_from == "" && $expiry_date_to == "")
                                            	       	{
                                            	       	   $check_count=1;
                                            	       	}
                                            	       	if($expiry_date == NULL)
                                            	       	{
                                            	       	    $expiry_date = "0000-00-00";
                                            	       	}
                	       	                    }
                	       	            }
                	       	            else
                	       	            {
                	       	                    $stock_quantity = "0";
                	       	                    $expiry_date = "0000-00-00";
                	       	            }
                	       	            
                	       	           
                	       	 $ware[] = array("warehouse"=>"$warehouse_name",
            	       	                 "stock"=> $stock_quantity,
            	       	                 "expiry_date" => $expiry_date);  
            	       	  
            	       	}
                    }
                    
                    $sql="SELECT * FROM inventory_warehouse where (user_id='$user_id' OR user_id='$staff_user_id') $w_w";
                    $warehouse=$this->db->query($sql)->result();
                    if(!empty($warehouse)) {
                        foreach($warehouse as $q) {
                            if($warehouse_id == "0")
                	       	{
                	       	    $q->id=0;
                	       	    $w_id = 0;
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
                	       	$where = "";
                	       	if($brand != "")
                	       	{
                	       	    $where = " AND distributor_id IN ($brand) ";
                	       	}
                	       	if($expiry_date_from != "" && $expiry_date_to != "")
                	       	{
                	       	    $where .= " AND expiry_date >= $expiry_date_from AND expiry_date <= $expiry_date_to   ";
                	       	}
                	       	$tot_value ="";
                	       	if($purchase_order != "")
                	       	{
                	       	    
                	       	   $sql2="SELECT * FROM inventory_po where user_id='$user_id' AND warehouse_id='$w_id' AND po_status='Created'";
                               $po_det=$this->db->query($sql2);
                               $count_po = $po_det->num_rows();
                               if($count_po > 0)
                               {
                                   foreach($po_det->result() as $pos)
                                   {
                                       $pos_id = $pos->id;
                                        $sql3="SELECT * FROM inventory_po_details where po_id='$pos_id' AND product_id='$id'";
                                           $po_det3=$this->db->query($sql3);
                                           $count_po3 = $po_det3->num_rows();
                                           if($count_po3 > 0)
                                           {
                                              $pos3 =  $po_det3->row();
                                              $ware[] = array("warehouse"=>"$warehouse_name",
                	       	                 "stock"=> $pos3->quantity,
                	       	                 "expiry_date" => $pos3->expiry_date); 
                	       	                 
                	       	                    $resultpost[] = array(
                                                'id'        => $id,
                                                'user_id' => $user_id,
                                                'vendor_id' => $vendor_id,
                                                'pname' => $pname,
                                                'inventory_image' => $profile_pic,
                                                'expiry_date' => $expiry_date, 
                                                'barcode' => $barcode,
                                                'stock_details' => $ware
                                                );
                                           }
                                   }
                               }
                               
                             
                	       	   
                	       	}
                	       	else
                	       	{
                	       	$check_table_existance = $this->PharmacyPartnerModel->stock_table_existance($user_id);
        	       
                    	    if($check_table_existance['status'] == true){
                                $table_name = $check_table_existance['table_name'];
                                    
                                $sql1="SELECT sum(quantity) as stock_quantity,expiry_date FROM $table_name where user_id='$user_id'  AND product_id='$id' AND warehouse_id='$w_id' $where";
                                $stock=$this->db->query($sql1)->row();
                    
                    	   }  else {
                    	       $stock = array();
                    	   }
                    	       	
          
                                if(!empty($stock)) {
                    	       	                     $expiry_date = $stock->expiry_date;
                    	       	                    if($stock->stock_quantity !=NULL)
                    	       	                    {
                    	       	                        $stock_quantity=$stock->stock_quantity;
                    	       	                        $check_count=1;
                    	       	                    }
                    	       	                    else
                    	       	                    {
                    	       	                         $stock_quantity = "0";
                    	       	                         if($expiry_date_from == "" && $expiry_date_to == "")
                                                	       	{
                                                	       	   $check_count=1;
                                                	       	}
                                                	       	if($expiry_date == NULL)
                                                	       	{
                                                	       	    $expiry_date = "0000-00-00";
                                                	       	}
                    	       	                    }
                    	       	            }
                    	       	            else
                    	       	            {
                    	       	                    $stock_quantity = "0";
                    	       	                    $expiry_date = "0000-00-00";
                    	       	            }
                    	       	            
                    	       	           
                    	       	 $ware[] = array("warehouse"=>"$warehouse_name",
                	       	                 "stock"=> $stock_quantity,
                	       	                 "expiry_date" => $expiry_date);  
                	       	  
                	       	}
                        }
                    }
                   
                    	if($purchase_order == "" && $check_count ==1)
            	       	{
                            $resultpost[] = array(
                                    'id'        => $id,
                                    'user_id' => $user_id,
                                    'vendor_id' => $vendor_id,
                                    'pname' => $pname,
                                    'inventory_image' => $profile_pic,
                                    'expiry_date' => $expiry_date, 
                                    'barcode' => $barcode,
                                    'stock_details' => $ware
                                    );   
            	       	}
                }
            } else {
                $resultpost = array();
            }
        }
        else if($filter =="no_stock")
        {
            if($find !="")
            {
                $whr .= " AND pname like '%$find% ";
            }
            if($pid_sort != "")
            {
                $sort = " order by id '$pid_sort' ";
            }
            if($pname_sort != "")
            {
                $sort = " order by product_name $pname_sort ";
            }
            $query = $this->db->query("SELECT * FROM product WHERE (created_by='$user_id' OR created_by='0') $whr $sort LIMIT $start, $limit");
            $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $id                     = $row['id'];
                    $user_id                = $user_id;
                    $vendor_id              = "13";
                    $pname                  = $row['product_name'];
                    $inventory_image        = $row['image'];
                    $expiry_date            = "";
                    $barcode                = "";
                   
                         
                    if ($inventory_image != '') {
                        $profile_pic = $inventory_image;
                        $profile_pic = str_replace(' ', '%20', $profile_pic);
                        $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/Inventory/' . $profile_pic;
                    } else {
                        $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/Inventory/product_default.jpg';
                    }
                    
                    $w_w = "";
                    if($warehouse_id !="")
                    {
                        $w_w = " AND id='$warehouse_id' ";
                    }
                    
                    $ware = array();
                    $check_count=0;
                    if($warehouse_id == "0" || $warehouse_id == "")
                    {
                        
            	       	    $w_id = 0;
            	       	    $warehouse_name="In-House";
            	       	
            	       	
            	       	$where = "";
            	       	if($brand != "")
            	       	{
            	       	    $where = " AND distributor_id IN ($brand) ";
            	       	}
            	       	if($expiry_date_from != "" && $expiry_date_to != "")
            	       	{
            	       	    $where .= " AND expiry_date >= $expiry_date_from AND expiry_date <= $expiry_date_to   ";
            	       	}
            	       	$tot_value ="";
            	       	if($purchase_order != "")
            	       	{
            	       	    
            	       	   $sql2="SELECT * FROM inventory_po where user_id='$user_id' AND warehouse_id='$w_id' AND po_status='Created'";
                           $po_det=$this->db->query($sql2);
                           $count_po = $po_det->num_rows();
                           if($count_po > 0)
                           {
                               foreach($po_det->result() as $pos)
                               {
                                   $pos_id = $pos->id;
                                    $sql3="SELECT * FROM inventory_po_details where po_id='$pos_id' AND product_id='$id'";
                                       $po_det3=$this->db->query($sql3);
                                       $count_po3 = $po_det3->num_rows();
                                       if($count_po3 > 0)
                                       {
                                          $pos3 =  $po_det3->row();
                                          $ware[] = array("warehouse"=>"$warehouse_name",
            	       	                 "stock"=> $pos3->quantity,
            	       	                 "expiry_date" => $pos3->expiry_date); 
            	       	                 
            	       	                    $resultpost[] = array(
                                            'id'        => $id,
                                            'user_id' => $user_id,
                                            'vendor_id' => $vendor_id,
                                            'pname' => $pname,
                                            'inventory_image' => $profile_pic,
                                            'expiry_date' => $expiry_date, 
                                            'barcode' => $barcode,
                                            'stock_details' => $ware
                                            );
                                       }
                               }
                           }
                           
                         
            	       	   
            	       	}
            	       	else
            	       	{
            	       	    $check_table_existance = $this->PharmacyPartnerModel->stock_table_existance($user_id);
        	                if($check_table_existance['status'] == true){
                                $table_name = $check_table_existance['table_name'];
                                
                    	       	$sql1="SELECT sum(quantity) as stock_quantity,expiry_date FROM $table_name where user_id='$user_id'  AND product_id='$id' AND warehouse_id='$w_id' $where";
                                $stock=$this->db->query($sql1)->row();
                    	    } else {
                    	        $stock = array();
                    	    }
   
                                    if(!empty($stock))
            	       	            {
            	       	                    
            	       	                    if($stock->stock_quantity ==NULL || $stock->stock_quantity <= 0)
            	       	                    {
            	       	                       /* $stock_quantity=$stock->stock_quantity;
            	       	                         $expiry_date = $stock->expiry_date;
            	       	                         	if($expiry_date == NULL)
                                            	       	{
                                            	       	    $expiry_date = "0000-00-00";
                                            	       	}
            	       	                       
            	       	                            $ware[] = array("warehouse"=>"$warehouse_name",
            	       	                                           "stock"=> $stock_quantity,
            	       	                                           "expiry_date"=> $expiry_date); 
            	       	                        
            	       	                        $check_count =1;*/
            	       	                        
            	       	                         $stock_quantity = "0";
            	       	                          $expiry_date = $stock->expiry_date;
            	       	                          	if($expiry_date == NULL)
                                            	       	{
                                            	       	    $expiry_date = "0000-00-00";
                                            	       	}
            	       	                         $ware[] = array("warehouse"=>"$warehouse_name",
            	       	                                           "stock"=> $stock_quantity,
            	       	                                           "expiry_date"=> $expiry_date); 
            	       	                         if($expiry_date_from == "" && $expiry_date_to == "")
                                            	 {
                                            	     $check_count=1;
                                            	 }
                                            	 
            	       	                    }
            	       	                   /* else
            	       	                    {
            	       	                         $stock_quantity = "No-Stock";
            	       	                          $expiry_date = $stock->expiry_date;
            	       	                          	if($expiry_date == NULL)
                                            	       	{
                                            	       	    $expiry_date = "0000-00-00";
                                            	       	}
            	       	                         $ware[] = array("warehouse"=>"$warehouse_name",
            	       	                                           "stock"=> $stock_quantity,
            	       	                                           "expiry_date"=> $expiry_date); 
            	       	                         if($expiry_date_from == "" && $expiry_date_to == "")
                                            	 {
                                            	     $check_count=1;
                                            	 }
            	       	                    }*/
            	       	                     
            	       	                    
            	       	            }
            	       	            else
            	       	            {
            	       	                    $stock_quantity = "0";
            	       	                     $expiry_date = $stock->expiry_date;
            	       	                     	if($expiry_date == NULL)
                                            	       	{
                                            	       	    $expiry_date = "0000-00-00";
                                            	       	}
            	       	                    $ware[] = array("warehouse"=>"$warehouse_name",
            	       	                                           "stock"=> $stock_quantity,
            	       	                                           "expiry_date"=> $expiry_date); 
            	       	            }
            	       	           
            	       	}
            	       	
                    }
                    
                    $sql="SELECT * FROM inventory_warehouse where (user_id='$user_id' OR user_id='$staff_user_id') $w_w";
                    $warehouse=$this->db->query($sql)->result();
                    if(!empty($warehouse)) {
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
                	       	$where = "";
                	       	if($brand != "")
                	       	{
                	       	    $where = " AND distributor_id IN ($brand) ";
                	       	}
                	       	if($expiry_date_from != "" && $expiry_date_to != "")
                	       	{
                	       	    $where .= " AND expiry_date >= $expiry_date_from AND expiry_date <= $expiry_date_to   ";
                	       	}
                	       	$tot_value ="";
                	       	if($purchase_order != "")
                	       	{
                	       	    
                	       	   $sql2="SELECT * FROM inventory_po where user_id='$user_id' AND warehouse_id='$w_id' AND po_status='Created'";
                               $po_det=$this->db->query($sql2);
                               $count_po = $po_det->num_rows();
                               if($count_po > 0)
                               {
                                   foreach($po_det->result() as $pos)
                                   {
                                       $pos_id = $pos->id;
                                        $sql3="SELECT * FROM inventory_po_details where po_id='$pos_id' AND product_id='$id'";
                                           $po_det3=$this->db->query($sql3);
                                           $count_po3 = $po_det3->num_rows();
                                           if($count_po3 > 0)
                                           {
                                              $pos3 =  $po_det3->row();
                                              $ware[] = array("warehouse"=>"$warehouse_name",
                	       	                 "stock"=> $pos3->quantity,
                	       	                 "expiry_date" => $pos3->expiry_date); 
                	       	                 
                	       	                    $resultpost[] = array(
                                                'id'        => $id,
                                                'user_id' => $user_id,
                                                'vendor_id' => $vendor_id,
                                                'pname' => $pname,
                                                'inventory_image' => $profile_pic,
                                                'expiry_date' => $expiry_date, 
                                                'barcode' => $barcode,
                                                'stock_details' => $ware
                                                );
                                           }
                                   }
                               }
                               
                             
                	       	   
                	       	}
                	       	else
                	       	{
                	       	    $check_table_existance = $this->PharmacyPartnerModel->stock_table_existance($user_id);
            	                if($check_table_existance['status'] == true){
                                    $table_name = $check_table_existance['table_name'];
                                    
                        	       	$sql1="SELECT sum(quantity) as stock_quantity, expiry_date FROM $table_name where user_id='$user_id'  AND product_id='$id' AND warehouse_id='$w_id' $where";
                                    $stock=$this->db->query($sql1)->row();
                
                        	    } else {
                        	        $stock = array();
                        	    }
                                        if(!empty($stock))
                	       	            {
                	       	                    
                	       	               if($stock->stock_quantity ==NULL || $stock->stock_quantity <= 0)
            	       	                    {
            	       	                       
            	       	                        
            	       	                         $stock_quantity = "0";
            	       	                          $expiry_date = $stock->expiry_date;
            	       	                          	if($expiry_date == NULL)
                                            	       	{
                                            	       	    $expiry_date = "0000-00-00";
                                            	       	}
            	       	                         $ware[] = array("warehouse"=>"$warehouse_name",
            	       	                                           "stock"=> $stock_quantity,
            	       	                                           "expiry_date"=> $expiry_date); 
            	       	                         if($expiry_date_from == "" && $expiry_date_to == "")
                                            	 {
                                            	     $check_count=1;
                                            	 }
                                            	 
            	       	                    }
            	       	                   
            	       	                     
                	       	            }
                	       	            else
                	       	            {
                	       	                    $stock_quantity = "0";
                	       	                     $expiry_date = $stock->expiry_date;
                	       	                     	if($expiry_date == NULL)
                                                	       	{
                                                	       	    $expiry_date = "0000-00-00";
                                                	       	}
                	       	                    $ware[] = array("warehouse"=>"$warehouse_name",
                	       	                                           "stock"=> $stock_quantity,
                	       	                                           "expiry_date"=> $expiry_date); 
                	       	            }
                	       	}
                        }
                    }
                   
                    if(!empty($ware))
                    {
                        	if($purchase_order == "" && $check_count==1)
            	       	    {
                                $resultpost[] = array(
                                                'id'        => $id,
                                                'user_id' => $user_id,
                                                'vendor_id' => $vendor_id,
                                                'pname' => $pname,
                                                'inventory_image' => $profile_pic,
                                                'expiry_date' => $expiry_date, 
                                                'barcode' => $barcode,
                                                'stock_details' => $ware
                                                );
            	       	    }
                    }
                   
                }
            } else {
                $resultpost = array();
            }
        }
        else if($filter =="surplus" || $filter =="lowstock")
        {
            if($find !="")
            {
                $whr .= " AND pname like '%$find% ";
            }
            $query = $this->db->query("SELECT * FROM product WHERE (created_by='$user_id' OR created_by='0') $whr LIMIT $start, $limit");
            $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $id                     = $row['id'];
                    $user_id                = $user_id;
                    $vendor_id              = "13";
                    $pname                  = $row['product_name'];
                    $inventory_image        = $row['image'];
                    $expiry_date            = "";
                    $barcode                = "";
                         
                    if ($inventory_image != '') {
                        $profile_pic = $inventory_image;
                        $profile_pic = str_replace(' ', '%20', $profile_pic);
                        $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/Inventory/' . $profile_pic;
                    } else {
                        $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/Inventory/product_default.jpg';
                    }
                    
                    $ware = array();
                    $check_count = 0;
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
            	       	
            	       	$where = "";
            	       	if($brand != "")
            	       	{
            	       	    $where = " AND distributor_id IN ($brand) ";
            	       	}
            	       	if($expiry_date_from != "" && $expiry_date_to != "")
            	       	{
            	       	    $where .= " AND expiry_date >= $expiry_date_from AND expiry_date <= $expiry_date_to   ";
            	       	}
            	       	 	$tot_value ="";
            	       	if($purchase_order != "")
            	       	{
            	       	    
            	       	   $sql2="SELECT * FROM inventory_po where user_id='$user_id' AND warehouse_id='$w_id' AND po_status='Created'";
                           $po_det=$this->db->query($sql2);
                           $count_po = $po_det->num_rows();
                           if($count_po > 0)
                           {
                               foreach($po_det->result() as $pos)
                               {
                                   $pos_id = $pos->id;
                                    $sql3="SELECT * FROM inventory_po_details where po_id='$pos_id' AND product_id='$id'";
                                       $po_det3=$this->db->query($sql3);
                                       $count_po3 = $po_det3->num_rows();
                                       if($count_po3 > 0)
                                       {
                                          $pos3 =  $po_det3->row();
                                          $ware[] = array("warehouse"=>"$warehouse_name",
            	       	                 "stock"=> $pos3->quantity,
            	       	                 "expiry_date" => $pos3->expiry_date); 
            	       	                 
            	       	                    $resultpost[] = array(
                                            'id'        => $id,
                                            'user_id' => $user_id,
                                            'vendor_id' => $vendor_id,
                                            'pname' => $pname,
                                            'inventory_image' => $profile_pic,
                                            'expiry_date' => $expiry_date, 
                                            'barcode' => $barcode,
                                            'stock_details' => $ware
                                            );
                                       }
                               }
                           }
                           
                         
            	       	   
            	       	}
            	       	else
            	       	{
            	       	    
            	       	    $check_table_existance = $this->PharmacyPartnerModel->stock_table_existance($user_id);
        	                if($check_table_existance['status'] == true){
                                $table_name = $check_table_existance['table_name'];
                                
                    	       	$sql1="SELECT sum(quantity) as stock_quantity,expiry_date FROM $table_name where user_id='$user_id'  AND product_id='$id' AND warehouse_id='$w_id' $where";
                                $stock=$this->db->query($sql1)->row();
            
                    	    } else {
                    	        $stock = array();
                    	    }
                    	    
            	       	
   
                                    if(!empty($stock))
            	       	            {
            	       	                    
            	       	                    if($stock->stock_quantity !=NULL)
            	       	                    {
            	       	                        $stock_quantity=$stock->stock_quantity;
            	       	                         $expiry_date = $stock->expiry_date;
            	       	                         	if($expiry_date == NULL)
                                            	       	{
                                            	       	    $expiry_date = "0000-00-00";
                                            	       	}
            	       	                        if($filter == "surplus")
            	       	                        {
                	       	                        if($stock_quantity >= $stock_limit)
                	       	                        {
                	       	                            $ware[] = array("warehouse"=>"$warehouse_name",
                	       	                                           "stock"=> $stock_quantity,
                	       	                                           "expiry_date"=>$expiry_date); 
                	       	                        }
            	       	                        }
            	       	                        if($filter == "lowstock")
            	       	                        {
            	       	                             $expiry_date = $stock->expiry_date;
            	       	                             	if($expiry_date == NULL)
                                            	       	{
                                            	       	    $expiry_date = "0000-00-00";
                                            	       	}
                	       	                        if($stock_quantity <= $stock_limit)
                	       	                        {
                	       	                            $ware[] = array("warehouse"=>"$warehouse_name",
                	       	                                           "stock"=> $stock_quantity,
                	       	                                           "expiry_date"=>$expiry_date); 
                	       	                        }
            	       	                        }
            	       	                        $check_count = 1;
            	       	                    }
            	       	                   
            	       	            }
            	       	}
                    }
                    
                    if(!empty($ware))
                    {
                        if($purchase_order != "" && $check_count == 1)
            	       	{
                            $resultpost[] = array(
                                            'id'        => $id,
                                            'user_id' => $user_id,
                                            'vendor_id' => $vendor_id,
                                            'pname' => $pname,
                                            'inventory_image' => $profile_pic,
                                            'expiry_date' => $expiry_date, 
                                            'barcode' => $barcode,
                                            'stock_details' => $ware
                                            );
            	       	}
                    }
                   
                }
            } else {
                $resultpost = array();
            }
        }
        
        return $resultpost;
        
    } 
    
    public function inventory_manufacturer_list($user_id,$staff_user_id,$page)
    {
        if($page==""){
         $page=1;   
        }
      
        $limit = 2;
      
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
      
       $query = $this->db->query("SELECT * FROM inventory_distributor WHERE user_id='$user_id' LIMIT $start, $limit");
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
                                'manufacturer_name' => $manufacturer_name,
                                'manufacturer_phone' => $manufacturer_phone,
                                'distributor_name' => $distributor_name,
                                'distributor_phone' => $distributor_phone,
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
    public function buy_pbioms($user_id,$admin,$staff,$total_amount,$gst)
    {
        $month1 ="1";
        $effectiveDate1 = date('Y-m-d');        
        $effectiveDate = date('Y-m-d', strtotime("$month1 year", strtotime($effectiveDate1)));
        
        $result = array(
                                'user_id' => $user_id,
                                'total_admin' => $admin,
                                'total_staff' => $staff,
                                'amount' => $total_amount,
                                'gst' => $gst,
                                'activate' => '1',
                                'created_by' => $user_id,
                                'created_at' => date('Y-m-d H:i:s'),
                                'book_date' => date('Y-m-d H:i:s'),
                                'start_date' => date('Y-m-d'),
                                'end_date' => $effectiveDate
                                );
        $this->db->insert('pbioms_buy_details',$result) ;
        
         $pbioms_record = $this->db->select('*')->from('pbioms_buy_details')->where('user_id', $user_id)->where('activate', '1')->get()->result();
            if(!empty($pbioms_record))
            {
                $pbioms =1;
                $pbioms_count = $this->db->select('count(*) as cnt')->from('users')->where('staff_hub_user_id', $user_id)->get()->row();
                $staff_used = $pbioms_count->cnt ;
                
                $total_admin = 0;
                $admin_used = 0;
                $total_staff = 0;
                
                foreach($pbioms_record as $pbioms_record1)
                {
                    $total_admin += $pbioms_record1->total_admin;
                    $total_staff += $pbioms_record1->total_staff;
                }
            }
            else
            {
                $pbioms = 0;
                $total_admin = 0;
                $admin_used = 0;
                $total_staff = 0;
                $staff_used = 0;
            }
            $query = $this->db->query("UPDATE medical_stores SET pbioms ='1' WHERE user_id='$user_id'");
        return $resultpost = array("status"=>"200",
                            "message" => "success",
                             'pbioms' => "$pbioms",
                            'total_admin' => "$total_admin",
                            'admin_used' => "$total_admin",
                            'total_staff' => "$total_staff",
                            'staff_used' => $staff_used
                            );
        
    } 
    
    public function pbioms_booking_details($user_id)
    {
        $resultpost_detail = array();
        $pbioms_record = $this->db->select('*')->from('pbioms_buy_details')->where('user_id', $user_id)->where('activate', '1')->get()->result();
            if(!empty($pbioms_record))
            {
                $pbioms =1;
                $pbioms_count = $this->db->select('count(*) as cnt')->from('users')->where('staff_hub_user_id', $user_id)->get()->row();
                $staff_used = $pbioms_count->cnt ;
                
                $total_admin = 0;
                
                $pbioms_count_ad = $this->db->select('count(*) as cnt')->from('users')->where('admin_hub_user_id', $user_id)->get()->row();
                $admin_used = $pbioms_count_ad->cnt ;
                
                $total_staff = 0;
                
                foreach($pbioms_record as $pbioms_record1)
                {
                    $total_admin += $pbioms_record1->total_admin;
                    $total_staff += $pbioms_record1->total_staff;
                    
                    $resultpost_detail[] = array(
                            "admin" => $pbioms_record1->total_admin,
                             'staff' => $pbioms_record1->total_staff,
                            'book_date' => $pbioms_record1->book_date,
                            'start_date' => $pbioms_record1->start_date,
                            'end_date' => $pbioms_record1->end_date,
                            'activate' => $pbioms_record1->activate,
                            );
                    
                }
            }
            else
            {
                $pbioms = 0;
                $total_admin = 0;
                $admin_used = 0;
                $total_staff = 0;
                $staff_used = 0;
            }
            
            $pbioms_price = $this->db->select('*')->from('pbioms_package_master')->where('active', '0')->get()->row();
            if(!empty($pbioms_price))
            {
                $admin_price = $pbioms_price->admin_price;
                $staff_price = $pbioms_price->staff_price;
                $year_price = $pbioms_price->year_price;
            }
            else
            {
                $admin_price = 0;
                $staff_price = 0;
                $year_price = 0;
            }
            
            $pbioms_record1 = $this->db->select('end_date')->from('pbioms_buy_details')->where('user_id', $user_id)->where('activate', '1')->get()->row();
            if(!empty($pbioms_record1))
            {
                $final_end_date = $pbioms_record1->end_date;
            }
            else
            {
                $final_end_date = "";
            }
        return $resultpost = array("status"=>"200",
                            "message" => "success",
                            'pbioms' => "$pbioms",
                            'total_admin' => "$total_admin",
                            'admin_used' => "$admin_used",
                            'total_staff' => "$total_staff",
                            'staff_used' => $staff_used,
                            'admin_final_price' => $admin_price,
                            'staff_final_price' => $staff_price,
                            'year_final_price' => $year_price,
                            'server_date' => date('Y-m-d'),
                            'final_end_date' => $final_end_date,
                            'details' => $resultpost_detail
                            );
        
    } 
      public function Notification_All_list($user_id,$page)
    {
        if($page==""){
         $page=1;   
        }
        $count1 = 0;
      //  $radius = $page*5;
        $limit = 10;
        $limitk = 20;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        $startk = ($page - 1) * $limitk;

        //$query = $this->db->query("SELECT * FROM `All_notification_Mobile` where (user_id = '$user_id' AND notification_type != 'user_feedback' AND notification_type != 'healthwall_notifications')  OR (listing_id = '$user_id' AND notification_type != 'view_notifications' AND notification_type != 'order') order by id desc LIMIT $start, $limit");
       $query =$this->db->query("SELECT * FROM `All_notification_Mobile` where (user_id = '$user_id' AND notification_type != 'user_feedback' AND notification_type != 'healthwall_notifications')  OR user_id = '$user_id'
UNION ALL
SELECT * FROM `other_notifications` where user_id = '$user_id' order by notification_date  desc  LIMIT $start, $limit");
        $count = $query->num_rows();
        if ($count > 0) {
            
            $query1 = $this->db->query("SELECT * FROM `All_notification_Mobile` where (user_id = '$user_id' AND notification_type != 'user_feedback' AND notification_type != 'healthwall_notifications')  OR user_id = '$user_id' ");
            $count1 = $query1->num_rows();
        
        
            foreach($query->result_array() as $row_main)
             {
                  $id  = $row_main['id'];
                  $user_id = $row_main['user_id'];
                  $listing_name = $row_main['listing_name'];
                  $listing_id = $row_main['listing_id'];
                  $post_id = $row_main['post_id'];
                  $order_id = $row_main['order_id'];
                  $order_date = date('Y-m-d',strtotime($row_main['order_date']));
                  $order_status = $row_main['order_status'];
                  $booking_id = $row_main['booking_id'];
                  $invoice_no = $row_main['invoice_no'];
                  $notification_type= $row_main['notification_type'];
                  $notification_date = date('Y-m-d',strtotime($row_main['notification_date']));
                  $package_id  = $row_main['package_id'];
                  $package_name  = $row_main['package_name'];
                  $title= $row_main['title'];
                  $list_type = $row_main['list_type'];
                  $message = $row_main['msg'];
                  $img_url = $row_main['img_url'];
                  $tag  = $row_main['tag'];
                  $healthmall_id  = $row_main['healthmall_id'];
                  $pdf_link  = $row_main['pdf_link'];
                  $booking_time  = $row_main['booking_time'];
                  $booking_from  = $row_main['booking_from'];
                  $booking_to  = $row_main['booking_to'];
                  $is_read  = $row_main['is_read'];
                  $presription_id= $row_main['presription_id'];
                  $article_id = $row_main['article_id'];
                  $med_video_id = $row_main['med_video_id'];
                  $datetime = $row_main['created_at'];
                  if($is_read==0)
                  {
                      $read = "false";
                  }
                  else
                  { 
                      $read = "true";
                  } 
                  $resultpost[] = array(
                      'id'  => $id,
                      'user_id' => $user_id,
                      'listing_name' => $listing_name,
                      'listing_id' => $listing_id,
                      'post_id'   => $post_id,
                      'order_id'  => $order_id,
                      'order_date' => $order_date,
                      'order_status' => $order_status,
                      'booking_id'  => $booking_id,
                      'presription_id' => $presription_id,
                      'article_id' => $article_id,
                      'invoice_no' => $invoice_no,
                      'notification_type' => $notification_type,
                      'notification_date' => $datetime,
                      'title'  => $title,
                      'package_id'  => $package_id,
                      'package_name'  => $package_name,
                      'message' => $message,
                      'img_url'  => $img_url,
                      'tag'  => $tag,
                      'list_type' => $list_type,
                      'healthmall_id' => $healthmall_id,
                      'pdf_link' => $pdf_link,
                      'booking_time' => $booking_time,
                      'booking_from' => $booking_from,
                      'booking_to' => $booking_to,
                      'med_video_id' => $med_video_id,
                      'is_read' => $read
                     
                      );
                  
             }
        } else {
            $resultpost = array();
        }
      
        return array(
                    'status' => 200,
                    'message' => 'success',
                    'count' => $count1,
                    'data' => $resultpost
                    );
    }
    public function Notification_All_list_v1($user_id,$page,$type)
    {
        $resultpost = array();
        if($page==""){
         $page=1;   
        }
        $count=0;
        $count1 = 0;
      //$radius = $page*5;
        $limit = 10;
        $limitk = 20;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        $startk = ($page - 1) * $limitk;


        if($type=="promo"){
        echo "hii";    
        $query = $this->db->query("SELECT * FROM `other_notifications` where (user_id = '$user_id' ) and   notification_type!='article' and notification_type!='HealthDictionary' order by notification_date desc LIMIT $start, $limit");
        $count = $query->num_rows();
        if ($count > 0) {
            
            foreach($query->result_array() as $row_main)
               {
                  $id  = $row_main['id'];
                  $user_id = $row_main['user_id'];
                  $listing_name = $row_main['listing_name'];
                  $listing_id = $row_main['listing_id'];
                  $post_id = $row_main['post_id'];
                  $order_id = $row_main['order_id'];
                  $order_date = date('Y-m-d',strtotime($row_main['order_date']));
                  $order_status = $row_main['order_status'];
                  $booking_id = $row_main['booking_id'];
                  $invoice_no = $row_main['invoice_no'];
                  $notification_type= $row_main['notification_type'];
                  $notification_date = date('Y-m-d',strtotime($row_main['notification_date']));
                  $package_id  = $row_main['package_id'];
                  $package_name  = $row_main['package_name'];
                  $title= $row_main['title'];
                  $list_type = $row_main['list_type'];
                  $message = $row_main['msg'];
                  $img_url = $row_main['img_url'];
                  $tag  = $row_main['tag'];
                  $healthmall_id  = $row_main['healthmall_id'];
                  $pdf_link  = $row_main['pdf_link'];
                  $booking_time  = $row_main['booking_time'];
                  $booking_from  = $row_main['booking_from'];
                  $booking_to  = $row_main['booking_to'];
                  $is_read  = $row_main['is_read'];
                  $presription_id= $row_main['presription_id'];
                  $article_id = $row_main['article_id'];
                  $med_video_id = $row_main['med_video_id'];
                  $datetime = $row_main['created_at'];
                  if($is_read==0)
                  {
                      $read = "false";
                  }
                  else
                  { 
                      $read = "true";
                  } 
                  $resultpost[] = array(
                      'id'  => $id,
                      'user_id' => $user_id,
                      'listing_name' => $listing_name,
                      'listing_id' => $listing_id,
                      'post_id'   => $post_id,
                      'order_id'  => $order_id,
                      'order_date' => $order_date,
                      'order_status' => $order_status,
                      'booking_id'  => $booking_id,
                      'presription_id' => $presription_id,
                      'article_id' => $article_id,
                      'invoice_no' => $invoice_no,
                      'notification_type' => $notification_type,
                      'notification_date' => $datetime,
                      'title'  => $title,
                      'package_id'  => $package_id,
                      'package_name'  => $package_name,
                      'message' => $message,
                      'img_url'  => $img_url,
                      'tag'  => $tag,
                      'list_type' => $list_type,
                      'healthmall_id' => $healthmall_id,
                      'pdf_link' => $pdf_link,
                      'booking_time' => $booking_time,
                      'booking_from' => $booking_from,
                      'booking_to' => $booking_to,
                      'med_video_id' => $med_video_id,
                      'is_read' => $read
                     
                      );
                  
                         }
            } else {
                $resultpost = array();
            }
        }
        
      /*  if($type=="learn"){
        $query = $this->db->query("SELECT * FROM `other_notifications` where (user_id = '$user_id' ) and notification_type='article' || notification_type='HealthDictionary' order by notification_date desc LIMIT $start, $limit");
        $count = $query->num_rows();
        if ($count > 0) {
            
        
            foreach($query->result_array() as $row_main)
               {
                  $id  = $row_main['id'];
                  $user_id = $row_main['user_id'];
                  $listing_name = $row_main['listing_name'];
                  $listing_id = $row_main['listing_id'];
                  $post_id = $row_main['post_id'];
                  $order_id = $row_main['order_id'];
                  $order_date = date('Y-m-d',strtotime($row_main['order_date']));
                  $order_status = $row_main['order_status'];
                  $booking_id = $row_main['booking_id'];
                  $invoice_no = $row_main['invoice_no'];
                  $notification_type= $row_main['notification_type'];
                  $notification_date = date('Y-m-d',strtotime($row_main['notification_date']));
                  $package_id  = $row_main['package_id'];
                  $package_name  = $row_main['package_name'];
                  $title= $row_main['title'];
                  $list_type = $row_main['list_type'];
                  $message = $row_main['msg'];
                  $img_url = $row_main['img_url'];
                  $tag  = $row_main['tag'];
                  $healthmall_id  = $row_main['healthmall_id'];
                  $pdf_link  = $row_main['pdf_link'];
                  $booking_time  = $row_main['booking_time'];
                  $booking_from  = $row_main['booking_from'];
                  $booking_to  = $row_main['booking_to'];
                  $is_read  = $row_main['is_read'];
                  $presription_id= $row_main['presription_id'];
                  $article_id = $row_main['article_id'];
                  $med_video_id = $row_main['med_video_id'];
                  $datetime = $row_main['created_at'];
                  if($is_read==0)
                  {
                      $read = "false";
                  }
                  else
                  { 
                      $read = "true";
                  } 
                  $resultpost[] = array(
                      'id'  => $id,
                      'user_id' => $user_id,
                      'listing_name' => $listing_name,
                      'listing_id' => $listing_id,
                      'post_id'   => $post_id,
                      'order_id'  => $order_id,
                      'order_date' => $order_date,
                      'order_status' => $order_status,
                      'booking_id'  => $booking_id,
                      'presription_id' => $presription_id,
                      'article_id' => $article_id,
                      'invoice_no' => $invoice_no,
                      'notification_type' => $notification_type,
                      'notification_date' => $datetime,
                      'title'  => $title,
                      'package_id'  => $package_id,
                      'package_name'  => $package_name,
                      'message' => $message,
                      'img_url'  => $img_url,
                      'tag'  => $tag,
                      'list_type' => $list_type,
                      'healthmall_id' => $healthmall_id,
                      'pdf_link' => $pdf_link,
                      'booking_time' => $booking_time,
                      'booking_from' => $booking_from,
                      'booking_to' => $booking_to,
                      'med_video_id' => $med_video_id,
                      'is_read' => $read
                     
                      );
                  
                         }
            } else {
                $resultpost = array();
            }
        }   
            
        if($type=="trans"){
                
                $query = $this->db->query("SELECT * FROM `All_notification_Mobile` where (user_id = '$user_id' AND notification_type != 'user_feedback' AND notification_type != 'healthwall_notifications')  OR (listing_id = '$user_id' AND notification_type != 'view_notifications' AND notification_type != 'order') order by id desc LIMIT $start, $limit");
        $count = $query->num_rows();
        if ($count > 0) {
            
            $query1 = $this->db->query("SELECT * FROM `All_notification_Mobile` where (user_id = '$user_id' AND notification_type != 'user_feedback' AND notification_type != 'healthwall_notifications')  OR (listing_id = '$user_id' AND notification_type != 'view_notifications' AND notification_type != 'order') ");
            $count1 = $query1->num_rows();
        
        
            foreach($query->result_array() as $row_main)
             {
                  $id  = $row_main['id'];
                  $user_id = $row_main['user_id'];
                  $listing_name = $row_main['listing_name'];
                  $listing_id = $row_main['listing_id'];
                  $post_id = $row_main['post_id'];
                  $order_id = $row_main['order_id'];
                  $order_date = date('Y-m-d',strtotime($row_main['order_date']));
                  $order_status = $row_main['order_status'];
                  $booking_id = $row_main['booking_id'];
                  $invoice_no = $row_main['invoice_no'];
                  $notification_type= $row_main['notification_type'];
                  $notification_date = date('Y-m-d',strtotime($row_main['notification_date']));
                  $package_id  = $row_main['package_id'];
                  $package_name  = $row_main['package_name'];
                  $title= $row_main['title'];
                  $list_type = $row_main['list_type'];
                  $message = $row_main['msg'];
                  $img_url = $row_main['img_url'];
                  $tag  = $row_main['tag'];
                  $healthmall_id  = $row_main['healthmall_id'];
                  $pdf_link  = $row_main['pdf_link'];
                  $booking_time  = $row_main['booking_time'];
                  $booking_from  = $row_main['booking_from'];
                  $booking_to  = $row_main['booking_to'];
                  $is_read  = $row_main['is_read'];
                  $presription_id= $row_main['presription_id'];
                  $article_id = $row_main['article_id'];
                  $med_video_id = $row_main['med_video_id'];
                  $datetime = $row_main['created_at'];
                  if($is_read==0)
                  {
                      $read = "false";
                  }
                  else
                  { 
                      $read = "true";
                  } 
                  $resultpost[] = array(
                      'id'  => $id,
                      'user_id' => $user_id,
                      'listing_name' => $listing_name,
                      'listing_id' => $listing_id,
                      'post_id'   => $post_id,
                      'order_id'  => $order_id,
                      'order_date' => $order_date,
                      'order_status' => $order_status,
                      'booking_id'  => $booking_id,
                      'presription_id' => $presription_id,
                      'article_id' => $article_id,
                      'invoice_no' => $invoice_no,
                      'notification_type' => $notification_type,
                      'notification_date' => $datetime,
                      'title'  => $title,
                      'package_id'  => $package_id,
                      'package_name'  => $package_name,
                      'message' => $message,
                      'img_url'  => $img_url,
                      'tag'  => $tag,
                      'list_type' => $list_type,
                      'healthmall_id' => $healthmall_id,
                      'pdf_link' => $pdf_link,
                      'booking_time' => $booking_time,
                      'booking_from' => $booking_from,
                      'booking_to' => $booking_to,
                      'med_video_id' => $med_video_id,
                      'is_read' => $read
                     
                      );
                  
                         }
                    } else {
                        $resultpost = array();
                    }
            }
            
        if($type=="myactivity"){
                
                $query = $this->db->query("SELECT * FROM `myactivity_notification` where (user_id = '$user_id' AND notification_type != 'user_feedback' AND notification_type != 'healthwall_notifications')  OR (listing_id = '$user_id' AND notification_type != 'view_notifications' AND notification_type != 'order') order by id desc LIMIT $start, $limit");
        $count = $query->num_rows();
        if ($count > 0) {
            
            $query1 = $this->db->query("SELECT * FROM `myactivity_notification` where (user_id = '$user_id' AND notification_type != 'user_feedback' AND notification_type != 'healthwall_notifications')  OR (listing_id = '$user_id' AND notification_type != 'view_notifications' AND notification_type != 'order') ");
            $count1 = $query1->num_rows();
        
        
            foreach($query->result_array() as $row_main)
             {
                  $id  = $row_main['id'];
                  $user_id = $row_main['user_id'];
                  $listing_name = $row_main['listing_name'];
                  $listing_id = $row_main['listing_id'];
                  $post_id = $row_main['post_id'];
                  $order_id = $row_main['order_id'];
                  $order_date = date('Y-m-d',strtotime($row_main['order_date']));
                  $order_status = $row_main['order_status'];
                  $booking_id = $row_main['booking_id'];
                  $invoice_no = $row_main['invoice_no'];
                  $notification_type= $row_main['notification_type'];
                  $notification_date = date('Y-m-d',strtotime($row_main['notification_date']));
                  $package_id  = $row_main['package_id'];
                  $package_name  = $row_main['package_name'];
                  $title= $row_main['title'];
                  $list_type = $row_main['list_type'];
                  $message = $row_main['msg'];
                  $img_url = $row_main['img_url'];
                  $tag  = $row_main['tag'];
                  $healthmall_id  = $row_main['healthmall_id'];
                  $pdf_link  = $row_main['pdf_link'];
                  $booking_time  = $row_main['booking_time'];
                  $booking_from  = $row_main['booking_from'];
                  $booking_to  = $row_main['booking_to'];
                  $is_read  = $row_main['is_read'];
                  $presription_id= $row_main['presription_id'];
                  $article_id = $row_main['article_id'];
                  $med_video_id = $row_main['med_video_id'];
                  $datetime = $row_main['created_at'];
                  if($is_read==0)
                  {
                      $read = "false";
                  }
                  else
                  { 
                      $read = "true";
                  } 
                  $resultpost[] = array(
                      'id'  => $id,
                      'user_id' => $user_id,
                      'listing_name' => $listing_name,
                      'listing_id' => $listing_id,
                      'post_id'   => $post_id,
                      'order_id'  => $order_id,
                      'order_date' => $order_date,
                      'order_status' => $order_status,
                      'booking_id'  => $booking_id,
                      'presription_id' => $presription_id,
                      'article_id' => $article_id,
                      'invoice_no' => $invoice_no,
                      'notification_type' => $notification_type,
                      'notification_date' => $datetime,
                      'title'  => $title,
                      'package_id'  => $package_id,
                      'package_name'  => $package_name,
                      'message' => $message,
                      'img_url'  => $img_url,
                      'tag'  => $tag,
                      'list_type' => $list_type,
                      'healthmall_id' => $healthmall_id,
                      'pdf_link' => $pdf_link,
                      'booking_time' => $booking_time,
                      'booking_from' => $booking_from,
                      'booking_to' => $booking_to,
                      'med_video_id' => $med_video_id,
                      'is_read' => $read
                     
                      );
                  
                         }
                    } else {
                        $resultpost = array();
                    }
            }*/
            
      
        return array(
                    'status' => 200,
                    'message' => 'success',
                    'count' => $count,
                    'data' => $resultpost
                    );
    }
     public function Notification_read_update($user_id,$noti_id)
    {
         $updated_at = date('Y-m-d H:i:s');
         $query = $this->db->query("UPDATE All_notification_Mobile SET is_read='1' WHERE id = '$noti_id'");
         
        if($this->db->affected_rows()>0)
        {
                 return array(
                    'status' => 200,
                    'message' => 'Success'
                );
        }
        else
        {
       
                return array(
                    'status' => 201,
                    'message' => 'Already Updated'
                );
            
        }
    }
     public function Notification_read_update_v1($user_id,$noti_id,$type)
    {
         $updated_at = date('Y-m-d H:i:s');
          if($type=="promo"){
         $query = $this->db->query("UPDATE other_notifications  WHERE id = '$noti_id'");
          }
             if($type=="trans"){
         $query = $this->db->query("UPDATE All_notification_Mobile SET is_read='1' WHERE (user_id='$user_id' || listing_id = '$user_id') &&  id = '$noti_id'");
          }
             if($type=="myactivity"){
         $query = $this->db->query("UPDATE myactivity_notification SET is_read='1' WHERE (user_id='$user_id' || listing_id = '$user_id') &&  id = '$noti_id'");
          }
            
        if($this->db->affected_rows()>0)
        {
                 return array(
                    'status' => 200,
                    'message' => 'Success'
                );
        }
        else
        {
       
                return array(
                    'status' => 201,
                    'message' => 'Already Updated'
                );
            
        }
    }
    public function Notification_Delete($user_id,$id)
    {
        
            if($id == '')
            {
               
                $count_query1 = $this->db->query("DELETE FROM `All_notification_Mobile` where user_id = '$user_id' OR post_id = '$user_id'");
                return array(
                    'status' => 200,
                    'message' => 'success',
                    );
            }
            else
            {
                $exp_noti = explode(',',$id);
                if(count($exp_noti)>0)
                {
                     
                    for($ip=0;$ip<count($exp_noti);$ip++)
                    {
                         
                        $ids=$exp_noti[$ip];
                        $count_query1 = $this->db->query("DELETE FROM `All_notification_Mobile` where id = '$ids'");
                    }
                }
                else
                {
                     
                  $count_query1 = $this->db->query("DELETE FROM `All_notification_Mobile` where id = '$id'");
                }
                   return array(
                    'status' => 200,
                    'message' => 'success',
                    );
            }
      
    }
    
    public function Notification_Delete_v1($user_id,$id,$type)
    {
        
            if($id == '')
            {
               
                $count_query1 = $this->db->query("DELETE FROM `All_notification_Mobile` where user_id = '$user_id' OR post_id = '$user_id'");
                return array(
                    'status' => 200,
                    'message' => 'success',
                    );
            }
            else
            {
                $exp_noti = explode(',',$id);
                if(count($exp_noti)>0)
                {
                     
                    for($ip=0;$ip<count($exp_noti);$ip++)
                    {
                         
                        $ids=$exp_noti[$ip];
                        if($type=="promo"){
                     $count_query1 = $this->db->query("DELETE FROM other_notifications WHERE id = '$ids'");
                      }
                        if($type=="trans"){
                     $count_query1 = $this->db->query("DELETE FROM All_notification_Mobile WHERE id = '$ids'");
                      }
                        if($type=="myactivity"){
                     $count_query1 = $this->db->query("DELETE FROM myactivity_notification  WHERE  id = '$ids'");
                      } 
                    }
                }
                else
                {
                        if($type=="promo"){
                     $count_query1 = $this->db->query("DELETE other_notifications WHERE id = '$id'");
                      }
                        if($type=="trans"){
                     $count_query1 = $this->db->query("DELETE All_notification_Mobile WHERE id = '$id'");
                      }
                        if($type=="myactivity"){
                     $count_query1 = $this->db->query("DELETE myactivity_notification  WHERE  id = '$id'");
                      } 
                 // $count_query1 = $this->db->query("DELETE FROM `All_notification_Mobile` where id = '$id'");
                }
                   return array(
                    'status' => 200,
                    'message' => 'success',
                    );
            }
      
    }
    
    public function check_contact_barcode($user_id,$number){
        
        // get entered number by customer and initialize all variables
        $customer_id = "";
        $getuserAddresses = $customer_details = $user_by_contact = array();
        $status = 0;
        $userInfo = array();
        // 1. check with users table's phone : if exists goto 4 else goto 2
       $user_by_contact = $this->db->query("SELECT `id` as user_id,`name`,`phone`,`email`  FROM `users` WHERE `phone` LIKE '%$number%' AND `vendor_id` = 0 && `is_active` = 1 ")->row_array();
        
       
        if(sizeof($user_by_contact) > 0){
            $customer_id = $user_by_contact['user_id']; 
        } else {
            $customer_id = "";
        }
        
        // 2. check with user_privilage_card table's card_no column : if exists goto  4  else  goto 3
        
        if($customer_id == ""){
            $privilage_card_details = $this->db->query("SELECT * FROM `user_privilage_card` WHERE `card_no` LIKE '%$number%'")->row_array();
            if(sizeof($privilage_card_details) > 0){
                $customer_id = $privilage_card_details['user_id'];
                
            } else {
                $customer_id = "";
            }
            
           
        }
        
        // 3. check with user_priviladge_card_new table's card_no column : if exists goto  4  else  goto 5
        
       if($customer_id == ""){
             $user_priviladge_card_new = $this->db->query("SELECT * FROM `user_priviladge_card_new` WHERE `card_no` LIKE '%$number%'  ")->row_array();
             if(sizeof($user_priviladge_card_new) > 0){
                $customer_id = $user_priviladge_card_new['user_id'];
            } else {
                $customer_id = "";
            }
        }
        
        if($customer_id > 0){
             // 4. get address of customer from user_address 
            $getuserAddresses = $this->LoginModel->address_list($customer_id);
            
            if(sizeof($user_by_contact) > 0){
                $customer_details = $user_by_contact;
            } else {
                $customer_details = $this->db->query("SELECT `id` as user_id,`name`,`phone`,`email`  FROM `users` WHERE `id` = '$customer_id' AND `vendor_id` = 0 && `is_active` = 1 ")->row_array();
            }
            
            
            
        } else {
             // 5. no user found
             $status = 2;
             
        }
    
        if(sizeof($customer_details) > 0){
            $userInfo = $customer_details;
            $userInfo['address'] =  $getuserAddresses;
            $status = 1;
        } else {
            $userInfo = (object)[];
            $status = 2;
        }
       
        
         // statuses =>  1: found user 2: user not found 
        $data['status'] = $status;
        $data['data'] = $userInfo; 
        
        return $data;
            
    }
    
    public function estimate_cost_mno($user_id,$invoice_no,$bill,$only_view){
        $this->load->model('PartnermnoModel');
        $this->load->model('PaymentModel');
        $drivingDistanceInKM = $delivery_time_in_hrs_mins = $delivery_time = $completedKms = $drivingDistance = $drivingTime = $estimated_delivery_time = $drivingDistance = $total_price = $tax = $chc = $discountInRupees = $DeliveryCharges = $net_amount =  0;
        $orderDetails = $this->db->query("SELECT uo.*, ms.lat as pharma_lat, ms.lng as pharma_lng FROM user_order as uo left join medical_stores as ms on ((uo.listing_id = ms.user_id AND ms.pharmacy_branch_user_id = 0) OR (ms.user_id > 0 AND ms.pharmacy_branch_user_id = uo.listing_id)) WHERE uo.invoice_no = '$invoice_no'  GROUP by ms.user_id")->row_array();
  
        $actual_cost  = $orderDetails['actual_cost'] ;
        $order_total  = $orderDetails['order_total'] ;
        $discountInRupees  = $orderDetails['discount'] ;
        $gst  = $orderDetails['gst'] ;
        $customer_lat = $orderDetails['lat'];
        $customer_lng = $orderDetails['lng'];
        $pharma_lat = $orderDetails['pharma_lat'];
        $pharma_lng = $orderDetails['pharma_lng'];
        $payment_method = $orderDetails['payment_method'];
        $order_id = $orderDetails['order_id'];
     //   $user_id = $orderDetails['user_id'];
            
            
            
            if($pharma_lat != "" && $pharma_lng != ""){
                
                $deliveryTimeAndCostMno = $this->PartnermnoModel->get_delivery_time_cost($order_id);
               $estimated_delivery_time = $deliveryTimeAndCostMno['delivery_time'];
               
                $delivery_time_in_hrs_mins = $deliveryTimeAndCostMno['delivery_time_in_hrs_mins'];
                $delivery_time = $deliveryTimeAndCostMno['delivery_time'];
                $DeliveryCharges = $deliveryTimeAndCostMno['delivery_charges'];
                $drivingDistance = $deliveryTimeAndCostMno['total_distance'];
                $drivingDistanceInKM = ($drivingDistance / 1000);
                $drivingDistanceInKM = number_format($drivingDistanceInKM,2). " Km";
                
            }     
      
        if (strpos($payment_method, 'Cash') !== false) {
            //$chc = $order_total * 0.02;
            $chc = 0;
        }
        // gst in rupees
        if ($gst > 0) {
            $discountedValue = $order_total - $discountInRupees; 
            
            // tax on discounted value
            $tax = ($discountedValue * $gst) / 100;
        } else {
           $tax = 0; 
        }
        
        // update user_order
        //swapnali :  if $only_view == 1 then update user_order 
        if($only_view != 1){
            $this->db->query("UPDATE `user_order` SET `chc` = '$chc',`delivery_charge` = '$DeliveryCharges', `order_deliver_by`='mno' WHERE `invoice_no` = '$invoice_no'");
        }
      
        // final amount
   
        $net_amount = $order_total + $tax + $chc - $discountInRupees  + $DeliveryCharges ;
            
        $data = array(
            'bill_link' => $bill,
            'estimated_delivery_time' => strval($delivery_time_in_hrs_mins),
            'estimated_delivery_time_sec' => strval($delivery_time),
            'approx_distance_meter' => strval($drivingDistance),
            'approx_distance' => strval($drivingDistanceInKM),
            'sub_total' => strval($order_total),
            'gst' => strval($tax),
            'cash_handling_charges' => '0',
            'discount' => strval($discountInRupees),
            'delivery_charge' => strval($DeliveryCharges),
            'grand_total' => strval($net_amount),
            'invoice_no' => strval($invoice_no),
            'lat' => strval($customer_lat),
            'lng' => strval($customer_lng),
            'order_id' => intval($order_id),
            'user_id' => $user_id
            
        );
        
        
        $data1 = $this->PaymentModel->get_invoice_costing($invoice_no);
        $data2 = $data1['data'];
       //      print_r($data); die();
       $real = array_merge($data2,$data);
      
        return $real;
    //    print_r($data); die();
    }
    
    
    // user notifications
    
       public function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent, $order_type) {
        
              
        
            if (!defined("GOOGLE_GCM_URL"))
                define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
            $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "message" => $msg, "image" => $img_url, "tag" => $tag,"notification_image" => $img_url, "notification_date" => $order_date,"notification_type" => $order_type, "order_status" => $order_status, "order_date" => $order_date, "invoice_no" => $invoice_no, "order_id" => $invoice_no, "name" => $name, "listing_name" => $listing_name)
            );
            $headers = array(
                GOOGLE_GCM_URL,
                'Content-Type: application/json',
                $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
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
            //echo $result;
            //print_r($result);
        }
        public function send_gcm_notify_usr($title, $reg_id, $msg, $img_url, $tag, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent, $order_type) {
           
            if (!defined("GOOGLE_GCM_URL"))
                define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
            $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "message" => $msg, "image" => $img_url, "tag" => $tag, "notification_image" => $img_url,"notification_date" => $order_date,"notification_type" => $order_type, "order_status" => $order_status, "order_date" => $order_date, "invoice_no" => $invoice_no, "order_id" => $invoice_no, "name" => $name, "listing_name" => $listing_name)
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
            //echo $result;
        }
     
    
    public function responce_user_behalf($invoice_no, $order_status, $cancel_reason, $payment_method) {
        $order_id = "";
        $this->load->model('PartnermnoModel');
        $this->load->model('OrderModel');
          
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d');
        $updated_at = date('Y-m-d H:i:s');
        $order_type_query = $this->db->query("select order_type,user_id,listing_type,listing_name,order_deliver_by from user_order where invoice_no='$invoice_no' ");
        $get_order_info = $order_type_query->row_array();
        $order_type = $get_order_info['order_type'];
        $user_id = $get_order_info['user_id'];
        $listing_type = $get_order_info['listing_type'];
        $listing_name = $get_order_info['listing_name'];
        $order_deliver_by = $get_order_info['order_deliver_by'];

        
        if ($order_status == 'Order Confirmed') {
            
            // i
            $update = $this->db->query("UPDATE `user_order` SET `updated_at`='$updated_at',`order_status`='Order Confirmed',`payment_method`='$order_status',`cancel_reason`='',`action_by`='customer' WHERE invoice_no='$invoice_no'");

            //$invno = $order_id;
            $action_by_status = "Pharmacy";
            $orderStatus = "Order Confirmed by ".$listing_name." pharmacy behalf of customer";
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
            $token_status = $order_info->token_status;
            if ($token_status > 0) {
                $reg_id = $order_info->token;
                $agent = $order_info->agent;
                $user_email = $order_info->email;
                $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png';
                $tag = 'text';
                $title = 'Order Confirmed by pharmacy behalf of you';
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
                $message = 'Order confirmed from the customer, Kindly deliver the order.';
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
                
                
                // notification to mno
       
       
                if($order_deliver_by == 'mno' || $listing_type == '44'){
                    $sql = "SELECT mo.mno_id , uo.user_id as customer_id,uo.delivery_charge,uo.delivery_charges_by_customer, uo.delivery_charges_by_vendor, uo.delivery_charges_by_mw,  uo.total_without_dc,uo.listing_type,uo.payment_method,uo.order_total, uo.listing_id,  uo.delivery_charges_to_mno, uo.listing_id,uo.name as user_name FROM `user_order` as uo left join mno_orders as mo on (mo.invoice_no = uo.invoice_no and mo.ongoing = 1) WHERE uo.`invoice_no` = '$invoice_no' ";
                        $existingOrder = $this->db->query($sql)->row_array();
                        if(sizeof($existingOrder) > 0){
                            $mno_id = $existingOrder['mno_id'];
                            $user_name = $existingOrder['user_name'];
                       
                          //  $invno = $order_id;
                        if($mno_id > 0 && $mno_id != ""){
                             $receiver_id = $mno_id;
                            $notification_type = 8; //CUSTOMER_ACCEPTED_ORDER refer mno_notification_types
                            $img = "https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png";
                            $mno_order_id = $existingOrder['id'];
                            // $title = "Order cancelled by you behalf of customer ";
                            $title = "Order confirmed by pharmacy  behalf of customer ".$user_name;
                            $msg ="Order confirmed by pharmacy  behalf of customer ".$user_name."of order id ".$invoice_no;
                            
                            
                           //  $upateMNOOrder = $this->db->query("UPDATE `mno_orders` SET cancel_reason_after_accept = '$cancel_reason', ongoing = 0 where id= '$mno_order_id'");
                
                    
                            $response = $this->PartnermnoModel->send_gcm_notification_to_mno($receiver_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
                        
                        }
                       
                    }
                    
                }
                
                
                
            
            return array(
                'status' => 201,
                'message' => 'Order Confirmed'
            );
        }
        if ($order_status == 'Order Cancelled') {
            $res_status = $this->db->query("select order_status from user_order where invoice_no='$invoice_no' limit 1");
            $o_status = $res_status->row_array();
            $check_status = $o_status['order_status'];
            if ($check_status == 'Order Delivered') {
                return array(
                    'status' => 201,
                    'message' => 'Order Delivered'
                );
            } else {
                $update = $this->db->query("UPDATE `user_order` SET `updated_at`='$updated_at',`order_status`='Order Cancelled',`cancel_reason`='$cancel_reason',`action_by`='customer' WHERE invoice_no='$invoice_no'");
                
              //  $invno = $order_id;
                  
                
                if ($update) {
                    
                    
                      $action_by_status = "Pharmacy";
                    $orderStatus = "Order cancelled by ".$listing_name." pharmacy behalf of customer";
                    
                    $this->OrderModel->update_status( $invoice_no,$orderStatus, $action_by_status);
                    
                    
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
                        $msg = 'Order Cancelled by pharmacy behalf of you';
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
                            $msg = 'Order Cancelled by you behalf of pharamcy';
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
                        
                        // notification to mno
       
       
                    if($order_deliver_by == 'mno' || $listing_type == '44'){
                        $sql = "SELECT mo.mno_id , uo.user_id as customer_id,uo.delivery_charge,uo.delivery_charges_by_customer, uo.delivery_charges_by_vendor, uo.delivery_charges_by_mw,  uo.total_without_dc,uo.listing_type,uo.payment_method,uo.order_total, uo.listing_id,  uo.delivery_charges_to_mno, uo.listing_id,uo.name as user_name FROM `user_order` as uo left join mno_orders as mo on (mo.invoice_no = uo.invoice_no and mo.ongoing = 1) WHERE uo.`invoice_no` = '$invoice_no' ";
                        $existingOrder = $this->db->query($sql)->row_array();
                        if(sizeof($existingOrder) > 0){
                            $mno_id = $existingOrder['mno_id'];
                            $user_name = $existingOrder['user_name'];
                              //  $invno = $order_id;
                            if($mno_id > 0 && $mno_id != ""){
                                $receiver_id = $mno_id;
                                $notification_type = 9; //CUSTOMER_REJECTED_ORDER refer mno_notification_types
                                $img = "https://s3.amazonaws.com/medicalwale/images/img/pharmacy.png";
                                $mno_order_id = $existingOrder['id'];
                                // $title = "Order cancelled by you behalf of customer ";
                                $title = "Order cancelled by pharmacy  behalf of customer ".$user_name;
                                $msg ="Order cancelled by pharmacy  behalf of customer ".$user_name."of order id ".$invoice_no;
                                
                                
                                 $upateMNOOrder = $this->db->query("UPDATE `mno_orders` SET  ongoing = 0 where id= '$mno_order_id'");
                    
                        
                                $response = $this->PartnermnoModel->send_gcm_notification_to_mno($receiver_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
                            
                            }
                            
                        }
                        
                    }
                    
                        return array(
                            'status' => 201,
                            'message' => 'Order Cancelled'
                        );
                } else {
                    return array(
                        'status' => 201,
                        'message' => 'failed'
                    );
                }
            }
        }
    }
    
    
    // call mno()
    
    public function call_mno(){
        
    }
    
    //cashcheque
    public function cashcheque($amount, $vendor_id, $vendor_name, $max_usage_day, $min_order, $max_order, $expiry_day,$first_txn,$save_type,$type,$id)
    {
        
        if($type=="add")
        {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d');
        $updated_at = date('Y-m-d H:i:s');
         $user_data = array(
                    'amount' => $amount,
                    'vendor_id' => $vendor_id,
                    'vendor_name' => $vendor_name,
                    'max_usage_day' => $max_usage_day,
                    'min_order' => $min_order,
                    'max_order' => $max_order,
                    'expiry_day' => $expiry_day,
                    'first_txn' => $first_txn,
                    'creation_date' => $updated_at,
                    'save_type'=>   $save_type
               
                );
                
                
                
                $success = $this->db->insert('cash_cheque_master', $user_data);
                $id      = $this->db->insert_id();
                if(!empty($id))
                {
                     return array(
                            'status' => 200,
                            'message' => 'success'
                        );
                }
                else
                {
                     return array(
                            'status' => 201,
                            'message' => 'failed'
                        );
                }
        }
        else
        {
            date_default_timezone_set('Asia/Kolkata');
            $date = date('Y-m-d');
            $updated_at = date('Y-m-d H:i:s');
            $user_data = array(
                        'amount' => $amount,
                        'vendor_id' => $vendor_id,
                        'vendor_name' => $vendor_name,
                        'max_usage_day' => $max_usage_day,
                        'min_order' => $min_order,
                        'max_order' => $max_order,
                        'expiry_day' => $expiry_day,
                        'first_txn' => $first_txn,
                        'creation_date' => $updated_at,
                        'save_type'=>   $save_type
                   
                    );
                
                
               $this->db->where('id', $id);
               $this->db->where('vendor_id', $vendor_id);
               $this->db->update('cash_cheque_master', $user_data);
              
                     return array(
                            'status' => 200,
                            'message' => 'success'
                        );
               
        }
    }
    
    public function cashcheque_list($vendor_id)
    {
      $cash_return=array();  
      $sel="SELECT * FROM `cash_cheque_master` where vendor_id='$vendor_id'";
        $query = $this->db->query($sel);
      $count = $query->num_rows();
        if($count>0)
          {
            foreach ($query->result_array() as $row) 
                    {
                      $id=$row['id'];    
                      $amount=$row['amount'];
                      $type=$row['save_type'];
                      $listing_id=$row['vendor_id'];
                      $listing_name=$row['vendor_name'];
                      $day=$row['max_usage_day'];
                      $min_order=$row['min_order'];
                      $max_order=$row['max_order'];
                      $expriy=$row['expiry_day'];
                      $first_txn=$row['first_txn'];
                      $status = $row['active'];
                      
                       $cash_return[] = array('id'=> $id,
                                             'amount' => $amount,
                                             'type' => $type,
                                             'listing_id' => $listing_id,
                                             'listing_name' => $listing_name,
                                             'day' => $day,
                                             'min_order' => $min_order,
                                             'max_order'=>$max_order,
                                             'expriy_day'=>$expriy,
                                             'first_txn'=>$first_txn,
                                             'status' =>$status
                                     );
                      
                       foreach($cash_return as $key => $value)
                                {
                                    
                                    if($value == null){
                                        $cash_return[$key] = "";
                                    }
                                }
                                
                      
                    }
          }
          
           return $cash_return;
    }
    
     public function cashcheque_delete($vendor_id,$id)
    {
      $querys = $this->db->query("UPDATE `cash_cheque_master` SET `active`='1' WHERE vendor_id='$vendor_id' and id='$id'");
        return array(
            'status' => 200,
            'message' => 'success'
            );
    }
     
     
   public function order_tracking_mno($user_id, $invoice_no){
     
       $delivery_time = $drivingTime = $mno_orders_id = 0;
         $completedKms = $drivingDistance = $drivingTime = $estimated_delivery_time = $drivingDistance = $total_price = $tax = $chc = $discountInRupees = $DeliveryCharges = $net_amount =  0;
        $delivery_time_in_hrs_mins = "";
        $customer_name = $customer_contact = $estimated_time = "";
        $this->load->model('All_booking_model');
        $this->load->model('PartnermnoModel');
        $this->load->model('user_mnoModel');
        $customer_lat = $customer_lng = $store_lat = $store_lng = $mno_lat = $mno_lng = $mno_name = $mno_contact = "";
         
        $tracker = array();
       
        $data = $details = array();
        // Done made chances by swapnali on 8th nov 2019 - if parent (hub) is loggedin the show all orders
        $order_details = $this->db->query("SELECT uo.listing_id ,uo.name as customer_name, uo.mobile as customer_contact,ms.user_id,ms.pharmacy_branch_user_id, uo.invoice_no,uo.user_id,uo.listing_id,uo.lat as customer_lat,uo.lng as customer_lng,ms.lat as store_lat,ms.lng as store_lng, msp.lat as suggested_pharmacy_lat, msp.lng as suggested_pharmacy_lng FROM `user_order` as uo left join medical_stores as ms on ((ms.user_id = '$user_id' && ms.pharmacy_branch_user_id = 0) || (ms.pharmacy_branch_user_id = '$user_id' && ms.pharmacy_branch_user_id != 0)) left join mno_suggested_pharmacies as msp on (msp.id = uo.suggested_pharmacy_id)    WHERE uo.`invoice_no` LIKE '$invoice_no'  GROUP by uo.invoice_no")->row_array();
        if(sizeof($order_details) > 0){
            $data['status'] = 1; // data found 
            $get_mno_details = $this->db->query("SELECT uo.order_id , mo.*, ml.phone, ml.mno_name FROM `mno_orders` as mo left join user_order as uo on (mo.invoice_no = uo.invoice_no) left join mno_list as ml on (ml.mno_id = mo.mno_id) WHERE mo.`invoice_no` LIKE '$invoice_no' AND  (mo.status = 'accepted' OR mo.status = '')  GROUP BY uo.order_id order by mo.id desc")->row_array();
            $mno_orders_id = $get_mno_details['id'];
            $status =  $get_mno_details['status'];
            $mno_id = $get_mno_details['mno_id'];
            $order_id = $get_mno_details['order_id'];
            
            $customer_lat = $order_details['customer_lat'];
            $customer_lng = $order_details['customer_lng'];
            $customer_name = $order_details['customer_name'];
            $customer_contact = $order_details['customer_contact'];
            $mno_contact = $get_mno_details['phone'];
            $mno_name = $get_mno_details['mno_name'];
            //   print_r($get_mno_details); die();
            if($status == "accepted"){
       
              
                $deliveryTimeAndCostMno = $this->PartnermnoModel->get_delivery_time_cost($order_id);
                $delivery_charges = $deliveryTimeAndCostMno['delivery_charges'];
                $delivery_time = $deliveryTimeAndCostMno['delivery_time'];
                $delivery_time_in_hrs_mins = $deliveryTimeAndCostMno['delivery_time_in_hrs_mins'];
                
                $store_lat = $deliveryTimeAndCostMno['store_lat'];
                $store_lng = $deliveryTimeAndCostMno['store_lng'];
                $mno_lat = $deliveryTimeAndCostMno['mno_lat'];
                $mno_lng = $deliveryTimeAndCostMno['mno_lng'];
                
            } else {
                $data['status'] = 3;
            }
            
        } else {
            $data['status'] = 2;  // no data found
        }
        
       
        // $tracker = $this->All_booking_model->pharmacy_tracker($user_id, $invoice_no);
                
        // $tracker = $this->user_mnoModel->tracker_notifications_for_users_mno($user_id,$invoice_no, $mno_orders_id);
    
        $tracker = $this->user_mnoModel->order_tracker_for_users($user_id,$invoice_no);
        
        $details['customer_lat'] = floatval($customer_lat);
        $details['customer_lng'] = floatval($customer_lng);
        $details['customer_name'] = $customer_name;
        $details['customer_contact'] = $customer_contact;
        $details['store_lat'] = floatval($store_lat);
        $details['store_lng'] = floatval($store_lng);
        $details['mno_lat'] = floatval($mno_lat);
        $details['mno_lng'] = floatval($mno_lng);
        $details['mno_name'] = $mno_name;
        $details['mno_contact'] = $mno_contact;
        $details['estimated_time'] = intval($delivery_time);
        $details['estimated_time_mins'] = $delivery_time_in_hrs_mins;
        $details['tracker'] = $tracker;     
        
       
     
        $data['data'] = $details;
        // print_r($data); die();
              
        return $data;
    }
    
    // get_branches
    public function get_branches($user_id){
        $data = array();
        //$medical_stores = $this->db->query("SELECT medical_name,pharmacy_branch_user_id,lat,lng,address1,address2,city,state,pincode FROM medical_stores where user_id='$user_id'  AND pharmacy_branch_user_id!='0' AND `is_active` = 1")->result_array();
        $medical_stores = $this->db->query("SELECT user_id,medical_name,pharmacy_branch_user_id,lat,lng,address1,address2,city,state,pincode FROM medical_stores where user_id='$user_id'  AND is_active = '1' order by id asc ")->result_array();
       
        if(sizeof($medical_stores) > 0) {
            $data['status'] = 1; // data found
            $data['data'] = $medical_stores;
        } else {
            $data['status'] = 2; // data not found
            
        }
        return $data;
    }
    
    
    public function payment_accepted($user_id,$invoice_no, $payment_id, $amount){
        
        $this->load->model('LedgerModel');
        $this->load->model('PartnermnoModel');
        $this->load->model('OrderModel');
        $data = array();
        $status = 0;
        // pending -> add payment accept by pharmacy new api
           
        //   if amount is greater than 0 then go to next step else goto X - done
        // first check is any order exists - done
        // check payment is exists - done
        // create ledger : in user order as user debited given $amount - done
        // send notification to user as well as pharmacy
        // X : Send payment not eccept from user notification to pharmacy 
       
        $sql = "SELECT  uo.user_id as customer_id,uo.delivery_charge,uo.delivery_charges_by_customer, uo.delivery_charges_by_vendor, uo.delivery_charges_by_mw,  uo.total_without_dc,uo.listing_type,uo.payment_method,uo.order_total, uo.listing_id,  uo.delivery_charges_to_mno, uo.listing_id,uo.name as user_name FROM `user_order` as uo WHERE uo.`invoice_no` = '$invoice_no' AND uo.`listing_id` = '$user_id' ";
        $existingOrder = $this->db->query($sql)->row_array();
      
        if(sizeof($existingOrder) > 0){
            $customer_id = $existingOrder['customer_id'];
            $customer_id_type = 0;
            $delivery_charge = $existingOrder['delivery_charge'];
            $delivery_charges_by_customer = $existingOrder['delivery_charges_by_customer']; 
            $delivery_charges_by_vendor = $existingOrder['delivery_charges_by_vendor']; 
            $delivery_charges_by_mw = $existingOrder['delivery_charges_by_mw'];  
            $total_without_dc = $existingOrder['total_without_dc'];
            $listing_type = $existingOrder['listing_type'];
            $payment_method = $existingOrder['payment_method'];
            $order_total = $existingOrder['order_total']; 
            $listing_id = $existingOrder['listing_id'];  
            $delivery_charges_to_mno = $existingOrder['delivery_charges_to_mno']; 
            $user_name = $existingOrder['user_name'];
            
            if($amount > 0){
                $paymentMethods = $this->db->query("SELECT * FROM `payment_method` WHERE `parent_id` = '$payment_id'")->result_array(); 
                if(sizeof($paymentMethods) > 0){
                    $status = 4; //please send subtype id
                } else {
                    $paymentMethodsSelected = $this->db->query("SELECT * FROM `payment_method` WHERE `id` = '$payment_id'")->row_array(); 
                    if(sizeof($paymentMethodsSelected) > 0){
                        $paymentMethosName = $paymentMethodsSelected['payment_method'];
                        
                        $order_type = 1;
            
                   // add for each transaction
                    $user_comments = "";
                    $mw_comments = "Payment accepted from customer by pharmacy";
                    $vendor_comments = "";
                    $payment_method = $payment_id;
                    $credit = "";
                    $debit = $amount;
                    $transaction_of = 2; // entry for package
                    // $payment_method = $paymentMethodsSelected['id'];
                    $transaction_id = "";
                    $trans_status = 1;
                    
                    // Order to user : amount entry from user to pharmacy
                    
                    // NOT - WORKING
                    $transaction_date = "";
                    $vendor_id = $listing_type;
                    $array_data = array();
                     $res = $this->LedgerModel->create_ledger($customer_id, $invoice_no, $customer_id_type, $listing_id, $listing_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status,$transaction_date,$vendor_id,$array_data);
                     
                    // print_r($res); die();
                    
                        if($listing_id != "" && $listing_id != 0 && $listing_id != ""){
                            $pharmacyDetails = $this->db->query("SELECT agent as ph_agent, token as ph_token from users where id = '$listing_id'")->row_array();
                            $agent = $pharmacyDetails['ph_agent'];
                            $reg_id = $pharmacyDetails['ph_token'];
                            
                              //send notificaion to vendor
                        
                            $title = "Delivery guy did not get any payment from customer";
                            // $msg = "Night owl accepted  payment using $paymentMethosName";
                            $msg = "Delivery guy did not get any payment from customer" ;
                            $img_url = 'https://medicalwale.com/img/noti_icon.png';
                            $tag = "";
                            $notification_type = "payment_accepted";
                            $this->PharmacyPartnerModel->send_gcm_notify_pharmacy($title, $reg_id, $msg, $img_url, $tag, $agent, $invoice_no,$notification_type);
                            
                             
                        }
                        
                        $status = 1; // success
                        
                    } else {
                        $status = 5; // No payment method found
                    }
                }
            } else {
                
                if($listing_id != "" && $listing_id != 0 && $listing_id != ""){
                    $pharmacyDetails = $this->db->query("SELECT agent as ph_agent, token as ph_token from users where id = '$listing_id'")->row_array();
                    $agent = $pharmacyDetails['ph_agent'];
                    $reg_id = $pharmacyDetails['ph_token'];
                    
                      //send notificaion to vendor
                
                    $title = "Delivery guy did not get any payment from customer";
                    // $msg = "Night owl accepted  payment using $paymentMethosName";
                    $msg = "Delivery guy did not get any payment from customer" ;
                    $img_url = 'https://medicalwale.com/img/noti_icon.png';
                    $tag = "";
                    $notification_type = "payment_accepted";
                    $this->PharmacyPartnerModel->send_gcm_notify_pharmacy($title, $reg_id, $msg, $img_url, $tag, $agent, $invoice_no,$notification_type);
                     $status = 1; // success
                } 
                
            } 
        } else {
            $status = 2; // order not found
        }
        
        
        $data['status'] = $status;
        return $data;
        
    }
    

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
    
    /*using*/
    public function product_barcode_scanner_v2($user_id,$hub_user_id,$barcode)
    {
        $stock_quantity = 0;
        $check_table_existance = $this->PharmacyPartnerModel->stock_table_existance($user_id);
        	       
        if($check_table_existance['status'] == true){
            $table_name = $check_table_existance['table_name'];
            $query = $this->db->query("SELECT ins.gst,ins.id as stock_id,ins.batch_no,ins.expiry_date,ins.mgf_date,pr.image,ins.quantity,inc.category as category_name,ind.distributor_name,ind.manufacturer_name, pr.product_price,pr.product_name,pr.category, pr.sub_category, pr.group_id, pr.product_description, ipt.type as product_type ,pr.pack_unit, ins.id, ins.user_id, ins.product_id, ins.barcode, ins.selling_price FROM $table_name as ins join product as pr on (pr.id = ins.product_id) left join inventory_distributor as ind on (ind.id = ins.distributor_id) left join inventory_category as inc on (pr.category = inc.category) left join inventory_product_type as ipt on (ipt.id = pr.product_type) WHERE (ins.barcode='$barcode' or ins.product_id='$barcode') AND ins.user_id = '$user_id'  AND ins.quantity > 0 AND ins.selling_price > 0 group by ins.stock_id limit 10 ")->result_array();
        } else {
            $query = array();
        }
      
        if (sizeof($query) > 0) {
            
            $row = $query[0]; 
            
                $inventory_image        = $row['image'];
                if ($inventory_image != '') {
                    $profile_pic = str_replace(' ', '%20', $inventory_image);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/Inventory/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/Inventory/product_default.jpg';
                }       
                $resultpost1[] = array(
                    'id'      => $row['product_id'] != null ? $row['product_id'] : "",
                    'user_id' => $user_id,
                    'staff_user_id' => $hub_user_id,
                    'vendor_id' => "13",
                    'pname' => $row['product_name'] != null ? $row['product_name'] : "",
                    'inventory_image' => $profile_pic,
                    'cost_price' => $row['product_price'] != null ? $row['product_price'] : "",
                    'mrp' => $row['product_price'] != null ? $row['product_price'] : "",
                    'selling_price' => $row['selling_price'] != null ? $row['selling_price'] : "",
                    'distributor_name' => $row['distributor_name'] != null ? $row['distributor_name'] : "",
                    'manufacture_date' => $row['mgf_date'] != null ? $row['mgf_date'] : "",
                    'expiry_date' => $row['expiry_date'] != null ? $row['expiry_date'] : "", 
                    'gst' => $row['gst'] != null ? $row['gst'] : "",
                    'stock_id' => $row['stock_id'] != null ? $row['stock_id'] : "",
                    'barcode' => $row['barcode'] != null ? $row['barcode'] : "",
                    'batch_no' => $row['batch_no'] != null ? $row['batch_no'] : "",
                    'ingredients' => "",
                    'category_name' => $row['category_name'] != null ? $row['category_name'] : "",
                    'sub_category_name' => "",
                    'product_type_name' => $row['product_type'] != null ? $row['product_type'] : "",
                    'size' => ""
                );
                $resultpost = array( 'status' => 200,
                    'message' => 'Product is in stock!!',
                    'data'=>$resultpost1);
           
        } else {
             $resultpost = array( 'status' => 400,
                            'message' => 'Product Not Found',
                            'data'=>array());
        }
        return $resultpost;
    }
    
    
    //Hitesh, already sorry if I screw up!!
    
    public function stock_table_existance($user_id){
        $return = array();
        $table_name = 'stock_'.$user_id;
        $check_table_existance = $this->db->query("SHOW TABLES LIKE '$table_name'")->row_array();
        if(sizeof($check_table_existance) > 0){
            $status = true;
            $table_name = $table_name;
        } else {
            $status = false;
            $table_name = "";
        }
        $return['status'] = $status;
        $return['table_name'] = $table_name;
        return $return;
    }
    
    public function product_list($user_id,$page_no,$per_page,$search)
    {
        // check  stock_'user_id' table exists or not - done
        // (use : `SHOW TABLES LIKE 'yourtable'` query to che it) - done
            // if exists : get data from stock_'user_id' table - done
            // if not exists : send error : no products found please add products first - done
        //   distributor_id , mfg_id ,  warehouse_id  , rack_id - done
        // pagination data - done
        // return - done
        
        $all_data = $get_all_products = $data = array();
        $current_data_count = $product_count = $status = 0;
        
        $offset = $per_page*($page_no - 1);
        $limit = "LIMIT $per_page OFFSET $offset"; 
        
        $check_table_existance = $this->PharmacyPartnerModel->stock_table_existance($user_id);
        if($check_table_existance['status'] == true){
            $table_name = $check_table_existance['table_name'];
            // table found
            $search_where = "";
            if($search != ""){
                $search_where = "AND (inr.rack like '%$search%' OR iw.wname like '%$search%' OR ind.distributor_name like '%$search%' OR ind.manufacturer_name like '%$search%' OR st.product_name  like '%$search%' OR  st.barcode  like '%$search%' OR st.size like '%$search%' OR st.ptr like '%$search%' OR st.mrp like '%$search%' OR st.expiry_date like '%$search%' OR st.quantity like '%$search%' OR st.selling_price like '%$search%' OR   st.bill_no like '%$search%' OR   st.batch_no like '%$search%')";
            }
            $get_all_products = $this->db->query("Select pr.brand_id,inr.rack as rack_name, inr.details as rack_details, iw.wname as warehouse_name, ind.distributor_name, ind.manufacturer_name, st.* from $table_name  as st left join product as pr ON st.product_id=pr.id left join inventory_distributor as ind on (st.distributor_id = ind.id) left join inventory_warehouse as iw on (st.warehouse_id = iw.id) left join inventory_rack as inr on (st.rack_id = inr.id) where st.user_id = '$user_id' $search_where order by st.id desc $limit")->result_array();
            $product_count_total = $this->db->query("Select inr.rack as rack_name, inr.details as rack_details, iw.wname as warehouse_name, ind.distributor_name, ind.manufacturer_name, st.id from $table_name  as st left join inventory_distributor as ind on (st.distributor_id = ind.id) left join inventory_warehouse as iw on (st.warehouse_id = iw.id) left join inventory_rack as inr on (st.rack_id = inr.id) where st.user_id = '$user_id' $search_where")->result_array();
            $product_count = sizeof($product_count_total);
            $current_data_count = sizeof($get_all_products);
            if(sizeof($get_all_products) > 0){
                foreach($get_all_products as $ap ){
                    foreach($ap as $k=>$v){
                        if($v == null){
                            $a[$k]  = "";
                        } else {
                            $a[$k]  = $v;
                        }
                    }
                    $all_data[] = $a;
                }
                // product found
                $status = 1;
            } else {
                // No product found
                $status = 2;
            }
        } else {
            // table not found
            $status = 3;
        }
        
        $data['status'] = $status;
        if($product_count > 0){
            $last_page = strval(ceil($product_count/$per_page));
            $return_data['data_count'] = strval($product_count);
            $return_data['current_page_count'] = strval($current_data_count);
            $return_data['current_page'] = $page_no;
            $return_data['first_page'] = "1";
            $return_data['last_page'] = "$last_page";
            $return_data['products'] = $all_data;
        } else {
            $last_page = "0";
            $return_data['data_count'] = "0";
            $return_data['current_page_count'] = "0";
            $return_data['current_page'] = "0";
            $return_data['first_page'] = "1";
            $return_data['last_page'] = "$last_page";
            $return_data['products'] = (object)[];
        }
       
        
        
        
        $data['products'] = $return_data;
        
        return $data;
    }

    public function edit_product($user_id,$product_id, $size, $ptr, $mrp, $selling_price, $distributor_id, $statuss, $rack_id,$quantity,$expiry_date,$barcode,$brand,$max_disc,$loose_quantity_factor,$our_margin,$accept_mfg_date_of,$check_expiry_till,$min_qty_to_bill,$fixed_min_level,$fixed_max_level,$net_purch_rate,$calc_min_level,$cal_max_level,$pack,$pack_unit,$batch_no,$gst){
        $status = 0;
        $data = array();
        $set1 ="";
        $set = "";
        $check_table_existance = $this->PharmacyPartnerModel->stock_table_existance($user_id);
        if($check_table_existance['status']){
            $table_name = $check_table_existance['table_name'];
            if($size !=""){
                $set .= " size = '$size'";
            }
            
            if($ptr !=""){
                if($set != ""){
                    $set .= " , ";
                }
                $set .= " ptr = '$ptr'";
            }
            
            
            if($mrp !=""){
                if($set != ""){
                    $set .= " , ";
                }
                $set .= " mrp = '$mrp'";
            }
            
            if($selling_price !=""){
                if($set != ""){
                    $set .= " , ";
                }
                $set .= " selling_price = '$selling_price'";
            }
            
            if($distributor_id !=""){
                if($set != ""){
                    $set .= " , ";
                }
                $set .= " distributor_id = '$distributor_id'";
            }
            
            if($statuss !=""){
                if($set != ""){
                    $set .= " , ";
                }
                $set .= " status = '$statuss'";
            }
            
            if($rack_id !=""){
                if($set != ""){
                    $set .= " , ";
                }
                $set .= " rack_id = '$rack_id'";
            }
            
            if($quantity !=""){
                if($set != ""){
                    $set .= " , ";
                }
                $set .= " quantity = '$quantity'";
            }
            
            if($expiry_date !=""){
                if($set1 != ""){
                    $set1 .= " , ";
                }
                $set1 .= " expiry_date = '$expiry_date'";
            }
            
            if($barcode !=""){
                if($set != ""){
                    $set .= " , ";
                }
                $set .= " barcode = '$barcode'";
            }
             if($batch_no !=""){
                if($set != ""){
                    $set .= " , ";
                }
                $set .= " batch_no = '$batch_no'";
            }
             if($brand !=""){
                if($set != ""){
                    $set .= " , ";
                }
                $set .= " brand_id = '$brand'";
            }
             if($max_disc !=""){
                if($set != ""){
                    $set .= " , ";
                }
                $set .= " max_disc = '$max_disc'";
            }
             if($loose_quantity_factor !=""){
                if($set != ""){
                    $set .= " , ";
                }
                $set .= " loose_quantity_factor = '$loose_quantity_factor'";
            }
             if($our_margin !=""){
                if($set != ""){
                    $set .= " , ";
                }
                $set .= " our_margin = '$our_margin'";
            }
            
             if($accept_mfg_date_of !=""){
                if($set != ""){
                    $set .= " , ";
                }
                $set .= " accept_mfg_date_of = '$accept_mfg_date_of'";
            }
             if($check_expiry_till !=""){
                if($set != ""){
                    $set .= " , ";
                }
                $set .= " check_expiry_till = '$check_expiry_till'";
            }
             if($min_qty_to_bill !=""){
                if($set != ""){
                    $set .= " , ";
                }
                $set .= " min_qty_to_bill = '$min_qty_to_bill'";
            }
             if($fixed_min_level !=""){
                if($set != ""){
                    $set .= " , ";
                }
                $set .= " fixed_min_level = '$fixed_min_level'";
            }
             if($fixed_max_level !=""){
                if($set != ""){
                    $set .= " , ";
                }
                $set .= " fixed_max_level = '$fixed_max_level'";
            }
            
             if($net_purch_rate !=""){
                if($set != ""){
                    $set .= " , ";
                }
                $set .= " net_purch_rate = '$net_purch_rate'";
            }
             if($calc_min_level !=""){
                if($set != ""){
                    $set .= " , ";
                }
                $set .= " calc_min_level = '$calc_min_level'";
            }
             if($cal_max_level !=""){
                if($set != ""){
                    $set .= " , ";
                }
                $set .= " cal_max_level = '$cal_max_level'";
            }
             if($pack !=""){
                if($set != ""){
                    $set .= " , ";
                }
                $set .= " pack = '$pack'";
            }
             if($pack_unit !=""){
                if($set != ""){
                    $set .= " , ";
                }
                $set .= " pack_unit = '$pack_unit'";
            }
            
            if($gst !=""){
                if($set != ""){
                    $set .= " , ";
                }
                $set .= " gst = '$gst'";
            }
            
            
            
            if($set != ""){
                $update_query = "UPDATE ".$table_name." SET $set WHERE product_id = '$product_id' AND user_id = '$user_id' "   ;
                $update = $this->db->query($update_query);
                $status = 1; // updated
            } else {
                $status = 2; // nothing to update
            }
            if($set1 != "" && $batch_no !=""){
                $update_query = "UPDATE ".$table_name." SET $set1 WHERE product_id = '$product_id' AND user_id = '$user_id' AND batch_no='$batch_no'"   ;
                $update = $this->db->query($update_query);
                $status = 1; // updated
            }
            
            
        } else {
            $status = 3;// no stock exists;
        }
        $data['status'] = $status;
        return $data;
    }
    
    public function select_payment_options($pharmacy_id,$vendor_type){
        $alreadyExists = 0;
        $spm = $data = array();
        $payment_methods = $this->db->query("SELECT ppm.user_id, p.* FROM payment_method as p left join pharmacy_payment_methods as ppm on (ppm.payment_method_id = p.id AND ppm.user_id = '$pharmacy_id' AND ppm.active = 1) WHERE p.status = '1' AND NOT EXISTS (SELECT * FROM payment_method WHERE parent_id = p.id) order by sequence_no asc")->result_array();
        foreach($payment_methods as $pm){
            $allowed_vendor_type = explode(",",$pm['vendor_type']);
            foreach($allowed_vendor_type as $vt){
                if($vendor_type == $vt){
                    if($pm['id'] == 3 || $pm['id'] == 6){
                        $add_upi_phone = 0;
                    } else {
                        $add_upi_phone = 1;
                    }
                    
                    $p['id'] = $pm['id'];
                    $p['payment_method'] = $pm['payment_method'];
                    $p['icon'] =  $pm['icon'];
                    if($pm['user_id'] != null){
                        $alreadyExists = 1;
                    } else {
                        $alreadyExists = 0;
                    }
                    $p['already_added'] =  $alreadyExists;
                    $p['add_phone_upi'] =  $add_upi_phone;
                    $spm[] = $p;
                }
            }
        }
        $data['data'] = $spm;
        return $data;
    }
    
    public function get_payment_info_by_id($user_id, $payment_id){
        $pm = $spm = $data = array();
        $status = 0;
        $id = $payment_method = $phone = $image = $icon = "";
        $payment_methods = $this->db->query("SELECT mpm.id,mpm.payment_method,mpm.icon,   ppm.`user_id` as pharmacy_id, ppm.phone, ppm.`image` FROM `pharmacy_payment_methods` as ppm left join payment_method as mpm on(ppm.payment_method_id = mpm.id) WHERE ppm.`user_id` = '$user_id' AND ppm.payment_method_id = '$payment_id' AND ppm.active = 1 ")->row_array();
        if(sizeof($payment_methods) > 0){
            $id = $payment_methods['id'];
            $payment_method = $payment_methods['payment_method'];
            $phone = $payment_methods['phone'];
            $image = $payment_methods['image'] ? $payment_methods['image'] : "";
            $icon =  $payment_methods['icon'];
            $status = 1;
        }
        $p['id'] = $id;
        $p['payment_method'] = $payment_method;
        $p['phone'] = $phone;
        $p['image'] = $image;
        $p['icon'] =  $icon;
        $spm['status'] = $status;
        $spm['data'] = $p;
        return $spm;
    }
    
    public function add_edit_payment_method($payment_id, $phone, $user_id){
         /*PENDING*/
        // check payment id is not any others's parent id => status = 2 - done
        // if pharmacy and payment method exists in pharmacy_payment_methods ; get id and update existing on => status = 1 - done
        // if not found : insert new entry => status = 1
        // in return send updated or inserted id and old image if existed to add image
        // update image is pending
        
        $new_image_path = "";
        $data = array();
        $updated_id = 0;
        $get_payment_methods = $this->db->query("SELECT * FROM `payment_method` WHERE `parent_id` = '$payment_id'")->row_array();
        if(sizeof($get_payment_methods) > 0){
            $status = 2; // parent payment id
        } else {
            if(!empty($_FILES["image"]["name"])){
                $img_format = array("jpg","png","gif","bmp","jpeg","PNG","JPG","JPEG","GIF","BMP");
                include('s3_config.php');
                
                $img_name = $_FILES['image']['name'];
                $img_size = $_FILES['image']['size'];
                $img_tmp  = $_FILES['image']['tmp_name'];
                $ext      = getExtension($img_name);
                
                if (strlen($img_name) > 0) {
                    if ($img_size < (50000 * 50000)) {
                        if (in_array($ext, $img_format)) {
                            $pic_file  = uniqid() . date("YmdHis") . "." . $ext;
                            $actual_image_path = 'images/pharmacy_images/' . $pic_file;
                            $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ); 
                            $new_image_path = 'https://medicalwale.s3.amazonaws.com/'.$actual_image_path;
                        }
                    }
                }
                            
            }
            /*delete uploaded image*/
            /*
            
            if ($get_file) {
            $licence_pic = $get_file->licence_pic;
                $file = "images/pharmacy_images/" . $licence_pic;
				@unlink(trim($file));
				DeleteFromToS3($file);
            }
            
            */
            
            
            $payment_method_exists = $this->db->query("SELECT * FROM `pharmacy_payment_methods` WHERE `payment_method_id` = '$payment_id' AND `user_id` = '$user_id' ")->row_array();
            if(sizeof($payment_method_exists) > 0){
                // already exists
                if($new_image_path == ""){
                    $new_image_path = $payment_method_exists['image'];
                }
                
                if($phone == ""){
                    $phone = $payment_method_exists['phone'];
                }
                $updated_id = $payment_method_exists['id'];
                $this->db->query("UPDATE pharmacy_payment_methods SET phone = '$phone', image = '$new_image_path' , active = '1'  WHERE id = '$updated_id'  ");
            } else {
                // insert new
                
                $active = 1;
                $user_id = $user_id;
                $this->db->query("INSERT INTO `pharmacy_payment_methods`( `payment_method_id`, `user_id`, `phone`, `image`,`active`, `created_by`, `updated_by`) VALUES ('$payment_id','$user_id','$phone','$new_image_path', '$active' ,  '$user_id' , '$user_id')");
                $updated_id = $this->db->insert_id();
            }
            $status = 1;
        }
        $data['status'] = $status;
        $data['updated_id'] = $updated_id;
        return $data;
    }
         
    public function create_new_table($user_id){
        $table_name = "";
        $status = 0;
        $check_table_existance = $this->PharmacyPartnerModel->stock_table_existance($user_id);
        	       
        if($check_table_existance['status'] == true){
            $table_name = $check_table_existance['table_name'];
            $status = 1;
        } else {
            // CREATE TABLE stock_test1 LIKE stock_test
            $table_name = 'stock_'.$user_id;
            $sql = "CREATE TABLE $table_name LIKE stock_master";
            $create_new_table = $this->db->query($sql);
            $status = 2;
    	}
    	$data['status'] = $status;
    	$data['table_name'] = $table_name;
    	return $data;
    }
    
    /*by swapnali*/
    public function monthly_report_v2($user_id,$hub_user_id)
    {
        $whereHubId = '';
        if($hub_user_id != '' && $hub_user_id != 0){
            $whereHubId = " AND created_by = '$hub_user_id'";
        }
        $session1 = array();
        $startdate = "";
        $enddate = "";
        $grand_total = 0;
     
        $user_info = $this->db->query("SELECT created_at from users where id = '$user_id'")->row_array();
        if(sizeof($user_info) > 0){
            
            $created_at = $user_info['created_at'];
           
            $timestamp = strtotime($created_at);
            $start_year = date("Y", $timestamp);
            $start_month = intval(date("m", $timestamp));
            $start_date = intval(date("d", $timestamp));
            $end_year = date('Y');
            $end_month = intval(date('m'));	

           
            
            $current_date1 = new DateTime(); 
            $current_date = time(); // or your date as well
            $datediff = $current_date - $timestamp;  // total dayes
            $totalDays = round($datediff / (60 * 60 * 24));


            $start_date_day = date('w',mktime(0,0,0,$start_month,$start_date,$start_year));
            $last = 7 - $start_date_day; 	
            $noweeks = ceil((($totalDays - ($last + 1))/7) + 1);
           
            $year = date('Y');
            $month = intval(date('m'));				            //force month to single integer if '0x'
           	$end = date('t',mktime(0,0,0,$month,1,$year)); 		//last date day of month: 28 - 31 --- $totalDays
        	$start = date('w',mktime(0,0,0,$month,1,$year)); 	//1st day of month: 0 - 6 (Sun - Sat) ---$start_date_day
          	
          	//$last = 7 - $start; 					            //get last day date (Sat) of first week
        	// $noweeks = ceil((($end - ($last + 1))/7) + 1);		//total no. weeks in month
        	$output = "";						                //initialize string		
             $monthlabel = str_pad($month, 2, '0', STR_PAD_LEFT); 
        	for($x=1;$x<$noweeks+1;$x++){	
        	    $grand_total = 0;
        		if($x == 1){
        			$startdate = date("Y-m-d", $timestamp);  //"$year-$monthlabel-01"; die();
        			$day = $last - 6;
        		}else{
        		    
        			$day = $last + 1 + (($x-2)*7);
        			$day = str_pad($day, 2, '0', STR_PAD_LEFT);
        			$startdate = date('Y-m-d', strtotime('next sunday', strtotime($laststartdate)));
        		}
        		if($x == $noweeks){
        			$enddate = date('Y-m-d');
        		}else{
        			$dayend = $day + 6;
        			$dayend = str_pad($dayend, 2, '0', STR_PAD_LEFT);
            		$enddate = date('Y-m-d', strtotime('next saturday', strtotime($startdate)));
        		}
        		
        	$gross_amount1  = $this->db->query("select * from user_order where listing_id='$user_id' AND order_date >= '$startdate' AND order_date <= '$enddate' AND order_status != 'Order Cancelled' $whereHubId group by invoice_no");
            $gross_amount11 = $gross_amount1->result();
            $final_amount = 0;
            $final_amount1 = 0;
            foreach($gross_amount11 as $det) {
                $order_id = $det->order_id;
                $order_type = $det->order_type;
                $delivery_charge = $det->delivery_charge;
                $gst = $det->gst;
                $chc = $det->chc;
                $order_status = $det->order_status;
               
                    $actual_cost = $det->actual_cost ;
                    $order_total = $det->order_total ;
                    $delivery_charge = $det->delivery_charge ;
                    $delivery_charges_by_customer = $det->delivery_charges_by_customer ;
                    $delivery_charges_by_vendor = $det->delivery_charges_by_vendor;
                    $delivery_charges_by_mw = $det->delivery_charges_by_mw;
                    $delivery_charges_to_mno = $det->delivery_charges_to_mno ;
                    $discount = $det->discount;
                    $chc = $det->chc;
                    $gst = $det->gst;
                    
                    $amountTotal = $order_total + $delivery_charges_by_customer - $discount;
                    $total_gst = ($amountTotal * $gst)/ 100;
                    $grand_total = $grand_total + $amountTotal + $total_gst + $chc; 
              
                
                
            }
            
            $session1[] = array(
                                        'from_date'=> $startdate,
                                        'to_date' => $enddate,
                                        'price'=>$grand_total,
                                        'current_date' => date('Y-m-d')
                                       );

                $laststartdate = $startdate;
                $lastenddate = $enddate;
                   
            
        } 
        return $session1;
        }
        
    }  
    
    /*by swapnali*/
    
    public function inventory_dashboard_v2($user_id,$hub_user_id,$from_date,$to_date)
    {
        $whereHub = '';
        if($hub_user_id != '' && $hub_user_id != 0){
            $whereHub = "AND created_by = '$hub_user_id'";
        }
        $total_orders1 = $grand_total = 0;
        if(empty($from_date) && empty($to_date))
        {
            $from_date = date('Y-m-d',strtotime('last Monday'));
            $to_date = date('Y-m-d',strtotime('next Sunday'));
        }
        
        $new_from_date = date('Y-m-d',strtotime($from_date . "-1 days"));
        $new_to_date = date('Y-m-d',strtotime($to_date . "+1 days"));
        
       /* $total_orders = $this->db->query("select count(*) as tot from user_order where listing_id='$user_id' AND order_date between '$new_from_date' AND  '$new_to_date' AND order_status != 'Order Cancelled' GROUP BY invoice_no");
        $total_orders1 = $total_orders->row()->tot;
        */
        $gross_amount1  = $this->db->query("select * from user_order where listing_id='$user_id' AND order_date between '$new_from_date' AND  '$new_to_date' AND order_status != 'Order Cancelled'  $whereHub GROUP BY invoice_no");
        $gross_amount11 = $gross_amount1->result();
        $final_amount = 0;
        $final_amount1 = 0;
        $final_discount = 0;
        $final_discount1 = 0;
        $final_quantity = 0;
        $final_quantity1 = 0;
        $final_gst = 0;
        $final_gst1 = 0;
        $final_gross = 0;
        $final_gross1 = 0;
        $final_total_amount = $total_grand_total = $final_total_gst = $final_total_discount  =0;
        
        $tot_array = array();
        foreach($gross_amount11 as $det)
        {
            $order_id = $det->order_id;
            $order_type = $det->order_type;
            $delivery_charge = $det->delivery_charge;
            $gst = $det->gst;
            $chc = $det->chc;
            $order_status = $det->order_status;
           
            $actual_cost = $det->actual_cost ;
            $order_total = $det->order_total ;
            $delivery_charge = $det->delivery_charge ;
            $delivery_charges_by_customer = $det->delivery_charges_by_customer ;
            $delivery_charges_by_vendor = $det->delivery_charges_by_vendor;
            $delivery_charges_by_mw = $det->delivery_charges_by_mw;
            $delivery_charges_to_mno = $det->delivery_charges_to_mno ;
            $discount = $det->discount;
            $chc = $det->chc;
            $gst = $det->gst;
            
            $amountTotal = $order_total + $delivery_charges_by_customer - $discount;
            $total_gst = ($amountTotal * $gst)/ 100;
            $grand_total = $grand_total + $amountTotal + $total_gst + $chc; 
            
          
            $tot_array[] = $grand_total; 
        }
        
        if(!empty($tot_array))
        {
            $max = max($tot_array);
            $min = min($tot_array);
            $avg = round(array_sum($tot_array)/count($tot_array));
        }
        else
        {
            $max = 0;
            $min = 0;
            $avg = 0;
        }
        //----------------------------daywise---------------------------
        for($d=0;$d<7;$d++)
        {
            $total_orders2 = $grand_total_ = $amountTotal_ = $max_ = $min_ = $avg_ = $total_gst_ = $discount_ = 0;
            $grand_total_ = 0;
            $date = $from_date;
            $date1 = str_replace('-', '/', $date);
            $tomorrow = date('Y-m-d',strtotime($date1 . "+$d days"));
            //echo "select * from user_order where listing_id='$user_id' AND order_date ='$tomorrow'";
            $gross_amount1_  = $this->db->query("select * from user_order where listing_id='$user_id' AND order_date LIKE '%" .$tomorrow."%' AND order_status != 'Order Cancelled' $whereHub GROUP BY invoice_no");
            $gross_amount11_ = $gross_amount1_->result();
            
            $total_orders2 = count($gross_amount11_);
            
            $final_amount_ = 0;
            $final_amount1_ = 0;
            $final_discount_ = 0;
            $final_discount1_ = 0;
            $final_quantity_ = 0;
            $final_quantity1_ = 0;
            $final_gst_ = 0;
            $final_gst1_ = 0;
            $final_gross_ = 0;
            $final_gross1_ = 0;
            
            $tot_array_ = array();
            
            foreach($gross_amount11_ as $det)
            {
                $order_id_ = $det->order_id;
                $order_type_ = $det->order_type;
                $delivery_charge_ = $det->delivery_charge;
                $gst_ = $det->gst;
                $chc_ = $det->chc;
                $order_status_ = $det->order_status;
               
                $actual_cost_ = $det->actual_cost ;
                $order_total_ = $det->order_total ;
                $delivery_charge_ = $det->delivery_charge ;
                $delivery_charges_by_customer_ = $det->delivery_charges_by_customer ;
                $delivery_charges_by_vendor_ = $det->delivery_charges_by_vendor;
                $delivery_charges_by_mw_ = $det->delivery_charges_by_mw;
                $delivery_charges_to_mno_ = $det->delivery_charges_to_mno ;
                $discount_ = $det->discount;
                $chc_ = $det->chc;
                $gst_ = $det->gst;
                
                $amountTotal_ = $order_total_ + $delivery_charges_by_customer_ - $discount_;
                $total_gst_ = ($amountTotal_ * $gst_)/ 100;
                $grand_total_ = $grand_total_ + $amountTotal_ + $total_gst_ + $chc_; 
               
               
              
            
                $tot_array_[] = $grand_total_; 
                $total_grand_total = $total_grand_total + $grand_total_;
                
               $total_orders1 = $total_orders1 + $total_orders2;
               $final_total_amount = $final_total_amount +  $amountTotal_;
               
                $final_total_gst = $final_total_gst + $total_gst_;
            $final_total_discount = $final_total_discount + $discount_ ;
            
            }
            
             if(!empty($tot_array_))
            {
                $max_ = max($tot_array_);
                $min_ = min($tot_array_);
                $avg_ = round(array_sum($tot_array_)/count($tot_array_));
            }
            else
            {
                $max_ = 0;
                $min_ = 0;
                $avg_ = 0;
            }
        
            $day = date('l',strtotime($tomorrow));
            
            $daywise[]=array('day' => $day,
                            'total_orders' => $total_orders2,
                            'date' => $tomorrow,  
                            'gross_amount' => $grand_total_,
                            'total_units'  => $final_quantity_+$final_quantity1_,
                            'net_amount'  => $amountTotal_,
                            'max_bill'    => $max_,
                            'min_bill' => $min_,
                            'avg_bill'  => $avg_ ,
                            'total_gst'   => $total_gst_,
                            'total_disc' => intval($discount_));
        }
        
        $resultpost[] = array(
                            'from_date' => $from_date,
                            'to_date' => $to_date,
                            'total_orders' => $total_orders1,
                            'total_units'  => $final_quantity+$final_quantity1,
                            'net_amount'  => $final_total_amount ,
                            'max_bill'    => $max,
                            'min_bill' => $min,
                            'avg_bill'  => $avg,
                            'gross_amount' => $total_grand_total,
                            'total_gst'   => $final_total_gst,
                            'total_disc' => $final_total_discount,
                            'daywise' => $daywise
                           
                            );
            
        $final = array(
            'status'=> 200,
             'message' => "success",
            'data'=> $resultpost
                       );
        return $final;
        
    }
    
    // notification in table
    public function notification_in_table($title, $msg, $img_url, $listing_id, $appointment_id, $user_id, $notification_type){
        $notification_array = array(
                      'title' => $title,
                      'msg'  => $msg,
                      'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => "",
                      'order_date' => "",
                      'order_id'   => "",
                      'post_id'  => "",
                      'listing_id'  => $listing_id,
                      'booking_id'  => $appointment_id,
                       'invoice_no' => "",
                       'user_id'  => $user_id,
                       'notification_type'  => $notification_type,
                       'notification_date'  => date('Y-m-d H:i:s')
            );
         $this->db->insert('All_notification_Mobile', $notification_array);
    }
    public function pbioms_subscription($user_id)
    {
        $resultpost = array();
        $query = $this->db->query("SELECT * FROM pbioms_subscription WHERE status='0'");
        if($query->num_rows() > 0)
        {
            foreach($query->result_array() as $row)
            {
                $id= $row['id'];
                $inarray = array();
                $query1 = $this->db->query("SELECT * FROM pbioms_subscription_details WHERE status='0' AND s_id='$id'");
                if($query1->num_rows() > 0)
                {
                    foreach($query1->result_array() as $row1)
                    {
                        $inarray[] = array(
                             'detail_id' => $row1['id'],
                            'title' => $row1['title'],
                            'content' => $row1['content'],
                           
                            );
                    }
                }
                $resultpost[] = array(
                             'id' => $id,
                            'main_title' => $row['title'],
                            'sub_title' => $row['subtitle'],
                            'amount' => $row['amount'],
                            'image' => $row['image'],
                            'sub_details' => $inarray
                            );
            }
        }
        return $resultpost;
    }
    
    
    public function inventory_dashboard_v3($user_id,$hub_user_id,$from_date,$to_date)
    {
        $whereHub = '';
        if($hub_user_id != '' && $hub_user_id != 0){
            $whereHub = "AND created_by = '$hub_user_id'";
        }
        $total_orders1 = $grand_total = 0;
        if(empty($from_date) && empty($to_date))
        {
            $from_date = date('Y-m-d',strtotime('last Sunday'));
            $to_date = date('Y-m-d',strtotime('next Saturday'));
        }
        
        $new_from_date = date('Y-m-d',strtotime($from_date));
        $new_to_date = date('Y-m-d',strtotime($to_date . "+1 days"));
        
        // echo "select * from user_order where listing_id='$user_id' AND order_date between '$new_from_date' AND  '$new_to_date' AND order_status != 'Order Cancelled'  $whereHub GROUP BY invoice_no"; die();
        $gross_amount1  = $this->db->query("select * from user_order where listing_id='$user_id' AND order_date between '$new_from_date' AND  '$new_to_date' AND order_status != 'Order Cancelled'  $whereHub GROUP BY invoice_no");
        $gross_amount11 = $gross_amount1->result();
        $total_orders1 = sizeof($gross_amount11);
        $final_amount = 0;
        $final_amount1 = 0;
        $final_discount = 0;
        $final_discount1 = 0;
        $final_quantity = 0;
        $final_quantity1 = 0;
        $final_gst = 0;
        $final_gst1 = 0;
        $final_gross = 0;
        $final_gross1 = 0;
        $final_total_amount = $total_grand_total = $final_total_gst = $final_total_discount  =0;
        
        $tot_array = array();
        foreach($gross_amount11 as $det)
        {
            $order_id = $det->order_id;
            $order_type = $det->order_type;
            $delivery_charge = $det->delivery_charge;
            $gst = $det->gst;
            $chc = $det->chc;
            $order_status = $det->order_status;
           
            $actual_cost = $det->actual_cost ;
            $order_total = $det->order_total ;
            $delivery_charge = $det->delivery_charge ;
            $delivery_charges_by_customer = $det->delivery_charges_by_customer ;
            $delivery_charges_by_vendor = $det->delivery_charges_by_vendor;
            $delivery_charges_by_mw = $det->delivery_charges_by_mw;
            $delivery_charges_to_mno = $det->delivery_charges_to_mno ;
            $discount = $det->discount;
            $chc = $det->chc;
            $gst = $det->gst;
            
            $amountTotal = $actual_cost + $delivery_charges_by_customer - $discount;
            $total_gst = ($amountTotal * $gst)/ 100;
            $grand_total = $grand_total + $amountTotal  + $chc; 
            
          
            $tot_array[] = $amountTotal; 
        }
        
        if(!empty($tot_array))
        {
            $max = max($tot_array);
            $min = min($tot_array);
            $avg = round(array_sum($tot_array)/count($tot_array));
        }
        else
        {
            $max = 0;
            $min = 0;
            $avg = 0;
        }
        //----------------------------daywise---------------------------
        for($d=0;$d<7;$d++)
        {
            $total_orders2 = $grand_total_ = $amountTotal_ = $max_ = $min_ = $avg_ = $total_gst_ = $discount_ = 0;
            $grand_total_ = 0;
            $date = $from_date;
            $date1 = str_replace('-', '/', $date);
            $before_date = date('Y-m-d',strtotime($date1 . "+$d days"));
            $d2 = $d + 1;
            $tomorrow = date('Y-m-d',strtotime($date1 . "+$d2 days"));
            // echo "select * from user_order where listing_id='$user_id' AND order_date between '$before_date' AND '$tomorrow' AND order_status != 'Order Cancelled'  $whereHub GROUP BY invoice_no"; 
            // echo "<br>";
            // die();
            $gross_amount1_  = $this->db->query("select * from user_order where listing_id='$user_id' AND order_date between '$before_date' AND '$tomorrow' AND order_status != 'Order Cancelled'  $whereHub GROUP BY invoice_no");
            $gross_amount11_ = $gross_amount1_->result();
            
            $total_orders2 = count($gross_amount11_);
            
            $final_amount_ = 0;
            $final_amount1_ = 0;
            $final_discount_ = 0;
            $final_discount1_ = 0;
            $final_quantity_ = 0;
            $final_quantity1_ = 0;
            $final_gst_ = 0;
            $final_gst1_ = 0;
            $final_gross_ = 0;
            $final_gross1_ = 0;
            $total_discount_ = 0;
            
            $tot_array_ = array();
            
            foreach($gross_amount11_ as $det)
            {
                $order_id_ = $det->order_id;
                $order_type_ = $det->order_type;
                $delivery_charge_ = $det->delivery_charge;
                $gst_ = $det->gst;
                $chc_ = $det->chc;
                $order_status_ = $det->order_status;
               
                $actual_cost_ = $det->actual_cost ;
                $order_total_ = $det->order_total ;
                $delivery_charge_ = $det->delivery_charge ;
                $delivery_charges_by_customer_ = $det->delivery_charges_by_customer ;
                $delivery_charges_by_vendor_ = $det->delivery_charges_by_vendor;
                $delivery_charges_by_mw_ = $det->delivery_charges_by_mw;
                $delivery_charges_to_mno_ = $det->delivery_charges_to_mno ;
                $discount_ = $det->discount;
                $chc_ = $det->chc;
                $gst_ = $det->gst;
                
                $amountTotal_ = $actual_cost_ + $delivery_charges_by_customer_ - $discount_ + $chc_;
                //  echo "day ". $before_date ." : ". $amountTotal_ . "<br>";
                $total_gst_ = ($amountTotal_ * $gst_)/ 100;
                $grand_total_ = $grand_total_ + $amountTotal_ ; 
               
                $tot_array_[] = $amountTotal_; 
                $total_grand_total = $total_grand_total + $amountTotal_;
                $total_discount_ = $total_discount_ + $discount_;
                
                $final_total_amount = $final_total_amount +  $amountTotal_;
               
                $final_total_gst = $final_total_gst + $total_gst_;
                $final_total_discount = $final_total_discount + $discount_ ;
            
            }
            
             if(!empty($tot_array_))
            {
                $max_ = max($tot_array_);
                $min_ = min($tot_array_);
                $avg_ = round(array_sum($tot_array_)/count($tot_array_));
            }
            else
            {
                $max_ = 0;
                $min_ = 0;
                $avg_ = 0;
            }
        
            $day = date('l',strtotime($before_date));
            
            $daywise[]=array('day' => $day,
                            'total_orders' => $total_orders2,
                            'date' => $before_date,  
                            'gross_amount' => $grand_total_,
                            'total_units'  => $final_quantity_+$final_quantity1_,
                            'net_amount'  => $grand_total_,
                            'max_bill'    => $max_,
                            'min_bill' => $min_,
                            'avg_bill'  => $avg_ ,
                            'total_gst'   => $total_gst_,
                            'total_disc' => intval($total_discount_));
        }
        // die();
        $resultpost[] = array(
                            'from_date' => $from_date,
                            'to_date' => $to_date,
                            'total_orders' => $total_orders1,
                            'total_units'  => $final_quantity+$final_quantity1,
                            'net_amount'  => $total_grand_total ,
                            'max_bill'    => $max,
                            'min_bill' => $min,
                            'avg_bill'  => $avg,
                            'gross_amount' => $total_grand_total,
                            'total_gst'   => $final_total_gst,
                            'total_disc' => $final_total_discount,
                            'daywise' => $daywise
                           
                            );
            
        $final = array(
            'status'=> 200,
             'message' => "success",
            'data'=> $resultpost
                       );
        return $final;
        
    }
    
    public function products_with_batches($user_id,$hub_user_id,$barcode)
    {
        $stock_quantity = 0;
        $check_table_existance = $this->PharmacyPartnerModel->stock_table_existance($user_id);
        	       
        if($check_table_existance['status'] == true){
            $table_name = $check_table_existance['table_name'];
            $query = $this->db->query("SELECT ins.gst,ins.id as stock_id,ins.batch_no,ins.expiry_date,ins.mgf_date,pr.image,ins.quantity,inc.category as category_name,ind.distributor_name,ind.manufacturer_name, pr.product_price,pr.product_name,pr.category, pr.sub_category, pr.group_id, pr.product_description, ipt.type as product_type ,pr.pack_unit, ins.id, ins.user_id, ins.product_id, ins.barcode, ins.selling_price FROM $table_name as ins join product as pr on (pr.id = ins.product_id) left join inventory_distributor as ind on (ind.id = ins.distributor_id) left join inventory_category as inc on (pr.category = inc.category) left join inventory_product_type as ipt on (ipt.id = pr.product_type) WHERE ins.barcode='$barcode'  AND ins.user_id = '$user_id'  AND ins.quantity > 0 AND ins.selling_price > 0  AND ins.status = '1' GROUP BY ins.id  limit 10 ")->result_array();
        } else {
            $query = array();
        }
      
        if (sizeof($query) > 0) {
            
            foreach($query as $row){
                
            
            
                $inventory_image        = $row['image'];
                if ($inventory_image != '') {
                    $profile_pic = str_replace(' ', '%20', $inventory_image);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/Inventory/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/Inventory/product_default.jpg';
                }       
                $resultpost1[] = array(
                    'id'      => $row['product_id'] != null ? $row['product_id'] : "",
                    'user_id' => $user_id,
                    'staff_user_id' => $hub_user_id,
                    'vendor_id' => "13",
                    'pname' => $row['product_name'] != null ? $row['product_name'] : "",
                    'inventory_image' => $profile_pic,
                    'cost_price' => $row['product_price'] != null ? $row['product_price'] : "",
                    'mrp' => $row['product_price'] != null ? $row['product_price'] : "",
                    'selling_price' => $row['selling_price'] != null ? $row['selling_price'] : "",
                    'distributor_name' => $row['distributor_name'] != null ? $row['distributor_name'] : "",
                    'manufacture_date' => $row['mgf_date'] != null ? $row['mgf_date'] : "",
                    'expiry_date' => $row['expiry_date'] != null ? $row['expiry_date'] : "", 
                    'gst' => $row['gst'] != null ? $row['gst'] : "",
                    'stock_id' => $row['stock_id'] != null ? $row['stock_id'] : "",
                    'barcode' => $row['barcode'] != null ? $row['barcode'] : "",
                    'batch_no' => $row['batch_no'] != null ? $row['batch_no'] : "",
                    'ingredients' => "",
                    'category_name' => $row['category_name'] != null ? $row['category_name'] : "",
                    'sub_category_name' => "",
                    'product_type_name' => $row['product_type'] != null ? $row['product_type'] : "",
                    'size' => "",
                    'quantity' => $row['quantity'] != null ? $row['quantity'] : ""
                );
            }    
                $resultpost = array( 'status' => 200,
                    'message' => 'Product is in stock!!', 
                    'data'=>$resultpost1);
           
        } else {
             $resultpost = array( 'status' => 400,
                            'message' => 'Product Not Found',
                            'data'=>array());
        }
        return $resultpost;
    }  
    
    public function product_find_with_batches($user_id,$hub_user_id,$find)
    {
        $resultpost = array();
        $check_table_existance = $this->PharmacyPartnerModel->stock_table_existance($user_id);
        	       
        if($check_table_existance['status'] == true){
            $table_name = $check_table_existance['table_name'];
            $query = $this->db->query("SELECT ins.gst,ins.quantity,p.id as id,p.product_name,p.image,ins.barcode,ins.selling_price,ins.id as stock_id, ins.batch_no FROM product as p inner join $table_name as ins on ins.product_id=p.id  WHERE p.product_name LIKE '%" .$find."%'  AND ins.quantity > 0  AND ins.status = '1' GROUP BY ins.id limit 10 ");
            $count = $query->num_rows();
                if ($count > 0) {
                    foreach ($query->result_array() as $row) {
                        $id                     = $row['id'];
                        $pname                  = $row['product_name'];
                        $inventory_image        = $row['image'];
                        $barcode                = $row['barcode'];
                        $price                  = $row['selling_price'];
                        $stock_id               = $row['stock_id'];
                        $batch_no               = $row['batch_no'];
                        $gst                    = $row['gst'];
                        $quantity               = $row['quantity'];
                        $resultpost[] = array(
                                                'id'        => $id,
                                                'pname' => $pname,
                                                'inventory_image' => $inventory_image,
                                                'barcode'         => $barcode,
                                                'cost_price'           => $price,
                                                'selling_price'           => $price,
                                                'stock_id'          => $stock_id,
                                                'batch_no'          => $batch_no,
                                                'gst'          => $gst,
                                                'quantity'     => $quantity
                                                );
                    }
                }
               
        } else {
            $resultpost = array();
        }
            return $resultpost;
    }
}

    
?>
