<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HealthrecordModel extends CI_Model {

    var $client_service = "frontend-client";
    var $auth_key       = "medicalwalerestapi";

    public function check_auth_client(){
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key  = $this->input->get_request_header('Auth-Key', TRUE);
        if($client_service == $this->client_service && $auth_key == $this->auth_key){
            return true;
        } else {
            return json_output(401,array('status' => 401,'message' => 'Unauthorized.'));
        }
    }

    public function auth()
    {
        date_default_timezone_set('Asia/Kolkata');
        $users_id  = $this->input->get_request_header('User-ID', TRUE);
        $token     = $this->input->get_request_header('Authorizations', TRUE);
        $q  = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id',$users_id)->where('token',$token)->get()->row();
        if($q == ""){
            return json_output(401,array('status' => 401,'message' => 'Unauthorized.'));
        } else {
            if($q->expired_at < date('Y-m-d H:i:s')){
                return json_output(401,array('status' => 401,'message' => 'Your session has been expired.'));
            } else {
                $updated_at = date('Y-m-d H:i:s');
                $expired_at = '2018-11-12 08:57:58';
                $this->db->where('users_id',$users_id)->where('token',$token)->update('api_users_authentication',array('expired_at' => $expired_at,'updated_at' => $updated_at));
                return array('status' => 200,'message' => 'Authorized.');
            }
        }
    }
    
    
    
    

    
    public function add_record($user_id,$patient_name,$relationship,$date_of_birth,$gender)
     {
        date_default_timezone_set('Asia/Calcutta');
        $created_at = date('Y-m-d');

		$health_record = array(
        'user_id'=>$user_id,
		'patient_name'=>$patient_name,
		'relationship'=>$relationship,
		'date_of_birth'=>$date_of_birth,
        'gender'=>$gender, 
        'created_at'=>$created_at
        );
        $this->db->insert('health_record',$health_record);
         $patient_id = $this->db->insert_id();
        return array(
            'status' => 200,
            'message' => 'success',
            'patient_id' => $patient_id
        );
		
    }
    
public function healthrecord_list($user_id)
    {
        $query = $this->db->query("SELECT id,user_id,patient_name,relationship,date_of_birth,gender,created_at FROM health_record WHERE user_id='$user_id' order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
        foreach ($query->result_array() as $row) {
        
        
        $health_record_id=$row['id'];
        $user_id=$row['user_id'];
        $patient_name=$row['patient_name'];
        $relationship=$row['relationship'];
        $date_of_birth=$row['date_of_birth'];
        $gender=$row['gender'];
        $created_at=$row['created_at'];
     
       
         $query2 = $this->db->query("SELECT id,media,created_at,type FROM health_record_media WHERE health_record_id='$health_record_id' order by created_at desc");        
            $count2 = $query2->num_rows();
            if ($count2 > 0) {
           foreach ($query2->result_array() as $row_media) {    
            $media_id=$row_media['id'];
            $media=$row_media['media'];
            $created_at=$row_media['created_at'];  
            $type_=$row_media['type'];
             if($type_=='pdf'){
               $type_='files'; 
            }
            else{
              $type_='image';  
            }
            $media_source='https://d2c8oti4is0ms3.cloudfront.net/images/health_record_media/'.$type_.'/' .$media;
            $health_record_media[]=array(
            'document_id'=>$media_id,
            'document_link'=>$media_source,
            'document_date'=>$created_at
            );
            }        
         }
        else
        {
        $health_record_media=array(); 
        }
                

        $resultpost[]=array(
        'id'=>$health_record_id,
        'patient_name'=>$patient_name,
        'relationship'=>$relationship,
        'date_of_birth'=>$date_of_birth,
        'created_at'=>$created_at,
        'document_count'=>sizeof($health_record_media),
        'document_list'=>$health_record_media
        );                    
         }
        }
        else {
            $resultpost = array();
        }
        return $resultpost;
    }



public function health_list_by_date($patient_id)    
    {
        
        $resultpost='';
        $query = $this->db->query("SELECT id,media,date,created_at FROM health_record_media WHERE health_record_id='$patient_id' GROUP BY date order by date desc");
        $count = $query->num_rows();
        if ($count > 0) {
        foreach ($query->result_array() as $row_media_) {        
        $date=$row_media_['date'];
		
         $query2 = $this->db->query("SELECT id,media,date,created_at,type FROM health_record_media WHERE date='$date' and health_record_id='$patient_id' order by date desc");   
        $health_record_doc='';
           foreach ($query2->result_array() as $row_media) {    
				$media_id=$row_media['id'];
				$media=$row_media['media'];
				$date=$row_media['date'];
				$created_at=$row_media['created_at'];
				
                $type_=$row_media['type'];
                 if($type_=='pdf'){
                   $type_='files'; 
                }
                else{
                  $type_='image';  
                }
                $media_source='https://d2c8oti4is0ms3.cloudfront.net/images/health_record_media/'.$type_.'/' .$media;
				
				$health_record_doc[]=array(
				'document_id'=>$media_id,
				'document_link'=>$media_source
				);         
			}    
			
			$resultpost[]=array(
			'document_date'=>$date,
			'document_array'=>$health_record_doc,
			'created_at'=>$created_at
			);			
           }
         
          }
    
        else {
            $resultpost = array();
        }
      return $resultpost;
    }


    
}
