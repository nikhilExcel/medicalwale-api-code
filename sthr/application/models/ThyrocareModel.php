<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class ThyrocareModel extends CI_Model {

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

    public function auth() {
        date_default_timezone_set('Asia/Kolkata');
        $users_id = $this->input->get_request_header('User-ID', TRUE);
        $token = $this->input->get_request_header('Authorizations', TRUE);
        $q = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id', $users_id)->where('token', $token)->get()->row();
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

    public function add_order($data) {
        $lab_booking_insert = $this->db->insert('lab_booking_details', $data);
        //$order_id = $this->db->insert_id();

        if ($lab_booking_insert) {
            $data_array = array(
                //'order_id' => $order_id,
                'success' => "OK"
            );
            return $data_array;
        }
    }
    
     public function fetch_thyrocare($user_id)
    {
        $resultpost = array();
         $query = $this->db->query("SELECT * From thyrocare_cart where user_id='$user_id'");
        $count      = $query->result_array();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id       = $row['id'];
                $product_id = $row['product_id'];
                $name    = $row['name'];
                $price = $row['price'];
                $user_id = $row['user_id'];
                $date = $row['date'];
                $test_name = $row['test_name'];
                $type = $row['type'];
                $margin = $row['margin'];
                $resultpost[]  = array(
                     "id" => $id,
                     "product_id" => $product_id,
                     "name" => $name,
                     "price" => $price,
                     "user_id" => $user_id,
                     "date" => $date,
                     "test_name"=>$test_name,
                     "type"=>$type,
                     "margin"=>$margin
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

 public function add_lab_booking_details($data) {
        $patient_insert = $this->db->insert('lab_booking_details', $data);
        
        if ($patient_insert) {
            $data_array = array(
                //'order_id' => $order_id,
                'success' => "OK"
            );
            return $data_array;
        }
    }
    
     public function add_thyrocare($data) {
        
        $tyrocare_insert = $this->db->insert('thyrocare_cart', $data);
        
        if ($tyrocare_insert) {
            $data_array =  array(
                            'status' => 200,
                            'message' => 'Success'
                        );
        }
        else
        {
           $data_array =   array(
                            'status' => 400,
                            'message' => 'Failure'
                        ); 
        }
          return $data_array;
    }
    public function list_orders($user_id) {

        //$query = $this->db->get('thyrocare_orders');
        $this->db->select('*');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('thyrocare_orders');
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $user_id = $row['user_id'];
                $order_id = $row['order_id'];
                $user_name = $row['user_name'];
                $schedule_date = $row['schedule_date'];
                $ordered_date = $row['date'];
                $mobile_cancel_status = $row['mobile_cancel_status'];
                $thyrocare_cancel_status = $row['thyrocare_cancel_status'];
                $lead_id = $row['lead_id'];

                $resultpost[] = array(
                    "id" => $id,
                    "user_id" => $user_id,
                    "order_id" => $order_id,
                    "user_name" => $user_name,
                    "schedule_date" => $schedule_date,
                    "ordered_date" => $ordered_date,
                    "mobile_cancel_status" => $mobile_cancel_status,
                    "thyrocare_cancel_status" => $thyrocare_cancel_status,
                    "lead_id" => $lead_id
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    
  /*  public function cancel_order($id) {
        
        $query = $this->db->query("UPDATE thyrocare_orders SET mobile_cancel_status='1' WHERE id='$id'");
        
        if($query){
            
            $data = array(
                
                "status" => "200",
                 "message" => "success",
                'mobile_cancel_status' => $query
            );
        }
        return $data;
    }*/
    
    
    
     public function delete_thyrocare_order($user_id,$id) {
        
        $query = $this->db->query("DELETE FROM `thyrocare_cart` WHERE id='$id' and user_id='$user_id'");
        
        if($query){
            
            $data = array(
                
                "status" => "200",
                 "message" => "success"
               
            );
        }
        else
        {
           
            $data = array(
                
                "status" => "400",
                 "message" => "Failed"
               
            ); 
        }
        return $data;
    }
    
     public function delete_thyrocare_cart($user_id) {
        
        $query = $this->db->query("DELETE FROM `thyrocare_cart` WHERE user_id='$user_id'");
        
        if($query){
            
            $data = array(
                
                "status" => "200",
                 "message" => "success"
               
            );
        }
        else
        {
           
            $data = array(
                
                "status" => "400",
                 "message" => "Failed"
               
            ); 
        }
        return $data;
    }
    
    public function cancel_order($id) {
        
        $query = $this->db->query("UPDATE booking_master SET status='Cancelled' WHERE booking_id='$id'");
        
        if($query){
            
            $data = array(
                
                "status" => "200",
                 "message" => "success",
                'mobile_cancel_status' => $query
            );
        }
        return $data;
    }
    
    
  

}

?>