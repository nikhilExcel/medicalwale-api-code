<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class VendorModel extends CI_Model {

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
                $expired_at = '2018-11-12 08:57:58';
                $this->db->where('users_id', $users_id)->where('token', $token)->update('api_users_authentication', array('expired_at' => $expired_at, 'updated_at' => $updated_at));
                return array('status' => 200, 'message' => 'Authorized.');
            }
        }
    }

    public function login($username, $password) {
        $password = md5($password);
        $query = $this->db->query("SELECT id,name,email,vendor_id FROM `users` where is_active='1' and (email='$username' or phone='$username') and password='$password'");
        $count = $query->num_rows();
        if ($count > 0) {
            $getusr = $query->row_array();
            
            $uid = $getusr['id'];
            $sql = "SELECT users.name,media.source,users.id FROM users LEFT JOIN media ON(users.avatar_id=media.id) WHERE users.id= '$uid'";
            $result = $this->db->query($sql)->row();
            if (!empty($result->source)) {
                $uimage = $result->source;
            } else {
                $uimage = 'user_avatar.jpg';
            }
            $data = array(
                'email' => $getusr['email'],
                'id' => $getusr['id'],
                'v_id' => $getusr['vendor_id'],
                'loggedin' => TRUE,
                'uimage' => $result->source,
                'uname' => $result->name
            );
            $this->session->set_userdata($data);
            echo json_encode($data);
            redirect('https://pharmacy.medicalwale.com/dashboard');
        } else {
            return array('status' => 208, 'message' => 'Username not exist');
        }
    }

}
