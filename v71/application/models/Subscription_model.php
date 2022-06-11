<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Subscription_model extends CI_Model {

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

    public function get_subscription($user_id,$vendor_type) {

      date_default_timezone_set('Asia/Kolkata');
        $resultpost =array();
            	
                $query = $this->db->query("SELECT * FROM `app_subscriptions_plan` where vendor_type='$vendor_type' and active=1");
                $count=$query->num_rows();
                if($query->num_rows()>0)
                {
                foreach ($query->result_array() as $row) {
                    
                    $vendor_type= $row['vendor_type'];
                    $plan_type= $row['plan_type'];
                    $plan_id= $row['plan_id'];
                    $validity_type= $row['validity_type'];
                    $grade= $row['grade'];
                    $price= $row['price'];
                    $medicalwale_comission= $row['medicalwale_comission'];
                    $discount= $row['discount'];
                    $clinic= $row['clinic'];
                    $hospital= $row['hospital'];
                    $doctor= $row['doctor'];
                    $hospital_doctor= $row['hospital_doctor'];
                    $specialization= $row['specialization'];
                    $no_of_visits= $row['no_of_visits'];
                    
                    $total_dis=$price-($price*$discount/100);
                   
                    
                    $query_plan = $this->db->query("SELECT * FROM `app_subscriptions` where id='$plan_type' ");
                    $row_plan=$query_plan->row_array();
                    
                    $plan_name=$row_plan['name'];
                    
                    $clinic_array = array();
                    $clinic_      = explode(',', $clinic);
                    $count_clinic = count($clinic_);
                    if ($count_clinic > 0) {
                        foreach ($clinic_ as $clinic_) {
       
                        $query_clinic = $this->db->query("SELECT * FROM `doctor_clinic` where id='$clinic_' ");
                        $row_clinic=$query_clinic->row_array();   
                           
                         $clinic_name=$row_plan['name']; 
                         
                         $clinic_array[]=array('clinic_name'=>$clinic_name);
                         
                            
                        }
                    } else {
                        $clinic_array = array();
                    }
                    
                    
                    $hospital_array = array();
                    $hospital_      = explode(',', $hospital);
                    $count_hospital = count($hospital_);
                    if ($count_hospital > 0) {
                        foreach ($hospital_ as $hospital_) {
      
                        $query_hospital = $this->db->query("SELECT * FROM `hospitals` where user_id='$hospital_' ");
                        $row_hospital=$query_hospital->row_array();   
                         $hospitals_name=$row_hospital['name_of_hospital']; 
                         $hospital_array[]=array('hospitals_name'=>$hospitals_name);
                         
                        }
                    } else {
                        $hospital_array = array();
                    }
                    
                    $doc_array = array();
                    $hospital_doc_      = explode(',', $hospital_doctor);
                    $count_hospital_doc = count($hospital_doc_);
                    if ($count_hospital_doc > 0) {
                        foreach ($hospital_doc_ as $hospital_doc_) {
      
                        $query_hospital_doc = $this->db->query("SELECT * FROM `hospital_doctor_list` where id='$hospital_doc_' ");
                        $row_hospital_doc=$query_hospital_doc->row_array();   
                         $hospital_doc_name=$row_hospital_doc['doctor_name']; 
                         $doc_array[]=array('doctor'=>$hospital_doc_name);
                         
                        }
                    } else {
                        $doc_array = array();
                    }
                    
                    
                    $doctor_      = explode(',', $doctor);
                    $count_doctor = count($doctor_);
                    if ($count_doctor > 0) {
                        foreach ($doctor_ as $doctor_) {
      
                        $query_doctor = $this->db->query("SELECT * FROM `doctor_list` where user_id='$doctor_' ");
                        $row_doctor=$query_doctor->row_array();   
                         $doctor_name=$row_doctor['doctor_name']; 
                         $doc_array[]=array('doctor'=>$doctor_name);
                         
                        }
                    } else {
                        $doc_array = array();
                    }
                    
                   $area_expertise = array();
                $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'" . $specialization . "')");
                $total_category = $query_sp->num_rows();
                if ($total_category > 0) {
                    foreach ($query_sp->result_array() as $get_sp) {
                        $id               = $get_sp['id'];
                        $area_expertised  = $get_sp['area_expertise'];
                        $area_expertise[] = array(
                            'id' => $id,
                            'area_expertise' => $area_expertised
                        );
                    }
                } else {
                    $area_expertise = array();
                }
                    
                   
                    
                    $resultpost[]=array(
                        'image'=>"",
                        'plan_name'=>$plan_name,
                        'consultation'=>$no_of_visits,
                        'clinic'=>$clinic_array,
                        'hospital'=>$hospital_array,
                        'doctor'=>$doc_array,
                        'specialization'=>$area_expertise,
                        'price'=>intval($price),
                        'dis_price'=>$total_dis,
                        'validity'=>$validity_type
                        );
                    
                    
                        
                            
                }
          return $resultpost;   
    	    } 
    	    else
    	    {
    	      return  $resultpost; 
    	    }
    }
    
      
}
