<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sponsored_adsModel extends CI_Model {

     var $client_service = "frontend-client";
    var $auth_key = "medicalwalerestapi";

    //validate auth key and client
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
                $expired_at = '2020-11-12 08:57:58';
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
        
    // 	get_sponsored_ads
    public function sponsored_ads_list($user_id){
        $data = array();
        $today = date('Y-m-d');
        $main_cat_name ="";
        $query = $this->db->query("SELECT * FROM userprofile_question_answer WHERE  user_id='$user_id'");
        $user_data = $query->result();
        if(!empty($user_data)) {
            foreach($user_data as $ua)
            {
                $id=$ua->question_id;
                $ans = $ua->answer;
                
                $query1 = $this->db->query("SELECT question_type,question FROM userprofile_question WHERE  id='$id'");
                $user_data1 = $query1->row()->question_type;
                $ans_name = $query1->row()->question;
               if($user_data1!=0)
               {
                 $query2 = $this->db->query("SELECT question FROM userprofile_question WHERE  id='$user_data1'");
                 $user_data2[] = array('question'=>$query2->row()->question,
                                       'answer' => $ans_name) ;  
               }
               else
               {
                   $query2 = $this->db->query("SELECT question FROM userprofile_question WHERE  id='$user_data1'");
                   $user_data2[] = array('question'=>$query2->row()->question,
                                       'answer' => $ans_name) ; 
               }
             
            }
        }
        $addiction ="";
        $medical_condition = "";
        $Allergies = "";
           foreach($user_data2 as $datas)
           {
               if($datas['question'] == "Addiction")
               {
                   $addiction .= $datas['answer'].",";
               }
               else if($datas['question'] == "Medical Condition")
               {
                   $medical_condition .= $datas['answer'].",";
               }
               else if($datas['question'] == "Allergies")
               {
                   $Allergies .= $datas['answer'].",";
               }
           }
           
        $query_u = $this->db->query("SELECT dob,gender,diet_fitness,age,blood_group FROM users WHERE  id='$user_id'");
        $data_u = $query_u->row(); 
        $gender = $data_u->gender;
        if($gender == "")
        {
            $fgender = 0;
        }
        else if($gender == "Male")
        {
            $fgender = 1;
        }
        else if($gender == "Female")
        {
            $fgender = 2;
        }
        $diet_fitness = $data_u->diet_fitness;
        $age = $data_u->age;
        if($age =="" && $data_u->dob !="")
        {
            $age = date_diff(date_create($data_u->dob), date_create('today'))->y;
        }
        
        $blood_group = $data_u->blood_group;
       
        $res = $this->db->query("SELECT sa.*, vt.vendor_name FROM sponsored_advertisements as sa LEFT JOIN vendor_type as vt ON (sa.vendor_type = vt.id) WHERE sa.status = 1 AND sa.expiry >= '$today' AND (sa.gender='$fgender' OR sa.gender='0' OR (sa.age_from >= '$age' AND sa.age_to <= '$age') OR sa.blood_group = '$blood_group' OR sa.addiction LIKE '%$addiction%' OR sa.medical_condition LIKE '%$medical_condition%' OR sa.allergies LIKE '%$Allergies%') ORDER BY sa.id DESC")->result_array();
            if(empty($res)) {
               
                $res = $this->db->query("SELECT sa.*, vt.vendor_name FROM sponsored_advertisements as sa LEFT JOIN vendor_type as vt ON (sa.vendor_type = vt.id) WHERE sa.status = 1 AND sa.expiry >= '$today' ORDER BY sa.id DESC")->result_array();
            }
            foreach($res as $r){
                $row['id'] = $r['id'];
                $row['ad_main_cat'] = $r['ad_main_cat'];
                $row['main_cat_id'] = $r['main_cat_id'];
                $main_cat_id = $r['main_cat_id'];
                if($r['ad_main_cat'] == "1")            // brand
                {
                    $query_info = $this->db->query("SELECT v_company_name FROM vendor_details_hm WHERE  v_id='$main_cat_id'");
                    $data_info= $query_info->row(); 
                    $main_cat_name = $data_info->v_company_name;
                }
                else if($r['ad_main_cat'] == "2")            // brand
                {
                    $query_info = $this->db->query("SELECT pd_name FROM product_details_hm WHERE  pd_id='$main_cat_id'");
                    $data_info= $query_info->row(); 
                    $main_cat_name = $data_info->pd_name;
                }
                $row['main_cat_name'] = $main_cat_name;
                $row['ad_image'] = "https://s3.amazonaws.com/medicalwale/images/Sponsored_files/".$r['ad_image'];
                $row['vendor_type'] = $r['vendor_type'];
                $row['vendor_name'] = $r['vendor_name'];
              
                $data[] = $row; 
            }
       
        return $data;
    }
    public function add_sponsored_ads($user_id,$add_for,$add_for_id,$listing_type,$expiry_date,$gender,$age_from,$age_to,$source_file,$blood_group,$diet_fitness,$addiction,$med_condition,$allergies) {
		date_default_timezone_set('Asia/Kolkata');
        $created_date = date('Y-m-d H:i:s');
        $img = array();
        $img1 = "";
        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP", "PDF", "mp4","vdo");
        $actual_image_name = "";
            include('../s3_config.php'); 
         
            if($source_file !="") {
             
                    $img_name = $_FILES['source']['name'];
                    $img_size = $_FILES['source']['size'];
                    $img_tmp = $_FILES['source']['tmp_name'];
                    $ext = getExtension($img_name);
                    
                    if (strlen($img_name) > 0) {
                            if (in_array($ext, $img_format)) {
                                $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                $actual_image_path = 'images/Sponsored_files/' . $actual_image_name;
                                $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                            }
                    }
                
            }
                                 $data = array(
                                        'user_id' => $user_id,
                                        'ad_main_cat' => $add_for,
                                        'main_cat_id' => $add_for_id,
                                        'ad_image' => $actual_image_name,
                                        'vendor_type' => $listing_type,
                            			'expiry' => $expiry_date,
                            			'gender' => $gender,
                            			'age_from' => $age_from,
                            			'age_to' => $age_to,
                            			'blood_group' => $blood_group,
                            			'diet_fitness' => $diet_fitness,
                            			'addiction' => $addiction,
                            			'medical_condition' =>$medical_condition,
                            			'allergies' => $allergies,
                            			'created_at' => $created_date,
                            			'created_by' => $user_id 
                                    );

                                    $event_insert = $this->db->insert('sponsored_advertisements', $data);   
                                    if(!empty($event_insert))
                                    {
                                        return array(
                                            'status' => 200,
                                            'message' => 'success'
                                            
                                        );
                                    }else
                                    {
                                         return array(
                                            'status' => 201,
                                            'message' => 'failed'
                                            
                                        );
                                    }
        
    }
}