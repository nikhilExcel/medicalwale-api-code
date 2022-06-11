<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class LoginModel extends CI_Model
{
    
    var $client_service = "frontend-client";
    var $auth_key = "bulkorderapi";
    
    
    public function check_auth_client()
    {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key       = $this->input->get_request_header('Auth-Key', TRUE);
        
        if ($client_service == $this->client_service && $auth_key == $this->auth_key) {
            return true;
        } else {
            return true;
        }
    }
    
    
    
    
    public function auth()
    {
        date_default_timezone_set('Asia/Kolkata');
        $users_id = $this->input->get_request_header('User-ID', TRUE);
        
        $token = $this->input->get_request_header('Authorizations', TRUE);
        $q     = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id', $users_id)->where('token', $token)->get()->row();
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
    
    public function request_inventory_access($user_id,$party_code,$distributor_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date_created = date('Y-m-d H:i');
        if($party_code>0){
            
        }
        else{
            $query2 = $this->db->query("SELECT id FROM `pbioms_distributor_access` WHERE `vendor_user_id` = '$user_id' and distributor_user_id='$distributor_id' limit 1");
            if($query2->num_rows()>0){
                //
            }
            else{
                $order_data  = array(
                    'vendor_user_id' => $user_id,
                    'distributor_user_id' => $distributor_id,
                    'party_code' => '',
                    'stock' => '1',
                    'price' => '1',
                    'scheme' => '1',
                    'created_at' => $date_created
                );
                $check_order = $this->db->insert('pbioms_distributor_access', $order_data);
                $order_id    = $this->db->insert_id();
            }
        }
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    public function web_token($token,$user_id)
    {
        $this->db->where('id', $user_id)->update('users', array(
            'web_token' => $token
        ));
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    
    public function get_user($id)
    {
        $id = clean_number($id);
        $this->db->where('id', $id);
        $query = $this->db->get('users');
        return $query->row();
    }
    
    //get user by email
    public function get_user_by_email($email)
    {
        $this->db->where('email', $email);
        $query = $this->db->get('users');
        return $query->row();
    }
    
    //get user by mobile
    public function get_user_by_mobile($phone_number)
    {
        $this->db->where('phone', $phone_number);
        $query = $this->db->get('users');
        return $query->row();
    }
    
    //check if email is unique
    public function is_unique_email($email, $user_id = 0)
    {
        $user_id = $user_id;
        $user    = $this->get_user_by_email($email);
        
        //if id doesnt exists
        if ($user_id == 0) {
            if (empty($user)) {
                return true;
            } else {
                return false;
            }
        }
        
        if ($user_id != 0) {
            if (!empty($user) && $user->id != $user_id) {
                //email taken
                return false;
            } else {
                return true;
            }
        }
    }
    
    
    //check if email is unique
    public function is_unique_mobile($phone_number, $user_id = 0)
    {
        $user_id = $user_id;
        $user    = $this->get_user_by_mobile($phone_number);
        
        //if id doesnt exists
        if ($user_id == 0) {
            if (empty($user)) {
                return true;
            } else {
                return false;
            }
        }
        
        if ($user_id != 0) {
            if (!empty($user) && $user->id != $user_id) {
                //email taken
                return false;
            } else {
                return true;
            }
        }
    }
    
    
    public function login($email, $password)
    {
        $user  = $this->get_user_by_email($email);
        $user2 = $this->get_user_by_mobile($email);
        
        if (!empty($user) || !empty($user2)) {
            
            if (!empty($user2)) {
                $user = $user2;
            }
            if (md5($password)!=$user->password) {
                $this->session->set_flashdata('error', trans("login_error"));
                $data       = array();
                $resultpost = array(
                    'status' => 400,
                    'message' => 'Invalid Username or Password',
                    'data' => $data
                );
            }
            else{
                $user_id = $user->id;
                $data[] = array(
                    "user_id" => $user_id,
                    "user_name" => $user->name,
                    "email" => $user->email,
                    "phone" => $user->phone,
                    "firm_name" => $user->name,
                    "logo" => ''
                );
                $resultpost = array(
                    'status' => 200,
                    'message' => 'success',
                    'data' => $data
                );
            }
        } else {
            $data       = array();
            $resultpost = array(
                'status' => 400,
                'message' => 'Account not exist!',
                'data' => $data
            );
        }
        
        return $resultpost;
    }
    
    
    public function register_vendor($name, $email, $phone, $password, $firm_name)
    {
        date_default_timezone_set('Asia/Kolkata');
        $this->load->library('bcrypt');
        $data['username']     = html_escape($name);
        $data['email']        = html_escape($email);
        $data['firm_name']    = html_escape($firm_name);
        $data['phone_number'] = html_escape($phone);
        $data['password']     = $this->bcrypt->hash_password($password);
        $data['user_type']    = "registered";
        $data["slug"]         = $this->auth_model->generate_uniqe_slug($name);
        $data['banned']       = 0;
        $data['created_at']   = date('Y-m-d H:i:s');
        $data['token']        = generate_token();
        $data['email_status'] = 0;
        $otp = $this->auth_model->generatePIN();
        //$otp                  = '1155';
        $data['otp']          = $otp;
        $data['role']         = 'vendor';
        if ($this->db->insert('temp_users', $data)) {
            $last_id      = $this->db->insert_id();
            $message_user = 'OTP to validate your Skoozo account is ' . $otp;
            $this->auth_model->send_sms($message_user, $data['phone_number']);
            
            $data_[] = array(
                "user_id" => $last_id
            );
            
            $resultpost = array(
                'status' => 200,
                'message' => 'success',
                'data' => $data_
            );
            
        } else {
            $data       = array();
            $resultpost = array(
                'status' => 400,
                'message' => 'Error while registration!',
                'data' => $data
            );
        }
        return $resultpost;
    }
    
    
    
    public function register_otp_verify($otp, $ref_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $query = $this->db->get_where('temp_users', array(
            'id' => $ref_id,
            'otp!=' => '0',
            'otp' => $otp
        ));
        $count = $query->num_rows();
        
        if ($count > 0) {
            $info                 = $query->row_array();
            $data['username']     = $info['username'];
            $data['email']        = $info['email'];
            $data['phone_number'] = $info['phone_number'];
            $data['password']     = $info['password'];
            $data['firm_name']    = $info['firm_name'];
            $data['user_type']    = "registered";
            $data["slug"]         = $this->auth_model->generate_uniqe_slug($data["username"]);
            $data['banned']       = 0;
            $data['created_at']   = date('Y-m-d H:i:s');
            $data['token']        = generate_token();
            $data['email_status'] = 0;
            $data['otp']          = $info['otp'];
            $data['role'] = 'vendor';
            
            $this->db->insert('users', $data);
            $last_id = $this->db->insert_id();
            
            //delete in temp
            $this->db->where('id', $ref_id);
            $this->db->delete('temp_users');
            
            $message_user = 'Thank you for registering with Skoozo. To activate your account please go to https://vendor.skoozo.com/settings/update-profile ‘My profile’ and fill in all the details.';
            // $this->auth_model->send_sms($message_user,$data['phone_number']);
            
            
            $mail_message = '<td align="left" bgcolor="#ffffff" style="padding: 15px;">
                          <p><b>Dear Seller,</b> </p>
                          <p>Welcome to Skoozo.com and Congratulations for successfully registering on India’s 1st fully integrated Edu-commerce platform. </p>
                          <p>To activate your account please visit ‘My profile’ section on skoozo platform and fill in all the details and ensure that the profile is 100% complete. </p>
                          <p><b>Note: </b>Your account will be activated only after ‘My Profile’ information is 100% complete</p>
                          <p>Once your account is activated, you will be informed via email/ SMS.</p>
                          <br/>
                          <br/>
                        
                          Happy Selling,<br/>
                          <a href="https://vendor.skoozo.com/">Team Skoozo</a> <br/>
                          
                          </td>';
            $mail_subject = 'Skoozo Account Registered';
            // $this->auth_model->sent_mail($mail_message,$data['email'],$mail_subject);
            
            
            $user_data[] = array(
                "user_id" => $last_id,
                "user_name" => ucfirst($info['username']),
                "email" => $info['email'],
                "phone" => $info['phone_number'],
                "firm_name" => $info['firm_name']
            );
            
            $resultpost = array(
                'status' => 200,
                'message' => 'success',
                'data' => $user_data
            );
        } else {
            $resultpost = array(
                'status' => 400,
                'message' => 'Invalid OTP'
            );
        }
        return $resultpost;
    }
    
    
    
    
    
    public function register_resend_otp($ref_id)
    {
        $info         = $this->db->get_where('temp_users', array(
            'id' => $ref_id
        ))->row_array();
        $otp          = $info['otp'];
        $mobile       = $info['phone_number'];
        $message_user = 'Use OTP ' . $otp . ' to confirm Vendor.';
        $res          = $this->auth_model->send_sms($message_user, $mobile);
        $resultpost   = array(
            'status' => 200,
            'message' => 'success'
        );
        
        return $resultpost;
    }
    
    
    
    public function module_list($user_id, $school_id, $profile)
    {
        $query       = $this->db->query("SELECT id,module FROM module_list order by id asc");
        $count       = $query->num_rows();
        $module_arr  = array();
        $checked_arr = array();
        foreach ($query->result_array() as $row) {
            $module_id      = $row['id'];
            $module         = $row['module'];
            $no_id          = 0;
            $sub_module_arr = array();
            $sub_query      = $this->db->query("SELECT id,module FROM sub_module_list where module_id='$module_id' order by id asc");
            foreach ($sub_query->result_array() as $sub_row) {
                $sub_module_id = $sub_row['id'];
                $sub_module    = $sub_row['module'];
                
                $sub_child_module_arr = array();
                $sub_child_query      = $this->db->query("SELECT id,module FROM sub_child_module_list where module_id='$module_id' and sub_module_id='$sub_module_id' order by id asc");
                foreach ($sub_child_query->result_array() as $sub_child_row) {
                    $sub_child_module_id = $sub_child_row['id'];
                    $sub_child_module    = $sub_child_row['module'];
                    
                    $sub_child_last_module_arr = array();
                    $sub_child_last_query      = $this->db->query("SELECT id,module FROM sub_child_last_module_list where module_id='$module_id' and sub_module_id='$sub_module_id' and sub_child_module_id='$sub_child_module_id' order by id asc");
                    foreach ($sub_child_last_query->result_array() as $sub_child_last_row) {
                        $sub_child_last_module_id = $sub_child_last_row['id'];
                        $sub_child_last_module    = $sub_child_last_row['module'];
                        
                        $sub_child_last_value = $module_id . '_' . $sub_module_id . '_' . $sub_child_module_id . '_' . $sub_child_last_module_id;
                        $query_count          = $this->db->query("SELECT id FROM module_access_list WHERE value='$sub_child_last_value' and profile='$profile' and school_id='$school_id' limit 1");
                        if ($query_count->num_rows() > 0) {
                            $checked_arr[] = array(
                                $sub_child_last_value
                            );
                        }
                        $sub_child_last_module_arr[] = array(
                            "value" => $sub_child_last_value,
                            "label" => $sub_child_last_module
                        );
                    }
                    
                    $sub_child_value = $module_id . '_' . $sub_module_id . '_' . $sub_child_module_id . '_' . $no_id;
                    $query_count     = $this->db->query("SELECT id FROM module_access_list WHERE value='$sub_child_value' and profile='$profile' and school_id='$school_id' limit 1");
                    if ($query_count->num_rows() > 0) {
                        $checked_arr[] = array(
                            $sub_child_value
                        );
                    }
                    if (count($sub_child_last_module_arr) > 0) {
                        $sub_child_module_arr[] = array(
                            "value" => $sub_child_value,
                            "label" => $sub_child_module,
                            "children" => $sub_child_last_module_arr
                        );
                    } else {
                        $sub_child_module_arr[] = array(
                            "value" => $sub_child_value,
                            "label" => $sub_child_module
                        );
                    }
                }
                $sub_value   = $module_id . '_' . $sub_module_id . '_' . $no_id . '_' . $no_id;
                $query_count = $this->db->query("SELECT id FROM module_access_list WHERE value='$sub_value' and profile='$profile' and school_id='$school_id' limit 1");
                if ($query_count->num_rows() > 0) {
                    $checked_arr[] = array(
                        $sub_value
                    );
                }
                if (count($sub_child_module_arr) > 0) {
                    $sub_module_arr[] = array(
                        "value" => $sub_value,
                        "label" => $sub_module,
                        "children" => $sub_child_module_arr
                    );
                } else {
                    $sub_module_arr[] = array(
                        "value" => $sub_value,
                        "label" => $sub_module
                    );
                }
            }
            $value       = $module_id . '_' . $no_id . '_' . $no_id . '_' . $no_id;
            $query_count = $this->db->query("SELECT id FROM module_access_list WHERE value='$value' and profile='$profile' and school_id='$school_id' limit 1");
            if ($query_count->num_rows() > 0) {
                $checked_arr[] = array(
                    $value
                );
            }
            if (count($sub_module_arr) > 0) {
                $module_arr[] = array(
                    "value" => $value,
                    "label" => $module,
                    "children" => $sub_module_arr
                );
            } else {
                $module_arr[] = array(
                    "value" => $value,
                    "label" => $module
                );
            }
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'checked' => $checked_arr,
            'data' => $module_arr
        );
        return $resultpost;
    }
    
    
    
    
    public function validate_admin($token,$type)
    {   
        if($type == '1'){
            $token = remove_special_characters($token);
            $this->db->where('token', $token);
            $this->db->where('id', $type);
            $query = $this->db->get('bulk_users');
            $user  = $query->row();
            $name = ucfirst($user->username);
            $phone_number = ucfirst($user->phone_number);
            $back_url = 'https://admin2.skoozo.com/admin/';
        }else{
            $token = remove_special_characters($token);
            $this->db->where('token', $token);
            $this->db->where('id', $type);
            $query = $this->db->get('staff');
            if($query->num_rows()>0){
                $user  = $query->row();
                $name = ucfirst($user->name);
                $phone_number = ucfirst($user->mobile);
            }
            else{
                $name='';
            }
            
            $back_url = 'https://staffsandbox.skoozo.com/admin/';
        }
            if($name != ''){
                $data[] = array(        
                    "user_id" => $user->id,
                    "user_name" => $name,
                    "email" => $user->email,
                    "phone" => $phone_number,
                    "back_url" => $back_url,
                );
            
            $resultpost = array(
                'status' => 200,
                'message' => 'success',
                'data' => $data
            );
        } else {
            $data       = array();
            $resultpost = array(
                'status' => 400,
                'message' => 'failure',
                'data' => $data
            );
        }
        
        
        return $resultpost;
    }
    
    
    
    
}
?>