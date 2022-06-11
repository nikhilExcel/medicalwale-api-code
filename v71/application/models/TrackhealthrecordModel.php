<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class TrackhealthrecordModel extends CI_Model {

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

    public function record_list($user_id, $type,$from,$record) {
        /*old query -- commented by nikhil ( 26-03-2021)*/
        //return $this->db->select('id,type,value,date')->from('track_health_record')->where('user_id', $user_id)->where('type', $type)->order_by('date', 'desc')->get()->result();
        /*--comment ends*/
        if($from == 'year'){
            $result  = $this->db->query("SELECT id,type,value,date FROM `track_health_record` WHERE user_id = '$user_id' AND type = '$type' AND YEAR(created_at) = $record")->result_array();
            return $result;
        } else if ($from == 'month'){
            $year   = preg_replace('/[^0-9]/', '', $record);
            $month  = preg_replace('/[^a-zA-Z]/', '', $record);
            $nmonth = date('m',strtotime($month));
            $result = $this->db->query("SELECT id,type,value,date FROM `track_health_record` WHERE user_id = '$user_id' AND type = '$type' AND MONTH(created_at) = '$nmonth' AND YEAR(created_at) = '$year'")->result_array();
            return $result;
        } else if($from == 'week'){
            $start_date = date('Y-m-d', strtotime($record));
            $end_date = date( "Y-m-d", strtotime( "$start_date +7 day" ) );
            $result = $this->db->query("SELECT id,type,value,date FROM `track_health_record` WHERE user_id = '$user_id' AND type = '$type' AND created_at BETWEEN '$start_date' and '$end_date'")->result_array();
            return $result;
        } else {
            $result = $this->db->query("SELECT id,type,value,date FROM `track_health_record` WHERE user_id = '$user_id' AND type = '$type' ORDER BY date DESC")->result_array();
            return $result;
        }
    }

    public function add_record($user_id, $type, $value, $date) {
        $add_query = $this->db->query("SELECT id from track_health_record where user_id='$user_id' and type='$type' and date='$date'");
        $count_add = $add_query->num_rows();
        if ($count_add > 0) {
            return array('status' => 201, 'message' => 'success');
        } else {
            $health_record = array(
                'user_id' => $user_id,
                'type' => $type,
                'value' => $value,
                'date' => $date
            );
            $this->db->insert('track_health_record', $health_record);
            return array('status' => 201, 'message' => 'success');
        }
    }

    public function update_record($user_id, $type, $value, $date) {
        $health_record = array(
            'user_id' => $user_id,
            'type' => $type,
            'value' => $value,
            'date' => $date
        );
        $this->db->query("UPDATE `track_health_record` SET `type`='$type',`value`='$value',`date`='$date' where user_id='$user_id' and type='$type' and date='$date'");
        return array('status' => 201, 'message' => 'success');
    }

    public function delete_record($user_id, $type, $date) {
        $this->db->query("DELETE FROM `track_health_record` where user_id='$user_id' and type='$type' and date='$date'");
        return array('status' => 201, 'message' => 'success');
    }

    // public function update_profile($user_id, $gender, $height, $weight, $weight_date, $height_date, $age, $activity_level) {
    //     $this->db->query("UPDATE `users` SET `gender`='$gender',`height`='$height',`weight`='$weight',`weight_date`='$weight_date',`height_date`='$height_date',`age`='$age',`activity_level`='$activity_level' where id='$user_id'");
    //     return array('status' => 201, 'message' => 'success');
    // }
    
       public function update_profile($user_id, $gender, $height, $weight, $weight_date, $height_date, $age, $activity_level) {
        $this->db->query("UPDATE `users` SET `gender`='$gender',`weight`='$weight',`weight_date`='$weight_date',`age`='$age',`activity_level`='$activity_level' where id='$user_id'");
        return array('status' => 201, 'message' => 'success');
    }
    
    public function tracker_information_details($user_id,$key){
        if($key != ''){
            $query = $this->db->query("SELECT * FROM `tracker_information_details` WHERE paramter = '$key'");
        } else {
            $query = $this->db->query("SELECT * FROM `tracker_information_details`");
        }
        
        $resultpost = array();
        foreach ($query->result_array() as $row) {
            $paramter               = $row['paramter'];
            $specifications         = $row['specifications'];
            $normal                 = $row['normal'];
            $abnormal               = $row['abnormal'];
            $concerning             = $row['concerning'];
            $ideal_range            = $row['ideal_range'];
            $best_measured          = $row['best_measured'];
            $factors_affecting_para = $row['factors_affecting_para'];
            $useful_info            = $row['useful_info'];
            $source                 = $row['source'];
            $information            = $row['information'];
            


            $resultpost[] = array(
                'paramter'                  => $paramter,
                'specifications'            => $specifications,
                'normal'                    => $normal,
                'abnormal'                  => $abnormal,
                'concerning'                => $concerning,
                'ideal_range'               => $ideal_range,
                'best_measured'             => $best_measured,
                'factors_affecting_para'    => $factors_affecting_para,
                'useful_info'               => $useful_info,
                'source'                    => $source,
                'information'               => $information,
            );
        }
        
        return array('status' => 201, 'message' => 'success' ,'data' =>$resultpost );
    }
    
    public function tracker_value_details($user_id,$key){
        if($key != ''){
            $query = $this->db->query("SELECT * FROM `tracker_values` WHERE (`app_key` LIKE '$key' OR `tracker_name` like '%$key%') AND status = 1");
        } else {
            $query = $this->db->query("SELECT * FROM `tracker_values` WHERE `status` = 1 ");
        }
        
        $resultpost = array();
        foreach ($query->result_array() as $row) {
            $id                 = $row['id'];
            $tracker_name       = $row['tracker_name'];
            $tracker_sub_name   = $row['tracker_sub_name'];
            // $unit               = $row['unit'];
            // $min                = $row['min'];
            // $max                = $row['max'];
            $normal_ranges   = $row['normal_ranges'];
            $abnormal_range   = $row['abnormal_range'];
            $concerning   = $row['concerning'];
            $best_measured   = $row['best_measured'];
            $factors_affecting_the_parameter   = $row['factors_affecting_the_parameter'];
            $useful_info   = $row['useful_info'];
            $image              = 'https://d2c8oti4is0ms3.cloudfront.net/images/medicalwale_mobile_app_icons/track_health_record/'.$row['image'];

            $resultpost[] = array(
                'id'                => $id,
                'tracker_name'      => $tracker_name,
                'tracker_sub_name'  => $tracker_sub_name,
                'normal_ranges'      => $normal_ranges,
                'abnormal_range'      => $abnormal_range,
                'concerning'      => $concerning,
                'best_measured'      => $best_measured,
                'factors_affecting_the_parameter'      => $factors_affecting_the_parameter,
                'useful_info'      => $useful_info,
                'image'             => $image
                
            );
        }
        
        return array('status' => 201, 'message' => 'success' ,'data' =>$resultpost );
    }
    

}
