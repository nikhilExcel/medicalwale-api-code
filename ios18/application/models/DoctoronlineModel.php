<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class DoctoronlineModel extends CI_Model
{
    var $client_service = "frontend-client";
    var $auth_key = "medicalwalerestapi";
    public function check_auth_client()
    {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key       = $this->input->get_request_header('Auth-Key', TRUE);
        if ($client_service == $this->client_service && $auth_key == $this->auth_key) {
            return true;
        } else {
            return json_output(401, array(
                'status' => 401,
                'message' => 'Unauthorized.'
            ));
        }
    }
    public function auth()
    {
        date_default_timezone_set('Asia/Kolkata');
        $users_id = $this->input->get_request_header('User-ID', TRUE);
        $token    = $this->input->get_request_header('Authorizations', TRUE);
        $q        = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id', $users_id)->where('token', $token)->get()->row();
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
    public function encrypt($str)
    {
        $this->key = hash('MD5', '8655328655mdwale', true);
        $this->iv  = hash('MD5', 'mdwale8655328655', true);
        $module    = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($module, $this->key, $this->iv);
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $pad   = $block - (strlen($str) % $block);
        $str .= str_repeat(chr($pad), $pad);
        $encrypted = mcrypt_generic($module, $str);
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);
        return base64_encode($encrypted);
    }
    public function decrypt($str)
    {
        $this->key = hash('MD5', '8655328655mdwale', true);
        $this->iv  = hash('MD5', 'mdwale8655328655', true);
        $module    = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($module, $this->key, $this->iv);
        $str = mdecrypt_generic($module, base64_decode($str));
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);
        $slast = ord(substr($str, -1));
        $str   = substr($str, 0, strlen($str) - $slast);
        return $str;
    }
  
    public function doctor_online_cat($user_id,$mlat,$mlng)
    {
         $radius = '5';
         $ty ="LIMIT 5";
        
        
        // genral Doctor Start Here
         date_default_timezone_set('Asia/Kolkata');
                    $current_date = date('Y-m-d H:i:s');
        
        $sql   =  sprintf("SELECT doctor_list.*  FROM doctor_list INNER JOIN doctor_online ON doctor_list.user_id=doctor_online.doctor_id WHERE FIND_IN_SET('41', doctor_list.category) and  doctor_list.is_active = 1 and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ORDER BY RAND () $ty  ");
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) 
            {
                 
                $doctor_name         = $row['doctor_name'];
                $experience          = $row['experience'];
                $doctor_user_id      = $row['user_id'];
                 $doctor_image         = $row['image'];
            if ($doctor_image != '') {
                $doctor_image = str_replace(' ', '%20', $doctor_image);
                $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
            } else {
                $doctor_image = 'https://medicalwale.com/img/doctor_default.png';
            }
                $category            = explode(",",$row['category']);
                $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  id='41' ");
                $total_category = $query_sp->num_rows();
                $get_sp=$query_sp->row_array();
                $cat_name               = $get_sp['area_expertise'];
               
                if(in_array("41", $category))
                {
                   
                    
                        $genral[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>"41",
                            'category_name'=>$cat_name
                        );
                    
                }
            }
        } else {
            $genral = array();
        }
         // genral Doctor End Here
        
        
        
         // Gynecologist Doctor Start Here
        $Where = ' CONCAT(",", doctor_list.category, ",") REGEXP ",(46|47),"';
        
        $sql   =  sprintf("SELECT doctor_list.* FROM doctor_list INNER JOIN doctor_online ON doctor_list.user_id=doctor_online.doctor_id WHERE $Where and  doctor_list.is_active = 1 and doctor_online.starttime <= '$current_date' AND doctor_online.endtime >= '$current_date' ORDER BY RAND () $ty  ");
        
       
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) 
            {
                 
                $doctor_name         = $row['doctor_name'];
                $experience          = $row['experience'];
                $doctor_user_id      = $row['user_id'];
                 $doctor_image         = $row['image'];
            if ($doctor_image != '') {
                $doctor_image = str_replace(' ', '%20', $doctor_image);
                $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
            } else {
                $doctor_image = 'https://medicalwale.com/img/doctor_default.png';
            } 
                $category       = $row['category'];
                $cat=explode(",",$category);
               if(in_array('46', $cat))
               {
                   
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'46')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $gy[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name
                        );
                   
               }
               else if(in_array('47', $cat))
               {
                    
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'47')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $gy[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name
                        );
                    
               }
              
               
            }
        } else {
            $gy = array();
        }
        
        // Gynecologist Doctor End  Here
        
        // Dermatologist Start Here
        $sql   =  sprintf("SELECT doctor_list.* FROM doctor_list INNER JOIN doctor_online ON doctor_list.user_id=doctor_online.doctor_id WHERE FIND_IN_SET('27', doctor_list.category) and  doctor_list.is_active = 1 and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ORDER BY RAND () $ty  ");
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) 
            {
                 
                $doctor_name         = $row['doctor_name'];
                $experience          = $row['experience'];
                $doctor_user_id      = $row['user_id'];
                 $doctor_image         = $row['image'];
            if ($doctor_image != '') {
                $doctor_image = str_replace(' ', '%20', $doctor_image);
                $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
            } else {
                $doctor_image = 'https://medicalwale.com/img/doctor_default.png';
            }
                    
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  id='27' $ty");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $dermatologist[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>"27",
                            'category_name'=>$cat_name
                        );
                     
            }
        } else {
            $dermatologist = array();
        }
        //Dermatologist End Here
        
        
         //Ayurvedic End Here
         $Where1 = ' CONCAT(",", doctor_list.category, ",") REGEXP ",(9|10|11|12|13|14|15|40),"';
         $doctor_data=array();
        $sql   =  sprintf("SELECT doctor_list.* FROM doctor_list INNER JOIN doctor_online ON doctor_list.user_id=doctor_online.doctor_id  WHERE $Where1 and  doctor_list.is_active = 1 and  doctor_list.is_active = 1 and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ORDER BY RAND () $ty  ");
        
       
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
           
            foreach ($query->result_array() as $row) 
            {
                 
                $doctor_name         = $row['doctor_name'];
                $experience          = $row['experience'];
                $doctor_user_id      = $row['user_id'];
                $doctor_image        = $row['image'];
            if ($doctor_image != '') {
                $doctor_image = str_replace(' ', '%20', $doctor_image);
                $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
            } else {
                $doctor_image = 'https://medicalwale.com/img/doctor_default.png';
            }
             $category       = $row['category'];
                $cat=explode(",",$category);
            
                if(in_array($doctor_user_id,$doctor_data))
                {
                }
                else
                {
                    
               
               
                //print_r($cat);
               if(in_array('9', $cat))
               {
                   
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'9')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $ayur[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name
                        );
                    
               }
               else if(in_array('10', $cat))
               {
                  
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'10')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $ayur[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name
                        );
                    
               }
               
               
               
               else if(in_array('11', $cat))
               {
                  
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'11')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $ayur[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name
                        );
                    
               }
               else if(in_array('12', $cat))
               {
                  
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'12')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $ayur[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name
                        );
                    
               }
               
               else if(in_array('13', $cat))
               {
                  
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'13')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $ayur[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name
                        );
                    
               }
               
                else if(in_array('14', $cat))
               {
                  
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'14')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $ayur[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name
                        );
                    
               }
               
               else if(in_array('15', $cat))
               {
                   
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'15')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $ayur[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name
                        );
                    
               }
               
               
               else if(in_array('40', $cat))
               {
                  
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'40')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $ayur[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name
                        );
                    
               }
                }
                $doctor_data[]=$doctor_user_id;
            }
           
        } else {
            $ayur = array();
        }
         //Ayurvedic End Here
        
        //ENT Start Here
         $sql   =  sprintf("SELECT doctor_list.* FROM doctor_list INNER JOIN doctor_online ON doctor_list.user_id=doctor_online.doctor_id  WHERE FIND_IN_SET('31', doctor_list.category) and  doctor_list.is_active = 1 and  doctor_list.is_active = 1 and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ORDER BY RAND () $ty  ");
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) 
            {
                 
                $doctor_name         = $row['doctor_name'];
                $experience          = $row['experience'];
                $doctor_user_id      = $row['user_id'];
                 $doctor_image         = $row['image'];
            if ($doctor_image != '') {
                $doctor_image = str_replace(' ', '%20', $doctor_image);
                $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
            } else {
                $doctor_image = 'https://medicalwale.com/img/doctor_default.png';
            }
              
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  id='31' $ty");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $ent[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>"31",
                            'category_name'=>$cat_name
                        );
                       
            }
        } else {
            $ent = array();
        }
        //ENT End Here
        
        //Dentist Start Here
         $Where2 = ' CONCAT(",", doctor_list.category, ",") REGEXP ",(23|26|239|240),"';
        $doctor_data_1=array();
        $sql   =  sprintf("SELECT doctor_list.* FROM doctor_list INNER JOIN doctor_online ON doctor_list.user_id=doctor_online.doctor_id   WHERE $Where2 and  doctor_list.is_active = 1 and  doctor_list.is_active = 1 and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ORDER BY RAND () $ty  ");
        
       
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) 
            {
                 
                $doctor_name         = $row['doctor_name'];
                $experience          = $row['experience'];
                $doctor_user_id      = $row['user_id'];
                 $doctor_image         = $row['image'];
            if ($doctor_image != '') {
                $doctor_image = str_replace(' ', '%20', $doctor_image);
                $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
            } else {
                $doctor_image = 'https://medicalwale.com/img/doctor_default.png';
            }
            $category       = $row['category'];
                $cat=explode(",",$category);
            
                if(in_array($doctor_user_id,$doctor_data_1))
                {
                }
                else
                {
                    
               
               
                //print_r($cat);
               if(in_array('23', $cat))
               {
                  
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'23')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $dentist[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name
                        );
                    
               }
               else if(in_array('26', $cat))
               {
                  
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'26')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $dentist[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name
                        );
                    
               }
               
               
               
               else if(in_array('239', $cat))
               {
                
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'239')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $dentist[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name
                        );
                    
               }
               else if(in_array('240', $cat))
               {
                 
                            $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'240')");
                            $total_category = $query_sp->num_rows();
                            $get_sp=$query_sp->row_array();
                            $cat_name               = $get_sp['area_expertise'];
                            $cat_id  = $get_sp['id'];
                            
                          
                              $final_cat_id=$cat_id;
                          
                            $dentist[] = array(
                                'doctor_user_id' => $doctor_user_id,
                                'doctor_name' => $doctor_name,
                                'experience' => $experience,
                                'image'=>$doctor_image,
                                'category_id'=>$final_cat_id,
                                'category_name'=>$cat_name
                            );
                        
               }
             
                }
                $doctor_data_1[]=$doctor_user_id;
             
            
            }
        } else {
            $dentist = array();
        }
         //Dentist End Here
         
         //Cardiologist Start Here
         $Where2 = ' CONCAT(",", doctor_list.category, ",") REGEXP ",(9|19|91),"';
        $doctor_data_2=array();
        $sql   =  sprintf("SELECT doctor_list.* FROM doctor_list INNER JOIN doctor_online ON doctor_list.user_id=doctor_online.doctor_id    WHERE $Where2 and  doctor_list.is_active = 1 and  doctor_list.is_active = 1 and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ORDER BY RAND () $ty  ");
        
       
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) 
            {
                 
                $doctor_name         = $row['doctor_name'];
                $experience          = $row['experience'];
                $doctor_user_id      = $row['user_id'];
                 $doctor_image         = $row['image'];
            if ($doctor_image != '') {
                $doctor_image = str_replace(' ', '%20', $doctor_image);
                $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
            } else {
                $doctor_image = 'https://medicalwale.com/img/doctor_default.png';
            }
            $category       = $row['category'];
                $cat=explode(",",$category);
            
                if(in_array($doctor_user_id,$doctor_data_2))
                {
                }
                else
                {
                    
               
               
                //print_r($cat);
               if(in_array('9', $cat))
               {
                  
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'9')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $cardiologist[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name
                        );
                    
                
               }
               else if(in_array('19', $cat))
               {
                 
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'19')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $cardiologist[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name
                        );
                    
                
               }
               
               
               
               else if(in_array('91', $cat))
               {
                 
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'91')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $cardiologist[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name
                        );
                    
                
               }
              
             
                }
                $doctor_data_2[]=$doctor_user_id;
             
            
            }
        } else {
            $cardiologist = array();
        }
         //Cardiologist End Here
         
         
         
         //Obstetrician Start Here
         $Where2 = ' CONCAT(",", doctor_list.category, ",") REGEXP ",(12|73),"';
        $doctor_data_3=array();
        $sql   =  sprintf("SELECT doctor_list.* FROM doctor_list INNER JOIN doctor_online ON doctor_list.user_id=doctor_online.doctor_id  WHERE $Where2 and  doctor_list.is_active = 1 and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ORDER BY RAND () $ty  ");
        
       
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) 
            {
                 
                $doctor_name         = $row['doctor_name'];
                $experience          = $row['experience'];
                $doctor_user_id      = $row['user_id'];
                 $doctor_image         = $row['image'];
            if ($doctor_image != '') {
                $doctor_image = str_replace(' ', '%20', $doctor_image);
                $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
            } else {
                $doctor_image = 'https://medicalwale.com/img/doctor_default.png';
            }
            $category       = $row['category'];
                $cat=explode(",",$category);
            
                if(in_array($doctor_user_id,$doctor_data_3))
                {
                }
                else
                {
                    
               
               
                //print_r($cat);
               if(in_array('12', $cat))
               {
                  
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'12')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $obstetrician[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name
                        );
                    
                
               }
               else if(in_array('73', $cat))
               {
                  
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'73')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $obstetrician[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name
                        );
                    
               }
             
             
                }
                $doctor_data_3[]=$doctor_user_id;
             
            
            }
        } else {
            $obstetrician = array();
        }
         //Obstetrician End Here
         
         
         
         //Neurologist Start Here
         
          $Where2 = ' CONCAT(",", doctor_list.category, ",") REGEXP ",(67|68|94),"';
        $doctor_data_3=array();
        $sql   =  sprintf("SELECT doctor_list.* FROM doctor_list INNER JOIN doctor_online ON doctor_list.user_id=doctor_online.doctor_id WHERE $Where2 and  doctor_list.is_active = 1 and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ORDER BY RAND () $ty  ");
        
       
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) 
            {
                 
                $doctor_name         = $row['doctor_name'];
                $experience          = $row['experience'];
                $doctor_user_id      = $row['user_id'];
                 $doctor_image         = $row['image'];
            if ($doctor_image != '') {
                $doctor_image = str_replace(' ', '%20', $doctor_image);
                $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
            } else {
                $doctor_image = 'https://medicalwale.com/img/doctor_default.png';
            }
            $category       = $row['category'];
                $cat=explode(",",$category);
            
                if(in_array($doctor_user_id,$doctor_data_2))
                {
                }
                else
                {
                    
               
               
                //print_r($cat);
               if(in_array('67', $cat))
               {
                  
                $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'67')");
                $total_category = $query_sp->num_rows();
                $get_sp=$query_sp->row_array();
                $cat_name               = $get_sp['area_expertise'];
                $cat_id  = $get_sp['id'];
                
              
                  $final_cat_id=$cat_id;
              
                $neurologist[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name
                );
                    
                
               }
               else if(in_array('68', $cat))
               {
                  
                $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'68')");
                $total_category = $query_sp->num_rows();
                $get_sp=$query_sp->row_array();
                $cat_name               = $get_sp['area_expertise'];
                $cat_id  = $get_sp['id'];
                
              
                  $final_cat_id=$cat_id;
              
                $neurologist[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name
                );
                    
                
               }
               
               
               
               else if(in_array('94', $cat))
               {
                 
                $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'94')");
                $total_category = $query_sp->num_rows();
                $get_sp=$query_sp->row_array();
                $cat_name               = $get_sp['area_expertise'];
                $cat_id  = $get_sp['id'];
                
              
                  $final_cat_id=$cat_id;
              
                $neurologist[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name
                );
                    
                
               }
              
             
                }
                $doctor_data_3[]=$doctor_user_id;
             
            
            }
        } else {
            $neurologist = array();
        }
         //Neurologist End Here
         
         
          //Homeopathic Start Here
         $Where2 = ' CONCAT(",", doctor_list.category, ",") REGEXP ",(52|53),"';
        $doctor_data_4=array();
        $sql   =  sprintf("SELECT doctor_list.* FROM doctor_list INNER JOIN doctor_online ON doctor_list.user_id=doctor_online.doctor_id WHERE $Where2 and  doctor_list.is_active = 1 and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ORDER BY RAND () $ty  ");
        
       
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) 
            {
                 
                $doctor_name         = $row['doctor_name'];
                $experience          = $row['experience'];
                $doctor_user_id      = $row['user_id'];
                 $doctor_image         = $row['image'];
            if ($doctor_image != '') {
                $doctor_image = str_replace(' ', '%20', $doctor_image);
                $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
            } else {
                $doctor_image = 'https://medicalwale.com/img/doctor_default.png';
            }
            $category       = $row['category'];
                $cat=explode(",",$category);
            
                if(in_array($doctor_user_id,$doctor_data_4))
                {
                }
                else
                {
                    
               
               
                //print_r($cat);
               if(in_array('52', $cat))
               {
                  
                $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'52')");
                $total_category = $query_sp->num_rows();
                $get_sp=$query_sp->row_array();
                $cat_name               = $get_sp['area_expertise'];
                $cat_id  = $get_sp['id'];
                
              
                  $final_cat_id=$cat_id;
              
                $homeopathic[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name
                );
                    
                
               }
               else if(in_array('53', $cat))
               {
                
                $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'53')");
                $total_category = $query_sp->num_rows();
                $get_sp=$query_sp->row_array();
                $cat_name               = $get_sp['area_expertise'];
                $cat_id  = $get_sp['id'];
                
              
                  $final_cat_id=$cat_id;
              
                $homeopathic[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name
                );
                    
                
               }
             
             
                }
                $doctor_data_4[]=$doctor_user_id;
             
            
            }
        } else {
            $homeopathic = array();
        }
         //Homeopathic End Here
         
        
          if(!empty($genral)) 
         {
           $resultpost[] = $genral;  
         }
         if(!empty($gy)) 
         {
           $resultpost[] = $gy;  
         }
         if(!empty($dermatologist)) 
         {
           $resultpost[] = $dermatologist;  
         }
         if(!empty($ayur)) 
         {
           $resultpost[] = $ayur;  
         }
         if(!empty($ent)) 
         {
           $resultpost[] = $ent;  
         }
         if(!empty($dentist)) 
         {
           $resultpost[] = $dentist;  
         }
         if(!empty($cardiologist)) 
         {
           $resultpost[] = $cardiologist;  
         }
        if(!empty($obstetrician)) 
         {
           $resultpost[] = $obstetrician;  
         }
         if(!empty($neurologist)) 
         {
           $resultpost[] = $neurologist;  
         }
         if(!empty($homeopathic)) 
         {
           $resultpost[] = $homeopathic;  
         }
        
         if(empty($resultpost))
         {
             $resultpost=array();
         }
        return $resultpost;
    }
    
     public function doctor_online_detail($user_id,$doctor_user_id)
    {
        // genral Doctor Start Here
          date_default_timezone_set('Asia/Kolkata');
                    $current_date = date('Y-m-d H:i:s');
         $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
        if ($countdi > 0) {
            $row=$querydi->row_array();
            
                $id           = $row['id']; 
                $chat         = $row['chat'];
                $video        = $row['video'];
                 $chat_fee         = $row['chat_fee'];
                $video_fee        = $row['video_fee'];
                 
               if($chat!=0)
               {
                $resultpost[] = array(
                    'id' => $id,
                    'chat' => $chat,
                    'chat_fee' => $chat_fee,
                    'image'=>""
                   
                );
               }
               if($video!=0)
               {
                   $resultpost[] = array(
                    'id' => $id,
                    'chat' => $video,
                    'chat_fee' => $video_fee,
                    'image'=>""
                   
                );
               }
            
        } else {
            $resultpost = array();
        }
         // genral Doctor End Here
      
         
         
        return $resultpost;
    }
    
}