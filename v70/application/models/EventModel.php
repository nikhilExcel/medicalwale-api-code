<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class EventModel extends CI_Model
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
    
      public function get_story_list($user_id)
     {
        date_default_timezone_set('Asia/Kolkata');
        $data=array();
        $date = date('Y-m-d H:i:s');
        $sql   = "SELECT * from follow_user where user_id='$user_id'";
        $query1 = $this->db->query($sql);
        $count1 = $query1->num_rows();
        if ($count1 > 0) 
            {
                
             foreach ($query1->result_array() as $row1) 
                     {
                         $final_id        = $row1['parent_id'];
                          //echo "SELECT * FROM Doctor_story where doctor_id = '$final_id' and status='0' order by id Desc";
                         $query = $this->db->query("SELECT * FROM Doctor_story where doctor_id = '$final_id' and status='0' order by id Desc");
                        
                         $count = $query->num_rows();
                         $data = array();
                         if($count>0)
                         {
                             
                             foreach($query->result_array() as $row)
                             {
                                
                                 $id = $row['id'];
                                 $doctor_id = $row['doctor_id'];
                                 $type = $row['type'];
                                 $source = $row['source'];
                                 $text = $row['text'];
                                 $highlight = $row['highlight'];
                                 $created_at = $row['created_at'];
                                 $url = $row['url'];
                                 $sour_final="";
                                 $live=0;
                                if(empty($url))
                                {
                                    $url="";
                                    $sour_final='https://s3.amazonaws.com/medicalwale/images/story_images/'.$source;
                                    $live=0;
                                }
                                else
                                {
                                    $sour_final="";
                                    $live=1;
                                }
                                 $doctor_info = $this->db->query("SELECT doctor_name,image FROM `doctor_list` WHERE `user_id` = '$doctor_id'");  
                     
                         $dCount = $doctor_info->num_rows();
                         $d_info = $doctor_info->row();
                         
                         if($dCount>0)
                         {
                              $doctor_name = $d_info->doctor_name;
                              $doctor_image = $d_info->image;
                         }
                         else
                         {
                             $doctor_name = "";
                              $doctor_image = "";
                         }
                                 
                                  if ($doctor_image != '') {
                                    $profile_pic = $doctor_image;
                                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                                } else {
                                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                                }
                                 
                            $hourdiff = round((strtotime($date) - strtotime($created_at)) / 3600, 1);
                                // echo $hourdiff; echo "<br>";
                             if($hourdiff <= 24)
                                 {
                                     
                                    $sql2  = "SELECT * from doctor_story_history where user_id='$user_id' and doctor_id='$doctor_id' and FIND_IN_SET('" . $id . "',story_id)";
                                    $query12 = $this->db->query($sql2);
                                    $count12 = $query12->num_rows();  
                                    if($count12 > 0)
                                       {
                                           $row12=$query12->row_array(); 
                                           $coun_story=$row12['count'];
                                           $is_seen=1;
                                           $id_arr=$row12['id'];
                                       }
                                    else
                                       {
                                          
                                           $coun_story=0;
                                           $is_seen=0;
                                           $id_arr=0;
                                       }
                                     
                               $data[] = array(
                                         'id' => $id,
                                         'doctor_id' => $doctor_id,
                                         'type' => $type,
                                         'source' =>$sour_final ,
                                         'text' => $text,
                                         'created_at' => $created_at,
                                         'ago' => $hourdiff,
                                         'doctor_name' => $doctor_name,
                                         'doctor_image' => $profile_pic,
                                         'highlight' => $highlight,
                                         'live_url'=>$url,
                                         'is_live'=>$live,
                                         'count_story'=>(int)$coun_story,
                                         'is_seen'=>$is_seen,
                                         'track_id'=>(int)$id_arr
                                      );
                                 }
                                 
                                     
                                     
                             }
                         }
                     }
                   
               
            }
            
          $ids_old='63835,64250,64251,64680,29042,66288';
         $ids=explode(",",$ids_old);
         for($i=0;$i < count($ids);$i++)
         {    
        $query = $this->db->query("SELECT * FROM Doctor_story where doctor_id ='$ids[$i]' and status='0' order by id Desc");
        $count = $query->num_rows();
         if($count>0)
            {
                             
                             foreach($query->result_array() as $row)
                             {
                                
                                 $id = $row['id'];
                                 $doctor_id = $row['doctor_id'];
                                 $type = $row['type'];
                                 $source = $row['source'];
                                 $text = $row['text'];
                                 $highlight = $row['highlight'];
                                 $created_at = $row['created_at'];
                                 $url = $row['url'];
                                 $sour_final="";
                                 $live=0;
                                if(empty($url))
                                {
                                    $url="";
                                    $sour_final='https://s3.amazonaws.com/medicalwale/images/story_images/'.$source;
                                    $live=0;
                                }
                                else
                                {
                                    $sour_final="";
                                    $live=1;
                                }
                                 $doctor_info = $this->db->query("SELECT doctor_name,image FROM `doctor_list` WHERE `user_id` = '$doctor_id'");  
                     
                         $dCount = $doctor_info->num_rows();
                         $d_info = $doctor_info->row();
                         
                         if($dCount>0)
                         {
                              $doctor_name = $d_info->doctor_name;
                              $doctor_image = $d_info->image;
                         }
                         else
                         {
                             $doctor_name = "";
                              $doctor_image = "";
                         }
                                 
                                  if ($doctor_image != '') {
                                    $profile_pic = $doctor_image;
                                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                                } else {
                                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                                }
                                 
                            $hourdiff = round((strtotime($date) - strtotime($created_at)) / 3600, 1);
                                // echo $hourdiff; echo "<br>";
                             if($hourdiff <= 24)
                                 {
                                     $sql2  = "SELECT * from doctor_story_history where user_id='$user_id' and doctor_id='$doctor_id' and FIND_IN_SET('" . $id . "',story_id)";
                                    $query12 = $this->db->query($sql2);
                                    $count12 = $query12->num_rows();  
                                    if($count12 > 0)
                                       {
                                           $row12=$query12->row_array(); 
                                           $coun_story=$row12['count'];
                                           $is_seen=1;
                                           $id_arr=$row12['id'];
                                       }
                                    else
                                       {
                                          
                                           $coun_story=0;
                                           $is_seen=0;
                                           $id_arr=0;
                                       }
                               $data[] = array(
                                         'id' => $id,
                                         'doctor_id' => $doctor_id,
                                         'type' => $type,
                                         'source' =>$sour_final ,
                                         'text' => $text,
                                         'created_at' => $created_at,
                                         'ago' => $hourdiff,
                                         'doctor_name' => $doctor_name,
                                         'doctor_image' => $profile_pic,
                                         'highlight' => $highlight,
                                         'live_url'=>$url,
                                         'is_live'=>$live,
                                         'count_story'=>(int)$coun_story,
                                         'is_seen'=>$is_seen,
                                         'track_id'=>(int)$id_arr
                                      );
                                 }
                                 
                                     
                                     
                             }
                         }     
         }
         
         return $data;
       
     }
      public function get_story_list_id($user_id)
     {
         date_default_timezone_set('Asia/Kolkata');
         $data=array();
         $date = date('Y-m-d H:i:s');
     
                         $final_id        = $user_id;
                          //echo "SELECT * FROM Doctor_story where doctor_id = '$final_id' and status='0' order by id Desc";
                         $query = $this->db->query("SELECT * FROM Doctor_story where doctor_id = '$final_id' and status='0' order by id Desc");
                        
                         $count = $query->num_rows();
                         $data = array();
                         if($count>0)
                         {
                             
                             foreach($query->result_array() as $row)
                             {
                                
                                 $id = $row['id'];
                                 $doctor_id = $row['doctor_id'];
                                 $type = $row['type'];
                                 $source = $row['source'];
                                 $text = $row['text'];
                                 $highlight = $row['highlight'];
                                 $created_at = $row['created_at'];
                                 $url = $row['url'];
                                 $sour_final="";
                                 $live=0;
                                if(empty($url))
                                {
                                    $url="";
                                    $sour_final='https://s3.amazonaws.com/medicalwale/images/story_images/'.$source;
                                    $live=0;
                                }
                                else
                                {
                                    $sour_final="";
                                    $live=1;
                                }
                                 $doctor_info = $this->db->query("SELECT doctor_name,image FROM `doctor_list` WHERE `user_id` = '$doctor_id'");  
                     
                         $dCount = $doctor_info->num_rows();
                         $d_info = $doctor_info->row();
                         
                         if($dCount>0)
                         {
                              $doctor_name = $d_info->doctor_name;
                              $doctor_image = $d_info->image;
                         }
                         else
                         {
                             $doctor_name = "";
                              $doctor_image = "";
                         }
                                 
                                  if ($doctor_image != '') {
                                    $profile_pic = $doctor_image;
                                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                                } else {
                                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                                }
                                 
                            $hourdiff = round((strtotime($date) - strtotime($created_at)) / 3600, 1);
                                // echo $hourdiff; echo "<br>";
                             if($hourdiff <= 24)
                                 {
                                      
                               $data[] = array(
                                         'id' => $id,
                                         'doctor_id' => $doctor_id,
                                         'type' => $type,
                                         'source' =>$sour_final ,
                                         'text' => $text,
                                         'created_at' => $created_at,
                                         'ago' => $hourdiff,
                                         'doctor_name' => $doctor_name,
                                         'doctor_image' => $profile_pic,
                                         'highlight' => $highlight,
                                         'live_url'=>$url,
                                         'is_live'=>$live,
                                          'count_story'=>(int)0,
                                         'is_seen'=>0,
                                         'track_id'=>(int)0
                                      );
                                 }
                                 
                                     
                                     
                             }
                         }
                   
            
         
         
         return $data;
       
     }
     
  
    
    

    
    
      public function all_event_list_new($user_id,$status)
    {
            $system_date = date('Y-m-d');
            $result_array = array();
            $where = "";
         if($status == 'upcoming')
         {
           /*  $d = date_parse_from_format("Y-m-d", $find_date);
             $month =  $d["month"];
             $year =  $d["year"];*/
             
              $where =" WHERE end_date >= '$system_date' ";
             
             
         }
         else if($status == 'all')
         {
             /* $d = date_parse_from_format("Y-m-d", $system_date);
              $month =  $d["month"];
              $year =  $d["year"];*/
              
              $where = "";
         }
         else if($status == 'my')
         {
             $where = "WHERE user_id = '$user_id' ";
         }
         date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
         /* $sql   = "SELECT * from follow_user where user_id='$user_id'";
         $query1 = $this->db->query($sql);
          $count1 = $query1->num_rows();
         if ($count1 > 0) 
            {
                
             foreach ($query1->result_array() as $row1) 
                     {
                         $final_id        = $row1['parent_id'];
                        //echo "SELECT * FROM events WHERE user_id ='$final_id' $where  order by id desc";
                        $query = $this->db->query("SELECT * FROM events WHERE user_id ='$final_id' $where  order by id desc");
                      $count = $query->num_rows();
                      if($count>0)
                      {
                          foreach($query->result_array() as $row)
                          {
                                $event_start_date = $row['start_date'];
                                $event_end_date = $row['end_date'];
                                $event_start_time = $row['from_time'];
                                $event_end_time = $row['to_time'];
                                $lat=$row['lat'];
                                $lang=$row['lang'];
                                $event_d = date_parse_from_format("Y-m-d", $event_start_date);
                                $event_month =  $event_d["month"];
                                $event_year =  $event_d["year"];
                                $vendor_name = $row["user_name"];
                            //  if($event_month == $month &&  $event_year == $year)                  {
                                //  echo $event_month;
                                $id = $row['id'];
                              
                              //added for event count 
                               $interested_query = $this->db->query("SELECT id FROM event_attending_list where event_id = '$id' and interest = '1' order by id desc");
                               $interested_count = $interested_query->num_rows();
                               
                               $notinterested_query = $this->db->query("SELECT id FROM event_attending_list where event_id = '$id' and interest = '2' order by id desc");
                               $notinterested_count = $notinterested_query->num_rows();
                               
                                $maybe_query = $this->db->query("SELECT id FROM event_attending_list where event_id = '$id' and interest = '3' order by id desc");
                                $maybe_count = $maybe_query->num_rows();
                               
                                $event_attending = $this->db->query("SELECT interest FROM event_attending_list where event_id = '$id' and user_id = '$user_id' ");
                                $event_attending_count = $event_attending->num_rows();
                                if($event_attending_count>0)
                                {
                                    $event_attending_count_f = $event_attending->row()->interest;
                                }
                                else
                                {
                                    $event_attending_count_f = 0;
                                }
                               
                              $image = $row['image'];
                              if($image != "")
                              {
                                  $image= "https://s3.amazonaws.com/medicalwale/images/Event_images/".$image;
                              }
                              else
                              {
                                  $image ="";
                              }
                              $event_user_id = $row['user_id'];
                              
                            $vendor_status = $this->db->select('name')->from('users')->where('id', $event_user_id)->get()->row();
                            if(!empty($vendor_status)) {
                            $user_name = $vendor_status->name;
                            }
                            else
                            {
                                $user_name = "";
                            }
                              
                              $title = $row['title'];
                              $description = $row['description'];
                              $vendor_type = $row['vendor_id'];
                              $venue = $row['venue'];
                            
                              $event_status = $row['status'];
                              if($user_id == $event_user_id)
                              {
                                  $is_user = 'yes';
                              }
                              else
                              {
                                  $is_user = 'no';
                              }
                              if(empty($vendor_name))
                              {
                                  $vendor_name="";
                              }
                              $result_array[] = array(
                                      'id' => $id,
                                      'image' => $image,
                                      'vendor_name' => $vendor_name,
                                      'user_name' => $user_name,
                                      'event_user_id' => $event_user_id,
                                      'title' => $title,
                                      'description' => $description,
                                      'vendor_type' => $vendor_type,
                                      'venue' => $venue,
                                      'lat' => $lat,
                                      'lng' => $lang,
                                      'event_start_date' => $event_start_date,
                                      'event_end_date' => $event_end_date,
                                      'event_start_time' => $event_start_time,
                                      'event_end_time' => $event_end_time,
                                      'event_status' => $event_status,
                                      'is_user'  => $is_user,
                                      'interested_count' => $interested_count,
                                      'notinterested_count' => $notinterested_count,
                                      'maybe_count'  => $maybe_count,
                                      'your_interest'  => $event_attending_count_f
                                  );
                              
                          }
          }
                      else
                      {
                          $result_array = array();
                      }
                     }
            }
            
          $ids_old='63835,64250,64251,64680';
         $ids=explode(",",$ids_old);
         for($i=0;$i < count($ids);$i++)
         {
         $query = $this->db->query("SELECT * FROM events WHERE user_id ='$ids[$i]' $where  order by id desc");
                      $count = $query->num_rows();
                      if($count>0)
                      {
                          foreach($query->result_array() as $row)
                          {
                                $event_start_date = $row['start_date'];
                                $event_end_date = $row['end_date'];
                                $event_start_time = $row['from_time'];
                                $event_end_time = $row['to_time'];
                                $lat=$row['lat'];
                                $lang=$row['lang'];
                                $event_d = date_parse_from_format("Y-m-d", $event_start_date);
                                $event_month =  $event_d["month"];
                                $event_year =  $event_d["year"];
                                $vendor_name = $event_d["user_name"];
                            //  if($event_month == $month &&  $event_year == $year)                  {
                                //  echo $event_month;
                                $id = $row['id'];
                              
                              //added for event count 
                               $interested_query = $this->db->query("SELECT id FROM event_attending_list where event_id = '$id' and interest = '1' order by id desc");
                               $interested_count = $interested_query->num_rows();
                               
                               $notinterested_query = $this->db->query("SELECT id FROM event_attending_list where event_id = '$id' and interest = '2' order by id desc");
                               $notinterested_count = $notinterested_query->num_rows();
                               
                                $maybe_query = $this->db->query("SELECT id FROM event_attending_list where event_id = '$id' and interest = '3' order by id desc");
                                $maybe_count = $maybe_query->num_rows();
                               
                                $event_attending = $this->db->query("SELECT interest FROM event_attending_list where event_id = '$id' and user_id = '$user_id' ");
                                $event_attending_count = $event_attending->num_rows();
                                if($event_attending_count>0)
                                {
                                    $event_attending_count_f = $event_attending->row()->interest;
                                }
                                else
                                {
                                    $event_attending_count_f = 0;
                                }
                               
                              $image = $row['image'];
                              if($image != "")
                              {
                                  $image= "https://s3.amazonaws.com/medicalwale/images/Event_images/".$image;
                              }
                              else
                              {
                                  $image ="";
                              }
                              $event_user_id = $row['user_id'];
                              
                            $vendor_status = $this->db->select('name')->from('users')->where('id', $event_user_id)->get()->row();
                             if(!empty($vendor_status)) {
                            $user_name = $vendor_status->name;
                            }
                            else
                            {
                                $user_name = "";
                            }
                              
                              $title = $row['title'];
                              $description = $row['description'];
                              $vendor_type = $row['vendor_id'];
                              $venue = $row['venue'];
                            
                              $event_status = $row['status'];
                              if($user_id == $event_user_id)
                              {
                                  $is_user = 'yes';
                              }
                              else
                              {
                                  $is_user = 'no';
                              }
                        if(empty($vendor_name))
                              {
                                  $vendor_name="";
                              }
                              $result_array[] = array(
                                      'id' => $id,
                                      'image' => $image,
                                      'vendor_name' => $vendor_name,
                                      'user_name' => $user_name,
                                      'event_user_id' => $event_user_id,
                                      'title' => $title,
                                      'description' => $description,
                                      'vendor_type' => $vendor_type,
                                      'venue' => $venue,
                                      'lat' => $lat,
                                      'lng' => $lang,
                                      'event_start_date' => $event_start_date,
                                      'event_end_date' => $event_end_date,
                                      'event_start_time' => $event_start_time,
                                      'event_end_time' => $event_end_time,
                                      'event_status' => $event_status,
                                      'is_user'  => $is_user,
                                      'interested_count' => $interested_count,
                                      'notinterested_count' => $notinterested_count,
                                      'maybe_count'  => $maybe_count,
                                      'your_interest'  => $event_attending_count_f
                                  );
                              
                          }
          }
                      
         }*/
         
          $query = $this->db->query("SELECT * FROM events $where  order by id desc");
                      $count = $query->num_rows();
                      if($count>0)
                      {
                          foreach($query->result_array() as $row)
                          {
                                $event_start_date = $row['start_date'];
                                $event_end_date = $row['end_date'];
                                $event_start_time = $row['from_time'];
                                $event_end_time = $row['to_time'];
                                $lat=$row['lat'];
                                $lang=$row['lang'];
                                $event_d = date_parse_from_format("Y-m-d", $event_start_date);
                                $event_month =  $event_d["month"];
                                $event_year =  $event_d["year"];
                                $vendor_name = $row["user_name"];
                            //  if($event_month == $month &&  $event_year == $year)                  {
                                //  echo $event_month;
                                $id = $row['id'];
                              
                              //added for event count 
                               $interested_query = $this->db->query("SELECT id FROM event_attending_list where event_id = '$id' and interest = '1' order by id desc");
                               $interested_count = $interested_query->num_rows();
                               
                               $notinterested_query = $this->db->query("SELECT id FROM event_attending_list where event_id = '$id' and interest = '2' order by id desc");
                               $notinterested_count = $notinterested_query->num_rows();
                               
                                $maybe_query = $this->db->query("SELECT id FROM event_attending_list where event_id = '$id' and interest = '3' order by id desc");
                                $maybe_count = $maybe_query->num_rows();
                               
                                $event_attending = $this->db->query("SELECT interest FROM event_attending_list where event_id = '$id' and user_id = '$user_id' ");
                                $event_attending_count = $event_attending->num_rows();
                                if($event_attending_count>0)
                                {
                                    $event_attending_count_f = $event_attending->row()->interest;
                                }
                                else
                                {
                                    $event_attending_count_f = 0;
                                }
                               
                              $image = $row['image'];
                              if($image != "")
                              {
                                  $image= "https://s3.amazonaws.com/medicalwale/images/Event_images/".$image;
                              }
                              else
                              {
                                  $image ="";
                              }
                              $event_user_id = $row['user_id'];
                              
                            $vendor_status = $this->db->select('name')->from('users')->where('id', $event_user_id)->get()->row();
                            if(!empty($vendor_status)) {
                            $user_name = $vendor_status->name;
                            }
                            else
                            {
                                $user_name = "";
                            }
                              
                              $title = $row['title'];
                              $description = $row['description'];
                              $vendor_type = $row['vendor_id'];
                              $venue = $row['venue'];
                            
                              $event_status = $row['status'];
                              if($user_id == $event_user_id)
                              {
                                  $is_user = 'yes';
                              }
                              else
                              {
                                  $is_user = 'no';
                              }
                              if(empty($vendor_name))
                              {
                                  $vendor_name="";
                              }
                              $result_array[] = array(
                                      'id' => $id,
                                      'image' => $image,
                                      'vendor_name' => $vendor_name,
                                      'user_name' => $user_name,
                                      'event_user_id' => $event_user_id,
                                      'title' => $title,
                                      'description' => $description,
                                      'vendor_type' => $vendor_type,
                                      'venue' => $venue,
                                      'lat' => $lat,
                                      'lng' => $lang,
                                      'event_start_date' => $event_start_date,
                                      'event_end_date' => $event_end_date,
                                      'event_start_time' => $event_start_time,
                                      'event_end_time' => $event_end_time,
                                      'event_status' => $event_status,
                                      'is_user'  => $is_user,
                                      'interested_count' => $interested_count,
                                      'notinterested_count' => $notinterested_count,
                                      'maybe_count'  => $maybe_count,
                                      'your_interest'  => $event_attending_count_f
                                  );
                              
                          }
          }
                      else
                      {
                          $result_array = array();
                      }
            
     
        return $result_array;
          
          
    }
    public function update_event_list($user_id,$event_id,$intrested_status)
    {
         $system_date = date('Y-m-d');
          $query = $this->db->query("SELECT id FROM event_attending_list where user_id = '$user_id' and event_id = '$event_id' order by id desc");
          $count = $query->num_rows();
          if($count>0)
          {
              //$query_data = $query->rows();
                  
                   $this->db->where('event_id', $event_id)->where('user_id', $user_id)->update('event_attending_list', array(
                          'event_id' => $event_id,
                          'interest' => $intrested_status
                  ));
                  
               return array(
                'status' => 200,
                'message' => 'Updated Successfully'
            );
          }
          else
          {
                $event_list = array(
                'user_id' => $user_id,
                'event_id' => $event_id,
                'interest' => $intrested_status,
                'created_at' => $system_date
            );
            $this->db->insert('event_attending_list', $event_list);
            
              return array(
                'status' => 200,
                'message' => 'Inserted Successfully'
            );
          }
          
          
    }
       public function event_tracker($user_id,$doctor_id,$count,$seen,$story_id,$track_id)
     {
         date_default_timezone_set('Asia/Kolkata');
         $date = date('Y-m-d H:i:s');
         if($track_id==0)
         {
            $p_results = $this->db->query("INSERT INTO `doctor_story_history`(`user_id`, `doctor_id`, `count`, `story_id`, `datetime`) 
               VALUES ('$user_id','$doctor_id','$count','$story_id','$date')");
            $insert_id = $this->db->insert_id();
            if($insert_id!=0)
            {
               $data= array(
                'status' => 200,
                'message' => 'success'
                 ); 
            }
            else
            {
                $data= array(
                'status' => 201,
                'message' => 'failed'
                 ); 
            }
         }
         else
         {
            $p_results = $this->db->query("UPDATE `doctor_story_history` SET `count`='$count',`story_id`='$story_id' WHERE `id`='$track_id'");
            if($this->db->affected_rows() > 0)
            {
                $data= array(
                'status' => 200,
                'message' => 'success'
                 ); 
            }
            else
            {
                $data= array(
                'status' => 201,
                'message' => 'failed'
                 ); 
            }
             
         }
         
         
         
         
         return $data;
       
     }
}    
?>
