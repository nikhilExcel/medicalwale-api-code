<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class OrderModel extends CI_Model {

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

    public function address_list($user_id) {
        $query = $this->db->query("SELECT address_id,name,mobile,pincode,address1,address2,landmark,city,state FROM `user_address` WHERE user_id='$user_id' order by address_id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $address_id = $row['address_id'];
                $name = $row['name'];
                $mobile = $row['mobile'];
                $address1 = $row['address1'];
                $address2 = $row['address2'];
                $landmark = $row['landmark'];
                $city = $row['city'];
                $state = $row['state'];
                $pincode = $row['pincode'];

                $resultpost[] = array(
                    "address_id" => $address_id,
                    "name" => $name,
                    "mobile" => $mobile,
                    'address1' => $address1,
                    'address2' => $address2,
                    'landmark' => $landmark,
                    'city' => $city,
                    'state' => $state,
                    'pincode' => $pincode
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

}
