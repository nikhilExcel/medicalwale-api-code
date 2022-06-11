<?php
 class Vendor_model extends CI_Model {
       
    public function __construct(){
          
        
        $this->load->database();
        
       
      }
      
    var $client_service = "frontend-client";
    var $auth_key = "medicalwalerestapi";
    
    /*public function check_auth_client() {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key = $this->input->get_request_header('Auth-Key', TRUE);
        if ($client_service == $this->client_service && $auth_key == $this->auth_key) {
            return true;
        } else {
            $resp = array(
		        "status" => "Unauthorized",
                "statuspic_root_code" => "401",
		        );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        }
    }*/
    
   /*public function auth() {
        date_default_timezone_set('Asia/Kolkata');
        $users_id = $this->input->get_request_header('User-ID', TRUE);
        $token = $this->input->get_request_header('Authorizations', TRUE);
        $q = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id', $users_id)->where('token', $token)->get()->row();
        if ($q == "") {
            $resp = array(
		        "status" => "Unauthorized",
                "statuspic_root_code" => "401",
		        );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            
             if ($q->expired_at < date("Y-m-d H:i:s", strtotime("-1 days"))) {
                // return json_output(401, array(
                //     'status' => 401,
                //     'message' => 'Your session has been expired.'
                // ));
                return  array(
                    'status' => 401,
                    'message' => 'Your session has been expired.'
                );
            } else {
                $updated_at = date('Y-m-d H:i:s');
                $expired_at = date("Y-m-d H:i:s", strtotime("+1 days"));//'2020-12-12 08:57:58';
                $this->db->where('users_id', $users_id)->where('token', $token)->update('api_users_authentication', array(
                    'expired_at' => $expired_at,
                    'updated_at' => $updated_at
                ));
                //echo $this->db->last_query(); die();
                return 200;
            }
        }
    }*/
    
    public function get_vendor_offers($v_id){
       
            $resp = $this->db->query("SELECT * FROM `vendor_offers` WHERE `vendor_id` = $v_id AND `listing_id` = 34 AND `status` = 0");
            
            return $resp->result_array();
    }
    
    public function get_vendor_by_categories($cat_ids){
        
        $vendors = array();
        $category_ids = $this->db->query("SELECT DISTINCT `pd_added_v_id` FROM `product_details_hm` WHERE `pd_pc_id` IN ($cat_ids) OR `pd_psc_id` IN ($cat_ids)");
        
        foreach($category_ids->result_array() as $category_id){
            $v_id = $category_id['pd_added_v_id'];
            $vendor = $this->db->query("SELECT * FROM `vendor_details_hm` WHERE `v_id` = $v_id")->result_array();
            if(!empty($vendor)){
                $vendors[] = $vendor[0];    
            }
            
        }
        
            return $vendors;
    }
    
 }
 ?>