<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class JobplacementModel extends CI_Model {

    var $client_service = "frontend-client";
    var $auth_key = "medicalwalerestapi";
    
    
     
    
    
    public function check_auth_client() {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key = $this->input->get_request_header('Auth-Key', TRUE);
        if ($client_service == $this->client_service && $auth_key == $this->auth_key) {
            return true;
        } else {
            return json_output(401, array('status' => 401, 'message' => 'Unauthorized.'));
        }
    }

    public function auth() {
        date_default_timezone_set('Asia/Kolkata');
        $users_id = $this->input->get_request_header('User-ID', TRUE);
        $token = $this->input->get_request_header('Authorizations', TRUE);
        $q = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id', $users_id)->where('token', $token)->get()->row();
        if ($q == "") {
            return json_output(401, array('status' => 401, 'message' => 'Unauthorized.'));
        } else {
            if ($q->expired_at < date('Y-m-d H:i:s')) {
                return json_output(401, array('status' => 401, 'message' => 'Your session has been expired.'));
            } else {
                $updated_at = date('Y-m-d H:i:s');
                $expired_at = '2030-11-12 08:57:58';
                $this->db->where('users_id', $users_id)->where('token', $token)->update('api_users_authentication', array('expired_at' => $expired_at, 'updated_at' => $updated_at));
                return array('status' => 200, 'message' => 'Authorized.');
            }
        }
    }

    public function placement_list() {
        return $this->db->select('id,category')->from('job_category')->order_by('category', 'asc')->get()->result();
    }

    public function job_list($id,$job_title, $state, $city,$employment_type) {
        if ($job_title != '0'||$state != '0'||$city != '0'||$employment_type != '0') {
            
    /*   echo"select job_list.*,states.state_name,cities.city_name,cities.state_id,job_cat.cat_id,job_cat.cat_name,job_category.category from job_list 
            INNER JOIN job_category on (job_category.id=job_list.job_title)
            INNER JOIN states on (states.state_id=job_list.state)
            INNER JOIN cities on( cities.city_id=job_list.id)
            INNER JOIN job_cat on (job_cat.cat_id=job_list.employment_type)
            where job_list.id='$id' order by job_list.id desc";   */
        
            $query = $this->db->query("select job_list.*,states.state_name,cities.city_name,cities.state_id,job_cat.cat_id,job_cat.cat_name,job_category.category as jobtitle_category from job_list 
            INNER JOIN job_category on (job_category.id=job_list.job_title)
            INNER JOIN states on (states.state_id=job_list.state)
            INNER JOIN cities on( cities.city_id=job_list.id)
            INNER JOIN job_cat on (job_cat.cat_id=job_list.employment_type)
            where job_list.id='$id' order by job_list.id desc");
        }


        $resultpost = array();

        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            $user_id = $row['user_id'];
            $job_title = $row['job_title'];
            $category = $row['jobtitle_category'];
            $company_name = $row['company_name'];
            $employment_type = $row['employment_type'];
            $cat_name = $row['cat_name'];
            
            $seniority_level = $row['seniority_level'];
            $state = $row['state'];
            $job_state = $row['state_name'];
            $city = $row['city'];
            $city_name = $row['city_name'];
            $min_salary = $row['min_salary'];
            $max_salary = $row['max_salary'];
            $intern_duration = $row['intern_duration'];
            $candidates_required = $row['candidates_required'];
            $gender = $row['gender'];
            $job_description=$row['job_description'];
            $submit_resume=$row['submit_resume'];
            $mobile=$row['mobile'];
            $email=$row['email'];
            $posted_on = $row['posted_on'];

            $resultpost[] = array(
            'id' => $id, 
            'user_id' => $user_id, 
            'job_title' => $job_title,
            'jobtitle_category' => $category, 
            'company_name' => $company_name, 
            'employment_type' => $employment_type,
            'cat_name' => $cat_name,
            
            'seniority_level' => $seniority_level,
            'state' => $state,
            'state_name'=>$job_state,
            'city' => $city,
             'city_name' => $city_name,
            'min_salary' => $min_salary,
            'max_salary' => $max_salary,
            'intern_duration' => $intern_duration, 
            'candidates_required' => $candidates_required, 
            'gender' => $gender, 
            'job_description' => $job_description, 
            'submit_resume' => $submit_resume, 
            'mobile' => $mobile,
            'email' => $email,
            'posted_on' => $posted_on);
        }

        return $resultpost;
    }

    public function add_job($job_type, $job_title, $job_description, $job_role, $job_location, $min_salary, $max_salary, $company_name, $email, $mobile, $gender) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $job_data = array(
            'job_type' => $job_type,
            'job_title' => $job_title,
            'job_description' => $job_description,
            'job_role' => $job_role,
            'job_location' => $job_location,
            'min_salary' => $min_salary,
            'max_salary' => $max_salary,
            'company_name' => $company_name,
            'email' => $email,
            'mobile' => $mobile,
            'gender' => $gender,
            'posted_on' => $created_at
        );
        $success = $this->db->insert('job_list', $job_data);
        $id = $this->db->insert_id();
        return array('status' => 201, 'message' => 'success', 'job_id' => $id);
    }

    public function add_job_user_profile($name, $phone, $email, $dob, $gender, $job_role, $min_salary, $max_salary, $year_exp, $month_exp, $city, $user_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $job_data = array(
            'user_id' => $user_id,
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'dob' => $dob,
            'gender' => $gender,
            'job_role' => $job_role,
            'min_salary' => $min_salary,
            'max_salary' => $max_salary,
            'year_exp' => $year_exp,
            'month_exp' => $month_exp,
            'city' => $city,
            'posted_on' => $created_at
        );


        $success = $this->db->insert('job_user_profile', $job_data);
        $id = $this->db->insert_id();
        if ($success) {
            $date_array = array(
                'user_id' => $user_id,
                'name' => $name,
                'phone' => $phone,
                'gender' => $gender
            );
            return array('status' => 201, 'message' => 'success', 'data' => $date_array);
        } else {
            return array(
                'status' => 208,
                'message' => 'failed'
            );
        }
    }

    public function user_profile_doc($user_id, $resume_file) {
        $query = $this->db->query("UPDATE job_user_profile SET resume='$resume_file' WHERE user_id='$user_id'");

        return array(
            'status' => 200,
            'message' => 'success'
        );
    }

    public function job_role() {
        $query = $this->db->query("SELECT * FROM `job_category` ORDER BY category ASC");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $job_role_id = $row['id'];
                $job_role = $row['category'];
                $resultpost[] = array(
                    "job_role_id" => $job_role_id,
                    "job_role" => $job_role
                );
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }

    public function user_profile_list($user_id) {



        $query = $this->db->query("SELECT * FROM `job_user_profile` ORDER BY id DESC");


        $resultpost = array();

        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            $name = $row['name'];
            $phone = $row['phone'];
            $email = $row['email'];
            $dob = $row['dob'];
            $gender = $row['gender'];
            $min_salary = $row['min_salary'];
            $max_salary = $row['max_salary'];
            $job_role = $row['job_role'];
            $year_exp = $row['year_exp'];
            $month_exp = $row['month_exp'];
            $city = $row['city'];
            $resume = $row['resume'];
            $posted_on = $row['posted_on'];

            if ($dob != "") {
                $new_dob = date("Y-m-d", strtotime($dob));
                $today = date("Y-m-d");
                $diff = date_diff(date_create($new_dob), date_create($today));
                $age = $diff->format('%y');
            }
            if ($resume != '') {
                $resume = 'Resume uploded';
            } else {
                $resume = 'Resume not uploded';
            }


            $resultpost[] = array('id' => $id,
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'dob' => $dob,
                'age' => $age,
                'gender' => $gender,
                'gender' => $gender,
                'min_salary' => $min_salary,
                'max_salary' => $max_salary,
                'job_role' => $job_role,
                'year_exp' => $year_exp,
                'month_exp' => $month_exp,
                'city' => $city,
                'resume' => $resume,
                'posted_on' => $posted_on);
        }

        return $resultpost;
    }
    
    
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
                "notification_type" => 'Jobplacement_notifications',
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
    
    
        public function notify_shortlist($user_id,$vendor_id,$user_name,$user_phone,$user_email,$vendor_email,$vendor_name,$vendor_phone) {
        
        $message = 'Your Resume has been shortlisted by '. $vendor_name.' at Medicalwale Job Portal';
        $post_data = array('From' => '02233721563', 'To' => $user_phone, 'Body' => $message);
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
        
         $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
            $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $vendor_id)->get()->num_rows();
            if ($img_count > 0) {
                $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $vendor_id)->get()->row();
                $img_file = $profile_query->source;
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
            } else {
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
            }
            $getusr = $user_plike->row_array();
            $usr_name = $getusr['name'];
            $msg = $usr_name . ' Your Resume has been shortlisted by '. $vendor_name.' at Medicalwale Job Placement. ';
            $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
            $title = $usr_name . ' Your Resume Shortlisted by '. $vendor_name.' at Medicalwale Job Placement. ';
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
            
        $this->job_placement_sendmail($user_email,$user_name);
        
       
        $resp = array(
            'status' => 204,
            'message' => 'success'
        );
        
        return $resp;
    }
    
     function  job_placement_sendmail($user_email,$user_name){    
$subject="REGISTRATION INFORMATION"; 
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= 'From: Medicalwale.com <no-reply@medicalwale.com>' . "\r\n";
$headers .= 'Cc: ' . "\r\n";


$message='<div style="max-width: 700px;float: none;margin: 0px auto;">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
   <div id="styles_holder">
      <style>
         .ReadMsgBody { width: 100%; background-color: #ffffff; }
         .ExternalClass { width: 100%; background-color: #ffffff; }
         .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }
         html { width: 100%; }
         body { -webkit-text-size-adjust: none; -ms-text-size-adjust: none; margin: 0; padding: 0; }
         table { border-spacing: 0; border-collapse: collapse; table-layout: fixed; margin:0 auto; }
         table table table { table-layout: auto; }
         img { display: block !important; }
         table td { border-collapse: collapse; }
         .yshortcuts a { border-bottom: none !important; }
         a { color: #1abc9c; text-decoration: none; }
         /*Responsive*/
         @media only screen and (max-width: 640px) {
         body { width: auto !important; }
         table[class="table-inner"] { width: 90% !important; }
         table[class="table-full"] { width: 100% !important; text-align: center !important; }
         /* Image */
         img[class="img1"] { width: 100% !important; height: auto !important; }
         }
         @media only screen and (max-width: 479px) {
         body { width: auto !important; }
         table[class="table-inner"] { width: 90% !important; }
         table[class="table-full"] { width: 100% !important; text-align: center !important; }
         /* image */
         img[class="img1"] { width: 100% !important; }
         }


      </style>
   </div>
  
   <div id="frame" class="ui-sortable">
      <table data-thumb="" data-module="header-bar" data-bgcolor="Main BG" align="center" width="100%" bgcolor="#e7e7e7" border="0" cellspacing="0" cellpadding="0" class="">
         <tbody>
            <tr>
               <td data-border-top-color="Top Border" style="border-top:4px solid #049341;"></td>
            </tr>
            <tr>
               <td height="25"></td>
            </tr>
            <tr>
               <td align="center">
                  <table data-bgcolor="Container" width="600" class="table-inner" bgcolor="#ffffff" style="border-top-left-radius:5px;border-top-right-radius:5px;" border="0" align="center" cellpadding="0" cellspacing="0">
                     <tbody>
                        <tr>
                           <td align="center" style="border-bottom: 5px solid #049341;">
                              <table class="table-inner" width="520" border="0" align="center" cellpadding="0" cellspacing="0">
                                 <tbody>
                                    <tr>
                                       <td style="padding: 10px 0px;">
                                          <!--Logo-->
                                          <table class="table-full" border="0" align="left" cellpadding="0" cellspacing="0">
                                             <tbody>
                                                <tr>
                                                   <td align="center" style="line-height:0px;">
                                                      <img data-crop="false" mc:edit="quinn-box-1" style="display:block; line-height:0px; font-size:0px; border:0px;height: 70px;" src="http://medicalwale.com/img/email-logo.png" alt="logo"   >
                                                   </td>
                                                </tr>
                                             </tbody>
                                          </table>
                                          <!--End Logo-->
                                          <!--social-->
                                          <table border="0" align="right" cellpadding="0" cellspacing="0" class="table-full">
                                             <tbody>
                                                <tr>
                                                   <td height="15"></td>
                                                </tr>
                                                <tr>
                                                   <td align="center">
                                                      <table border="0" align="center" cellpadding="0" cellspacing="0">
                                                         <tbody>
                                                            <tr>
                                                               <td align="center" style="">
                                                                   <font style="font-size:11px;line-height:16px" face="font-family: arial,sans-serif;" color="#666666">
                                    <b style="font-size: 12px;font-family: arial, sans-serif;">Congratulations Your Resume Shortlisted</b><br>
                                    </font>
                                    <font style="font-size:14px" face="HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue, Helvetica, Arial, sans-serif" color="#666666">
                                    Call Us: </font><font style="font-size:16px" face="HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica, Arial, sans-serif" color="#7db701"><strong>022-60123457</strong></font>
                                                               </td>
                                                            </tr>
                                                         </tbody>
                                                      </table>
                                                   </td>
                                                </tr>
                                             </tbody>
                                          </table>
                                          <!--End social-->
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                           </td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
         </tbody>
      </table>
	  
	  
      <table data-thumb="" data-module="1-2-right" data-bgcolor="Main BG" width="100%" align="center" bgcolor="#e7e7e7" border="0" cellspacing="0" cellpadding="0" class="">
         <tbody>
            <tr>
               <td align="center">
                  <table data-bgcolor="Container" bgcolor="#FFFFFF" class="table-inner" width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="height: 250px;">
                     <tbody  style="background: url(https://medicalwale.com/img/mail_bg.jpg);background-size: cover;">
                        <tr>
                           <td height="20"></td>
                        </tr>
                        <tr>
                           <td align="center">
                              <table class="table-inner" width="570" border="0" align="center" cellpadding="0" cellspacing="0">
                                 <tbody >
                                    <tr>
                                       <td>
                                          <!-- img -->
                                          <table class="table-full" border="0" align="left" cellpadding="0" cellspacing="0" style="width: 320px;">
                                             <tbody>
                                                <tr>
                                                   <td align="left" style="padding-bottom: 10px;">
                                                       <p><font style="font-size:14px;color:#fff;line-height: 20px;" face="arial,sans-serif" >Welcome to </font><br>
                                       <font style="font-size:22px;color:#fff;" face="arial,sans-serif" ><span class="il">Medicalwale.com</span></font>
                                    </p>
                                    <p><font style="font-size:14px;color:#fff;line-height: 20px;" face="arial,sans-serif" >You are now part of 50,000+ Healthcare Service providers. We are delighted to have you on board with us. </font></p>
                                    <p><font style="font-size:14px;color:#fff;" face="arial,sans-serif" >For any clarification <br> Mail us on: <a href="mailto:partner@medicalwale.com" style="color:#fff">partner@medicalwale.com</a> </font></p>
                                 
                                                   </td>
                                                </tr>
                                             </tbody>
                                          </table>
                                          <table border="0" align="right" cellpadding="0" cellspacing="0" class="table-full" style="width: 250px;">
                                             <!-- Title -->
                                             <tbody>
                                                <tr>
                                                   <td>
												  <!--  <table style="background-color:rgb(249,246,246);border:0px solid rgb(213,210,210);width:100%;" cellpadding="0" cellspacing="0">
                                       <tbody>
									   <tr>
                                             <td style="padding-left:20px;padding-top:10px;padding-bottom:10px;padding-right:10px;    background: #a8abaf;    text-align: center;" valign="top" align="left"><font style="font-size:16px" face="arial,sans-serif" color="#ffffff">Your Login Details</font></td>
                                          </tr>
                                          <tr>
                                             <td style="padding:18px 15px 4px;background: #fff;" align="left">
											 <font style="font-size:14px;color: #656060;font-weight: 600;" face="arial,sans-serif">Link</font>
                                                <font style="font-size:14px;line-height:21px" face="arial,sans-serif" color="#333333">
												<a href="" target="_blank" style="color: #656060;text-decoration: none;"></a></font>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="padding:18px 15px 4px;background: #fff;" align="left">
											 <font style="font-size:14px;color: #656060;font-weight: 600;" face="arial,sans-serif">Email:</font>
                                                <font style="font-size:14px;line-height:21px" face="arial,sans-serif" color="#333333">
												<a href="#" target="_blank" style="color: #656060;text-decoration: none;">'.$user_name.'</a></font>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="padding-bottom:18px;padding-top:4px;padding-left:15px;background: #fff;" align="left">
                                                <font style="font-size:14px;color: #656060;font-weight: 600;" face="arial,sans-serif">Password :</font>
                                                <font style="font-size:14px;line-height:21px;color: #656060" face="arial,sans-serif" ></font>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table> -->
												   </td>
                                                </tr>
                                                <!--End Title-->
                                           
                                               
                                                <!--Content-->
                                              <!--   <tr>
                                                   <td data-link-style="text-decoration:none; color:#1abc9c;" data-link-color="Content" data-size="Content" data-color="Content" mc:edit="quinn-box-25" align="left" style="font-family: Open Sans, Arial, sans-serif; font-size:14px; color:#fff; line-height:28px;">
                                                    <a href="" target="_blank"> <button type="button" style="width: 100%;margin-right: 5px;background: #3c98ed;font-size: 16px;font-weight: bold;color: #fff;font-family: Arial,Helvetica,sans-serif;padding: 6px 12px;font-size: 14px;font-weight: 400;line-height: 1.42857143;text-align: center;-ms-touch-action: manipulation;cursor: pointer;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;background-image: none;border: 1px solid transparent;cursor: pointer;">Login </button></a>
													 
                                                   </td>
                                                </tr> -->
                                                <!--End Content-->
                                             </tbody>
                                          </table>
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                           </td>
                        </tr>
                        <tr>
                           <td height="20"></td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
         </tbody>
      </table>
	  

	  
      <table data-thumb="" data-module="quote" data-bgcolor="Main BG" width="100%" align="center" bgcolor="#e7e7e7" border="0" cellspacing="0" cellpadding="0" class="" >
         <tbody>
            <tr>
               <td data-bgcolor="Feature BG" align="center" bgcolor="#e7e7e7">
                  <table data-bgcolor="Feature inner" bgcolor="#fff" class="table-inner" width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;"> 
                     <tbody>
                        <tr>
                           <td align="center">
                              <table class="table-inner" width="520" border="0" align="center" cellpadding="0" cellspacing="0">
                                 <tbody>
                                    <tr>
                                       <td height="35"></td>
                                    </tr>
                                    <!-- intro -->
                                   
                                    <!-- end intro -->
                                    <tr>
                                       <td height="5"></td>
                                    </tr>
                                    <!-- Quote -->
                                    <tr>
                                       <td data-link-style="text-decoration:none; color:#4a4a4a;" data-link-color="Quote" data-size="Quote" data-color="Quote" mc:edit="quinn-box-56" align="center" style="font-family: century Gothic, arial, sans-serif; font-size:16px; color:#4a4a4a; font-weight:bold;">"Your Resume has been Shorlisted"</td>
                                    </tr>
                                    <!-- end Quote -->
                                                                       <tr>
                                       <td height="35"></td>
                                    </tr>
                                  
                                 </tbody>
                              </table>
                           </td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
          
         </tbody>
      </table>
	  
	  
	  	

      <table data-thumb="" data-module="footer" data-bgcolor="Main BG" width="100%" bgcolor="#e7e7e7" border="0" align="center" cellpadding="0" cellspacing="0" class="">
         <tbody>
            
            <tr>
               <td data-bgcolor="Footer Bar" bgcolor="#191919" align="center" style="font-family: Open Sans, Arial, sans-serif; font-size:11px; color:#ffffff;background: #e7e7e7;">
                  <table class="table-inner" width="600" border="0" align="center" cellpadding="0" cellspacing="0">
                     <tbody>
                        <tr>
                           <td>
                              <!-- copyright -->
                              <table class="table-full" bgcolor="#e7e7e7" border="0" align="left" cellpadding="0" cellspacing="0" style="width: 100%;">
                                 <tbody>
                                    <tr>
                                       <td data-link-style="text-decoration:none; color:#ffffff;" data-link-color="Footer Link" data-size="Content" data-color="Footer Text" mc:edit="quinn-box-81" height="30" style="font-family: Open Sans, Arial, sans-serif; font-size:12px; color: #6d6d6d;line-height: 19px;text-align:center;    padding: 10px 0px;">
By using these service, you agree that you are bound by the Terms of Service.<br/>Copyright 2017 AEGIS HEALTH SOLUTIONS PVT. LTD. All rights reserved.
                                       </td>
                                    </tr>
                                    <tr>
                                       <td height="15"></td>
                                    </tr>
                                 </tbody>
                              </table>
                             
                             
                           </td>
                        </tr>
               
                     </tbody>
                  </table>
               </td>
            </tr>
	
         </tbody>
      </table>

   </div>
</div>';
	
$sentmail = mail($user_email,$subject,$message,$headers);   
}
        
        

}
