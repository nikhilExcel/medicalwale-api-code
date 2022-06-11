<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Substitute_model extends CI_Model {

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

    public function encrypt($str) {
        $this->key = hash('MD5', '8655328655mdwale', true);
        $this->iv = hash('MD5', 'mdwale8655328655', true);
        $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($module, $this->key, $this->iv);
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $pad = $block - (strlen($str) % $block);
        $str .= str_repeat(chr($pad), $pad);
        $encrypted = mcrypt_generic($module, $str);
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);
        return base64_encode($encrypted);
    }

    public function decrypt($str) {
        $this->key = hash('MD5', '8655328655mdwale', true);
        $this->iv = hash('MD5', 'mdwale8655328655', true);
        $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($module, $this->key, $this->iv);
        $str = mdecrypt_generic($module, base64_decode($str));
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);
        $slast = ord(substr($str, -1));
        $str = substr($str, 0, strlen($str) - $slast);
        return $str;
    }
    public function substitute_list($mname) {
        $mname1 = addslashes($mname);
        $sql = "SELECT group_id FROM substitute WHERE medicine_name like '%$mname1%' limit 1";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            
            $g = $query->row()->group_id;
            
            $sql1 = "SELECT * FROM substitute WHERE group_id = '$g' ORDER BY CAST(price AS DECIMAL(18,2)) ASC";
        $query1 = $this->db->query($sql1);
        $count1 = $query1->num_rows();
        if ($count1 > 0) {
            foreach ($query1->result_array() as $row) {
              
              $resultpost[] = array(
                                   'id' => $row['id'],
                                   'group_id' => $row['group_id'],
                                  'medicine_name' =>  stripslashes($row['medicine_name']),
                                  'qty' => $row['qty'],
                                  'qty_unit' => $row['qty_unit'],
                                  'type' => $row['type'],
                                  'type_qty' => $row['type_qty'],
                                  'type_qty_unit' => $row['type_qty_unit'],
                                  'price' => $row['price'],
                                  'price_currency' => $row['price_currency']
                                  );
                                  
                                  
            }
        
        } else {
            $resultpost = array();
        }
    }
    else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    public function substitute_search($mname) {
        $mname1 = addslashes($mname);
        $sql = "SELECT * FROM substitute WHERE medicine_name like '%$mname1%' GROUP BY medicine_name ORDER BY CAST(price AS DECIMAL(18,2)) ASC";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
  
            foreach ($query->result_array() as $row) {
              
              $resultpost[] = array(
                                  'id' => $row['id'],
                                  'group_id' => $row['group_id'],
                                  'medicine_name' =>  stripslashes($row['medicine_name']),
                                  'qty' => $row['qty'],
                                  'qty_unit' => $row['qty_unit'],
                                  'type' => $row['type'],
                                  'type_qty' => $row['type_qty'],
                                  'type_qty_unit' => $row['type_qty_unit'],
                                  'price' => $row['price'],
                                  'price_currency' => $row['price_currency']
                                  );
                                  
                                  
            }
        
       
    }
    else {
            $resultpost = array();
        }
        return $resultpost;
    }
}