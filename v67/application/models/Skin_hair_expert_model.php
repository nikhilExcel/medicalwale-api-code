<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Skin_hair_expert_model extends CI_Model {

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

    public function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('j M Y h:i A');
        if (!defined("GOOGLE_GCM_URL"))
            define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
        $fields = array(
            'to' => $reg_id,
            'priority' => "high",
            $agent === 'android' ? 'data' : 'notification' => array(
                "title" => $title,
                "message" => $msg,
                "notification_image" => $img_url,
                "tag" => $tag,
                'sound' => 'default',
                "notification_type" => 'missbelly_notifications',
                "notification_date" => $date,
                "post_id" => $post_id
            )
        );
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
            $agent === 'android' ? 'Authorization: key=AIzaSyDTr3BzDHKj_6FnUiZXT__VCmamH-_gupM' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);

        if ($result === FALSE) {
            die('Problem occurred: ' . curl_error($ch));
        }

        curl_close($ch);
        //echo $result;
    }
    
    //added for notification switch not to send for perticular user
    public function get_stop_notification_for_user($user_id)
    {
         $query = $this->db->query("SELECT * FROM `notification_switch_control` WHERE user_id = '$user_id' and category = 'Miss belly'");
        $count = $query->num_rows();
        if($count > 0 )
        {
             $query1 = $this->db->query("SELECT * FROM `notification_switch_control` WHERE user_id = '$user_id' and category = 'Miss belly' and status = 'on'");
             $count1 = $query1->num_rows();
             if($count1>0)
             {
                 return TRUE;
             }
             else
             {
            return FALSE;
             }
        }
        else
        {
            return TRUE;
        }
        
    }

   public function add_comment($data,$comment1,$user_id){
       
		$this->db->insert("skin_hair_answer", $data);

		if($this->db->affected_rows() > 0)
		{
		    $data=array("user_id"=>$user_id,
                        "pstid"=>$data['post_id'],
                        "comment1"=>$comment1);
		    return $data; // to the controller
		}
		else{
			return array();
		}
	}
    public function miss_belly_add_reply($user_id, $doctor_id, $post_id, $type, $answer) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $sex_education_question = array(
            'type' => $type,
            'doctor_id' => $doctor_id,
            'post_id' => $post_id,
            'answer' => $answer,
            'date' => $created_at
        );
        $this->db->insert('skin_hair_answer', $sex_education_question);
        $answer_id = $this->db->insert_id();
        return array(
            'answer_id' => $answer_id,
            'status' => 200,
            'message' => 'success'
        );
    }
     
    public function skin_hair_add_question($type, $user_id, $for_user_id, $user_name, $user_image, $question, $post_location, $is_anonymous) 
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        
        if ($is_anonymous > 0) {
        $is_anonymous = $is_anonymous;
        } else {
            $is_anonymous = '0';
        }
        $healthwall_category = 0;
        $querys = $this->db->query("SELECT healthwall_category FROM `askexpert_details` WHERE type='$type'");
        if ($querys->num_rows() > 0) {
            $healthwall_category = $querys->row()->healthwall_category;
            
        }
        $posts = array(
            'is_anonymous' => $is_anonymous,
            'user_id' => $user_id,
            'healthwall_category' => $healthwall_category,
            'type' => 'question',
            'post_location' => $post_location,
            'description' => $question,
            'category' => '6',
            'question_type' => $type,
            'created_at' => $created_at
            
        );
        $this->db->insert('posts', $posts);
        $post_id = $this->db->insert_id();
        
        $sex_education_question = array(
            'type' => $type,
            'user_id' => $user_id,
            'for_user_id' => $for_user_id,
            'post_id' => $post_id,
            'user_name' => $user_name,
            'user_image' => $user_image,
            'question' => $question,
            'post_location' => $post_location,
            'date' => $created_at
        );
        $this->db->insert('skin_hair_expert_question', $sex_education_question);
        
       /* $field_de = array(
                        'user_id' => $user_id,
                        'age' => $age,
                        'height' => $height,
                        'weight' => $weight,
                        'diet_preference' => $diet_preference,
                        'skin_type' => $skin_type,
                        'skin_color' => $skin_color,
                        'skin_concern' => $skin_concern,
                        );*/
                        
         $query = $this->db->query("SELECT * FROM `expert_fields` WHERE type='$type' AND status='1'");
        $params = json_decode(file_get_contents('php://input'), TRUE);
        if ($query->num_rows() > 0) {
            
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $type = $row['type'];
                $variables = $row['variables'];
                
                
                $resultpost1 = array(
                    'type' => $type,
                    'field_id' => $id,
                    'post_id' => $post_id,
                    'user_id' => $user_id,
                    'variables' => $variables,
                    'details' => $params["$variables"]
                );
                $this->db->insert('expert_fields_details', $resultpost1);
            }
        }
        
         $field_de = array(
                        'user_id' => $user_id,
                        'post_id' => $post_id,
                        'seen' => '1',
                        'description' => ' Asked Question.',
                        'type' => $type,
                        'comment' => 'Question',
                        'created_at' => date('Y-m-d H:i:s'),
                        );
        $this->db->insert('expert_notifications', $field_de);
        
        
         //define("POST_URL", "http://sandboxapi.medicalwale.com/v52/healthwall/post_comment");
                    $comment = 'Thank you for connecting with us, Experts will be answering your query within 48 hours.';
                    $fields = array(
                        'type' => $type,
                        'doctor_id' => $user_id,
                        'post_id' => $post_id,
                        'answer' => $comment,
                        'date' => date('Y-m-d H:i:s')
                    );
                  $this->db->insert('skin_hair_answer', $fields);  
                    /*$headers = array(
                        'Client-Service:frontend-client',
                        'Auth-Key:medicalwalerestapi',
                        'Content-Type:application/json',
                        'User-ID:1',
                        'Authorizations:25iwFyq/LSO1U'
                    );
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, POST_URL);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                    $result = curl_exec($ch);
                    curl_close($ch);
        */
        
        
        
        /*$post_data    = array(
                            'userId' => $user_id,
                            'postId' => $post_id,
                            'text' => $this->decrypt($question)
                        ); 
        $new_post_data=json_encode($post_data);  
             
            $url='http://52.66.208.83:8003/doctor/lelo/forum/auto/answer/';
            $ch = curl_init();
          
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $new_post_data);
            curl_setopt($ch, CURLOPT_FAILONERROR, true); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'auth-key: medicalwalerestapi',
            'authorizations: 25iwFyq/LSO1U',
             'cache-control: no-cache',
              'client-service: frontend-client',
               'content-type: application/json',
               'postman-token: c111bd37-b1f3-e223-27ad-666e10c38f12',
               'user-id: 1',
        ));
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
            $result = curl_exec($ch);
             if ($result === FALSE) {
              
            }
             curl_close($ch);*/
   
   
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
     public function skin_hair_edit_question($type, $user_id,$post_id, $for_user_id, $user_name, $user_image, $question, $post_location, $is_anonymous) 
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        
        if ($is_anonymous > 0) {
        $is_anonymous = $is_anonymous;
        } else {
            $is_anonymous = '0';
        }
        $healthwall_category = 0;
        $querys = $this->db->query("SELECT healthwall_category FROM `askexpert_details` WHERE type='$type'");
        if ($querys->num_rows() > 0) {
            $healthwall_category = $querys->row()->healthwall_category;
            
        }
        $posts = array(
            'is_anonymous' => $is_anonymous,
            //'user_id' => $user_id,
            'healthwall_category' => $healthwall_category,
            'type' => 'question',
            'post_location' => $post_location,
            'description' => $question,
            'category' => '6',
            'created_at' => $created_at
            
        );
        $this->db->where('id',$post_id);
        $this->db->where('user_id',$user_id);
        $this->db->update('posts', $posts);
        //$this->db->insert('posts', $posts);
        //$post_id = $this->db->insert_id();
        
        $sex_education_question = array(
            'type' => $type,
            //'user_id' => $user_id,
            'for_user_id' => $for_user_id,
            //'post_id' => $post_id,
            'user_name' => $user_name,
            'user_image' => $user_image,
            'question' => $question,
            'post_location' => $post_location,
            'date' => $created_at
        );
         $this->db->where('post_id',$post_id);
        $this->db->where('user_id',$user_id);
        $this->db->update('skin_hair_expert_question', $sex_education_question);
     
                        
         $query = $this->db->query("SELECT * FROM `expert_fields` WHERE type='$type' AND status='1'");
        $params = json_decode(file_get_contents('php://input'), TRUE);
        if ($query->num_rows() > 0) {
            
                        $this->db->where('post_id',$post_id);
                     $this->db->where('user_id',$user_id);
                    $this->db->delete('expert_fields_details');
                    
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $type = $row['type'];
                $variables = $row['variables'];
                
                
                $resultpost1 = array(
                    'type' => $type,
                    'field_id' => $id,
                    'post_id' => $post_id,
                    'user_id' => $user_id,
                    'variables' => $variables,
                    'details' => $params["$variables"]
                );
                 
        
                $this->db->insert('expert_fields_details', $resultpost1);
            }
        }
        
         $field_de = array(
                       // 'user_id' => $user_id,
                       // 'post_id' => $post_id,
                        'seen' => '1',
                        'description' => ' Asked Question.',
                        'type' => $type,
                        'comment' => 'Question',
                        'created_at' => date('Y-m-d H:i:s'),
                        );
                         $this->db->where('post_id',$post_id);
                        $this->db->where('user_id',$user_id);
        $this->db->update('expert_notifications', $field_de);
      
   
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    public function skin_hair_question_list($user_id, $type, $page) {

        function get_time_difference_php($created_time) {
            date_default_timezone_set('Asia/Calcutta');
            $str = strtotime($created_time);
            $today = strtotime(date('Y-m-d H:i:s'));

            $time_differnce = $today - $str;
            $years = 60 * 60 * 24 * 365;
            $months = 60 * 60 * 24 * 30;
            $days = 60 * 60 * 24;
            $hours = 60 * 60;
            $minutes = 60;
            if (intval($time_differnce / $years) > 1) {
                return intval($time_differnce / $years) . " yrs ago";
            } else if (intval($time_differnce / $years) > 0) {
                return intval($time_differnce / $years) . " yr ago";
            } else if (intval($time_differnce / $months) > 1) {
                return intval($time_differnce / $months) . " months ago";
            } else if (intval(($time_differnce / $months)) > 0) {
                return intval(($time_differnce / $months)) . " month ago";
            } else if (intval(($time_differnce / $days)) > 1) {
                return intval(($time_differnce / $days)) . " days ago";
            } else if (intval(($time_differnce / $days)) > 0) {
                return intval(($time_differnce / $days)) . " day ago";
            } else if (intval(($time_differnce / $hours)) > 1) {
                return intval(($time_differnce / $hours)) . " hrs ago";
            } else if (intval(($time_differnce / $hours)) > 0) {
                return intval(($time_differnce / $hours)) . " hr ago";
            } else if (intval(($time_differnce / $minutes)) > 1) {
                return intval(($time_differnce / $minutes)) . " min ago";
            } else if (intval(($time_differnce / $minutes)) > 0) {
                return intval(($time_differnce / $minutes)) . " min ago";
            } else if (intval(($time_differnce)) > 1) {
                return intval(($time_differnce)) . " sec ago";
            } else {
                return "few seconds ago";
            }
        }

        $limit = 10;
        $start = 0;
        if ($page > 1) {
            if (!is_numeric($page)) {
                $page = 1;
            }
            $start = ($page - 1) * $limit;
        }
        

//echo "SELECT skin_hair_expert_question.id,skin_hair_expert_question.age,skin_hair_expert_question.weight,skin_hair_expert_question.diet_preference,skin_hair_expert_question.height,skin_hair_expert_question.user_image,skin_hair_expert_question.user_name,skin_hair_expert_question.user_id,skin_hair_expert_question.question,skin_hair_expert_question.date,IFNULL(skin_hair_expert_question.post_location,'') AS post_location,user_character.image AS c_image  FROM  `skin_hair_expert_question` INNER JOIN `user_character` ON skin_hair_expert_question.user_image=user_character.id  WHERE skin_hair_expert_question.id NOT IN (SELECT post_id FROM miss_belly_hide WHERE post_id=skin_hair_expert_question.id AND user_id='$user_id')  order by skin_hair_expert_question.id DESC limit $start, $limit";
        $query = $this->db->query("SELECT skin_hair_expert_question.post_id,skin_hair_expert_question.id,skin_hair_expert_question.user_image,skin_hair_expert_question.user_name,skin_hair_expert_question.user_id,skin_hair_expert_question.question,skin_hair_expert_question.date,IFNULL(skin_hair_expert_question.post_location,'') AS post_location,user_character.image AS c_image  FROM  `skin_hair_expert_question` INNER JOIN `user_character` ON skin_hair_expert_question.user_image=user_character.id  WHERE skin_hair_expert_question.id NOT IN (SELECT post_id FROM miss_belly_hide WHERE post_id=skin_hair_expert_question.id AND user_id='$user_id')  order by skin_hair_expert_question.id DESC limit $start, $limit");

       // $count_query = $this->db->query("SELECT skin_hair_expert_question.id,skin_hair_expert_question.age,skin_hair_expert_question.weight,skin_hair_expert_question.height,skin_hair_expert_question.user_image,skin_hair_expert_question.user_name,skin_hair_expert_question.user_id,skin_hair_expert_question.question,skin_hair_expert_question.date,user_character.image AS c_image  FROM  `skin_hair_expert_question` INNER JOIN `user_character` ON skin_hair_expert_question.user_image=user_character.id  WHERE skin_hair_expert_question.id NOT IN (SELECT post_id FROM miss_belly_hide WHERE post_id=skin_hair_expert_question.id AND user_id='$user_id')  order by skin_hair_expert_question.id DESC");
        $count_post = $query->num_rows();


        // print_r($query);
        $resultpost = array();

        if ($count_post > 0) {
            foreach ($query->result_array() as $row) {
                
                $id = $row['id'];
                $post_id = $row['post_id'];
                $images = $row['c_image'];
                $user_name = $row['user_name'];
                $post_user_id = $row['user_id'];
                $question = $row['question'];
                $post_location = $row['post_location'];
                
                $weight = "";
                $height = "";
                $diet_preference = "";
                $age = "";
                $skin_color = "";
                $skin_type = "";
                $skin_concern = "";
                $skin_concern_other = "";
                $resultpost1 = array();
               /* $resultpost1 = array(
                    'diet_preference' => $diet_preference,
                    'age' => $age,
                    'height' => $height,
                    'weight' => $weight,
                    'skin_type' => $skin_type,
                    'skin_color' => $skin_color,
                    'skin_concern' => $skin_concern,
                    'skin_concern_other' => $skin_concern_other,
                   );*/
                $this->db->select('*');
                $this->db->from('expert_fields');
                $this->db->where('type',$type);
                $fields = $this->db->get()->result_array();
                if(!empty($fields))
                {
                    foreach($fields as $fie) {
                        $variables = $fie['variables'];
                        $this->db->select('*');
                        $this->db->from('expert_fields_details');
                        $this->db->where('type',$type);
                        $this->db->where('status','1');
                        $this->db->where('post_id',$post_id);
                        $this->db->where('variables',$variables);
                       
                        $vars = $this->db->get()->row();
                        $equal = $vars->details;
                       
                        $resultpost1[] = array("$variables" => $equal);
                    }
                }
                if($question!=''){
                $question = preg_replace('~[\r\n]+~', '', $question);
             /*   if ($id > '619') {
                    $decrypt = $this->decrypt($question);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $question) {
                        $question = $decrypt;
                    }
                } else {*/
                    //if (base64_encode(base64_decode($question)) === $question) {
                        $question = $this->decrypt($question);
                    //}
               // }
                }
                $date = $row['date'];
                $date = get_time_difference_php($date);
                if ($images != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $images;
                } else {
                    $image = '';
                }

                $is_notify_query = $this->db->query("SELECT id FROM `miss_belly_is_notify` where post_id='$id' AND user_id='$user_id'");
                $is_notify = $is_notify_query->num_rows();
                $is_follow = '0';

                $answer_lists = array();
                $answer_query = $this->db->query("SELECT id,type,answer,post_id FROM `skin_hair_answer` WHERE `post_id`='$post_id'");
                $count_answers = $answer_query->num_rows();
                if ($count_answers > 0) {
                    foreach ($answer_query->result_array() as $rows) {
                        $answer_id = $rows['id'];
                        $answer = $rows['answer'];
                        $type = $rows['type'];
                        $answer = preg_replace('~[\r\n]+~', '', $answer);
                      /*  if ($answer_id > '526') {
                            $decrypt = $this->decrypt($answer);
                            $encrypt = $this->encrypt($decrypt);
                            if ($encrypt == $answer) {
                                $answer = $decrypt;
                            }
                        } else {*/
                           // if (base64_encode(base64_decode($answer)) === $answer) {
                                $answer = $this->decrypt($answer);
                           // }
                       // }
                        $answer_lists[] = array(
                            'answer_id' => $answer_id,
                            'type' => $type,
                            'answer' => $answer
                        );
                    }
                } else {
                    $answer_lists = array();
                }


                $share_url = "https://medicalwale.com/share/missbelly/" . $id;
                $answer_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/medicalwale_mobile_app_icons/fitness/miss_belly.png';
                $count_query = $this->db->query("SELECT id FROM `miss_belly_likes` where post_id='$id'");
                $like_count = $count_query->num_rows();

                $like_count_query = $this->db->query("SELECT id FROM `miss_belly_likes` where user_id='$user_id' and post_id='$id'");
                $like_yes_no = $like_count_query->num_rows();

                $is_post_save_query = $this->db->query("SELECT id from post_save WHERE post_id='$post_id' AND user_id='$user_id' AND post_type='missbelly'");
                $is_post_save = $is_post_save_query->num_rows();

                $is_reported_query = $this->db->query("SELECT id from post_report WHERE post_id='$post_id' AND reporter_id='$user_id' AND post_type='missbelly'");
                $is_reported = $is_reported_query->num_rows();

                $resultpost[] = array(
                    'id' => $id,
                    'post_id' =>$post_id,
                    'post_user_id' => $post_user_id,
                    'post_location' => $post_location,
                    'user_name' => $user_name,
                    'question' => $question,
                    /*'diet_preference' => $diet_preference,*/
                    'answer_list' => $answer_lists,
                    'is_notify' => $is_notify,
                    
                   /* 'age' => $age,
                    'height' => $height,
                    'weight' => $weight,
                    'skin_type' => $skin_type,
                    'skin_color' => $skin_color,
                    'skin_concern' => $skin_concern,
                    'skin_concern_other' => $skin_concern_other,*/
                    'image' => $image,
                    'answer_image' => $answer_image,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'is_follow' => $is_follow,
                    'share_url' => $share_url,
                    'is_reported' => $is_reported,
                    'is_post_save' => $is_post_save,
                    'date' => $date,
                    'other_fields' => $resultpost1
                    );
            }
        } else {
            $resultpost = array();
        }

        $resultpost = array(
            "status" => 200,
            "message" => "success",
            "count" => $count_post,
            "data" => $resultpost
        );
        return $resultpost;
    }
    public function skin_hair_character($type) {
        $review_list_count = $this->db->select('id')->from('user_character')->where('type', $type)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT id,image FROM `user_character` WHERE type='$type' order by id desc");
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $image = $row['image'];
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $image;
                $resultpost[] = array(
                    'id' => $id,
                    'image' => $image
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    public function skin_concern_list($user_id)
    {
        $query = $this->db->query("SELECT * FROM `skin_concern`");
        
        if ($query->num_rows() > 0) {
            
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $name = $row['name'];
             
                $resultpost[] = array(
                    'id' => $id,
                    'name' => $name,
                
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
   
    public function asktheexpert_list($user_id)
    {
        $query = $this->db->query("SELECT * FROM `askexpert_details`");
        
        if ($query->num_rows() > 0) {
            
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $name = $row['name'];
                $title = $row['title'];
                $image = $row['image'];
                $type = $row['type'];
             
                $resultpost[] = array(
                    'id' => $id,
                    'name' => $name,
                    'title' => $title,
                    'image' => $image,
                    'type' => $type
                
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    public function skin_hair_fields($type) {
        $resultpost_new = array();
        $query = $this->db->query("SELECT * FROM `expert_fields` WHERE type='$type' AND status='1'");
        
        if ($query->num_rows() > 0) {
            
            foreach ($query->result_array() as $row) {
                $resultpost_new = array();
                $id = $row['id'];
                $type = $row['type'];
                $qfields = $row['qfields'];
                $variables = $row['variables'];
                $placeholder = $row['placeholder'];
                $input_type = $row['input_type'];
                $information = $row['information'];
                $validation = $row['validation'];
                
                $query1 = $this->db->query("SELECT * FROM `expert_fields_dropdown` WHERE type='$type' AND variable='$variables' AND status='1'");
        
                if ($query1->num_rows() > 0) {
                    
                    foreach ($query1->result_array() as $row1) {
                        
                         $id1 = $row1['id'];
                        $type1 = $row1['type'];
                        $qfields1 = $row1['qfields'];
                        $variables1 = $row1['variable'];
                        
                        $sub_variable = $row1['sub_variable'];
                
                        $resultpost_new[] = array(
                        'id' => $id1,
                        'type' => $type1,
                        'qfields' => $qfields1,
                        'variables' => $variables1,
                        'sub_variable' => $sub_variable
                        
                    );
                    }
                }
                $resultpost[] = array(
                    'id' => $id,
                    'type' => $type,
                    'qfields' => $qfields,
                    'variables' => $variables,
                    'placeholder'=>$placeholder,
                    'input_type' => $input_type,
                    'information' => $information,
                    'validation' => $validation,
                    'dropdown_fields' =>   $resultpost_new              
                    
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    public function skin_hair_question_details($user_id, $post_id) {

        function get_time_difference_php($created_time) {
            date_default_timezone_set('Asia/Calcutta');
            $str = strtotime($created_time);
            $today = strtotime(date('Y-m-d H:i:s'));

            $time_differnce = $today - $str;
            $years = 60 * 60 * 24 * 365;
            $months = 60 * 60 * 24 * 30;
            $days = 60 * 60 * 24;
            $hours = 60 * 60;
            $minutes = 60;
            if (intval($time_differnce / $years) > 1) {
                return intval($time_differnce / $years) . " yrs ago";
            } else if (intval($time_differnce / $years) > 0) {
                return intval($time_differnce / $years) . " yr ago";
            } else if (intval($time_differnce / $months) > 1) {
                return intval($time_differnce / $months) . " months ago";
            } else if (intval(($time_differnce / $months)) > 0) {
                return intval(($time_differnce / $months)) . " month ago";
            } else if (intval(($time_differnce / $days)) > 1) {
                return intval(($time_differnce / $days)) . " days ago";
            } else if (intval(($time_differnce / $days)) > 0) {
                return intval(($time_differnce / $days)) . " day ago";
            } else if (intval(($time_differnce / $hours)) > 1) {
                return intval(($time_differnce / $hours)) . " hrs ago";
            } else if (intval(($time_differnce / $hours)) > 0) {
                return intval(($time_differnce / $hours)) . " hr ago";
            } else if (intval(($time_differnce / $minutes)) > 1) {
                return intval(($time_differnce / $minutes)) . " min ago";
            } else if (intval(($time_differnce / $minutes)) > 0) {
                return intval(($time_differnce / $minutes)) . " min ago";
            } else if (intval(($time_differnce)) > 1) {
                return intval(($time_differnce)) . " sec ago";
            } else {
                return "few seconds ago";
            }
        }

      

//echo "SELECT skin_hair_expert_question.id,skin_hair_expert_question.age,skin_hair_expert_question.weight,skin_hair_expert_question.diet_preference,skin_hair_expert_question.height,skin_hair_expert_question.user_image,skin_hair_expert_question.user_name,skin_hair_expert_question.user_id,skin_hair_expert_question.question,skin_hair_expert_question.date,IFNULL(skin_hair_expert_question.post_location,'') AS post_location,user_character.image AS c_image  FROM  `skin_hair_expert_question` INNER JOIN `user_character` ON skin_hair_expert_question.user_image=user_character.id  WHERE skin_hair_expert_question.id NOT IN (SELECT post_id FROM miss_belly_hide WHERE post_id=skin_hair_expert_question.id AND user_id='$user_id')  order by skin_hair_expert_question.id DESC limit $start, $limit";
        $query = $this->db->query("SELECT skin_hair_expert_question.post_id,skin_hair_expert_question.id,skin_hair_expert_question.age,skin_hair_expert_question.weight,skin_hair_expert_question.diet_preference,skin_hair_expert_question.height,skin_hair_expert_question.user_image,skin_hair_expert_question.user_name,skin_hair_expert_question.user_id,skin_hair_expert_question.question,skin_hair_expert_question.date,IFNULL(skin_hair_expert_question.post_location,'') AS post_location,skin_hair_expert_question.skin_color,skin_hair_expert_question.skin_type,skin_hair_expert_question.skin_concern,skin_hair_expert_question.skin_concern_other,user_character.image AS c_image  FROM  `skin_hair_expert_question` INNER JOIN `user_character` ON skin_hair_expert_question.user_image=user_character.id  WHERE skin_hair_expert_question.id NOT IN (SELECT post_id FROM miss_belly_hide WHERE post_id=skin_hair_expert_question.id AND user_id='$user_id') AND skin_hair_expert_question.post_id='$post_id'");

       // $count_query = $this->db->query("SELECT skin_hair_expert_question.id,skin_hair_expert_question.age,skin_hair_expert_question.weight,skin_hair_expert_question.height,skin_hair_expert_question.user_image,skin_hair_expert_question.user_name,skin_hair_expert_question.user_id,skin_hair_expert_question.question,skin_hair_expert_question.date,user_character.image AS c_image  FROM  `skin_hair_expert_question` INNER JOIN `user_character` ON skin_hair_expert_question.user_image=user_character.id  WHERE skin_hair_expert_question.id NOT IN (SELECT post_id FROM miss_belly_hide WHERE post_id=skin_hair_expert_question.id AND user_id='$user_id')  order by skin_hair_expert_question.id DESC");
        $count_post = $query->num_rows();


        // print_r($query);
        $resultpost = array();

        if ($count_post > 0) {
            $row =$query->row_array() ; 
                $id = $row['id'];
                $post_id = $row['post_id'];
                $weight = $row['weight'];
                $post_location = $row['post_location'];
                $height = $row['height'];
                $diet_preference = $row['diet_preference'];
                $age = $row['age'];
                $images = $row['c_image'];
                $user_name = $row['user_name'];
                $post_user_id = $row['user_id'];
                $question = $row['question'];
                $skin_color = $row['skin_color'];
                $skin_type = $row['skin_type'];
                $skin_concern = $row['skin_concern'];
                $skin_concern_other = $row['skin_concern_other'];
                if($question!=''){
                $question = preg_replace('~[\r\n]+~', '', $question);
             /*   if ($id > '619') {
                    $decrypt = $this->decrypt($question);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $question) {
                        $question = $decrypt;
                    }
                } else {*/
                    //if (base64_encode(base64_decode($question)) === $question) {
                        $question = $this->decrypt($question);
                    //}
               // }
                }
                $date = $row['date'];
                $date = get_time_difference_php($date);
                if ($images != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $images;
                } else {
                    $image = '';
                }

                $is_notify_query = $this->db->query("SELECT id FROM `miss_belly_is_notify` where post_id='$id' AND user_id='$user_id'");
                $is_notify = $is_notify_query->num_rows();
                $is_follow = '0';

                $answer_lists = array();
                $answer_query = $this->db->query("SELECT id,type,answer,post_id FROM `skin_hair_answer` WHERE `post_id`='$post_id'");
                $count_answers = $answer_query->num_rows();
                if ($count_answers > 0) {
                    foreach ($answer_query->result_array() as $rows) {
                        $answer_id = $rows['id'];
                        $answer = $rows['answer'];
                        $type = $rows['type'];
                        $answer = preg_replace('~[\r\n]+~', '', $answer);
                      /*  if ($answer_id > '526') {
                            $decrypt = $this->decrypt($answer);
                            $encrypt = $this->encrypt($decrypt);
                            if ($encrypt == $answer) {
                                $answer = $decrypt;
                            }
                        } else {*/
                           // if (base64_encode(base64_decode($answer)) === $answer) {
                                $answer = $this->decrypt($answer);
                           // }
                       // }
                        $answer_lists[] = array(
                            'answer_id' => $answer_id,
                            'type' => $type,
                            'answer' => $answer
                        );
                    }
                } else {
                    $answer_lists = array();
                }


                $share_url = "https://medicalwale.com/share/missbelly/" . $id;
                $answer_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/medicalwale_mobile_app_icons/fitness/miss_belly.png';
                $count_query = $this->db->query("SELECT id FROM `miss_belly_likes` where post_id='$id'");
                $like_count = $count_query->num_rows();

                $like_count_query = $this->db->query("SELECT id FROM `miss_belly_likes` where user_id='$user_id' and post_id='$id'");
                $like_yes_no = $like_count_query->num_rows();

                $is_post_save_query = $this->db->query("SELECT id from post_save WHERE post_id='$post_id' AND user_id='$user_id' AND post_type='missbelly'");
                $is_post_save = $is_post_save_query->num_rows();

                $is_reported_query = $this->db->query("SELECT id from post_report WHERE post_id='$post_id' AND reporter_id='$user_id' AND post_type='missbelly'");
                $is_reported = $is_reported_query->num_rows();

                $resultpost[] = array(
                    'id' => $id,
                    'post_id' =>$post_id,
                    'post_user_id' => $post_user_id,
                    'post_location' => $post_location,
                    'user_name' => $user_name,
                    'question' => $question,
                    'diet_preference' => $diet_preference,
                    'answer_list' => $answer_lists,
                    'is_notify' => $is_notify,
                    'age' => $age,
                    'height' => $height,
                    'weight' => $weight,
                    'skin_type' => $skin_type,
                    'skin_color' => $skin_color,
                    'skin_concern' => $skin_concern,
                    'skin_concern_other' => $skin_concern_other,
                    'image' => $image,
                    'answer_image' => $answer_image,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'is_follow' => $is_follow,
                    'share_url' => $share_url,
                    'is_reported' => $is_reported,
                    'is_post_save' => $is_post_save,
                    'date' => $date);
            
        } else {
            $resultpost = array();
        }

        $resultpost = array(
            "status" => 200,
            "message" => "success",
            "count" => $count_post,
            "data" => $resultpost
        );
        return $resultpost;
    }
    
    public function miss_belly_like($user_id, $post_id, $user_image, $user_name, $post_user_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `miss_belly_likes` WHERE post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `miss_belly_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from miss_belly_likes WHERE post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $sex_education_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id,
                'user_image' => $user_image,
                'user_name' => $user_name,
            );
            $this->db->insert('miss_belly_likes', $sex_education_likes);

            if ($user_name == '0' || $user_name == '') {
                $user_name = 'Someone';
            }

            if ($user_image == '0') {
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/medicalwale_mobile_app_icons/fitness/miss_belly.png';
            } else {
                $img_query = $this->db->query("select user_character.image as character_image FROM miss_belly_likes INNER JOIN user_character on user_character.id=miss_belly_likes.user_image  WHERE  miss_belly_likes.user_id='$user_id' AND miss_belly_likes.post_id='$post_id'");
                $getimg = $img_query->row_array();
                $character_image = $getimg['character_image'];
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $character_image;
            }

            $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id='$post_user_id' AND id<>'$user_id'");

            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $usr_name = $user_name;
                $agent = $token_status['agent'];
                $reg_id = $token_status['token'];
                $img_url = $userimage;
                $tag = 'text';
                $key_count = '1';
                $title = $usr_name . ' Beats on your Question';
                $msg = $usr_name . ' Beats on your question click here to view question.';
               /* if($this->get_stop_notification_for_user)
                {*/
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id);
               /* }*/
            }

            $like_query = $this->db->query("SELECT id from miss_belly_likes WHERE post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }
    public function miss_belly_user_like_list($post_id) {
        $query = $this->db->query("SELECT miss_belly_likes.id,miss_belly_likes.user_image,miss_belly_likes.user_name,miss_belly_likes.user_id,user_character.image AS c_image  FROM  `miss_belly_likes` INNER JOIN `user_character` ON miss_belly_likes.user_image=user_character.id WHERE miss_belly_likes.post_id='$post_id' order by miss_belly_likes.id desc");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {

                $user_name = $row['user_name'];
                $images = $row['c_image'];
                if ($images != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $images;
                } else {
                    $image = '';
                }

                $resultpost[] = array(
                    'user_name' => $user_name,
                    'image' => $image);
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function miss_belly_is_notify($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `miss_belly_is_notify` WHERE  user_id='$user_id' and post_id='$post_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `miss_belly_is_notify` WHERE user_id='$user_id' and post_id='$post_id'");

            return array(
                'status' => 200,
                'message' => 'deleted',
                'is_notify' => '0'
            );
        } else {
            $miss_belly_is_notify = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('miss_belly_is_notify', $miss_belly_is_notify);

            return array(
                'status' => 200,
                'message' => 'success',
                'is_notify' => '1'
            );
        }
    }

    public function miss_belly_hide($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `miss_belly_hide` WHERE  user_id='$user_id' and post_id='$post_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `miss_belly_hide` WHERE user_id='$user_id' and post_id='$post_id'");
            return array(
                'status' => 200,
                'message' => 'deleted',
                'is_hide' => '0'
            );
        } else {
            $sex_education_hide = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('miss_belly_hide', $sex_education_hide);


            $this->db->query("DELETE FROM `miss_belly_is_notify` WHERE user_id='$user_id' and post_id='$post_id'");

            return array(
                'status' => 200,
                'message' => 'success',
                'is_hide' => '1'
            );
        }
    }

    public function miss_belly_user_update($user_id, $user_name, $user_image) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id FROM `miss_belly_expert` WHERE user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("UPDATE `miss_belly_expert` SET `user_name`='$user_name',`user_image`='$user_image' WHERE user_id='$user_id'");
            return array(
                'status' => 200,
                'message' => 'success'
            );
        } else {

            $ask_saheli_ask_user = array(
                'user_id' => $user_id,
                'user_name' => $user_name,
                'user_image' => $user_image
            );
            $this->db->insert('miss_belly_expert', $ask_saheli_ask_user);
            return array(
                'status' => 200,
                'message' => 'success'
            );
        }
    }

    public function miss_belly_user_check($user_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id FROM `miss_belly_expert` WHERE user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $query = $this->db->query("SELECT miss_belly_expert.user_id, miss_belly_expert.user_name, miss_belly_expert.user_image,user_character.image AS c_image  FROM `miss_belly_expert` INNER JOIN `user_character` ON miss_belly_expert.user_image=user_character.id  WHERE miss_belly_expert.user_id='$user_id'");

            $row = $query->row_array();
            $user_id = $row['user_id'];
            $user_name = $row['user_name'];
            $user_image = $row['user_image'];
            $images = $row['c_image'];
            if ($images != '') {
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $images;
            } else {
                $image = '';
            }

            $resultpost[] = array(
                'user_id' => $user_id,
                'user_name' => $user_name,
                'user_image' => $user_image,
                'images' => $image
            );
        } else {

            $resultpost = array();
        }

        return $resultpost;
    }

    public function miss_belly_delete($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `skin_hair_expert_question` WHERE  user_id='$user_id' and id='$post_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `skin_hair_expert_question` WHERE user_id='$user_id' and id='$post_id'");

            return array(
                'status' => 200,
                'message' => 'deleted'
            );
        } else {
            return array(
                'status' => 401,
                'message' => 'unauthorized'
            );
        }
    }

    public function miss_belly_reply_delete($post_id, $answer_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `skin_hair_answer` WHERE  post_id='$post_id' and id='$answer_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `skin_hair_answer` WHERE post_id='$post_id' and id='$answer_id'");

            return array(
                'status' => 200,
                'message' => 'deleted'
            );
        } else {
            return array(
                'status' => 401,
                'message' => 'unauthorized'
            );
        }
    }
   
}
