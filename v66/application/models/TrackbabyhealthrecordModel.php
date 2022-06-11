<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class TrackbabyhealthrecordModel extends CI_Model {
    
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

  public function add_record($user_id, $type, $value, $date, $child_id) {
        $add_query = $this->db->query("SELECT id from track_baby_health_record where user_id='$user_id' and type='$type' and date='$date' and child_id='$child_id'");
        $count_add = $add_query->num_rows();
        if ($count_add > 0) {
            return array('status' => 201, 'message' => 'success');
        } else {
            $health_record = array(
                'user_id' => $user_id,
                'type' => $type,
                'value' => $value,
                'date' => $date,
                'child_id' => $child_id
            );
            $this->db->insert('track_baby_health_record', $health_record);
            return array('status' => 201, 'message' => 'success');
        }
    }
    
      public function record_list($user_id, $type, $child_id) {
        return $this->db->select('id,type,value,date,child_id')->from('track_baby_health_record')->where('user_id', $user_id)->where('type', $type)->where('child_id',$child_id)->order_by('date', 'desc')->get()->result();
    }
    
     public function update_record($user_id, $type, $value, $date, $child_id) {
       
        $health_record = array(
            'user_id' => $user_id,
            'type' => $type,
            'value' => $value,
            'date' => $date,
            'child_id' => $child_id
           // 'id' => $id
        );
        $this->db->query("UPDATE `track_baby_health_record` SET `type`='$type',`value`='$value',`date`='$date' ,`child_id` = '$child_id' where user_id='$user_id' and type='$type' and date='$date' and child_id ='$child_id'");
        return array('status' => 201, 'message' => 'success');
    }
    
     public function delete_record($user_id, $type, $child_id,$date) {
        $this->db->query("DELETE FROM `track_baby_health_record` where user_id='$user_id' and type='$type' and child_id='$child_id' and date='$date'");
        return array('status' => 201, 'message' => 'success');
    }
    
    
}