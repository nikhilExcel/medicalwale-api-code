<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class HealthrecordModel extends CI_Model {

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
    
 
     public function add_folder($user_id,$patient_id,$folder_name,$date) {
        date_default_timezone_set('Asia/Calcutta');
        $created_at = date('Y-m-d');

        $health_record = array(
            'user_id' => $user_id,
            'health_record_id' => $patient_id,
            'folder_name' => $folder_name,
            'date' => $date,
            'created_at' => $created_at
        );
        $this->db->insert('health_record_folder', $health_record);
        $folder_id = $this->db->insert_id();
        return array(
            'status' => 200,
            'message' => 'success',
            'folder_id' => $folder_id
        );
    }
    
      public function health_folder_delete($folder_id)
    {
        
        $query = $this->db->query("SELECT id FROM health_record_folder WHERE id='$folder_id' order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            
            $deleted = $this->db->where('folder_id', $folder_id)->delete('health_record_media');
            if($deleted){
                
                $this->db->where('id', $folder_id)->delete('health_record_folder');
        
                return array(
                    'status' => 200,
                    'message' => 'Health Folder has been deleted.'
                );
            }
        }
      
        
    }
    
     public function edit_folder($folder_id,$user_id,$patient_id,$folder_name,$date) {
        date_default_timezone_set('Asia/Calcutta');
        $created_at = date('Y-m-d');

        $health_record = array(
            'user_id' => $user_id,
            'health_record_id' => $patient_id,
            'folder_name' => $folder_name,
            'date' => $date,
            'created_at' => $created_at
        );
        $this->db->where('id',$folder_id);
        $this->db->update('health_record_folder', $health_record);
        
        return array(
            'status' => 200,
            'message' => 'success',
            'folder_id' => $folder_id
        );
    }
    
    
     public function folder_list($health_record_id) {
            //echo "SELECT * FROM health_record WHERE user_id='$user_id' and vendor_type='$vendor_type' order by id desc";
        $query = $this->db->query("SELECT * FROM health_record_folder WHERE health_record_id='$health_record_id' order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $folder_id        = $row['id'];
                $folder_name        = $row['folder_name'];
                $date       = $row['date'];
                $user_id = $row['user_id'];
                $created_at = $row['created_at'];


                        
                $resultpost[] = array(
                    'id' => $folder_id,
                    'user_id'=>$user_id,
                    'folder_name' => $folder_name,
                    'date'=>$date,
                    'created_at'=>$created_at,
                   
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
     public function healthrecord_list_web($user_id) {
        $resultpost = array();
        $query = $this->db->query("SELECT * FROM health_record WHERE user_id='$user_id' order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {

                $patient_age        = $row['patient_age'];
                $patient_city       = $row['patient_city'];
                $patient_condition  = $row['patient_condition'];
                
                $health_record_id = $row['id'];
                $user_id = $row['user_id'];
                $patient_name = $row['patient_name'];
                $relationship = $row['relationship'];
                $date_of_birth = $row['date_of_birth'];
                $gender = $row['gender'];
                $created_at = $row['created_at'];

                $query2 = $this->db->query("SELECT * FROM health_record_folder WHERE health_record_id='$health_record_id' order by id desc");
                    $count2 = $query->num_rows();
                  if ($count2 > 0) {
                 foreach ($query2->result_array() as $row_folder) {
                     
                $folder_id        = $row_folder['id'];
                
                     
                $health_record_media = array();     
                $query3 = $this->db->query("SELECT id,media,created_at,type,image_title,image_caption FROM health_record_media WHERE folder_id='$folder_id' order by created_at desc");
                $count3 = $query3->num_rows();
                if ($count3 > 0) {
                    foreach ($query3->result_array() as $row_media) {
                        $media_id = $row_media['id'];
                        $media = $row_media['media'];
                        $created_at = $row_media['created_at'];
                         $title = $row_media['image_title'];
                        $caption = $row_media['image_caption'];
                        $type_ = $row_media['type'];
                        if ($type_ == 'pdf') {
                            $type_ = 'files';
                        } else {
                            $type_ = 'image';
                        }
                        $media_source = 'https://d2c8oti4is0ms3.cloudfront.net/images/health_record_media/' . $media;
                        $health_record_media[] = array(
                            'document_id' => $media_id,
                            'document_link' => $media_source,
                            'document_date' => $created_at,
                             'document_title' => $title,
                            'document_caption' => $caption
                        );
                    }
                } else {
                    $health_record_media = array();
                }
                
                
                   
                        
                $resultpost[] = array(
                    'id' => $health_record_id,
                    'folder_id'=>$folder_id,
                    'patient_name' => $patient_name,
                    'patient_age'=>$patient_age,
                    'patient_city'=>$patient_city,
                    'patient_condition'=>$patient_condition,
                    'date_of_birth' => $date_of_birth,
                    'relationship' => $relationship,
                    'gender' => $gender,
                    'document_count' => sizeof($health_record_media),
                    'document_list' => $health_record_media,
                    'created_at' => $created_at
                );
                   
                }
               } else {
               $resultpost = array();
             }
                
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
      public function healthrecord_document_delete($media_id)
    {
       
      
        $this->db->where('id', $media_id)->delete('health_record_media');
        
        return array(
            'status' => 200,
            'message' => 'Document has been deleted.'
        );
    }

    public function add_record($user_id, $patient_name, $relationship, $date_of_birth, $gender) {
        date_default_timezone_set('Asia/Calcutta');
        $created_at = date('Y-m-d');

        $health_record = array(
            'user_id' => $user_id,
            'patient_name' => $patient_name,
            'relationship' => $relationship,
            'date_of_birth' => $date_of_birth,
            'gender' => $gender,
            'created_at' => $created_at
        );
        $this->db->insert('health_record', $health_record);
        $patient_id = $this->db->insert_id();
        return array(
            'status' => 200,
            'message' => 'success',
            'patient_id' => $patient_id
        );
    }
    
    // Family tree function by ghanshyam parihar starts
    public function healthrecord_familytree_list($user_id) {
        //echo "SELECT * FROM health_record WHERE user_id='$user_id' order by id desc";
        $query = $this->db->query("SELECT * FROM health_record WHERE user_id='$user_id' order by id desc");
        $count = $query->num_rows();
        
        $health_record_grandparent = array();
        $health_record_parent = array();
        $health_record_subparent = array();
        $health_record_neighbour = array();
        $health_record_spouse = array();
        $health_record_myself = array();
        $health_record_sibling = array();
        $health_record_child = array();
        
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $patient_age        = $row['patient_age'];
                $patient_city       = $row['patient_city'];
                $patient_condition  = $row['patient_condition'];
                
                $health_record_id = $row['id'];
                $user_id = $row['user_id'];
                $patient_name = $row['patient_name'];
                $relationship = $row['relationship'];
                $date_of_birth = $row['date_of_birth'];
                $gender = $row['gender'];
                $created_at = $row['created_at'];

                $query2 = $this->db->query("SELECT id,media,created_at,type FROM health_record_media WHERE health_record_id='$health_record_id' order by created_at desc");
                $count2 = $query2->num_rows();
                if ($count2 > 0) {
                    foreach ($query2->result_array() as $row_media) {
                        $media_id = $row_media['id'];
                        $media = $row_media['media'];
                        $created_at = $row_media['created_at'];
                        $type_ = $row_media['type'];
                        if ($type_ == 'pdf') {
                            $type_ = 'files';
                        } else {
                            $type_ = 'image';
                        }
                        $media_source = 'https://d2c8oti4is0ms3.cloudfront.net/images/health_record_media/' . $type_ . '/' . $media;
                        $health_record_media[] = array(
                            'document_id' => $media_id,
                            'document_link' => $media_source,
                            'document_date' => $created_at
                        );
                    }
                } else {
                    $health_record_media = array();
                }

                if($relationship==='Grand Father' || $relationship==='Grand Mother'){
                    $health_record_grandparent_[] = array(
                            'id' => $health_record_id,
                            'patient_name' => $patient_name,
                            'patient_age'=>$patient_age,
                            'patient_city'=>$patient_city,
                            'patient_condition'=>$patient_condition,
                            'date_of_birth' => $date_of_birth,
                            'relationship' => $relationship,
                            'gender' => $gender,
                            'document_count' => sizeof($health_record_media),
                            'document_list' => $health_record_media,
                            'created_at' => $created_at
                        );
                }
                else{
                    $health_record_grandparent_ = array();
                }
                
                if($relationship==='Mother' || $relationship==='Father'){
                    $health_record_parent_[] = array(
                            'id' => $health_record_id,
                            'patient_name' => $patient_name,
                            'patient_age'=>$patient_age,
                            'patient_city'=>$patient_city,
                            'patient_condition'=>$patient_condition,
                            'date_of_birth' => $date_of_birth,
                            'relationship' => $relationship,
                            'gender' => $gender,
                            'document_count' => sizeof($health_record_media),
                            'document_list' => $health_record_media,
                            'created_at' => $created_at
                        );
                }
                else{
                    $health_record_parent_ = array();
                }
                
                 if($relationship==='Aunty' || $relationship==='Uncle'){
                    $health_record_subparent_[] = array(
                            'id' => $health_record_id,
                            'patient_name' => $patient_name,
                            'patient_age'=>$patient_age,
                            'patient_city'=>$patient_city,
                            'patient_condition'=>$patient_condition,
                            'date_of_birth' => $date_of_birth,
                            'relationship' => $relationship,
                            'gender' => $gender,
                            'document_count' => sizeof($health_record_media),
                            'document_list' => $health_record_media,
                            'created_at' => $created_at
                        );
                }
                else{
                    $health_record_subparent_ = array();
                }
                
                if($relationship==='Friend' || $relationship==='Neighbour'){
                    $health_record_neighbour_[] = array(
                            'id' => $health_record_id,
                            'patient_name' => $patient_name,
                            'patient_age'=>$patient_age,
                            'patient_city'=>$patient_city,
                            'patient_condition'=>$patient_condition,
                            'date_of_birth' => $date_of_birth,
                            'relationship' => $relationship,
                            'gender' => $gender,
                            'document_count' => sizeof($health_record_media),
                            'document_list' => $health_record_media,
                            'created_at' => $created_at
                        );
                }
                else{
                    $health_record_neighbour_ = array();
                }
                
                if($relationship==='Brother' || $relationship==='Sister'){
                    $health_record_sibling_[] = array(
                            'id' => $health_record_id,
                            'patient_name' => $patient_name,
                            'patient_age'=>$patient_age,
                            'patient_city'=>$patient_city,
                            'patient_condition'=>$patient_condition,
                            'date_of_birth' => $date_of_birth,
                            'relationship' => $relationship,
                            'gender' => $gender,
                            'document_count' => sizeof($health_record_media),
                            'document_list' => $health_record_media,
                            'created_at' => $created_at
                        );
                }
                else{
                    $health_record_sibling_ = array();
                }
                
                if($relationship==='Husband' || $relationship==='Wife'){
                    $health_record_spouse_[] = array(
                            'id' => $health_record_id,
                            'patient_name' => $patient_name,
                            'patient_age'=>$patient_age,
                            'patient_city'=>$patient_city,
                            'patient_condition'=>$patient_condition,
                            'date_of_birth' => $date_of_birth,
                            'relationship' => $relationship,
                            'gender' => $gender,
                            'document_count' => sizeof($health_record_media),
                            'document_list' => $health_record_media,
                            'created_at' => $created_at
                        );
                }
                else{
                    $health_record_spouse_ = array();
                }
                
                if($relationship==='Myself'){
                    $health_record_myself_[] = array(
                            'id' => $health_record_id,
                            'patient_name' => $patient_name,
                            'patient_age'=>$patient_age,
                            'patient_city'=>$patient_city,
                            'patient_condition'=>$patient_condition,
                            'date_of_birth' => $date_of_birth,
                            'relationship' => $relationship,
                            'gender' => $gender,
                            'document_count' => sizeof($health_record_media),
                            'document_list' => $health_record_media,
                            'created_at' => $created_at
                        );
                }
                else{
                    $health_record_myself_ = array();
                }
                
                if($relationship==='Son' || $relationship==='Daughter'){
                    $health_record_child_[] = array(
                            'id' => $health_record_id,
                            'patient_name' => $patient_name,
                            'patient_age'=>$patient_age,
                            'patient_city'=>$patient_city,
                            'patient_condition'=>$patient_condition,
                            'date_of_birth' => $date_of_birth,
                            'relationship' => $relationship,
                            'gender' => $gender,
                            'document_count' => sizeof($health_record_media),
                            'document_list' => $health_record_media,
                            'created_at' => $created_at
                        );
                }
                else{
                    $health_record_child_ = array();
                }
                
                if(!empty($health_record_grandparent_)){        
                    $health_record_grandparent = $this->super_unique(array_merge($health_record_grandparent,$health_record_grandparent_),'id');
                }
                if(!empty($health_record_parent_)){        
                    $health_record_parent = $this->super_unique(array_merge($health_record_parent,$health_record_parent_),'id');
                }
                if(!empty($health_record_subparent_)){        
                    $health_record_subparent = $this->super_unique(array_merge($health_record_subparent,$health_record_subparent_),'id');
                }
                if(!empty($health_record_neighbour_)){        
                    $health_record_neighbour = $this->super_unique(array_merge($health_record_neighbour,$health_record_neighbour_),'id');
                }
                if(!empty($health_record_spouse_)){        
                    $health_record_spouse = $this->super_unique(array_merge($health_record_spouse,$health_record_spouse_),'id');
                }
                if(!empty($health_record_myself_)){        
                    $health_record_myself = $this->super_unique(array_merge($health_record_myself,$health_record_myself_),'patient_name');
                }
                if(!empty($health_record_sibling_)){        
                    $health_record_sibling = $this->super_unique(array_merge($health_record_sibling,$health_record_sibling_),'id');
                }
                if(!empty($health_record_child_)){        
                    $health_record_child = $this->super_unique(array_merge($health_record_child,$health_record_child_),'id');
                }
            }
            $resultpost[] = array(
                        'health_record_myself'=>$health_record_myself,
                        'health_record_grandparent' => $health_record_grandparent,
                        'health_record_parent' => $health_record_parent,
                        'health_record_subparent_aunty_uncle' => $health_record_subparent,
                        'health_record_friend_neighbour' => $health_record_neighbour,
                        '$health_record_spouse' => $health_record_spouse,
                        'health_record_sibling'=>$health_record_sibling,
                        'health_record_child'=>$health_record_child
                    );
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    // Family tree function by ghanshyam parihar ends 
    
    // Family Tree User details by ghanshyam parihar starts
    public function healthrecord_familytree_user_list($user_id) {
        //echo "SELECT * FROM health_record WHERE user_id='$user_id' and vendor_type='$vendor_type' order by id desc";
        $query = $this->db->query("SELECT * FROM health_record WHERE id='$user_id' order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {

                $patient_age        = $row['patient_age'];
                $patient_city       = $row['patient_city'];
                $patient_condition  = $row['patient_condition'];
                
                $health_record_id = $row['id'];
                $user_id = $row['user_id'];
                $patient_name = $row['patient_name'];
                $relationship = $row['relationship'];
                $date_of_birth = $row['date_of_birth'];
                $gender = $row['gender'];
                $created_at = $row['created_at'];


                $query2 = $this->db->query("SELECT id,media,created_at,type FROM health_record_media WHERE health_record_id='$health_record_id' order by created_at desc");
                $count2 = $query2->num_rows();
                if ($count2 > 0) {
                    foreach ($query2->result_array() as $row_media) {
                        $media_id = $row_media['id'];
                        $media = $row_media['media'];
                        $created_at = $row_media['created_at'];
                        $type_ = $row_media['type'];
                        if ($type_ == 'pdf') {
                            $type_ = 'files';
                        } else {
                            $type_ = 'image';
                        }
                        $media_source = 'https://d2c8oti4is0ms3.cloudfront.net/images/health_record_media/' . $type_ . '/' . $media;
                        $health_record_media[] = array(
                            'document_id' => $media_id,
                            'document_link' => $media_source,
                            'document_date' => $created_at
                        );
                    }
                } else {
                    $health_record_media = array();
                }
                
                   
                        
                $resultpost[] = array(
                    'id' => $health_record_id,
                    'patient_name' => $patient_name,
                    'patient_age'=>$patient_age,
                    'patient_city'=>$patient_city,
                    'patient_condition'=>$patient_condition,
                    'date_of_birth' => $date_of_birth,
                    'relationship' => $relationship,
                    'gender' => $gender,
                    'document_count' => sizeof($health_record_media),
                    'document_list' => $health_record_media,
                    'created_at' => $created_at
                );
                   

                
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    // Family Tree User details by ghanshyam parihar ends
    
    public function healthrecord_list($user_id) {
        //echo "SELECT * FROM health_record WHERE user_id='$user_id' and vendor_type='$vendor_type' order by id desc";
        $query = $this->db->query("SELECT * FROM health_record WHERE user_id='$user_id' and active=0 order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {

                $patient_age        = $row['patient_age'];
                $patient_city       = $row['patient_city'];
                $patient_condition  = $row['patient_condition'];
                
                $health_record_id = $row['id'];
                $user_id = $row['user_id'];
                $patient_name = $row['patient_name'];
                $relationship = $row['relationship'];
                $date_of_birth = $row['date_of_birth'];
                $gender = $row['gender'];
                $created_at = $row['created_at'];


                $query2 = $this->db->query("SELECT id,media,created_at,type FROM health_record_media WHERE health_record_id='$health_record_id' order by created_at desc");
                $count2 = $query2->num_rows();
                if ($count2 > 0) {
                    foreach ($query2->result_array() as $row_media) {
                        $media_id = $row_media['id'];
                        $media = $row_media['media'];
                        $created_at = $row_media['created_at'];
                        $type_ = $row_media['type'];
                        if ($type_ == 'pdf') {
                            $type_ = 'files';
                        } else {
                            $type_ = 'image';
                        }
                        $media_source = 'https://d2c8oti4is0ms3.cloudfront.net/images/health_record_media/' . $type_ . '/' . $media;
                        $health_record_media[] = array(
                            'document_id' => $media_id,
                            'document_link' => $media_source,
                            'document_date' => $created_at
                        );
                    }
                } else {
                    $health_record_media = array();
                }
                
                   
                        
                $resultpost[] = array(
                    'id' => $health_record_id,
                    'patient_name' => $patient_name,
                    'patient_age'=>$patient_age,
                    'patient_city'=>$patient_city,
                    'patient_condition'=>$patient_condition,
                    'date_of_birth' => $date_of_birth,
                    'relationship' => $relationship,
                    'gender' => $gender,
                    'document_count' => sizeof($health_record_media),
                    'document_list' => $health_record_media,
                    'created_at' => $created_at
                );
                   

                
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    
     public function healthrecord_list_group($user_id) {
        //echo "SELECT * FROM health_record WHERE user_id='$user_id' and vendor_type='$vendor_type' order by id desc";
        $query = $this->db->query("SELECT * FROM health_record WHERE user_id='$user_id' order by id desc");
        $count = $query->num_rows();
        $parents = array();
        $brothers = array();
        $myself = array();
        $friendsandother = array();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {

                $patient_age        = $row['patient_age'];
                $patient_city       = $row['patient_city'];
                $patient_condition  = $row['patient_condition'];
                
                $health_record_id = $row['id'];
                $user_id = $row['user_id'];
                $patient_name = $row['patient_name'];
                $relationship = $row['relationship'];
                $date_of_birth = $row['date_of_birth'];
                $gender = $row['gender'];
                $created_at = $row['created_at'];
                
                   $img_count = $this->db->select('media.id')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();

                if ($img_count > 0) {
                    $media = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $media->source;
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                   // $image_status = '5';
                } else {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                  //  $image_status = '0';
                }

                $query2 = $this->db->query("SELECT id,media,created_at,type FROM health_record_media WHERE health_record_id='$health_record_id' order by created_at desc");
                $count2 = $query2->num_rows();
                if ($count2 > 0) {
                    foreach ($query2->result_array() as $row_media) {
                        $media_id = $row_media['id'];
                        $media = $row_media['media'];
                        $created_at = $row_media['created_at'];
                        $type_ = $row_media['type'];
                        if ($type_ == 'pdf') {
                            $type_ = 'files';
                        } else {
                            $type_ = 'image';
                        }
                        $media_source = 'https://d2c8oti4is0ms3.cloudfront.net/images/health_record_media/' . $type_ . '/' . $media;
                        $health_record_media[] = array(
                            'document_id' => $media_id,
                            'document_link' => $media_source,
                            'document_date' => $created_at
                        );
                    }
                } else {
                    $health_record_media = array();
                }
                
                if($relationship == 'father' ||  $relationship == 'mother' || $relationship == 'uncle' || $relationship == 'aunty' || $relationship == 'Father' ||  $relationship == 'Mother' || $relationship == 'Uncle' || $relationship == 'Aunty')   
                        {
                      $parents[]  =    array(
                    'id' => $health_record_id,
                    'patient_name' => $patient_name,
                    'patient_age'=>$patient_age,
                    'image' => $image,
                    'patient_city'=>$patient_city,
                    'patient_condition'=>$patient_condition,
                    'date_of_birth' => $date_of_birth,
                    'relationship' => $relationship,
                    'gender' => $gender,
                    'document_count' => sizeof($health_record_media),
                    'document_list' => $health_record_media,
                    'created_at' => $created_at
                );
                        }
                        else   if($relationship == 'brother' || $relationship == 'sister' || $relationship == 'cousin' || $relationship == 'Brother' || $relationship == 'Sister' || $relationship == 'Cousin')   
                        {
                      $brothers[]  =    array(
                    'id' => $health_record_id,
                    'patient_name' => $patient_name,
                    'patient_age'=>$patient_age,
                    'image' => $image,
                    'patient_city'=>$patient_city,
                    'patient_condition'=>$patient_condition,
                    'date_of_birth' => $date_of_birth,
                    'relationship' => $relationship,
                    'gender' => $gender,
                    'document_count' => sizeof($health_record_media),
                    'document_list' => $health_record_media,
                    'created_at' => $created_at
                );
                        }
                         else   if($relationship == 'myself' || $relationship == 'wife' || $relationship == 'Myself' || $relationship == 'Wife' || $relationship == 'Husband' || $relationship == 'husband' )   
                        {
                      $myself[]  =    array(
                    'id' => $health_record_id,
                    'patient_name' => $patient_name,
                    'patient_age'=>$patient_age,
                    'image' => $image,
                    'patient_city'=>$patient_city,
                    'patient_condition'=>$patient_condition,
                    'date_of_birth' => $date_of_birth,
                    'relationship' => $relationship,
                    'gender' => $gender,
                    'document_count' => sizeof($health_record_media),
                    'document_list' => $health_record_media,
                    'created_at' => $created_at
                );
                        }
                        else
                        {
                             $friendsandother[]  =    array(
                    'id' => $health_record_id,
                    'patient_name' => $patient_name,
                    'patient_age'=>$patient_age,
                    'image' => $image,
                    'patient_city'=>$patient_city,
                    'patient_condition'=>$patient_condition,
                    'date_of_birth' => $date_of_birth,
                    'relationship' => $relationship,
                    'gender' => $gender,
                    'document_count' => sizeof($health_record_media),
                    'document_list' => $health_record_media,
                    'created_at' => $created_at
                );
                        }
                        
            }
            $resultpost[] = array(
                    'parents' => $parents,
                    'brothers' => $brothers,
                    'myself'=>$myself,
                    'friends_others'=>$friendsandother
                );
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    
    public function healthrecord_list_group_v2($user_id) {
        $folder_id = "";
       $resultpost = array();
        // $spouse = $children = $parents = $siblings = $grandparents = $grandchildren = $friendsNrelatives = $neighbour = $guardian = $others = array();
            $spouse['cat_name'] = 'Spouse';  $spouse['relations'] = array();
                $spouse['cat_name'] = 'Spouse';  $spouse['relations'] = array();
                $children['cat_name'] = 'Children';  $children['relations'] = array();
                $parents['cat_name'] = 'Parents';  $parents['relations'] = array();
                $siblings['cat_name'] = 'Siblings';  $siblings['relations'] = array();
                $grandparents['cat_name'] = 'Grand parents';  $grandparents['relations'] = array();
                $grandchildren['cat_name'] = 'Grand children';  $grandchildren['relations'] = array();
                $friendsNrelatives['cat_name'] = 'Friends and Relatives';  $friendsNrelatives['relations'] = array();
                $neighbour['cat_name'] = 'Neighbour';  $neighbour['relations'] = array();
                $guardian['cat_name'] = 'Guardian';  $guardian['relations'] = array();
                $others['cat_name'] = 'Others';  $others['relations'] = array();
                
        //echo "SELECT * FROM health_record WHERE user_id='$user_id' and vendor_type='$vendor_type' order by id desc";
        $query = $this->db->query("SELECT * FROM health_record WHERE user_id='$user_id' and active=0 order by id desc");
        $count = $query->num_rows();
        //$parents = array();
        $brothers = array();
        // $myself = array();
        // $friendsandother = array();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                // print_r($row);  die();
                $patient_age        = $row['patient_age'];
                
                $patient_city       = $row['patient_city'];
                $patient_condition  = $row['patient_condition'];
                $health_record_id = $row['id'];
                $relation_record_id = $row['relation_id'];
                $user_id = $row['user_id'];
                $patient_name = $row['patient_name'];
                $relationship = $row['relationship'];
                $date_of_birth = $row['date_of_birth'];
                $gender = $row['gender'];
                $created_at = $row['created_at'];
                $phone=$row['phone'];
                $email=$row['email'];
                if(empty($patient_age)){$patient_age = ""; }
                if(empty($patient_city)){$patient_city = ""; }
                if(empty($patient_condition)){$patient_condition = ""; }
                if(empty($health_record_id)){$health_record_id = ""; }
                if(empty($user_id)){$user_id = ""; }
                if(empty($patient_name)){$patient_name = ""; }
                if(empty($relationship)){$relationship = ""; }
                if(empty($date_of_birth)){$date_of_birth = ""; }
                if(empty($gender)){$gender = ""; }
                if(empty($created_at)){$created_at = ""; }
                if(empty($phone) or $phone=="0")
                {$phone = ""; }
                if(empty($email))
                {$email = ""; }
                 $query12 = $this->db->query("SELECT * FROM health_record_folder WHERE health_record_id='$health_record_id' order by id desc");
                    $count2 = $query12->num_rows();
                  if ($count2 > 0) {
                 foreach ($query12->result_array() as $row_folder) {
                     
                    $folder_id        = $row_folder['id'];
                    
                    if($folder_id == null){
                        $folder_id = "";
                    }
                 
                 }}
                $img_count = $this->db->select('media.id')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
               
                if ($img_count > 0 && $relationship == 'Myself' ) {
                    $media = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $media->source;
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                   // $image_status = '5';
                } 
                else if ($relationship != 'Myself' ) {
                    $img_count1 = $this->db->select('media.id')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $relation_record_id)->get()->num_rows();
                     if ($img_count1 > 0 )
                     {
                    $media = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $relation_record_id)->get()->row();
                    $img_file = $media->source;
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                   // $image_status = '5';
                     }
                     else
                     {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg'; 
                     }
                   
                } 
                
                else {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                  //  $image_status = '0';
                }

                $query2 = $this->db->query("SELECT id,media,created_at,image_title,image_caption,type FROM health_record_media WHERE health_record_id='$health_record_id' order by created_at desc");
                $count2 = $query2->num_rows();
                if ($count2 > 0) {
                    foreach ($query2->result_array() as $row_media) {
                        $media_id = $row_media['id'];
                        $media = $row_media['media'];
                        $created_at = $row_media['created_at'];
                        $type_ = $row_media['type'];
                          $title = $row_media['image_title'];
                          if($title == null){
                              $title = "";
                          }
                        $caption = $row_media['image_caption'];
                        if($caption == null){
                              $caption = "";
                          }
                        if ($type_ == 'pdf') {
                            $type_ = 'files';
                        } else {
                            $type_ = 'image';
                        }
                        $media_source = 'https://d2c8oti4is0ms3.cloudfront.net/images/health_record_media/' . $type_ . '/' . $media;
                        $health_record_media[] = array(
                            'document_id' => $media_id,
                            'document_link' => $media_source,
                            'document_date' => $created_at,
                            'document_title' => $title,
                            'document_caption' => $caption
                        );
                    }
                } else {
                    $health_record_media = array();
                }
                
                // print_r($row); die();
                
                $relation_name = $relationship;
                $relation_info = array(
                    'id' => $health_record_id,
                    'member_id'=>$relation_record_id,
                    'patient_name' => $patient_name,
                    'folder_id' => $folder_id,
                    'patient_age'=>$patient_age,
                    'image' => $image,
                    'patient_city'=>$patient_city,
                    'patient_condition'=>$patient_condition,
                    'date_of_birth' => $date_of_birth,
                    'relationship' => $relationship,
                    'gender' => $gender,
                    'mobile'=>$phone,
                    'email'=>$email,
                    'document_count' => sizeof($health_record_media),
                    'document_list' => $health_record_media,
                    'created_at' => $created_at
                );
                
                
                // $resultp['relation_name'] = $relation_name;
                // $resultp['relation_info'] = $relation_info;
                $resultp = $relation_info;
                
                // $resultpost[] = $resultp;
                
                $relationship = strtolower($relationship); 
                
               
                
                if($relationship == 'myself'){
                    $myself['cat_name'] = 'Me';
                    $myself['relations'][]  =  $resultp;
                } else if($relationship == 'wife' || $relationship == 'husband' ){
                    $spouse['cat_name'] = 'Spouse';
                    $spouse['relations'][]  =  $resultp;
                } else if($relationship == 'daughter' || $relationship == 'son'){
                    $children['cat_name'] = 'Children';
                    $children['relations'][]  =  $resultp;
                } else if($relationship == 'father' ||  $relationship == 'mother'){
                    $parents['cat_name'] = 'Parents';
                    $parents['relations'][]  =  $resultp;
                } else if($relationship == 'sister' ||  $relationship == 'brother'){
                    $siblings['cat_name'] = 'Siblings';
                    $siblings['relations'][]  =  $resultp;
                } else if($relationship == 'grand father' ||  $relationship == 'grand mother'){
                    $grandparents['cat_name'] = 'Grand parents';
                    $grandparents['relations'][]  =  $resultp;
                } else if($relationship == 'grand son' ||  $relationship == 'grand daughter'){
                    $grandchildren['cat_name'] = 'Grand children';
                    $grandchildren['relations'][]  =  $resultp;
                } else if($relationship == 'uncle' || $relationship == 'aunty' || $relationship == 'cousin' || $relationship == 'friend'){
                    $friendsNrelatives['cat_name'] = 'Friends and Relatives';
                    $friendsNrelatives['relations'][]  =  $resultp;
                } else if($relationship == 'neighbour' ){
                    $neighbour['cat_name'] = 'Neighbour';
                    $neighbour['relations'][]  =  $resultp;
                } else if($relationship == 'guardian' ){
                    $guardian['cat_name'] = 'Guardian';
                    $guardian['relations'][]  =  $resultp;
                } else {
                    $others['cat_name'] = 'Others';
                    $others['relations'][]  =  $resultp;
                }
                        
            }
            // die();
        
            $myDataUsers = array();
            if(empty($myself)){ 
                $myDataUsers = array();
           
                // $myself = (object)[];
                $myDataUsers = $this->db->query("SELECT `id`,`name`,`phone`,`email`, `age`,`city`,`dob`,`gender` FROM `users` WHERE `id` = '$user_id'")->row_array();
                // print_R($myDataUsers['name']); die();
                $name = $myDataUsers['name'];
                $phone = $myDataUsers['phone'];
                $email = $myDataUsers['email'];
                $age = $myDataUsers['age'];
                $city = $myDataUsers['city'];
                $dob = $myDataUsers['dob'];
                $gender = $myDataUsers['gender'];
                $created_at = date("Y-m-d");
                $insertIntoHealthrecord = $this->db->query("INSERT INTO `health_record`(`user_id`, `patient_name`, `patient_age`, `patient_city`,`relationship`, `date_of_birth`, `gender`, `email`, `phone`, `created_at`) VALUES ('$user_id','$name','$age','$city','Myself','$dob','$gender','$email','$phone','$created_at')");
                
                $insert_id = $this->db->insert_id();
                
                $media = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                
                if(!empty($media)){
                    $img_file = $media->source;
                     $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                    
                   

                // print_r($image); die();
                $relation_info = array( 
                    'id' => $insert_id,
                    'patient_name' => $name,
                    'folder_id' => '',
                    'member_id'=>0,
                    'patient_age'=>$age,
                    'image' => $image,
                    'patient_city'=>$city,
                    'patient_condition'=>'',
                    'date_of_birth' => $dob,
                    'relationship' => 'Myself',
                    'gender' => $gender,
                     'mobile'=>$phone,
                     'email'=>$email,
                    'document_count' => 0,
                    'document_list' => array(),
                    'created_at' => $created_at
                );
                foreach($relation_info as $k=>$v){
                    if($v == null && $k != 'document_list' && $k != 'document_count'){
                        $relation_info[$k] = ''; 
                    }
                }
                $relation_info1[] = $relation_info;
                $myself['cat_name'] = 'Me';
                $myself['relations'] = $relation_info1;
               
         
               
                
            }
            
           
            
            
            /*
            if(!empty($spouse)){ $resultpost[] = $spouse; } 
            if(!empty($children)){ $resultpost[] = $children; }
            if(!empty($parents)){ $resultpost[] = $parents; }
            if(!empty($siblings)){ $resultpost[] = $siblings; }
            if(!empty($grandparents)){ $resultpost[] = $grandparents; }
            if(!empty($grandchildren)){ $resultpost[] = $grandchildren; }
            if(!empty($friendsNrelatives)){ $resultpost[] = $friendsNrelatives; }
            if(!empty($neighbour)){ $resultpost[] = $neighbour; }
            if(!empty($guardian)){ $resultpost[] = $guardian; }
            if(!empty($others)){ $resultpost[] = $others; }
            */
            
            $resultpost[] = $spouse; 
            $resultpost[] = $children; 
            $resultpost[] = $parents; 
            $resultpost[] = $siblings; 
            $resultpost[] = $grandparents; 
            $resultpost[] = $grandchildren; 
            $resultpost[] = $friendsNrelatives; 
            $resultpost[] = $neighbour; 
            $resultpost[] = $guardian; 
            $resultpost[] = $others; 
            
            
         
        } else {
            // $myself = (object)[];
            // $resultpost = array();
            
            
            
            // $myself = (object)[];
            // $resultpost = array();
            
            
            $myDataUsers = array();
            if(empty($myself)){ 
                // $myself = (object)[];
                $myDataUsers = $this->db->query("SELECT `id`,`name`,`phone`,`email`, `age`,`city`,`dob`,`gender` FROM `users` WHERE `id` = '$user_id'")->row_array();
                // print_R($myDataUsers['name']); die();
                $name = $myDataUsers['name'];
                $phone = $myDataUsers['phone'];
                $email = $myDataUsers['email'];
                $age = $myDataUsers['age'];
                $city = $myDataUsers['city'];
                $dob = $myDataUsers['dob'];
                $gender = $myDataUsers['gender'];
                $created_at = date("Y-m-d");
                $insertIntoHealthrecord = $this->db->query("INSERT INTO `health_record`(`user_id`, `patient_name`, `patient_age`, `patient_city`,`relationship`, `date_of_birth`, `gender`, `email`, `phone`, `created_at`) VALUES ('$user_id','$name','$age','$city','Myself','$dob','$gender','$email','$phone','$created_at')");
                
                $insert_id = $this->db->insert_id();
                
                $media = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                
                if(!empty($media)){
                    $img_file = $media->source;
                     $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                    
                   

                // print_r($image); die();
                $relation_info = array( 
                    'id' => $insert_id,
                    'patient_name' => $name,
                    'folder_id' => '',
                    'member_id'=>0,
                    'patient_age'=>$age,
                    'image' => $image,
                    'patient_city'=>$city,
                    'patient_condition'=>'',
                    'date_of_birth' => $dob,
                    'relationship' => 'Myself',
                    'gender' => $gender,
                     'mobile'=>$phone,
                     'email'=>$email,
                    'document_count' => 0,
                    'document_list' => array(),
                    'created_at' => $created_at
                );
                foreach($relation_info as $k=>$v){
                    if($v == null && $k != 'document_list' && $k != 'document_count'){
                        $relation_info[$k] = ''; 
                    }
                }
                $relation_info1[] = $relation_info;
                $myself['cat_name'] = 'Me';
                $myself['relations'] = $relation_info1;
               
                
            }
            
            $resultpost[] = $spouse; 
            $resultpost[] = $children; 
            $resultpost[] = $parents; 
            $resultpost[] = $siblings; 
            $resultpost[] = $grandparents; 
            $resultpost[] = $grandchildren; 
            $resultpost[] = $friendsNrelatives; 
            $resultpost[] = $neighbour; 
            $resultpost[] = $guardian; 
            $resultpost[] = $others; 
            
        // print_r($resultpost); die();
        }
        // echo "test"; die();
        return array(
            "status" => 200,
            "message" => "success",
            "Myself" => $myself,
            "data" => $resultpost
                   
        );
    }
    
    
    //added for web for perticular patient details 
    public function healthrecord_details($user_id,$patient_id)
    {
          $query = $this->db->query("SELECT * FROM health_record WHERE user_id='$user_id' and id='$patient_id'order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {

                $patient_age        = $row['patient_age'];
                $patient_city       = $row['patient_city'];
                $patient_condition  = $row['patient_condition'];
                
                $health_record_id = $row['id'];
                $user_id = $row['user_id'];
                $patient_name = $row['patient_name'];
                $relationship = $row['relationship'];
                
                $health_condition   = $row['health_condition'];
                $allergies          = $row['allergies'];
                $heradiatry_problem = $row['heradiatry_problem'];
                
                $date_of_birth = $row['date_of_birth'];
                $gender = $row['gender'];
                $created_at = $row['created_at'];


                $query2 = $this->db->query("SELECT id,media,created_at,type FROM health_record_media WHERE health_record_id='$health_record_id' order by created_at desc");
                $count2 = $query2->num_rows();
                if ($count2 > 0) {
                    foreach ($query2->result_array() as $row_media) {
                        $media_id = $row_media['id'];
                        $media = $row_media['media'];
                        $created_at = $row_media['created_at'];
                        $type_ = $row_media['type'];
                        if ($type_ == 'pdf') {
                            $type_ = 'files';
                        } else {
                            $type_ = 'image';
                        }
                        $media_source = 'https://d2c8oti4is0ms3.cloudfront.net/images/health_record_media/' . $type_ . '/' . $media;
                        $health_record_media[] = array(
                            'document_id' => $media_id,
                            'document_link' => $media_source,
                            'document_date' => $created_at
                        );
                    }
                } else {
                    $health_record_media = array();
                }
                
                   
                        
                $resultpost = array(
                    'id' => $health_record_id,
                    'patient_name' => $patient_name,
                    'patient_age'=>$patient_age,
                    'patient_city'=>$patient_city,
                    'patient_condition'=>$patient_condition,
                    'health_condition'=>$health_condition,
                    'allergies'=>$allergies,
                    'heradiatry_problem'=>$heradiatry_problem,
                    'date_of_birth' => $date_of_birth,
                    'relationship' => $relationship,
                    'gender' => $gender,
                    'document_count' => sizeof($health_record_media),
                    'document_list' => $health_record_media,
                    'created_at' => $created_at
                );
                   

                
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    //end 
    
    function super_unique($array,$key)
    {
       $temp_array = [];
       foreach ($array as &$v) {
           if (!isset($temp_array[$v[$key]]))
           $temp_array[$v[$key]] =& $v;
       }
       $array = array_values($temp_array);
       return $array;

    }

    public function health_list_by_date($patient_id) {

        $resultpost = '';
        $query = $this->db->query("SELECT id,media,date,created_at FROM health_record_media WHERE health_record_id='$patient_id' GROUP BY date order by date desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row_media_) {
                $date = $row_media_['date'];

                $query2 = $this->db->query("SELECT id,media,date,created_at,type FROM health_record_media WHERE date='$date' and health_record_id='$patient_id' order by id desc");
                $health_record_doc = '';
                foreach ($query2->result_array() as $row_media) {
                    $media_id = $row_media['id'];
                    $media = $row_media['media'];
                    $date = $row_media['date'];
                    $created_at = $row_media['created_at'];

                    $type_ = $row_media['type'];
                    if ($type_ == 'pdf') {
                        $type_ = 'files';
                    } else {
                        $type_ = 'image';
                    }
                    $media_source = 'https://d2c8oti4is0ms3.cloudfront.net/images/health_record_media/' . $type_ . '/' . $media;

                    $health_record_doc[] = array(
                        'document_id' => $media_id,
                        'document_link' => $media_source
                    );
                }

                $resultpost[] = array(
                    'document_date' => $date,
                    'document_array' => $health_record_doc,
                    'created_at' => $created_at
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    public function health_record_delete($user_id,$id)
    {
        $this->db->where('id', $id)->where('user_id',$user_id)->delete('health_record');
        $this->db->where('health_record_id',$id)->delete('health_record_media');
        return array(
            'status' => 200,
            'message' => 'Health record has been deleted.'
        );
    }
    
    public function health_document_delete($user_id,$id,$docmunt_id)
    {
        $document_id_array = explode(',',$docmunt_id);
        // print_r($document_id_array);
        // die();
        foreach($document_id_array as $doc_array)
        {
            
         $this->db->where('health_record_id',$id)->where('id',$doc_array)->delete('health_record_media');
        }
        return array(
            'status' => 200,
            'message' => 'Health record document has been deleted.'
        );
    }
    
    public function health_document_add_web($user_id, $folder_id, $patient_id, $document_name, $caption, $document_file, $date_added){
        
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
            
            
            $ext = getExtension($document_file);
            $add_docs = array(
                'folder_id'         => $folder_id,
                'health_record_id'  => $patient_id,
                'media'             => $document_file,
                'type'              => $ext,
                'source'            => $document_file,
                'image_title'       => $document_name,
                'image_caption'     => $caption,
                'img_width'         =>'0',
                'img_height'         =>'0',
                'date'              =>$date_added,
                'created_at'         =>$date,
            );  
            
            $inserted = $this->db->insert('health_record_media', $add_docs);
                
            if($inserted){    
                    return array(
                        'status' => 200,
                        'message' => 'success'
                    );
            }else{
                return array(
                        'status' => 201,
                        'message' => 'Error while inserting'
                    );
            }
                                  
               
            }
            
        public function healthrecord_list_web_v2($user_id) {
        $resultpost = array();
        $query = $this->db->query("SELECT * FROM health_record WHERE user_id='$user_id' order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {

                $patient_age        = $row['patient_age'];
                $patient_city       = $row['patient_city'];
                $patient_condition  = $row['patient_condition'];
                
                $health_record_id = $row['id'];
                $user_id = $row['user_id'];
                $patient_name = $row['patient_name'];
                $relationship = $row['relationship'];
                $date_of_birth = $row['date_of_birth'];
                $gender = $row['gender'];
                $created_at = $row['created_at'];

                $query2 = $this->db->query("SELECT * FROM health_record_folder WHERE health_record_id='$health_record_id' order by id desc");
                    $count2 = $query->num_rows();
                  if ($count2 > 0) {
                 foreach ($query2->result_array() as $row_folder) {
                     
                $folder_id        = $row_folder['id'];
                
                     
                $health_record_media = array();     
                $query3 = $this->db->query("SELECT id,media,created_at,type,image_title,image_caption FROM health_record_media WHERE folder_id='$folder_id' order by created_at desc");
                $count3 = $query3->num_rows();
                if ($count3 > 0) {
                    foreach ($query3->result_array() as $row_media) {
                        $media_id = $row_media['id'];
                        $media = $row_media['media'];
                        $created_at = $row_media['created_at'];
                         $title = $row_media['image_title'];
                        $caption = $row_media['image_caption'];
                        $type_ = $row_media['type'];
                        if ($type_ == 'pdf') {
                            $type_ = 'files';
                        } else {
                            $type_ = 'image';
                        }
                        $media_source = 'https://d2c8oti4is0ms3.cloudfront.net/images/health_record_media/' . $media;
                        $health_record_media[] = array(
                            'document_id' => $media_id,
                            'document_link' => $media_source,
                            'document_date' => $created_at,
                             'document_title' => $title,
                            'document_caption' => $caption
                        );
                    }
                } else {
                    $health_record_media = array();
                }
                
                
                   
                        
                $resultpost[] = array(
                    'id' => $health_record_id,
                    'folder_id'=>$folder_id,
                    'patient_name' => $patient_name,
                    'patient_age'=>$patient_age,
                    'patient_city'=>$patient_city,
                    'patient_condition'=>$patient_condition,
                    'date_of_birth' => $date_of_birth,
                    'relationship' => $relationship,
                    'gender' => $gender,
                    'document_count' => sizeof($health_record_media),
                    'document_list' => $health_record_media,
                    'created_at' => $created_at
                );
                   
                }
               } else {
               $resultpost = array();
             }
                
            }
        } else {
            $resultpost = array();
        }
        $finalResult = array();
        foreach($resultpost as $result){
            foreach($result as $k => $v){
                $isArray = is_array($result[$k]);
                
              
                if($v == null && $isArray != 1){
                    $result[$k] = "";
                }
            }
            $finalResult[] = $result;
        }
        return $finalResult;
    }
    
    public function healthrecord_document_delete_v2($media_id){
       
        $this->db->where('id', $media_id)->delete('health_record_media');
        
        return array(
            'status' => 200,
            'message' => 'Document has been deleted.'
        );
    }
    
     public function health_document_add_web_v2($user_id, $folder_id, $patient_id, $document_name, $caption, $document_file, $date_added){
        
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
            
            
            $ext = getExtension($document_file);
            $add_docs = array(
                'folder_id'         => $folder_id,
                'health_record_id'  => $patient_id,
                'media'             => $document_file,
                'type'              => $ext,
                'source'            => $document_file,
                'image_title'       => $document_name,
                'image_caption'     => $caption,
                'img_width'         =>'0',
                'img_height'         =>'0',
                'date'              =>$date_added,
                'created_at'         =>$date,
            );  
            
            $inserted = $this->db->insert('health_record_media', $add_docs);
                
            if($inserted){    
                    return array(
                        'status' => 200,
                        'message' => 'success'
                    );
            }else{
                return array(
                        'status' => 201,
                        'message' => 'Error while inserting'
                    );
            }
                                  
               
            }
            
    public function folder_list_v2($health_record_id) {
        
        $document_list = array();
        
            //echo "SELECT * FROM health_record WHERE user_id='$user_id' and vendor_type='$vendor_type' order by id desc";
        $query = $this->db->query("SELECT * FROM health_record_folder WHERE health_record_id='$health_record_id' order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $folder_id        = $row['id'];
                $folder_name        = $row['folder_name'];
                $date       = $row['date'];
                $user_id = $row['user_id'];
                $created_at = $row['created_at'];
                
                $query2 = $this->db->query("SELECT id,media,date,created_at,image_title,image_caption,type FROM health_record_media WHERE health_record_id='$health_record_id' and folder_id='$folder_id' order by created_at desc");
                // print_r($query2->result_array()); die();
                $count2 = $query2->num_rows();
                if ($count2 > 0) {
                    $health_record_media=array();
                    foreach ($query2->result_array() as $row_media) {
                        
                        $media_id = $row_media['id'];
                        $media = $row_media['media'];
                        $created_at = $row_media['created_at'];
                        $date = $row_media['date'];
                        $type_ = $row_media['type'];
                          $title = $row_media['image_title'];
                          if($title == null){
                              $title = "";
                          }
                        $caption = $row_media['image_caption'];
                        if($caption == null){
                              $caption = "";
                          }
                        if ($type_ == 'pdf') {
                            $type_ = 'files';
                        } else {
                            $type_ = 'image';
                        }
                        $media_source = 'https://d2c8oti4is0ms3.cloudfront.net/images/health_record_media/' . $type_ . '/' . $media;
                        $health_record_media[] = array(
                            'document_id' => $media_id,
                            'document_link' => $media_source,
                            'created_at' => $created_at,
                            'document_date' => $date,
                            'document_title' => $title,
                            'document_caption' => $caption
                        );
                    }
                } else {
                    $health_record_media = array();
                }

                $resultpost[] = array(
                    'id' => $folder_id,
                    'user_id'=>$user_id,
                    'folder_name' => $folder_name,
                    'date'=>$date,
                    'document_count'=>sizeof($health_record_media),
                    'document_list'=>$health_record_media,
                    'created_at'=>$created_at,
                );
            }
        } else {
            $resultpost = array();
        }
        
        return $resultpost;
    }
    
     public function add_folder_v2($user_id,$patient_id,$folder_name,$date) {
        date_default_timezone_set('Asia/Calcutta');
        $created_at = date('Y-m-d');

        $health_record = array(
            'user_id' => $user_id,
            'health_record_id' => $patient_id,
            'folder_name' => $folder_name,
            'date' => $date,
            'created_at' => $created_at
        );
        $this->db->insert('health_record_folder', $health_record);
        $folder_id = $this->db->insert_id();
        return array(
            'status' => 200,
            'message' => 'success',
            'folder_id' => $folder_id
        );
    }
    
    
     public function edit_folder_v2($folder_id,$user_id,$patient_id,$folder_name,$date) {
        date_default_timezone_set('Asia/Calcutta');
        $created_at = date('Y-m-d');

        $health_record = array(
            'user_id' => $user_id,
            'health_record_id' => $patient_id,
            'folder_name' => $folder_name,
            'date' => $date,
            'created_at' => $created_at
        );
        $this->db->where('id',$folder_id);
        $this->db->update('health_record_folder', $health_record);
        
        return array(
            'status' => 200,
            'message' => 'success',
            'folder_id' => $folder_id
        );
    }
    

}
