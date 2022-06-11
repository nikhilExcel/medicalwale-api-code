<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class NightowlsModel extends CI_Model {

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
    
    public function mno_list(){
        $mno_list = $this->db->query("SELECT ml.`mno_id`,ml.`mno_name`,ml.`phone`,ml.`email` FROM `mno_list` as ml left join users as u on (u.id = ml.mno_id) WHERE ml.`approval` = '1' AND u.is_active = '1'")->result_array();
        return $mno_list;
    }
}
?>