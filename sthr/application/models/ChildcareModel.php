<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ChildcareModel extends CI_Model {

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
                'message' => 'Unauthorized1.'
            ));
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

    public function auth() {
        date_default_timezone_set('Asia/Kolkata');
        $users_id = $this->input->get_request_header('User-ID', TRUE);
        $token = $this->input->get_request_header('Authorizations', TRUE);
        $q = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id', $users_id)->where('token', $token)->get()->row();
        if ($q == "") {
            return json_output(401, array(
                'status' => 401,
                'message' => 'Unauthorized2.'
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

    public function get_time_difference_php($created_time) {
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

    public function baby_vaccination_tracker_all_data() {
    //return $this->db->select('id,week,short,title,protects_against,details,title_hindi,protects_against_hindi,details_hindi')->from('baby_vaccination_tracker')->order_by('id', 'asc')->get()->result();
    
        $query = $this->db->query("SELECT id,week,short,title,protects_against,details,title_hindi,protects_against_hindi,details_hindi FROM baby_vaccination_tracker order by id asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $week = $row['week'];
                $short = $row['short'];
                $title = $row['title'];
                $protects_against = $row['protects_against'];
                $details = $row['details'];
                
                $title_hindi = $row['title_hindi'];
                if ($title_hindi != '') {
                    $title_hindi = preg_replace('~[\r\n]+~', '', $title_hindi);
                    $decrypt_title = $this->decrypt($title_hindi);
                    $encrypt_title = $this->encrypt($decrypt_title);
                    if ($encrypt_title == $title_hindi) {
                        $title_hindi = $decrypt_title;
                    }
                }
                
                $protects_against_hindi = $row['protects_against_hindi'];
                if ($protects_against_hindi != '') {
                    $protects_against_hindi = preg_replace('~[\r\n]+~', '', $protects_against_hindi);
                    $decrypt_protects_against = $this->decrypt($protects_against_hindi);
                    $encrypt_protects_against = $this->encrypt($decrypt_protects_against);
                    if ($encrypt_protects_against == $protects_against_hindi) {
                        $protects_against_hindi = $decrypt_protects_against;
                    }
                }
                
                $details_hindi = $row['details_hindi'];
                if ($details_hindi != '') {
                    $details_hindi = preg_replace('~[\r\n]+~', '', $details_hindi);
                    $decrypt_details = $this->decrypt($details_hindi);
                    $encrypt_details = $this->encrypt($decrypt_details);
                    if ($encrypt_details == $details_hindi) {
                        $details_hindi = $decrypt_details;
                    }
                }
                
                $resultpost[] = array(
                    'id' => $id,
                    'week' => $week,
                    'short' => $short,
                    'title' => $title,
                    'protects_against' => $protects_against,
                    'details' => $details,
                    'title_hindi' => $title_hindi,
                    'protects_against_hindi' => $protects_against_hindi,
                    'details_hindi' => $details_hindi
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
        
    }

    public function baby_vaccination_tracker_detail_data($id) {
        return $this->db->select('id,title,protects_against,details,title_hindi,protects_against_hindi,details_hindi')->from('baby_vaccination_tracker')->where('id', $id)->order_by('id', 'desc')->get()->row();
    }

    public function baby_vaccination_tracker_create_data($data) {
        $this->db->insert('baby_vaccination_tracker', $data);
        return array(
            'status' => 201,
            'message' => 'Data has been created.'
        );
    }

    public function baby_vaccination_tracker_update_data($id, $data) {
        $this->db->where('id', $id)->update('baby_vaccination_tracker', $data);
        return array(
            'status' => 200,
            'message' => 'Data has been updated.'
        );
    }

    public function baby_vaccination_tracker_delete_data($id) {
        $this->db->where('id', $id)->delete('baby_vaccination_tracker');
        return array(
            'status' => 200,
            'message' => 'Data has been deleted.'
        );
    }

    public function childprofile_list($user_id) {

        $query = $this->db->query("SELECT id as child_id,name,gender,dob,birth_place,parent,date,image,blood_group,height,current_height,weight,current_weight,cover_image,medical_condition,allergy,hereditary_problem,diet,active_level FROM childprofile WHERE user_id='$user_id' order by id asc");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $child_id = $row['child_id'];
                $name = $row['name'];
                $gender = $row['gender'];
                $dob = $row['dob'];
                $birth_place = $row['birth_place'];
                $parent = $row['parent'];
                $date = $row['date'];
                $image = $row['image'];
                $blood_group = $row['blood_group'];
                $current_weight = $row['current_weight'];
                $current_height = $row['current_height'];
                $height = $row['height'];
                $weight = $row['weight'];
                
                $medical_condition = $row['medical_condition'];
                $allergy = $row['allergy'];
                $hereditary_problem = $row['hereditary_problem'];
                $diet = $row['diet'];
                $active_level = $row['active_level'];
                
                
              
                $cover_image = $row['cover_image'];
                
                if ($cover_image == '0') {
                    $cover_id = "";
                } else {
                    $cover_id = $cover_image;
                }


                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/child_care_images/image/' . $image;
                } else {
                    $image = '';
                }

                if ($cover_image != '0') {
                    $query_cover = $this->db->query("SELECT image FROM `childprofile_cover` WHERE id='$cover_image'");
                    $cover = $query_cover->row_array();
                    $cover_images = $cover['image'];

                    $cover_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/child_care_images/cover/' . $cover_images;
                } else {
                    $cover_image = "";
                }


                $resultpost[] = array(
                    'child_id' => $child_id,
                    'name' => $name,
                    'gender' => $gender,
                    'image' => $image,
                    'cover_image' => $cover_image,
                    'cover_id' => $cover_id,
                    'dob' => $dob,
                    'birth_place' => $birth_place,
                    'parent' => $parent,
                    'date' => $date,
                    'blood_group' => $blood_group,
                    'current_weight' => $current_weight,
                    'current_height' => $current_height,
                    'height' => $height,
                    'weight' => $weight,
                    'medical_condition' => $medical_condition,
                    'allergy' => $allergy,
                    'hereditary_problem' => $hereditary_problem,
                    'diet' => $diet,
                    'active_level' => $active_level
                );
            }
        } else {
            $resultpost = array();
        }


        return $resultpost;
    }

    public function childprofile($name, $gender, $dob, $birth_place, $parent, $image, $user_id,$blood_group,$height,$current_height,$active_level,$diet,$weight,$current_weight,$medical_condition,$allergy,$hereditary_problem,$question) {
        if ($name != '' && $gender != '' && $dob != '' && $birth_place != '' && $parent != '' && $user_id != '') {
            date_default_timezone_set('Asia/Kolkata');
            $created_at = date('Y-m-d H:i:s');

            
            $childprofile_data = array(
                'name' => $name,
                'gender' => $gender,
                'dob' => $dob,
                'birth_place' => $birth_place,
                'parent' => $parent,
                'image' => $image,
                'date' => $created_at,
                'user_id' => $user_id,
                
                
                 'blood_group' => $blood_group,
                 'current_weight' => $current_weight,
                'current_height' => $current_height,
                'weight' => $weight,
                'height' => $height,
                'medical_condition' => $medical_condition,
                'allergy' => $allergy,
                'hereditary_problem' => $hereditary_problem,
                'diet' => $diet,
                'active_level' => $active_level
                
                
            );

            $this->db->insert('childprofile', $childprofile_data);
            $childprofile_id = $this->db->insert_id();
            
            if(!empty($question))
            {
                $questions = json_decode($question);
                if(!empty($questions))
                {
                    $pre_final_que = $questions->quastion;
                    $final_que = $pre_final_que[0];
                    $q_user_id = $final_que->user_id;
                    $final_q = $final_que->qas;
                    $data2 = array();
                    $resp = $this->delete_question($q_user_id,$childprofile_id);
                    for($i=0;$i<sizeof($final_q);$i++){
                         
                         $data2['user_id'] = $q_user_id; 
                         $data2['child_id'] = $childprofile_id; 
                         $data2['question_id'] = $final_q[$i]->qid;
                         $data2['answer'] = $final_q[$i]->qans;
                         
                         $resp = $this->update_child_care_question($data2);
                    }
                }
            } 
            $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/child_care_images/image/' . $image;

            $data[] = array(
                'id' => $childprofile_id,
                'name' => $name,
                'gender' => $gender,
                'dob' => $dob,
                'birth_place' => $birth_place,
                'parent' => $parent,
                'image' => $profile,
                
                 'blood_group' => $blood_group,
                  'current_weight' => $current_weight,
                'current_height' => $current_height,
                'weight' => $weight,
                'height' => $height,
                'medical_condition' => $medical_condition,
                'allergy' => $allergy,
                'hereditary_problem' => $hereditary_problem,
                'diet' => $diet,
                'active_level' => $active_level
            );


            return array(
                'status' => 201,
                'message' => 'success',
                'data' => $data
            );
        } else {
            return array(
                'status' => 208,
                'message' => 'Please enter all fields'
            );
        }
    }
    public function delete_question($user_id,$childprofile_id){
        
        $this->db->where('user_id', $user_id);
        $this->db->where('child_id', $childprofile_id);
        $this->db->delete('child_care_question_answer');
   
    }
    public function update_child_care_question($data2){
        $this->db->insert('child_care_question_answer', $data2);
    }
    public function childprofile_update($name, $gender, $dob, $birth_place, $parent, $image, $user_id, $childprofile_id,$blood_group,$height,$current_height,$active_level,$diet,$weight,$current_weight,$medical_condition,$allergy,$hereditary_problem,$question) {

        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');

        $data = array();

        if ($image === "") {
            $data = array(
                'id' => $childprofile_id,
                'name' => $name,
                'gender' => $gender,
                'dob' => $dob,
                'birth_place' => $birth_place,
                'parent' => $parent,
                'date' => $created_at,
                'user_id' => $user_id,
                 'blood_group' => $blood_group,
                 'current_weight' => $current_weight,
                'current_height' => $current_height,
                'weight' => $weight,
                'height' => $height,
                'medical_condition' => $medical_condition,
                'allergy' => $allergy,
                'hereditary_problem' => $hereditary_problem,
                'diet' => $diet,
                'active_level' => $active_level
            );
        } else {
            $data = array(
                'id' => $childprofile_id,
                'name' => $name,
                'gender' => $gender,
                'dob' => $dob,
                'birth_place' => $birth_place,
                'parent' => $parent,
                'image' => $image,
                'date' => $created_at,
                'user_id' => $user_id,
                 'blood_group' => $blood_group,
                 'current_weight' => $current_weight,
                'current_height' => $current_height,
                'weight' => $weight,
                'height' => $height,
                'medical_condition' => $medical_condition,
                'allergy' => $allergy,
                'hereditary_problem' => $hereditary_problem,
                'diet' => $diet,
                'active_level' => $active_level
            );
        }

        $this->db->where('user_id', $user_id)->where('id', $childprofile_id)->update('childprofile', $data);

         if(!empty($question))
            {
                $questions = json_decode($question);
                if(!empty($questions))
                {
                    $pre_final_que = $questions->quastion;
                    $final_que = $pre_final_que[0];
                    $q_user_id = $final_que->user_id;
                    $final_q = $final_que->qas;
                    $data2 = array();
                    $resp = $this->delete_question($q_user_id,$childprofile_id);
                    for($i=0;$i<sizeof($final_q);$i++){
                         
                         $data2['user_id'] = $q_user_id; 
                         $data2['child_id'] = $childprofile_id; 
                         $data2['question_id'] = $final_q[$i]->qid;
                         $data2['answer'] = $final_q[$i]->qans;
                         
                         $resp = $this->update_child_care_question($data2);
                    }
                }
            } 

        $query = $this->db->query("SELECT id as child_id,name,gender,image,dob,birth_place,parent,date,cover_image FROM childprofile WHERE user_id='$user_id' AND id='$childprofile_id' order by id asc");
        $row = $query->row_array();


        $child_id = $row['child_id'];
        $name = $row['name'];
        $gender = $row['gender'];
        $dob = $row['dob'];
        $birth_place = $row['birth_place'];
        $parent = $row['parent'];
        $date = $row['date'];
        $image = $row['image'];
        if ($image != '') {
            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/child_care_images/image/' . $image;
        } else {
            $image = '';
        }


        $data_childprofile = array(
            'child_id' => $child_id,
            'name' => $name,
            'gender' => $gender,
            'image' => $image,
            'dob' => $dob,
            'birth_place' => $birth_place,
            'parent' => $parent,
            'date' => $date
        );


        return array(
            'status' => 200,
            'message' => 'success',
            'data' => $data_childprofile
        );
    }

    public function childprofile_delete($user_id, $childprofile_id) {


        $query_profile = $this->db->query("SELECT image FROM `childprofile` WHERE id='$childprofile_id' AND user_id='$user_id'");
        $get_file = $query_profile->row_array();
        $profile_image = $get_file['image'];

        $profile_image_ = 'images/child_care_images/image/' . $profile_image;
        @unlink(trim($profile_image_));
        DeleteFromToS3($profile_image_);

        //unlink images
        $media_query = $this->db->query("SELECT  `type`, `source` FROM child_mydiary_media WHERE child_id='$childprofile_id'");

        foreach ($media_query->result_array() as $media_row) {
            $source = $media_row['source'];
            $media_type = $media_row['type'];


            $images = 'images/child_care_images/' . $media_type . '/' . $source;
            if ($media_type == 'video') {
                $thumb = 'images/child_care_images/thumb/' . substr_replace($source, 'jpg', strrpos($source, '.') + 1);
                @unlink(trim($thumb));
                DeleteFromToS3($thumb);
            }

            @unlink(trim($images));
            DeleteFromToS3($images);
        }

        $query = $this->db->query("DELETE FROM `childprofile` WHERE id='$childprofile_id' AND user_id='$user_id'");
        $query = $this->db->query("DELETE FROM `child_mydiary` WHERE child_id='$childprofile_id'");
        $query = $this->db->query("DELETE FROM `child_mydiary_media` WHERE child_id='$childprofile_id'");
        $query = $this->db->query("DELETE FROM `child_care_question_answer` WHERE id='$childprofile_id' AND user_id='$user_id'");
        //unlink images ends


        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    public function child_mydiary_delete($user_id, $childdiary_id) {

        //unlink images
        $media_query = $this->db->query("SELECT  `type`, `source` FROM child_mydiary_media WHERE post_id='$childdiary_id'");

        foreach ($media_query->result_array() as $media_row) {
            $source = $media_row['source'];
            $media_type = $media_row['type'];


            $images = 'images/child_care_images/' . $media_type . '/' . $source;
            if ($media_type == 'video') {
                $thumb = 'images/child_care_images/thumb/' . substr_replace($source, 'jpg', strrpos($source, '.') + 1);
                @unlink(trim($thumb));
                DeleteFromToS3($thumb);
            }

            @unlink(trim($images));
            DeleteFromToS3($images);
        }

        $query = $this->db->query("DELETE FROM `child_mydiary` WHERE id='$childdiary_id' AND user_id='$user_id'");

        $query = $this->db->query("DELETE FROM `child_mydiary_media` WHERE post_id='$childdiary_id'");
        //unlink images ends


        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    public function child_mydiary($child_id) {


        $query = $this->db->query("select id,child_id,comment,record_datetime FROM child_mydiary WHERE child_id='$child_id' order by record_datetime desc");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $child_id = $row['child_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if ($id > '119') {
                    $comment_decrypt = $this->decrypt($comment);
                    $comment_encrypt = $this->encrypt($comment_decrypt);
                    if ($comment_encrypt == $comment) {
                        $comment = $comment_decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($comment)) === $comment) {
                        $comment = base64_decode($comment);
                    }
                }
                $record_datetimes = $row['record_datetime'];
                $record_datetime = date("j F Y", strtotime($record_datetimes));


                $query_media = $this->db->query("SELECT child_mydiary_media.id AS media_id,child_mydiary_media.source,child_mydiary_media.type AS media_type,child_mydiary_media.img_width,child_mydiary_media.img_height,child_mydiary_media.video_width,child_mydiary_media.video_height FROM child_mydiary_media INNER JOIN child_mydiary ON child_mydiary_media.post_id=child_mydiary.id WHERE child_mydiary_media.post_id='$id' order by child_mydiary_media.id desc");

                $img_width = '';
                $img_height = '';
                $video_width = '';
                $video_height = '';

                $media_array = array();

                foreach ($query_media->result_array() as $media_row) {
                    $media_id = $media_row['media_id'];
                    $media_type = $media_row['media_type'];
                    $source = $media_row['source'];
                    $images = 'https://d2c8oti4is0ms3.cloudfront.net/images/child_care_images/' . $media_type . '/' . $source;
                    if ($media_type == 'video') {
                        $thumb = 'https://s3.amazonaws.com/medicalwale-thumbnails/videothumbnail/images/child_care_images/video/' . substr_replace($source, '_thumbnail.jpeg', strrpos($source, '.') + 0);
                    } else {
                        $thumb = '';
                    }

                    $img_width = $media_row['img_width'];
                    $img_height = $media_row['img_height'];
                    $video_width = $media_row['video_width'];
                    $video_height = $media_row['video_height'];


                    $media_array[] = array(
                        'media_id' => $media_id,
                        'type' => $media_type,
                        'images' => str_replace(' ', '%20', $images),
                        'thumb' => $thumb,
                        'img_height' => $img_height,
                        'img_width' => $img_width,
                        'video_height' => $video_height,
                        'video_width' => $video_width
                    );
                }

                $resultpost[] = array(
                    'id' => $id,
                    'child_id' => $child_id,
                    'comment' => $comment,
                    'record_datetime' => $record_datetime,
                    'media' => $media_array
                );
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }

    public function child_mydiary_album($child_id, $user_id) {
        $query = $this->db->query("SELECT id,record_datetime FROM child_mydiary WHERE child_id='$child_id' and user_id='$user_id' group by record_datetime order by id desc");
        $record_array = array();
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $record_datetime = $row['record_datetime'];
                $media_id = $row['id'];
                $media_query = $this->db->query("SELECT post_id,id AS media_id,source,type AS media_type,img_width,img_height,video_width,video_height FROM child_mydiary_media  WHERE record_datetime='$record_datetime' AND post_id IN (SELECT id FROM child_mydiary WHERE child_id='$child_id' and user_id='$user_id') order by id desc");
                $media_array = array();
                $media_count = $media_query->num_rows();
                if ($media_count > 0) {
                    foreach ($media_query->result_array() as $media_row) {
                        $img_width = '';
                        $img_height = '';
                        $video_width = '';
                        $video_height = '';
                        $media_id = $media_row['media_id'];
                        $media_type = $media_row['media_type'];
                        $source = $media_row['source'];
                        $images = 'https://d2c8oti4is0ms3.cloudfront.net/images/child_care_images/' . $media_type . '/' . $source;
                        if ($media_type == 'video') {
                            $thumb = 'https://s3.amazonaws.com/medicalwale-thumbnails/videothumbnail/images/child_care_images/video/' . substr_replace($source, '_thumbnail.jpeg', strrpos($source, '.') + 0);
                        } else {
                            $thumb = '';
                        }

                        $img_width = $media_row['img_width'];
                        $img_height = $media_row['img_height'];
                        $video_width = $media_row['video_width'];
                        $video_height = $media_row['video_height'];
                        $media_array[] = array(
                            'media_id' => $media_id,
                            'type' => $media_type,
                            'images' => str_replace(' ', '%20', $images),
                            'thumb' => $thumb,
                            'img_height' => $img_height,
                            'img_width' => $img_width,
                            'video_height' => $video_height,
                            'video_width' => $video_width
                        );
                    }
                } else {
                    $media_array = array();
                }

                $record_datetime_final = date("j F Y", strtotime($record_datetime));
                $resultpost[] = array('record_datetime' => $record_datetime_final, 'media' => $media_array);
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function childprofile_cover_list() {

        $query = $this->db->query("SELECT `id`, `image`  FROM `childprofile_cover` where type='system' order by id asc");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $image = $row['image'];
                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/child_care_images/cover/' . $image;
                } else {
                    $image = '';
                }

                $id = $row['id'];


                $resultpost[] = array(
                    'cover_id' => $id,
                    'image' => $image
                );
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }

    public function childprofile_cover_update($user_id, $childprofile_id, $cover_id, $cover_image) {
        if ($cover_image != '') {
            $childprofile_cover_data = array(
                'image' => $cover_image,
                'type' => 'other'
            );

            $childprofile_cover_insert = $this->db->insert('childprofile_cover', $childprofile_cover_data);
            $cover_id = $this->db->insert_id();
            $query = $this->db->query("UPDATE childprofile SET cover_image='$cover_id' WHERE user_id='$user_id' AND id='$childprofile_id'");
        } else {
            $query = $this->db->query("UPDATE childprofile SET cover_image='$cover_id' WHERE user_id='$user_id' AND id='$childprofile_id'");
        }



        if ($cover_id != '0') {
            $query_cover = $this->db->query("SELECT * FROM `childprofile_cover` WHERE id='$cover_id'");
            $cover = $query_cover->row_array();
            $cover_images = $cover['image'];

            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/child_care_images/cover/' . $cover_images;
        } else {
            $image = '';
        }


        return array(
            'status' => 200,
            'message' => 'success',
            'image' => $image
        );
    }

    public function babysitter_list($user_id, $latitude, $longitude) {
        $radius = '5';

        $query = $this->db->query("SELECT `id`, `user_id`, `name`, `about_us`, `establishment_year`, `address`, `phone`, `telephone`, `state`, `city`, `pincode`, `image`, `service_offered`, `lat`, `lng`, `reviews`, `ratings`,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM babysitter HAVING distance <= '$radius' order by id ASC");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $name = $row['name'];
                $about_us = $row['about_us'];
                $establishment_year = $row['establishment_year'];
                $address = $row['address'];
                $mobile = $row['phone'];
                $state = $row['state'];
                $city = $row['city'];
                $pincode = $row['pincode'];
                $babysitter_service_offered2 = $row['service_offered'];
                $image = $row['image'];
                $lat = $row['lat'];
                $lng = $row['lng'];
                $babysitter_service_offered = array();
                $gallery_list = array();
                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/babysitter_images/' . $image;
                } else {
                    $image = '';
                }

                $babysitter_user_id = $row['user_id'];
                $rating = $row['ratings'];
                $profile_views = '415';
                $reviews = $row['reviews'];


                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $babysitter_user_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $babysitter_user_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $babysitter_user_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }



                $service_offered_query = $this->db->query("SELECT `name` FROM `babysitter_service_offered` WHERE FIND_IN_SET(name,'" . $babysitter_service_offered2 . "')");
                foreach ($service_offered_query->result_array() as $get_list) {
                    $service_offered = $get_list['name'];
                    $babysitter_service_offered[] = array(
                        "service_offered" => $service_offered
                    );
                }

                $gallery_query = $this->db->query("SELECT `title` , CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/babysitter_images/',source) AS media FROM `babysitter_media` WHERE babysitter_id='$id'");
                foreach ($gallery_query->result_array() as $get_list2) {
                    $gallery_title = $get_list2['title'];
                    $gallery_image = $get_list2['media'];
                    $gallery_list[] = array(
                        "title" => $gallery_title,
                        "image" => $gallery_image
                    );
                }

                $resultpost[] = array(
                    'id' => $id,
                    'babysitter_user_id' => $babysitter_user_id,
                    'name' => $name,
                    'listing_type' => "15",
                    'about_us' => $about_us,
                    'establishment_year' => $establishment_year,
                    'address' => $address,
                    'mobile' => $mobile,
                    'state' => $state,
                    'city' => $city,
                    'pincode' => $pincode,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'image' => $image,
                    'service_offered' => $babysitter_service_offered,
                    'gallery' => $gallery_list,
                    'rating' => $rating,
                    'followers' => $followers,
                    'following' => $following,
                    'profile_views' => $profile_views,
                    'reviews' => $reviews,
                    'is_follow' => $is_follow
                );
            }
        } else {
            $resultpost = array();
        }


        return $resultpost;
    }

    public function babysitter_add_review($user_id, $listing_id, $rating, $review, $service) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'user_id' => $user_id,
            'babysitter_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('babysitter_review', $review_array);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }
    
    public function edit_babysitter_add_review($review_id,$user_id, $listing_id, $rating, $review, $service) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
        );
           $this->db->where('id',$review_id);
           $this->db->where('user_id',$user_id);
           $this->db->update('babysitter_review',$review_array);
        //$this->db->insert('fitness_center_review', $review_array);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    public function babysitter_review_list($user_id, $listing_id) {

        $resultpost = '';
        $review_count = $this->db->select('id')->from('babysitter_review')->where('babysitter_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT babysitter_review.id,babysitter_review.user_id,babysitter_review.babysitter_id,babysitter_review.rating,babysitter_review.review, babysitter_review.service,babysitter_review.date as review_date,users.id as user_id,users.name as firstname FROM `babysitter_review` INNER JOIN `users` ON babysitter_review.user_id=users.id WHERE babysitter_review.babysitter_id='$listing_id' order by babysitter_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '13') {
                    $decrypt = $this->decrypt($review);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $review) {
                        $review = $decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($review)) === $review) {
                        $review = base64_decode($review);
                    }
                }
                $service = $row['service'];
                $review_date = $row['review_date'];
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('babysitter_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('babysitter_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('babysitter_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

                $resultpost[] = array(
                    'id' => $id,
                    'username' => $username,
                    'rating' => $rating,
                    'review' => $review,
                    'service' => $service,
                    'review_date' => $review_date,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'comment_count' => $post_count
                );
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }
    
    
     public function babysitter_review_with_comment($user_id, $listing_id) {

        $resultpost = '';
        $review_count = $this->db->select('id')->from('babysitter_review')->where('babysitter_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT babysitter_review.id,babysitter_review.user_id,babysitter_review.babysitter_id,babysitter_review.rating,babysitter_review.review, babysitter_review.service,babysitter_review.date as review_date,users.id as user_id,users.name as firstname FROM `babysitter_review` INNER JOIN `users` ON babysitter_review.user_id=users.id WHERE babysitter_review.babysitter_id='$listing_id' order by babysitter_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
               /* if ($id > '13') {
                    $decrypt = $this->decrypt($review);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $review) {
                        $review = $decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($review)) === $review) {
                        $review = base64_decode($review);
                    }
                }*/
                $service = $row['service'];
                $review_date = $row['review_date'];
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('babysitter_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('babysitter_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('babysitter_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

            $review_list_count = $this->db->select('id')->from('babysitter_review_comment')->where('post_id', $id)->get()->num_rows();
             if ($review_list_count) {
             $resultcomment = array();
            $querycomment = $this->db->query("SELECT babysitter_review_comment.id,babysitter_review_comment.post_id,babysitter_review_comment.comment as comment,babysitter_review_comment.date,users.name,babysitter_review_comment.user_id as post_user_id FROM babysitter_review_comment INNER JOIN users on users.id=babysitter_review_comment.user_id WHERE babysitter_review_comment.post_id='$id' order by babysitter_review_comment.id asc");

            foreach ($querycomment->result_array() as $rowc) {
                $comment_id = $rowc['id'];
                $post_id = $rowc['post_id'];
                $comment = $rowc['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
             /*   if ($id > '10') {
                    $decrypt = $this->decrypt($comment);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $comment) {
                        $comment = $decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($comment)) === $comment) {
                        $comment = base64_decode($comment);
                    }
                }*/
                $usernamec = $rowc['name'];
                $date = $rowc['date'];
                $post_user_id = $rowc['post_user_id'];

                $like_countc = $this->db->select('id')->from('babysitter_review_comment_like')->where('comment_id', $comment_id)->get()->num_rows();
                $like_yes_noc = $this->db->select('id')->from('babysitter_review_comment_like')->where('comment_id', $comment_id)->where('user_id', $user_id)->get()->num_rows();
                $comment_date = $this->get_time_difference_php($date);

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();

                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimagec = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimagec = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $date = $this->get_time_difference_php($date);
                $resultcomment[] = array(
                    'id' => $comment_id,
                    'username' => $usernamec,
                    'userimage' => $userimagec,
                    'like_count' => $like_countc,
                    'like_yes_no' => $like_yes_noc,
                    'post_id' => $post_id,
                    'comment' => $comment,
                    'comment_date' => $comment_date
                );
            }
        } else {
            $resultcomment = array();
        }


                $resultpost[] = array(
                    'id' => $id,
                    'username' => $username,
                    'rating' => $rating,
                    'review' => $review,
                    'service' => $service,
                    'review_date' => $review_date,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'comment_count' => $post_count,
                    'comments' => $resultcomment
                    
                );
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }
    

    public function babysitter_review_like($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from babysitter_review_likes where post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `babysitter_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from babysitter_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $babysitter_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('babysitter_review_likes', $babysitter_review_likes);
            $like_query = $this->db->query("SELECT id from babysitter_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }

    public function babysitter_review_comment($user_id, $post_id, $comment) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');

        $babysitter_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('babysitter_review_comment', $babysitter_review_comment);
        $babysitter_review_comment_query = $this->db->query("SELECT id from babysitter_review_comment where post_id='$post_id'");
        $total_comment = $babysitter_review_comment_query->num_rows();
        return array(
            'status' => 200,
            'message' => 'success',
            'comment' => '1',
            'total_comment' => $total_comment
        );
    }

    public function babysitter_review_comment_like($user_id, $comment_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from babysitter_review_comment_like where comment_id='$comment_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `babysitter_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from babysitter_review_comment_like where comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $babysitter_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('babysitter_review_comment_like', $babysitter_review_comment_like);
            $comment_query = $this->db->query("SELECT id from babysitter_review_comment_like where comment_id='$comment_id'");
            $total_comment_like = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'comment_like' => '1',
                'total_comment_like' => $total_comment_like
            );
        }
    }

    public function babysitter_review_comment_list($user_id, $post_id) {


        $review_list_count = $this->db->select('id')->from('babysitter_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT babysitter_review_comment.id,babysitter_review_comment.post_id,babysitter_review_comment.comment as comment,babysitter_review_comment.date,users.name,babysitter_review_comment.user_id as post_user_id FROM babysitter_review_comment INNER JOIN users on users.id=babysitter_review_comment.user_id WHERE babysitter_review_comment.post_id='$post_id' order by babysitter_review_comment.id asc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if ($id > '10') {
                    $decrypt = $this->decrypt($comment);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $comment) {
                        $comment = $decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($comment)) === $comment) {
                        $comment = base64_decode($comment);
                    }
                }
                $username = $row['name'];
                $date = $row['date'];
                $post_user_id = $row['post_user_id'];

                $like_count = $this->db->select('id')->from('babysitter_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('babysitter_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
                $comment_date = $this->get_time_difference_php($date);

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();

                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $date = $this->get_time_difference_php($date);
                $resultpost[] = array(
                    'id' => $id,
                    'username' => $username,
                    'userimage' => $userimage,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'post_id' => $post_id,
                    'comment' => $comment,
                    'comment_date' => $comment_date
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function dai_nanny_list($user_id, $latitude, $longitude) {
        $radius = '5';

        $query = $this->db->query("SELECT `id`, `user_id`, `name`, `about_us`, `establishment_year`, `address`, `phone`, `telephone`, `state`, `city`, `pincode`, `image`, `service_offered`, `lat`, `lng`, `reviews`, `ratings`,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM dai_nanny HAVING distance <= '$radius' order by id ASC");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $name = $row['name'];
                $about_us = $row['about_us'];
                $establishment_year = $row['establishment_year'];
                $address = $row['address'];
                $mobile = $row['phone'];
                $state = $row['state'];
                $city = $row['city'];
                $pincode = $row['pincode'];
                $dai_nanny_service_offered2 = $row['service_offered'];
                $image = $row['image'];
                $lat = $row['lat'];
                $lng = $row['lng'];
                $dai_nanny_service_offered = array();
                $gallery_list = array();
                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/dai_nanny_images/' . $image;
                } else {
                    $image = '';
                }
                $dai_nanny_user_id = $row['user_id'];
                $rating = $row['ratings'];
                $profile_views = '415';
                $reviews = $row['reviews'];
                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $dai_nanny_user_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $dai_nanny_user_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $dai_nanny_user_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }

                $service_offered_query = $this->db->query("SELECT `name` FROM `dai_nanny_service_offered` WHERE FIND_IN_SET(name,'" . $dai_nanny_service_offered2 . "')");
                foreach ($service_offered_query->result_array() as $get_list) {
                    $service_offered = $get_list['name'];
                    $dai_nanny_service_offered[] = array(
                        "service_offered" => $service_offered
                    );
                }

                $gallery_query = $this->db->query("SELECT `title` , CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/dai_nanny_images/',source) AS media FROM `dai_nanny_media` WHERE dai_nanny_id='$id'");
                foreach ($gallery_query->result_array() as $get_list2) {
                    $gallery_title = $get_list2['title'];
                    $gallery_image = $get_list2['media'];
                    $gallery_list[] = array(
                        "title" => $gallery_title,
                        "image" => $gallery_image
                    );
                }

                $resultpost[] = array(
                    'id' => $id,
                    'dai_nanny_user_id' => $dai_nanny_user_id,
                    'name' => $name,
                    'listing_type' => "22",
                    'about_us' => $about_us,
                    'establishment_year' => $establishment_year,
                    'address' => $address,
                    'mobile' => $mobile,
                    'state' => $state,
                    'city' => $city,
                    'pincode' => $pincode,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'image' => $image,
                    'service_offered' => $dai_nanny_service_offered,
                    'gallery' => $gallery_list,
                    'rating' => $rating,
                    'followers' => $followers,
                    'following' => $following,
                    'profile_views' => $profile_views,
                    'reviews' => $reviews,
                    'is_follow' => $is_follow
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function dai_nanny_add_review($user_id, $listing_id, $rating, $review, $service) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'user_id' => $user_id,
            'dai_nanny_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('dai_nanny_review', $review_array);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }
    
    public function edit_dai_nanny_add_review($review_id,$user_id, $listing_id, $rating, $review, $service) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
        );
           $this->db->where('id',$review_id);
           $this->db->where('user_id',$user_id);
           $this->db->update('dai_nanny_review',$review_array);
        //$this->db->insert('fitness_center_review', $review_array);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    public function dai_nanny_review_list($user_id, $listing_id) {

        $resultpost = '';
        $review_count = $this->db->select('id')->from('dai_nanny_review')->where('dai_nanny_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT dai_nanny_review.id,dai_nanny_review.user_id,dai_nanny_review.dai_nanny_id,dai_nanny_review.rating,dai_nanny_review.review, dai_nanny_review.service,dai_nanny_review.date as review_date,users.id as user_id,users.name as firstname FROM `dai_nanny_review` INNER JOIN `users` ON dai_nanny_review.user_id=users.id WHERE dai_nanny_review.dai_nanny_id='$listing_id' order by dai_nanny_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '4') {
                    $decrypt = $this->decrypt($review);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $review) {
                        $review = $decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($review)) === $review) {
                        $review = base64_decode($review);
                    }
                }
                $service = $row['service'];
                $review_date = $row['review_date'];
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('dai_nanny_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('dai_nanny_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('dai_nanny_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

                $resultpost[] = array(
                    'id' => $id,
                    'username' => $username,
                    'rating' => $rating,
                    'review' => $review,
                    'service' => $service,
                    'review_date' => $review_date,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'comment_count' => $post_count
                );
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }

 public function dai_nanny_review_with_comment($user_id, $listing_id) {

        $resultpost = '';
        $review_count = $this->db->select('id')->from('dai_nanny_review')->where('dai_nanny_id', $listing_id)->get()->num_rows();
   
        if ($review_count > 0) {
            $query = $this->db->query("SELECT dai_nanny_review.id,dai_nanny_review.user_id,dai_nanny_review.dai_nanny_id,dai_nanny_review.rating,dai_nanny_review.review, dai_nanny_review.service,dai_nanny_review.date as review_date,users.id as user_id,users.name as firstname FROM `dai_nanny_review` INNER JOIN `users` ON dai_nanny_review.user_id=users.id WHERE dai_nanny_review.dai_nanny_id='$listing_id' order by dai_nanny_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                /*if ($id > '4') {
                    $decrypt = $this->decrypt($review);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $review) {
                        $review = $decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($review)) === $review) {
                        $review = base64_decode($review);
                    }
                }*/
                $service = $row['service'];
                $review_date = $row['review_date'];
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('dai_nanny_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('dai_nanny_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('dai_nanny_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

 $review_list_count = $this->db->select('id')->from('dai_nanny_review_comment')->where('post_id', $id)->get()->num_rows();
 
        if ($review_list_count) {
            $resultcomment = array();
            $querycomment = $this->db->query("SELECT dai_nanny_review_comment.id,dai_nanny_review_comment.post_id,dai_nanny_review_comment.comment as comment,dai_nanny_review_comment.date,users.name,dai_nanny_review_comment.user_id as post_user_id FROM dai_nanny_review_comment INNER JOIN users on users.id=dai_nanny_review_comment.user_id WHERE dai_nanny_review_comment.post_id='$id' order by dai_nanny_review_comment.id asc");

            foreach ($querycomment->result_array() as $row) {
                $comment_id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
              
                $usernamec = $row['name'];
                $date = $row['date'];
                $post_user_id = $row['post_user_id'];

                $like_countc = $this->db->select('id')->from('dai_nanny_review_comment_like')->where('comment_id', $comment_id)->get()->num_rows();
                $like_yes_noc = $this->db->select('id')->from('dai_nanny_review_comment_like')->where('comment_id', $comment_id)->where('user_id', $user_id)->get()->num_rows();
                $comment_date = $this->get_time_difference_php($date);

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();

                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $date = $this->get_time_difference_php($date);
                $resultcomment[] = array(
                    'id' => $comment_id,
                    'username' => $usernamec,
                    'userimage' => $userimage,
                    'like_count' => $like_countc,
                    'like_yes_no' => $like_yes_noc,
                    'post_id' => $post_id,
                    'comment' => $comment,
                    'comment_date' => $comment_date
                );
            }
        } else {
            $resultcomment = array();
        }


                $resultpost[] = array(
                    'id' => $id,
                    'username' => $username,
                    'rating' => $rating,
                    'review' => $review,
                    'service' => $service,
                    'review_date' => $review_date,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'comment_count' => $post_count,
                     'comments'=>$resultcomment
                );
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }

    public function dai_nanny_review_like($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from dai_nanny_review_likes where post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `dai_nanny_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from dai_nanny_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $dai_nanny_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('dai_nanny_review_likes', $dai_nanny_review_likes);
            $like_query = $this->db->query("SELECT id from dai_nanny_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }

    public function dai_nanny_review_comment($user_id, $post_id, $comment) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');

        $dai_nanny_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('dai_nanny_review_comment', $dai_nanny_review_comment);
        $dai_nanny_review_comment_query = $this->db->query("SELECT id from dai_nanny_review_comment where post_id='$post_id'");
        $total_comment = $dai_nanny_review_comment_query->num_rows();
        return array(
            'status' => 200,
            'message' => 'success',
            'comment' => '1',
            'total_comment' => $total_comment
        );
    }

    public function dai_nanny_review_comment_like($user_id, $comment_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from dai_nanny_review_comment_like where comment_id='$comment_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `dai_nanny_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from dai_nanny_review_comment_like where comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $dai_nanny_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('dai_nanny_review_comment_like', $dai_nanny_review_comment_like);
            $comment_query = $this->db->query("SELECT id from dai_nanny_review_comment_like where comment_id='$comment_id'");
            $total_comment_like = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'comment_like' => '1',
                'total_comment_like' => $total_comment_like
            );
        }
    }

    public function dai_nanny_review_comment_list($user_id, $post_id) {

        $review_list_count = $this->db->select('id')->from('dai_nanny_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT dai_nanny_review_comment.id,dai_nanny_review_comment.post_id,dai_nanny_review_comment.comment as comment,dai_nanny_review_comment.date,users.name,dai_nanny_review_comment.user_id as post_user_id FROM dai_nanny_review_comment INNER JOIN users on users.id=dai_nanny_review_comment.user_id WHERE dai_nanny_review_comment.post_id='$post_id' order by dai_nanny_review_comment.id asc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
               /* if ($id > '8') {
                    $decrypt = $this->decrypt($comment);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $comment) {
                        $comment = $decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($comment)) === $comment) {
                        $comment = base64_decode($comment);
                    }
                }*/
                $username = $row['name'];
                $date = $row['date'];
                $post_user_id = $row['post_user_id'];

                $like_count = $this->db->select('id')->from('dai_nanny_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('dai_nanny_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
                $comment_date = $this->get_time_difference_php($date);

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();

                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $date = $this->get_time_difference_php($date);
                $resultpost[] = array(
                    'id' => $id,
                    'username' => $username,
                    'userimage' => $userimage,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'post_id' => $post_id,
                    'comment' => $comment,
                    'comment_date' => $comment_date
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    public function question_list($user_id,$child_id){
        $query = $this->db->query("SELECT * FROM child_care_question");
        $count = $query->num_rows();
        $items = $query->result_array();

             $id = '';   
             $ar = array();
              foreach ($items as $item) {
                
                if($item['question_type'] == 0){
                    
                    
                    $ar['question'] =  $item['question'];
                    $id = $item['id'];
                    $ar['que_id'] = $item['id'];
                    $ar['field_type'] = $item['field_type'];

                    $query = $this->db->query("SELECT * FROM child_care_question_answer WHERE question_id='$id' and user_id='$user_id' and child_id='$child_id'");
                    
                    $items2 = $query->row_array();
                   
                    $r = $this->sub($items, $id, $user_id,$child_id);
                    $ar['sub_que'] = $r;
              
                    $data[] = $ar;
                }
                   
              }
        //   die();
          
               return array(
                    'status' => 201,
                    'message' => 'success',
                    'data'=>$data
                );
              
              
              
    }
    function sub($items,$id,$user_id,$child_id){
        $ar1 =  array();
        $ar = array();      
       foreach ($items as $item) {
           $ar = array();  
       if($item['question_type'] == $id){
           
             $ar['question'] = $item['question'];
            $ID = $item['id'];
             $ar['que_id'] = $item['id'];
            $query = $this->db->query("SELECT * FROM child_care_question_answer WHERE question_id='$ID' and user_id='$user_id' and child_id='$child_id'");
            
          
            $items2 = $query->row_array();
            
            $r = $this->sub($items,$item['id'],$user_id,$child_id); 
            
            if(!empty($r)){
            $ar['sub_que'] = $r;
            }
            $ar['field_type'] = $item['field_type'];
            if($items2['answer'] == null)
                    {
                        $ar['ans'] = "";
                    }
                    else{
                        
                        $query12 = $this->db->query("SELECT id,user_id,question_id,GROUP_CONCAT(answer) as new_ans FROM child_care_question_answer WHERE  question_id='$ID' and user_id='$user_id' and child_id='$child_id'");
                      
                        $items23 = $query12->row_array();    
                        $ar['ans'] = $items23['new_ans'];
                    }
            $ar1[] = $ar;
        }
      }
      
      return $ar1;  
    }
}
