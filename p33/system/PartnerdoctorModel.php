<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PartnerdoctorModel extends CI_Model {

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

    public function council_list($type) {
        $query = $this->db->query("SELECT `id`, `type`, `state`, `council`, `council_name` FROM `council` WHERE type='$type' order by council_name asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $council_name = $row['council_name'];

                $resultpost[] = array
                        (
                    "council_name" => $council_name
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    
     public function council_list_search($type,$keyword) {
        $query = $this->db->query("SELECT `id`, `type`, `state`, `council`, `council_name` FROM `council` WHERE type='$type' AND council_name LIKE '%$keyword%' order by council_name asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $council_name = $row['council_name'];

                $resultpost[] = array(
                    "council_name" => $council_name
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
     public function partner_area_expertise_model($category,$keyword)
    {
        return $this->db->select('id,category')->from('business_category')->where('category_id', $category)->like('category',$keyword)->order_by('category', 'asc')->get()->result();
    }
      public function partner_doctor_specialization_model($keyword)
    {
        return $this->db->select('id,specialization')->from('doctor_specialization')->like('specialization',$keyword)->order_by('specialization', 'asc')->get()->result();
    }
      public function partner_doctor_service_model($keyword)
    {
        return $this->db->select('id,service')->from('doctor_services')->like('service',$keyword)->order_by('service', 'asc')->get()->result();
    }
    
     public function partner_patient_list_model($doctor_id,$keyword)
    {
        return $this->db->select('id,patient_name')->from('doctor_patient')->where('doctor_id', $doctor_id)->like('patient_name',$keyword)->order_by('patient_name', 'asc')->get()->result();
    }
      public function partner_clinic_list_model($doctor_id,$keyword)
    {
        return $this->db->select('id,clinic_name')->from('doctor_clinic')->where('doctor_id', $doctor_id)->like('clinic_name',$keyword)->order_by('clinic_name', 'asc')->get()->result();
    }
      public function partner_medicines_list_model($keyword)
    {
        return $this->db->select('id,product_name')->from('product')->like('product_name',$keyword)->order_by('product_name', 'asc')->get()->result();
    }

    public function signup($category, $type, $doctor_name, $email, $phone, $qualification, $experience, $gender, $dob, $reg_council, $reg_number, $token, $agent) {
        if ($doctor_name != '' && $email != '' && $phone != '') {
            $vendor_id = $type;
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
            } else {
                date_default_timezone_set('Asia/Kolkata');
                $updated_at = date('Y-m-d H:i:s');


                $pas = $this->randomPassword();
                $vendor_id = $vendor_id;
                $password = md5($pas);

                $user_data = array(
                    'name' => $doctor_name,
                    'email' => $email,
                    'phone' => $phone,
                    'gender' => $gender,
                    'dob' => $dob,
                    'vendor_id' => $vendor_id,
                    'password' => $password,
                    'token' => $token,
                    'agent' => $agent,
                    'token_status' => '1',
                    'created_at' => $updated_at
                );

                $success = $this->db->insert('users', $user_data);
                $id = $this->db->insert_id();

                $doctor_data = array(
                    'user_id' => $id,
                    'doctor_name' => $doctor_name,
                    'category' => $category,
                    'email' => $email,
                    'telephone' => $phone,
                    'gender' => $gender,
                    'dob' => $dob,
                    'reg_council' => $reg_council,
                    'reg_number' => $reg_number,
                    'reg_date' => $updated_at,
                    'qualification' => $qualification,
                    'experience' => $experience,
                    'image' => "",
                    'is_approval' => 1
                );
                $success = $this->db->insert('doctor_list', $doctor_data);
                if ($success) {
                    $date_array = array(
                        'listing_id' => $id,
                        'name' => $doctor_name,
                        'type' => $vendor_id,
                        'phone' => $phone,
                        'email' => $email
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
        }
    }

    public function doctor_profile_pic($listing_id, $profile_pic_file) {
        $query = $this->db->query("UPDATE doctor_list SET image='$profile_pic_file' WHERE user_id='$listing_id'");

        $usr_query = $this->db->query("SELECT avatar_id FROM users WHERE id='$listing_id'");
        $get_usr = $usr_query->row_array();
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
            $a_id = $this->db->insert_id();
            $query = $this->db->query("UPDATE users SET avatar_id='$a_id' WHERE id='$listing_id'");
        } else {
            $query = $this->db->query("UPDATE media SET title='$profile_pic_file',source='$profile_pic_file' WHERE id='$avatar_id'");
        }
         $actual_image_path = 'images/doctor_images/' . $profile_pic_file;
        return array(
            'status' => 200,
            'message' => 'success',
            'image' =>  "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/$profile_pic_file",
        );
    }

    public function doctor_my_profile_details($listing_id, $type) {
        $query = $this->db->query("SELECT doctor_list.image,doctor_list.about_us,doctor_list.speciality,doctor_list.service,doctor_list.awards_recognition, doctor_list.awards_year,doctor_list.doctor_name, doctor_list.email, doctor_list.qualification, doctor_list.experience, doctor_list.gender, doctor_list.dob, doctor_list.reg_number, doctor_list.reg_council,doctor_list.is_approval,doctor_list.is_active, users.phone FROM doctor_list LEFT JOIN users ON doctor_list.user_id=users.id WHERE doctor_list.user_id='$listing_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            $row = $query->row_array();
            $doctor_name = $row['doctor_name'];
            $email = $row['email'];
            $phone = $row['phone'];
            $qualification = $row['qualification'];
            $experience = $row['experience'];
            $gender = $row['gender'];
            $dob = $row['dob'];
            $reg_number = $row['reg_number'];
            $reg_council = $row['reg_council'];
            $profile_pic = $row['image'];
            $is_approval = $row['is_approval'];
            $is_active = $row['is_active'];
            $about_us = $row['about_us'];
            $speciality = str_replace(", ", ",", $row['speciality']);
            $service = str_replace(", ", ",", $row['service']);
            $awards_recognition = str_replace(", ", ",", $row['awards_recognition']);
            $awards_year = str_replace(", ", ",", $row['awards_year']);
            $speciality = explode(',', $speciality);
            $service = explode(',', $service);

            if ($row['image'] != '') {
                $profile_pic = $row['image'];
                $profile_pic = str_replace(' ', '%20', $profile_pic);
                $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
            } else {
                $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
            }
            $doctor_profile = array(
                "doctor_name" => $doctor_name,
                "profile_pic" => $profile_pic,
                "email" => $email,
                "qualification" => $qualification,
                "experience" => $experience,
                "gender" => $gender,
                "dob" => $dob,
                "reg_number" => $reg_number,
                "reg_council" => $reg_council,
                "phone" => $phone,
                "is_approve" => (int) $is_approval,
                "is_active" => (int) $is_active
            );


            $specialization_query = $this->db->query("SELECT id,specialization FROM `doctor_specialization` order by specialization asc");
            foreach ($specialization_query->result_array() as $specialization_list) {
                $id = $specialization_list['id'];
                $speciality_value = $specialization_list['specialization'];
                if (in_array($speciality_value, $speciality)) {
                    $is_specialization_active = '1';
                } else {
                    $is_specialization_active = '0';
                }

                $specialization_result[] = array(
                    "id" => (int) $id,
                    "name" => $speciality_value,
                    "is_specialization_active" => (int) $is_specialization_active
                );
            }

            $service_query = $this->db->query("SELECT id,service FROM `doctor_services` order by service asc");
            foreach ($service_query->result_array() as $service_list) {
                $id = $service_list['id'];
                $servic_value = $service_list['service'];
                if (in_array($servic_value, $service)) {
                    $is_service_active = '1';
                } else {
                    $is_service_active = '0';
                }

                $service_result[] = array(
                    "id" => (int) $id,
                    "name" => $servic_value,
                    "is_service_active" => (int) $is_service_active
                );
            }

            $awards_recognition = explode(",", $awards_recognition);
            $awards_year = explode(",", $awards_year);
            foreach ($awards_recognition as $index => $awards) {
                $doctor_awards_recognition[] = array(
                    "awards_recognition" => $awards,
                    "awards_year" => (int) $awards_year[$index]
                );
            }

            $resultpost[] = array(
                "doctor_profile" => $doctor_profile,
                "about_us" => $about_us,
                "specialization" => $specialization_result,
                "service" => $service_result,
                "doctor_awards_recognition" => $doctor_awards_recognition
            );
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function doctor_specialization_update($listing_id, $speciality) {
        $query = $this->db->query("SELECT * FROM `doctor_list` WHERE user_id = '$listing_id'");
        $doctor_specialization = array(
            "speciality" => $speciality
        );

        $this->db->where('user_id', $listing_id);
        $this->db->UPDATE('doctor_list', $doctor_specialization);
        return array(
            'status' => 201,
            'message' => 'success',
            'speciality' => $speciality
        );
        return $resultpost;
    }

    public function doctor_specialization() {
        return $this->db->select('id,specialization')->from('doctor_specialization')->order_by('id', 'asc')->get()->result();
    }

    public function doctor_services() {
        return $this->db->select('id,service')->from('doctor_services')->order_by('id', 'asc')->get()->result();
    }

    public function doctor_details($listing_id, $type) {
        $query = $this->db->query("SELECT doctor_list.reg_date,doctor_list.image, doctor_list.doctor_name, doctor_list.lat, doctor_list.lng, doctor_list.email, doctor_list.qualification, doctor_list.experience, doctor_list.gender, doctor_list.dob, doctor_list.reg_number, doctor_list.reg_council, users.phone, doctor_list.is_approval, doctor_list.is_active FROM doctor_list LEFT JOIN users ON doctor_list.user_id=users.id WHERE doctor_list.user_id='$listing_id'");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {

                $reg_date = $row['reg_date'];
                $doctor_name = $row['doctor_name'];
                $latitude = $row['lat'];
                $longitude = $row['lng'];
                $email = $row['email'];
                $qualification = $row['qualification'];
                $experience = $row['experience'];
                $gender = $row['gender'];
                $dob = $row['dob'];
                $reg_number = $row['reg_number'];
                $reg_council = $row['reg_council'];
                $phone = $row['phone'];
                $is_approval = $row['is_approval'];
                $is_active = $row['is_active'];

                $profile_pic = $row['image'];
                if ($row['image'] != '') {
                    $profile_pic = $row['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }


                $resultpost = array(
                    "reg_date" => $reg_date,
                    "doctor_name" => $doctor_name,
                    "profile_pic" => $profile_pic,
                    "latitude" => $latitude,
                    "longitude" => $longitude,
                    "email" => $email,
                    "qualification" => $qualification,
                    "experience" => $experience,
                    "gender" => $gender,
                    "dob" => $dob,
                    "reg_number" => $reg_number,
                    "reg_council" => $reg_council,
                    "is_approval" => $is_approval,
                    "is_active" => $is_active,
                    "phone" => $phone
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function doctor_documents_upload($listing_id, $medical_registration_pic, $medical_degree_pic, $government_id_pic, $prescription_pad_pic, $business_card_pic) {
        $query = $this->db->query("UPDATE doctor_list SET `medical_registration_pic`='$medical_registration_pic',`medical_degree_pic`='$medical_degree_pic',`government_id_pic`='$government_id_pic',`prescription_pad_pic`='$prescription_pad_pic',`business_card_pic`='$business_card_pic'WHERE user_id='$listing_id'");
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }

    public function doctor_clinic_list($user_id) {
        $query = $this->db->query("SELECT dc.appointment_time,dc.consultation_charges,dc.id,dc.doctor_id,dc.image,dc.id as hospital_id,dc.clinic_name,dc.contact_no,dc.open_hours,dc.address,dc.state,dc.city,dc.pincode,dc.map_location,dc.lat,dc.lng FROM doctor_clinic as dc LEFT JOIN doctor_list as dl ON(dl.user_id=dc.doctor_id) LEFT JOIN users as u ON(dl.user_id=u.id) WHERE dc.doctor_id='" . $user_id . "' ORDER BY dc.id desc");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {

                $clinic_name = $row['clinic_name'];
                $address = $row['address'];
                $state = $row['state'];
                $city = $row['city'];
                $pincode = $row['pincode'];
                $map_location = $row['map_location'];
                $lat = $row['lat'];
                $lng = $row['lng'];
                $image = $row['image'];
                $consultation_charges = $row['consultation_charges'];
                $contact_no = $row['contact_no'];
                $appointment_time = $row['appointment_time'];
                $opening_hours = $row['open_hours'];
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
                                for ($l = 0; $l < count($time_list1); $l++) {
                                    $time_check = $time_list1[$l] . '-' . $time_list2[$l];
                                    $time[] = str_replace('close-close', 'close', $time_check);
                                    $system_start_time = date("H.i", strtotime($time_list1[$l]));
                                    $system_end_time = date("H.i", strtotime($time_list2[$l]));
                                    $current_time = date('H.i');
                                    if ($current_time > $system_start_time && $current_time < $system_end_time) {
                                        $open_close[] = 'open';
                                    } else {
                                        $open_close[] = 'close';
                                    }
                                }
                            }
                        }
                        $final_Day[] = array(
                            'day' => $day_list[0],
                            'time' => $time,
                            'status' => $open_close
                        );
                    }
                } else {
                    $final_Day[] = array(
                        'day' => 'close',
                        'time' => array(),
                        'status' => array()
                    );
                }


                $resultpost[] = array(
                    'id' => $row['id'],
                    'clinic_name' => $row['clinic_name'],
                    'address' => $row['address'],
                    'state' => $row['state'],
                    'city' => $row['city'],
                    'pincode' => $row['pincode'],
                    'map_location' => $row['map_location'],
                    'lat' => $row['lat'],
                    'lng' => $row['lng'],
                    'image' => "https://medicalwale.s3.amazonaws.com/images/doctor_images/".$row['image'],
                    'consultation_charges' => $row['consultation_charges'],
                    'contact_no' => $row['contact_no'],
                    'time_slot' => $appointment_time,
                    'opening_day' => $final_Day
                );
            }


        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function add_patient($data) {

        $patient_insert = $this->db->insert('doctor_patient', $data);
        $p_id = $this->db->insert_id();

        if ($patient_insert) {
            $data_array = array(
                'patient_id' => $p_id,
                'success' => "OK"
            );
            return $data_array;
        }
    }

    public function list_patient($doctor_id) {

        return $this->db->select('*')->from('doctor_patient')->where('doctor_id', $doctor_id)->get()->result();
    }

    public function edit_patient($patient_id, $data) {

        $result = $this->db->where('id', $patient_id)->update('doctor_patient', $data);
        return $result;
    }

    public function delete_patient($doctor_id, $patient_id) {

        $this->db->where('id', $patient_id);
        $delete_patient = $this->db->delete('doctor_patient');

        return $delete_patient;
    }

    public function partner_doctor_details($listing_id) {
        $query = $this->db->query("SELECT * FROM doctor_list WHERE user_id='$listing_id'");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $doctor_name = $row['doctor_name'];
                $phone = $row['telephone'];
                $email = $row['email'];
                $experience = $row['experience'];
                $reg_number = $row['reg_number'];
                $reg_council = $row['reg_council'];
                $gender = $row['gender'];
                $dob = $row['dob'];
                $speciality = $row['speciality'];
                $service = $row['service'];
                $degree = $row['qualification'];
                $category = $row['category'];
                $followers = 0;
                $following = 0;
                $total_review = 0;

                $profile_pic = $row['image'];
                if ($row['image'] != '') {
                    $profile_pic = $row['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }



                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
                $total_review = $this->db->select('id')->from('doctors_review')->where('doctor_id', $listing_id)->get()->num_rows();



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


                $speciality_array = array();
                $speciality_ = explode(',', $speciality);
                foreach ($speciality_ as $speciality_) {
                    $speciality_array[] = array(
                        'speciality' => $speciality_
                    );
                }

                $service_array = array();
                $service_ = explode(',', $service);
                foreach ($service_ as $service_) {
                    $service_array[] = array(
                        'service' => $service_
                    );
                }

                $degree_array = array();
                $degree_ = explode(',', $degree);
                foreach ($degree_ as $degree_) {
                    $degree_array[] = array(
                        'degree' => $degree_
                    );
                }

                $resultpost = array(
                    "doctor_name" => $doctor_name,
                    "phone" => $phone,
                    "email" => $email,
                    "profile_pic" => $profile_pic,
                    "area_expertise" => $area_expertise,
                    "speciality" => $speciality_array,
                    "service" => $service_array,
                    "degree" => $degree_array,
                    "experience" => $experience,
                    "reg_council" => $reg_council,
                    "reg_number" => $reg_number,
                    "gender" => $gender,
                    "dob" => $dob,
                    "followers" => $followers,
                    "following" => $following,
                    "total_review" => $total_review
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function doctor_add_clinic($data) {
        $this->db->insert('doctor_clinic', $data);
        $clinic_id = $this->db->insert_id();
        return $clinic_id;
    }

    public function doctor_edit_clinic($clinic_id, $data) {
        
  $q = $this->db->select('id')->from('doctor_clinic')->where('id', $clinic_id)->get()->row();
  if(!empty($q)){
        $this->db->where('id', $clinic_id)->update('doctor_clinic', $data);
        return '1'; 
        }

    }

    public function doctor_delete_clinic($clinic_id) {

        $this->db->where('id', $clinic_id);
        $delete_clinic = $this->db->delete('doctor_clinic');

        return $delete_clinic;
    }

    public function partner_doctor_update($listing_id, $doctor_name, $email, $experience, $reg_council, $reg_number, $gender, $dob, $area_expertise, $speciality, $service, $degree) {

        $query = $this->db->query("UPDATE doctor_list INNER JOIN users ON doctor_list.user_id=users.id
		SET
		users.name = '$doctor_name',
		doctor_list.doctor_name = '$doctor_name',		
		doctor_list.email = '$email',
		doctor_list.experience = '$experience',
		doctor_list.reg_council = '$reg_council',
		doctor_list.reg_number = '$reg_number',
		doctor_list.gender = '$gender',
		doctor_list.dob = '$dob',
		doctor_list.category = '$area_expertise',	
		doctor_list.speciality = '$speciality',	
		doctor_list.service = '$service',	
		doctor_list.qualification = '$degree'
		WHERE doctor_list.user_id ='$listing_id'");

        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
      public function add_doctor_medicine($doctor_id,$patient_id,$medicine_array)
    {
        $this->db->insert('doctor_medicine', $medicine_array);
        //$medicine_id = $this->db->insert_id();
        $patient_medicines = $this->db->select('id,doctor_id, patient_id, medicine_name, dose, dose_unit, frequency, hours, duration,duration_circle, dose_time, instruction')->from('doctor_medicine')->where('doctor_id',$doctor_id)->where('patient_id',$patient_id)->get()->result();
        return array(
            'status' => 200,
            'message' => 'success',
            'medicine_details' => $patient_medicines,
            //'medicine_id' => $medicine_id
        );
    }
    
         public function edit_medicine_details($medicine_id) {
        return $this->db->select('id,doctor_id, patient_id, medicine_name, dose, dose_unit, frequency, hours, duration,duration_circle, dose_time, instruction')->from('doctor_medicine')->where('id',$medicine_id)->get()->row();

    }
    public function delete_medicine_details($medicine_id) {
       $delete =  $this->db->query("DELETE FROM `doctor_medicine` WHERE id='$medicine_id'");
       return array(
            'status' => 200,
            'message' => 'success'
        );

    }
    
     public function update_doctor_medicine($medicine_id,$medicine_name,$dose,$dose_unit,$frequency,$hours,$duration,$duration_circle,$dose_time,$instruction)
    {
        $query = $this->db->query("UPDATE doctor_medicine SET medicine_name='$medicine_name',dose='$dose',dose_unit='$dose_unit',frequency='$frequency',hours='$hours',duration='$duration',duration_circle='$duration_circle',dose_time='$dose_time',instruction='$instruction' WHERE id='$medicine_id'");
        
        
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
     public function patient_all_medicine($doctor_id,$patient_id)
    {
        
        $patient_medicines = $this->db->select('id,doctor_id, patient_id, medicine_name, dose, dose_unit, frequency, hours, duration,duration_circle, dose_time, instruction')->from('doctor_medicine')->where('doctor_id',$doctor_id)->where('patient_id',$patient_id)->get()->result();
        return array(
            'status' => 200,
            'message' => 'success',
            'medicine_details' => $patient_medicines,
            //'medicine_id' => $medicine_id
        );
    }
    public function add_doctor_prescription($data) {
        $this->db->insert('doctor_prescription', $data);
        
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    public function doctors_prescription_list($doctor_id)
    {
        /*$this->db->select('count(id) as id');
        $this->db->where('doctor_id',$doctor_id);
        $this->db->from('doctor_prescription');
        $count = $this->db->get()->row()->id;*/
     $medicines = array();
     $query = $this->db->query("SELECT * from doctor_prescription where doctor_id = '$doctor_id' ");
        foreach ($query->result_array() as $row) {
            $medicines_id = $row['medicine_id'];
            
            $id             = $row['id'];
            $patient_id     = $row['patient_id'];
            $clinic_id      = $row['clinic_id'];
            $prescription   = $row['prescription'];
            $doctor_id      = $row['doctor_id'];
            $created_date   = $row['created_date'];
            
            $query1 = $this->db->query("SELECT id,medicine_name,dose,dose_unit from doctor_medicine where id IN ($medicines_id) ");
            
                $count = $query1->num_rows();
                $results = $query1->result();
               unset($medicines);
                for($i=0;$i<$count;$i++){
                $medicines[] = array(
                        "id" => $results[$i]->id,
                        "medicine_name" => $results[$i]->medicine_name,
                        "dose" => $results[$i]->dose,
                        "dose_unit" => $results[$i]->dose_unit

                    );
                
                }
              
         
            $query1 = $this->db->query("SELECT doctor_prescription.id,doctor_list.doctor_name, doctor_patient.patient_name, doctor_clinic.clinic_name FROM `doctor_prescription` LEFT JOIN doctor_patient on (doctor_patient.id = doctor_prescription.patient_id) LEFT JOIN  doctor_clinic ON (doctor_clinic.id = doctor_prescription.clinic_id) LEFT JOIN doctor_list ON (doctor_list.id = doctor_prescription.doctor_id) WHERE doctor_prescription.id = '$id'");
                $join_result = $query1->row();
                
                
            $resultpost[] = array(
                "id" => $id,
                "patient_id" => $patient_id,
                "clinic_id" => $clinic_id,
                "prescription" => $prescription,
                "doctor_id" => $doctor_id,
                "created_date" => $created_date,
                "medicines" => $medicines,
                "doctor_name" => $join_result->doctor_name,
                "clinic_name" => $join_result->clinic_name,
                "patient_name" => $join_result->patient_name
            );

             
        }
        
        if(count($resultpost) >0){
        return array(
            'status' => 200,
            'message' => 'success',
            'data' => $resultpost
        );
        }
        else{
            $resultpost = array();
        return $resultpost; 
        }
    }
    
        public function doctors_prescription_list_edit($prescription_id)
    {
        /*$this->db->select('count(id) as id');
        $this->db->where('doctor_id',$doctor_id);
        $this->db->from('doctor_prescription');
        $count = $this->db->get()->row()->id;*/

     $query = $this->db->query("SELECT * from doctor_prescription where doctor_prescription.id = '$prescription_id'");
         $results = $query->row();
            $medicines = $results->medicine_id;
            $id = $results->id;
            $patient_id = $results->patient_id;
            $clinic_id =$results->clinic_id;
            $prescription = $results->prescription;
            $doctor_id = $results->doctor_id;
            $created_date = $results->created_date;
            
            $query1 = $this->db->query("SELECT * from doctor_medicine where id IN ($medicines) ");
           
            $results = $query1->result();
            
                /* $madi = "";
                for($i=0;$i<count($results);$i++){
                $madi .= $results[$i]->medicine_name. ","  ;    
                } */
                for($i=0;$i<count($results);$i++){
                $mediciness[] = array(
                        "id" => $results[$i]->id,
                        "doctor_id" => $results[$i]->doctor_id,
                        "patient_id" => $results[$i]->patient_id,
                        "medicine_name" => $results[$i]->medicine_name,
                        "dose" => $results[$i]->dose,
                        "dose_unit" => $results[$i]->dose_unit,
                        "frequency" => $results[$i]->frequency,
                        "hours" => $results[$i]->hours,
                        "duration" => $results[$i]->duration,
                        "duration_circle" => $results[$i]->duration_circle,
                        "dose_time" => $results[$i]->dose_time,
                        "instruction" => $results[$i]->instruction

                    
                    );
                
                }
              
            $query1 = $this->db->query("SELECT doctor_prescription.id,doctor_list.doctor_name, doctor_patient.patient_name, doctor_clinic.clinic_name FROM `doctor_prescription` LEFT JOIN doctor_patient on (doctor_patient.id = doctor_prescription.patient_id) LEFT JOIN  doctor_clinic ON (doctor_clinic.id = doctor_prescription.clinic_id) LEFT JOIN doctor_list ON (doctor_list.id = doctor_prescription.doctor_id) WHERE doctor_prescription.id = '$prescription_id'");
                $join_result = $query1->row();
                
                
            $resultpost[] = array(
                "id" => $id,
                "patient_id" => $patient_id,
                "clinic_id" => $clinic_id,
                "prescription" => $prescription,
                "doctor_id" => $doctor_id,
                "created_date" => $created_date,
                "medicines" => $mediciness,
                "doctor_name" => $join_result->doctor_name,
                "clinic_name" => $join_result->clinic_name,
                "patient_name" => $join_result->patient_name
            );
        
        
        if(count($resultpost) >0){
        return array(
            'status' => 200,
            'message' => 'success',
            'data' => $resultpost
        );
        }
        else{
            $resultpost = array();
        return $resultpost; 
        }
     

    }


  
    
     public function delete_prescription($prescription_id) {
          $query = $this->db->query("SELECT * from doctor_prescription where doctor_prescription.id = '$prescription_id'");
         $results = $query->row();
         $medicines = $results->medicine_id;
       $delete =  $this->db->query("DELETE FROM `doctor_medicine` WHERE id IN ($medicines)");
       
       $delete_prescription =  $this->db->query("DELETE FROM `doctor_prescription` WHERE id='$prescription_id'");
      
       
       return array(
            'status' => 200,
            'message' => 'success'
        );

    }
    
    public function update_doctor_prescription($prescription_id,$medicine_id,$patient_id,$clinic_id,$doctor_id,$prescription,$updated_at)
    {
        echo "UPDATE doctor_prescription SET patient_id='$patient_id',clinic_id='$clinic_id',medicine_id='$medicine_id',prescription='$prescription',created_date='$updated_at' where doctor_prescription.id='$prescription_id'";
        die();
        
        $query = $this->db->query("UPDATE doctor_prescription SET patient_id='$patient_id',clinic_id='$clinic_id',medicine_id='$medicine_id',prescription='$prescription',created_date='$updated_at' where doctor_prescription.id='$prescription_id'");
        
        
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
        public function doctors_appointment_list($doctor_id) {
        $query = $this->db->query("select doctor_patient.id,doctor_patient.patient_name,doctor_patient.contact_no, doctor_clinic.clinic_name,doctor_clinic.image,doctor_clinic.consultation_charges,doctor_clinic.address,doctor_booking_master.booking_date,doctor_booking_master.booking_time  from doctor_patient INNER JOIN doctor_clinic ON(doctor_patient.doctor_id = doctor_clinic.doctor_id ) LEFT JOIN doctor_booking_master ON(doctor_booking_master.clinic_id = doctor_clinic.id) where doctor_clinic.doctor_id = '$doctor_id' ");

        foreach ($query->result_array() as $row) {
            $clinic_name = $row['clinic_name'];
            $clinic_image = "https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/".$row['image'];
            $consultation_charges = $row['consultation_charges'];
            $patient_name = $row['patient_name'];
            $contact_no = $row['contact_no'];
            $address = $row['address'];
            $booking_date = $row['booking_date'];
            $booking_time = $row['booking_time'];
            if($booking_date == null || $booking_time == null ){
                $booking_date = $booking_date.$booking_date;
                
            }
            
            
        $resultpost[] = array(
            'clinic_name'=> $clinic_name,
            'clinic_image'=>$clinic_image,
            'consultation_charges'=> $consultation_charges,
            'patient_name'=> $patient_name,
            'contact_no'=> $contact_no,
            'address'=> $address,
            'booking_date'=> $booking_date,
        );
        
        }

        if(!empty($resultpost)){
            return $resultpost;
        }else{
            $resultpost = array();
            return $resultpost;
        }
    }
    
        public function doctor_consultation_add_service($doctor_id, $data) {

                $success = $this->db->insert('doctor_consultation', $data);
                $id = $this->db->insert_id();

                if($id>0){
                    return "OK";
                }else{

                    return "";
                }
    }
        public function doctor_consultation_list_service($doctor_id,$consultation_name) {

                $success[] = $this->db->select('*')->from('doctor_consultation')->where('doctor_id', $doctor_id)->where('consultation_name', $consultation_name)->get()->row();

                if(!empty($success)){
                    return $success;
                }else{

                    return "";
                }
    }
    
    
}
