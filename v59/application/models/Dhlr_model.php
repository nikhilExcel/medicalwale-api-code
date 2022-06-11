<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dhlr_model extends CI_Model {

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

    public function dhlr_add($user_id,$status,$time,$title) {
        date_default_timezone_set('Asia/Calcutta');
        $created_at = date('Y-m-d H:i:s');

        $record = array(
            'user_id' => $user_id,
            'time'=>$time,
            'title'=>$title,
            'status'=>$status,
            'created_at' => $created_at
        );
        $this->db->insert('dhlr', $record);
        $id = $this->db->insert_id();
        return array(
            'status' => 200,
            'message' => 'success',
            'id' => $id
        );
    }

    public function dhlr_list($user_id) {
        //echo "SELECT * FROM health_record WHERE user_id='$user_id' and vendor_type='$vendor_type' order by id desc";
        $query = $this->db->query("SELECT * FROM dhlr WHERE user_id='$user_id' order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {

                $dhlr_id = $row['id'];
                $user_id = $row['user_id'];
                $time = $row['time'];
                $title = $row['title'];
                $status = $row['status'];
                $created_at = $row['created_at'];
                $updated_at = $row['updated_at'];

                $resultpost[] = array(
                    'id' => $dhlr_id,
                    'user_id' => $user_id,
                    'time'=>$time,
                    'title'=>$title,
                    'status'=>$status,
                    'created_at' => $created_at,
                    'updated_at' => $updated_at
                );
                   

                
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    public function dhlr_delete($user_id,$id)
    {
        $this->db->where('id', $id)->where('user_id',$user_id)->delete('dhlr');
        return array(
            'status' => 200,
            'message' => 'Record has been deleted.'
        );
    }
    public function dhlr_update($user_id,$id,$status,$time,$title)
    {
         date_default_timezone_set('Asia/Calcutta');
        $data = array(
            'time' => $time,
            'title' => $title,
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        );
        $this->db->where('id', $id)->where('user_id',$user_id)->update('dhlr',$data);
        if($this->db->affected_rows() > 0)
        {
            return array(
                'status' => 200,
                'message' => 'Record Updated Successfully.'
            );
        }
        else
        {
            return array(
                'status' => 400,
                'message' => 'Unable to Update.'
            );
        }
    }
    public function dhlr_update_status($user_id,$id,$status)
    {
         date_default_timezone_set('Asia/Calcutta');
        $data = array(
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        );
        $this->db->where('id', $id)->where('user_id',$user_id)->update('dhlr',$data);
        if($this->db->affected_rows() > 0)
        {
            return array(
                'status' => 200,
                'message' => 'Status Updated Successfully.'
            );
        }
        else
        {
            return array(
                'status' => 400,
                'message' => 'Unable to Update Status.'
            );
        }
    }
     public function dhlr_delete_all($user_id)
    {
        $this->db->where('user_id',$user_id)->delete('dhlr');
        return array(
            'status' => 200,
            'message' => 'Record has been deleted.'
        );
    }
  
}
