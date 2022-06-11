<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class SexeducationModel extends CI_Model {

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
                "notification_type" => 'sex_expert_notifications',
                "notification_date" => $date,
                "post_id" => $post_id
            )
        );
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
            $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
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
    
      public function get_stop_notification_for_user($user_id)
    {
         $query = $this->db->query("SELECT * FROM `notification_switch_control` WHERE user_id = '$user_id' and category = 'Ask expert'");
        $count = $query->num_rows();
        if($count > 0 )
        {
             $query1 = $this->db->query("SELECT * FROM `notification_switch_control` WHERE user_id = '$user_id' and category = 'Ask expert' and status = 'on'");
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

    public function kamasutra_category_list() {
        return $this->db->select('kamasutra_category.id as category_id,kamasutra_category.category,kamasutra_category.hindi_category,count(kamasutra_position_list.position_category) as total_count')->from('kamasutra_category')->join('kamasutra_position_list', 'kamasutra_category.id=kamasutra_position_list.position_category')->group_by('kamasutra_category.category')->order_by('kamasutra_category.id', 'asc')->get()->result();
    }

    public function kamasutra_pickup_lines() {
        $men_query = $this->db->query("select id,kama_text,kama_text_hindi from kamasutra_men_pickup_lines order by id asc");
        $women_query = $this->db->query("select id,kama_text,kama_text_hindi from kamasutra_women_pickup_lines order by id asc");

        foreach ($men_query->result_array() as $row) {
            $id = $row['id'];
            $pickup_lines = $row['kama_text'];
            $pickup_lines_hindi = $this->decrypt($row['kama_text_hindi']);
            $menresultpost[] = array('id' => $id, 'kama_text' => $pickup_lines, 'kama_text_hindi' => $pickup_lines_hindi);
        }

        foreach ($women_query->result_array() as $row) {
            $id = $row['id'];
            $pickup_lines = $row['kama_text'];
            $pickup_lines_hindi = $this->decrypt($row['kama_text_hindi']);
            $womenresultpost[] = array('id' => $id, 'kama_text' => $pickup_lines, 'kama_text_hindi' => $pickup_lines_hindi);
        }

        return array('status' => 200, 'message' => 'success', 'men' => $menresultpost, 'women' => $womenresultpost);
    }

    public function kamasutra_dirty_talks() {
        $men_query = $this->db->query("select id,kama_text,kama_text_hindi from kamasutra_men_dirty_talks order by id asc");
        $women_query = $this->db->query("select id,kama_text,kama_text_hindi from kamasutra_women_dirty_talks order by id asc");

        foreach ($men_query->result_array() as $row) {
            $id = $row['id'];
            $dirty_talks = $row['kama_text'];
            $dirty_talks_hindi = $this->decrypt($row['kama_text_hindi']);
            $menresultpost[] = array('id' => $id, 'kama_text' => $dirty_talks, 'kama_text_hindi' => $dirty_talks_hindi);
        }

        foreach ($women_query->result_array() as $row) {
            $id = $row['id'];
            $dirty_talks = $row['kama_text'];
            $dirty_talks_hindi = $this->decrypt($row['kama_text_hindi']);
            $womenresultpost[] = array('id' => $id, 'kama_text' => $dirty_talks, 'kama_text_hindi' => $dirty_talks_hindi);
        }

        return array('status' => 200, 'message' => 'success', 'men' => $menresultpost, 'women' => $womenresultpost);
    }

    /*public function kamasutra_dirty_talks_hindi() {
        $men_query = $this->db->query("select id,kama_text_hindi from kamasutra_men_dirty_talks order by id asc");
        $women_query = $this->db->query("select id,kama_text_hindi from kamasutra_women_dirty_talks order by id asc");

        foreach ($men_query->result_array() as $row) {
            $id = $row['id'];
            $dirty_talks = $this->decrypt($row['kama_text_hindi']);
            $menresultpost[] = array('id' => $id, 'kama_text_hindi' => $dirty_talks);
        }

        foreach ($women_query->result_array() as $row) {
            $id = $row['id'];
            $dirty_talks = $this->decrypt($row['kama_text_hindi']);
            $womenresultpost[] = array('id' => $id, 'kama_text_hindi' => $dirty_talks);
        }

        return array('status' => 200, 'message' => 'success', 'men' => $menresultpost, 'women' => $womenresultpost);
    }*/

    public function kamasutra_naughty_talks() {
        $men_query = $this->db->query("select id,kama_text,kama_text_hindi from kamasutra_men_naughty_talks order by id asc");
        $women_query = $this->db->query("select id,kama_text,kama_text_hindi from kamasutra_women_naughty_talks order by id asc");

        foreach ($men_query->result_array() as $row) {
            $id = $row['id'];
            $naughty_talks = $row['kama_text'];
            $naughty_talks_hindi = $this->decrypt($row['kama_text_hindi']);
            $menresultpost[] = array('id' => $id, 'kama_text' => $naughty_talks, 'kama_text_hindi' => $naughty_talks_hindi);
        }

        foreach ($women_query->result_array() as $row) {
            $id = $row['id'];
            $naughty_talks = $row['kama_text'];
            $naughty_talks_hindi = $this->decrypt($row['kama_text_hindi']);
            $womenresultpost[] = array('id' => $id, 'kama_text' => $naughty_talks, 'kama_text_hindi' => $naughty_talks_hindi);
        }

        return array('status' => 200, 'message' => 'success', 'men' => $menresultpost, 'women' => $womenresultpost);
    }
    
   /*  public function kamasutra_naughty_talks_hindi() {                                     
        $men_query = $this->db->query("select id,kama_text_hindi from kamasutra_men_naughty_talks order by id asc");
        $women_query = $this->db->query("select id,kama_text_hindi from kamasutra_women_naughty_talks order by id asc");

        foreach ($men_query->result_array() as $row) {
            $id = $row['id'];
            $naughty_talks = $this->decrypt($row['kama_text_hindi']);
            $menresultpost[] = array('id' => $id, 'kama_text_hindi' => $naughty_talks);
        }

        foreach ($women_query->result_array() as $row) {
            $id = $row['id'];
            $naughty_talks = $this->decrypt($row['kama_text_hindi']);
            $womenresultpost[] = array('id' => $id, 'kama_text_hindi' => $naughty_talks);
        }

        return array('status' => 200, 'message' => 'success', 'men' => $menresultpost, 'women' => $womenresultpost);
    }*/

    public function kamasutra_love_talks() {
        $men_query = $this->db->query("select id,kama_text,kama_text_hindi from kamasutra_men_love_talks order by id asc");
        $women_query = $this->db->query("select id,kama_text,kama_text_hindi from kamasutra_women_love_talks order by id asc");

        foreach ($men_query->result_array() as $row) {
            $id = $row['id'];
            $love_talks = $row['kama_text'];
            $love_talks_hindi = $this->decrypt($row['kama_text_hindi']);
            $menresultpost[] = array('id' => $id, 'kama_text' => $love_talks, 'kama_text_hindi' => $love_talks_hindi);
        }

        foreach ($women_query->result_array() as $row) {
            $id = $row['id'];
            $love_talks = $row['kama_text'];
            $love_talks_hindi = $this->decrypt($row['kama_text_hindi']);
            $womenresultpost[] = array('id' => $id, 'kama_text' => $love_talks, 'kama_text_hindi' => $love_talks_hindi);
        }

        return array('status' => 200, 'message' => 'success', 'men' => $menresultpost, 'women' => $womenresultpost);
    }
    
   /* public function kamasutra_love_talks_hindi() {
        $men_query = $this->db->query("select id,kama_text_hindi from kamasutra_men_love_talks order by id asc");
        $women_query = $this->db->query("select id,kama_text_hindi from kamasutra_women_love_talks order by id asc");

        foreach ($men_query->result_array() as $row) {
            $id = $row['id'];
            $love_talks = $this->decrypt($row['kama_text_hindi']);
            $menresultpost[] = array('id' => $id, 'kama_text_hindi' => $love_talks);
        }

        foreach ($women_query->result_array() as $row) {
            $id = $row['id'];
            $love_talks = $this->decrypt($row['kama_text_hindi']);
            $womenresultpost[] = array('id' => $id, 'kama_text_hindi' => $love_talks);
        }

        return array('status' => 200, 'message' => 'success', 'men' => $menresultpost, 'women' => $womenresultpost);
    }*/

    public function kamasutra_love_quotes() {
        //return $this->db->select('id,quote,author')->from('kamasutra_love_quotes')->order_by('id', 'asc')->get()->result();
        
        $women_query = $this->db->query("select id,quote,author,quote_hindi,author_hindi from kamasutra_love_quotes order by id asc");
       foreach ($women_query->result_array() as $row) {
            $id = $row['id'];
            $love_talks = $row['quote'];
            $author = $row['author'];
            
            $love_talks_hindi = $this->decrypt($row['quote_hindi']);
            $author_hindi = $this->decrypt($row['author_hindi']);
            
            $womenresultpost[] = array('id' => $id, 'quote' => $love_talks, 'author' =>$author, 'quote_hindi' => $love_talks_hindi, 'author_hindi' =>$author_hindi);
        }
        return $womenresultpost;
    }
    
   /*  public function kamasutra_love_quotes_hindi() {
      //  return $this->db->select('id,quote_hindi,author_hindi')->from('kamasutra_love_quotes')->order_by('id', 'asc')->get()->result();
      $women_query = $this->db->query("select id,quote_hindi,author_hindi from kamasutra_love_quotes order by id asc");
       foreach ($women_query->result_array() as $row) {
            $id = $row['id'];
            $love_talks = $this->decrypt($row['quote_hindi']);
            $author_hindi = $this->decrypt($row['author_hindi']);
            
            $womenresultpost[] = array('id' => $id, 'love_talks' => $love_talks, 'author_hindi' =>$author_hindi);
        }
        return $womenresultpost;
    }*/
    
    public function kamasutra_position_gif($position_id) {
        $gif_query = $this->db->select('regular_gif_image')->from('kamasutra_position_list')->where('id', $position_id)->get()->row();
        $gif_file = $gif_query->regular_gif_image;
        $webview_output = "<div><img src='https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/kamasutra_images/gif/" . $gif_file . "'/></div>";
        return $webview_output;
    }

    public function kamasutra_all_positions($user_id) {
        $query = $this->db->query("select kamasutra_position_list.id,kamasutra_position_list.position_category,kamasutra_position_list.kama_audio,kamasutra_category.category as position_category_name,kamasutra_category.hindi_category as hindi_position_category_name,kamasutra_position_list.position_name,kamasutra_position_list.hindi_position_name,kamasutra_position_list.hindi_position_tag,kamasutra_position_list.hindi_description_text,kamasutra_position_list.position_tag,kamasutra_position_list.position_description,kamasutra_position_list.regular_gif_image,kamasutra_position_list.regular_jpg_image,kamasutra_position_list.wild_gif_image from kamasutra_position_list INNER JOIN kamasutra_category on kamasutra_category.id=kamasutra_position_list.position_category order by kamasutra_position_list.id asc");

        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            $position_category = $row['position_category'];
            $kama_audio = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/kamasutra_images/audio/' . $row['kama_audio'];
            $position_category_name = $row['position_category_name'];
            $position_name = $row['position_name'];
            $position_tag = $row['position_tag'];
            $position_description = $row['position_description'];
            $hindi_kama_audio = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/kamasutra_images/hindiaudio/' . $row['kama_audio'];
            $hindi_position_category_name = $row['hindi_position_category_name'];
            $hindi_position_name = $row['hindi_position_name'];
            $hindi_position_tag = $row['hindi_position_tag'];
            $hindi_description_text = $row['hindi_description_text'];
           
            $regular_gif_image = $row['regular_gif_image'];
            $regular_jpg_image = $row['regular_jpg_image'];
            $wild_gif_image = $row['wild_gif_image'];

            $to_do_yes_no = $this->db->select('id')->from('kamasutra_to_do')->where('user_id', $user_id)->where('position_id', $id)->get()->num_rows();
            $favourite_yes_no = $this->db->select('id')->from('kamasutra_favourite')->where('user_id', $user_id)->where('position_id', $id)->get()->num_rows();
            $tried_yes_no = $this->db->select('id')->from('kamasutra_tried')->where('user_id', $user_id)->where('position_id', $id)->get()->num_rows();

            $resultpost[] = array('id' => $id, 
                'position_category' => $position_category, 
                'kama_audio' => $kama_audio, 
                'position_category_name' => $position_category_name, 
                'position_name' => $position_name, 
                'position_tag' => $position_tag, 
                'position_description' => $position_description, 
                'hindi_position_category_name' => $hindi_position_category_name,
                'hindi_kama_audio' => $hindi_kama_audio,
                 'hindi_position_name' => $hindi_position_name, 
                 'hindi_position_tag' => $hindi_position_tag, 
                 'hindi_description_text' => $hindi_description_text, 
                'regular_gif_image' => $regular_gif_image, 
                'regular_jpg_image' => $regular_jpg_image, 
                'wild_gif_image' => $wild_gif_image, 
                'to_do_yes_no' => $to_do_yes_no, 
                'favourite_yes_no' => $favourite_yes_no, 
                'tried_yes_no' => $tried_yes_no);
        }
        return $resultpost;
    }

    public function kamasutra_position_list($category_id, $user_id) {
        $query = $this->db->query("select kamasutra_position_list.id,kamasutra_position_list.position_category,kamasutra_position_list.kama_audio,kamasutra_category.category as position_category_name,kamasutra_category.hindi_category as hindi_position_category_name,kamasutra_position_list.position_name,kamasutra_position_list.position_tag,kamasutra_position_list.position_description,kamasutra_position_list.hindi_position_name,kamasutra_position_list.hindi_position_tag,kamasutra_position_list.hindi_description_text,kamasutra_position_list.regular_gif_image,kamasutra_position_list.regular_jpg_image,kamasutra_position_list.wild_gif_image from kamasutra_position_list INNER JOIN kamasutra_category on kamasutra_category.id=kamasutra_position_list.position_category where kamasutra_position_list.position_category='$category_id' order by kamasutra_position_list.id asc");

        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            $position_category = $row['position_category'];
            $kama_audio = 'https://s3.amazonaws.com/medicalwale/images/sex_education_images/kamasutra_images/audio/' . $row['kama_audio'];
            $position_category_name = $row['position_category_name'];
            $position_name = $row['position_name'];
            $position_tag = $row['position_tag'];
            $position_description = $row['position_description'];
            $hindi_kama_audio = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/kamasutra_images/hindiaudio/' . $row['kama_audio'];
            $hindi_position_category_name = $row['hindi_position_category_name'];
            $hindi_position_name = $row['hindi_position_name'];
            $hindi_position_tag = $row['hindi_position_tag'];
            $hindi_description_text = $row['hindi_description_text'];
            
            $regular_gif_image = $row['regular_gif_image'];
            $regular_jpg_image = $row['regular_jpg_image'];
            $wild_gif_image = $row['wild_gif_image'];

            $to_do_yes_no = $this->db->select('id')->from('kamasutra_to_do')->where('user_id', $user_id)->where('position_id', $id)->get()->num_rows();
            $favourite_yes_no = $this->db->select('id')->from('kamasutra_favourite')->where('user_id', $user_id)->where('position_id', $id)->get()->num_rows();
            $tried_yes_no = $this->db->select('id')->from('kamasutra_tried')->where('user_id', $user_id)->where('position_id', $id)->get()->num_rows();

            $resultpost[] = array('id' => $id, 'position_category' => $position_category, 'position_category_name' => $position_category_name, 'position_name' => $position_name, 'position_tag' => $position_tag, 'position_description' => $position_description,'hindi_position_category_name' => $hindi_position_category_name, 'hindi_position_name' => $hindi_position_name, 'hindi_position_tag' => $hindi_position_tag, 'hindi_description_text' => $hindi_description_text, 'regular_gif_image' => $regular_gif_image,
            'hindi_kama_audio' => $hindi_kama_audio,'regular_jpg_image' => $regular_jpg_image, 'wild_gif_image' => $wild_gif_image, 'to_do_yes_no' => $to_do_yes_no, 'favourite_yes_no' => $favourite_yes_no, 'tried_yes_no' => $tried_yes_no, 'kama_audio' => $kama_audio);
        }
        return $resultpost;
    }
    

    public function kamasutra_sex_tips() {
        //return $this->db->select('id,tips,description')->from('kamasutra_sex_tips')->order_by('id', 'asc')->get()->result();
        
        
        
        $men_query = $this->db->query("select id,tips,description,tips_hindi,description_hindi from kamasutra_sex_tips order by id asc");
     

        foreach ($men_query->result_array() as $row) {
            $id = $row['id'];
            $tips = $row['tips'];
            $description = $row['description'];
            $tips_hindi = $this->decrypt($row['tips_hindi']);
            $description_hindi = $this->decrypt($row['description_hindi']);
            //$love_talks_hindi = $this->decrypt($row['kama_text_hindi']);
            $menresultpost[] = array('id' => $id, 'tips' => $tips, 'description' => $description, 'tips_hindi' => $tips_hindi, 'description_hindi' => $description_hindi);
        }

       

        return $menresultpost;
    }

    public function kamasutra_to_do_create($user_id, $position_id) {
        $count_user1 = $this->db->select('id')->from('kamasutra_to_do')->where('user_id', $user_id)->get()->num_rows();
        $count_user2 = $this->db->select('id')->from('kamasutra_to_do')->where('position_id', $position_id)->get()->num_rows();

        if ($count_user1 > 0 && $count_user2 > 0) {
            $this->db->query("DELETE FROM `kamasutra_to_do` WHERE user_id='$user_id' and position_id='$position_id'");
            return array('status' => 210, 'message' => 'deleted');
        } else {
            $to_do_data = array(
                'user_id' => $user_id,
                'position_id' => $position_id
            );
            $this->db->insert('kamasutra_to_do', $to_do_data);
            return array('status' => 201, 'message' => 'success');
        }
    }

    public function kamasutra_to_do_list($user_id) {
        $query = $this->db->query("select kamasutra_position_list.id,kamasutra_position_list.position_category,kamasutra_position_list.kama_audio,kamasutra_category.category as position_category_name,kamasutra_category.hindi_category as hindi_position_category_name,kamasutra_position_list.position_name,kamasutra_position_list.position_tag,kamasutra_position_list.position_description,kamasutra_position_list.hindi_position_name,kamasutra_position_list.hindi_position_tag,kamasutra_position_list.hindi_description_text,kamasutra_position_list.regular_gif_image,kamasutra_position_list.regular_jpg_image,kamasutra_position_list.wild_gif_image from kamasutra_position_list INNER JOIN kamasutra_category on kamasutra_category.id=kamasutra_position_list.position_category INNER JOIN kamasutra_to_do on kamasutra_to_do.position_id=kamasutra_position_list.id where kamasutra_to_do.user_id='$user_id' order by kamasutra_position_list.id asc");
        $to_do_count = $query->num_rows();
        if ($to_do_count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $position_category = $row['position_category'];
                $kama_audio = 'https://s3.amazonaws.com/medicalwale/images/sex_education_images/kamasutra_images/audio/' . $row['kama_audio'];
                $position_category_name = $row['position_category_name'];
                $position_name = $row['position_name'];
                $position_tag = $row['position_tag'];
                $position_description = $row['position_description'];
                
                $hindi_position_category_name = $row['hindi_position_category_name'];
                $hindi_position_name = $row['hindi_position_name'];
                $hindi_position_tag = $row['hindi_position_tag'];
                $hindi_description_text = $row['hindi_description_text'];
                
                $regular_gif_image = $row['regular_gif_image'];
                $regular_jpg_image = $row['regular_jpg_image'];
                $wild_gif_image = $row['wild_gif_image'];

                $to_do_yes_no = $this->db->select('id')->from('kamasutra_to_do')->where('user_id', $user_id)->where('position_id', $id)->get()->num_rows();
                $favourite_yes_no = $this->db->select('id')->from('kamasutra_favourite')->where('user_id', $user_id)->where('position_id', $id)->get()->num_rows();
                $tried_yes_no = $this->db->select('id')->from('kamasutra_tried')->where('user_id', $user_id)->where('position_id', $id)->get()->num_rows();

                $resultpost[] = array('id' => $id, 'position_category' => $position_category, 'position_category_name' => $position_category_name, 'kama_audio' => $kama_audio, 'position_name' => $position_name, 'position_tag' => $position_tag, 'position_description' => $position_description,'hindi_position_category_name' => $hindi_position_category_name, 'hindi_position_name' => $hindi_position_name, 'hindi_position_tag' => $hindi_position_tag, 'hindi_description_text' => $hindi_description_text, 'regular_gif_image' => $regular_gif_image, 'regular_jpg_image' => $regular_jpg_image, 'wild_gif_image' => $wild_gif_image, 'to_do_yes_no' => $to_do_yes_no, 'favourite_yes_no' => $favourite_yes_no, 'tried_yes_no' => $tried_yes_no);
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }

    public function kamasutra_favourite_create($user_id, $position_id) {
        $count_user1 = $this->db->select('id')->from('kamasutra_favourite')->where('user_id', $user_id)->get()->num_rows();
        $count_user2 = $this->db->select('id')->from('kamasutra_favourite')->where('position_id', $position_id)->get()->num_rows();

        if ($count_user1 > 0 && $count_user2 > 0) {
            $this->db->query("DELETE FROM `kamasutra_favourite` WHERE user_id='$user_id' and position_id='$position_id'");
            return array('status' => 210, 'message' => 'deleted');
        } else {
            $favourite_data = array(
                'user_id' => $user_id,
                'position_id' => $position_id
            );
            $this->db->insert('kamasutra_favourite', $favourite_data);
            return array('status' => 201, 'message' => 'success');
        }
    }

    public function kamasutra_favourite_list($user_id) {
        $query = $this->db->query("select kamasutra_position_list.id,kamasutra_position_list.position_category,kamasutra_position_list.kama_audio,kamasutra_category.category as position_category_name,kamasutra_category.hindi_category as hindi_position_category_name,kamasutra_position_list.position_name,kamasutra_position_list.position_tag,kamasutra_position_list.position_description,kamasutra_position_list.hindi_position_name,kamasutra_position_list.hindi_position_tag,kamasutra_position_list.hindi_description_text,kamasutra_position_list.regular_gif_image,kamasutra_position_list.regular_jpg_image,kamasutra_position_list.wild_gif_image from kamasutra_position_list INNER JOIN kamasutra_category on kamasutra_category.id=kamasutra_position_list.position_category INNER JOIN kamasutra_favourite on kamasutra_favourite.position_id=kamasutra_position_list.id where kamasutra_favourite.user_id='$user_id' order by kamasutra_position_list.id asc");

        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            $position_category = $row['position_category'];
            $kama_audio = 'https://s3.amazonaws.com/medicalwale/images/sex_education_images/kamasutra_images/audio/' . $row['kama_audio'];
            $position_category_name = $row['position_category_name'];
            $position_name = $row['position_name'];
            $position_tag = $row['position_tag'];
            $position_description = $row['position_description'];
            
            $hindi_position_category_name = $row['hindi_position_category_name'];
            $hindi_position_name = $row['hindi_position_name'];
            $hindi_position_tag = $row['hindi_position_tag'];
            $hindi_description_text = $row['hindi_description_text'];
            
            
            $regular_gif_image = $row['regular_gif_image'];
            $regular_jpg_image = $row['regular_jpg_image'];
            $wild_gif_image = $row['wild_gif_image'];

            $to_do_yes_no = $this->db->select('id')->from('kamasutra_to_do')->where('user_id', $user_id)->where('position_id', $id)->get()->num_rows();
            $favourite_yes_no = $this->db->select('id')->from('kamasutra_favourite')->where('user_id', $user_id)->where('position_id', $id)->get()->num_rows();
            $tried_yes_no = $this->db->select('id')->from('kamasutra_tried')->where('user_id', $user_id)->where('position_id', $id)->get()->num_rows();

            $resultpost[] = array('id' => $id, 'position_category' => $position_category, 'kama_audio' => $kama_audio, 'position_category_name' => $position_category_name, 'position_name' => $position_name, 'position_tag' => $position_tag, 'position_description' => $position_description,'hindi_position_category_name' => $hindi_position_category_name, 'hindi_position_name' => $hindi_position_name, 'hindi_position_tag' => $hindi_position_tag, 'hindi_description_text' => $hindi_description_text, 'regular_gif_image' => $regular_gif_image, 'regular_jpg_image' => $regular_jpg_image, 'wild_gif_image' => $wild_gif_image, 'to_do_yes_no' => $to_do_yes_no, 'favourite_yes_no' => $favourite_yes_no, 'tried_yes_no' => $tried_yes_no);
        }
        return $resultpost;
    }

    public function kamasutra_tried_create($user_id, $position_id) {
        $count_user1 = $this->db->select('id')->from('kamasutra_tried')->where('user_id', $user_id)->get()->num_rows();
        $count_user2 = $this->db->select('id')->from('kamasutra_tried')->where('position_id', $position_id)->get()->num_rows();

        if ($count_user1 > 0 && $count_user2 > 0) {
            $this->db->query("DELETE FROM `kamasutra_tried` WHERE user_id='$user_id' and position_id='$position_id'");
            return array('status' => 210, 'message' => 'deleted');
        } else {
            $tried_data = array(
                'user_id' => $user_id,
                'position_id' => $position_id
            );
            $this->db->insert('kamasutra_tried', $tried_data);
            return array('status' => 201, 'message' => 'success');
        }
    }

    public function kamasutra_tried_list($user_id) {
        $query = $this->db->query("select kamasutra_position_list.id,kamasutra_position_list.position_category,kamasutra_position_list.kama_audio,kamasutra_category.category as position_category_name,kamasutra_category.hindi_category as hindi_position_category_name,kamasutra_position_list.position_name,kamasutra_position_list.position_tag,kamasutra_position_list.position_description,kamasutra_position_list.hindi_position_name,kamasutra_position_list.hindi_position_tag,kamasutra_position_list.hindi_description_text,kamasutra_position_list.regular_gif_image,kamasutra_position_list.regular_jpg_image,kamasutra_position_list.wild_gif_image from kamasutra_position_list INNER JOIN kamasutra_category on kamasutra_category.id=kamasutra_position_list.position_category INNER JOIN kamasutra_tried on kamasutra_tried.position_id=kamasutra_position_list.id where kamasutra_tried.user_id='$user_id' order by kamasutra_position_list.id asc");

        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            $position_category = $row['position_category'];
            $kama_audio = 'https://s3.amazonaws.com/medicalwale/images/sex_education_images/kamasutra_images/audio/' . $row['kama_audio'];
            $position_category_name = $row['position_category_name'];
            $position_name = $row['position_name'];
            $position_tag = $row['position_tag'];
            $position_description = $row['position_description'];
            
            $hindi_position_category_name = $row['hindi_position_category_name'];
            $hindi_position_name = $row['hindi_position_name'];
            $hindi_position_tag = $row['hindi_position_tag'];
            $hindi_description_text = $row['hindi_description_text'];
            
            $regular_gif_image = $row['regular_gif_image'];
            $regular_jpg_image = $row['regular_jpg_image'];
            $wild_gif_image = $row['wild_gif_image'];

            $to_do_yes_no = $this->db->select('id')->from('kamasutra_to_do')->where('user_id', $user_id)->where('position_id', $id)->get()->num_rows();
            $favourite_yes_no = $this->db->select('id')->from('kamasutra_favourite')->where('user_id', $user_id)->where('position_id', $id)->get()->num_rows();
            $tried_yes_no = $this->db->select('id')->from('kamasutra_tried')->where('user_id', $user_id)->where('position_id', $id)->get()->num_rows();

            $resultpost[] = array('id' => $id, 'position_category' => $position_category, 'position_category_name' => $position_category_name, 'kama_audio' => $kama_audio, 'position_name' => $position_name, 'position_tag' => $position_tag, 'position_description' => $position_description, 'hindi_position_category_name' => $hindi_position_category_name, 'hindi_position_name' => $hindi_position_name, 'hindi_position_tag' => $hindi_position_tag, 'hindi_description_text' => $hindi_description_text, 'regular_gif_image' => $regular_gif_image, 'regular_jpg_image' => $regular_jpg_image, 'wild_gif_image' => $wild_gif_image, 'to_do_yes_no' => $to_do_yes_no, 'favourite_yes_no' => $favourite_yes_no, 'tried_yes_no' => $tried_yes_no);
        }
        return $resultpost;
    }

    public function kamasutra_flag() {
        return $this->db->select('sex_tips_flag,position_list_flag')->from('kamasutra_flag')->get()->row();
    }

    /* SEX STORE STARTS   */

    public function sex_store_home() {
        $resultcharacter_new_arrival = array();
        $resultcharacter_best_seller = array();

        $banner_query = $this->db->query("SELECT GROUP_CONCAT(CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/sex_store_images/',images))  AS images FROM `sex_store_banner`");
        $banner_list = $banner_query->row();

        if ($banner_list) {
            $image_banner = $banner_list->images;
        } else {
            $image_banner = '';
        }


        $sex_store_new_arrival = $this->db->query("SELECT sex_store_products.description,sex_store_category.id AS category_id,sex_store_category.category,sex_store_subcategory.id AS sub_category_id,sex_store_subcategory.subcategory,sex_store_products.id AS product_id,sex_store_products.name AS product_name,sex_store_products.image1,sex_store_products.image2,sex_store_products.image3,sex_store_products.price,sex_store_products.discount,sex_store_products.availibility,sex_store_products.description,sex_store_products.how_to_use_app,sex_store_products.specification FROM `sex_store_category`
INNER JOIN `sex_store_subcategory` ON sex_store_category.id=sex_store_subcategory.category_id
INNER JOIN sex_store_products ON sex_store_products.sub_category_id=sex_store_subcategory.id ORDER BY sex_store_products.id desc LIMIT 0,12");

        $sex_store_best_seller = $this->db->query("SELECT sex_store_products.description,sex_store_category.id AS category_id,sex_store_category.category,sex_store_subcategory.id AS sub_category_id,sex_store_subcategory.subcategory,sex_store_products.id AS product_id,sex_store_products.name AS product_name,sex_store_products.image1,sex_store_products.image2,sex_store_products.image3,sex_store_products.price,sex_store_products.discount,sex_store_products.availibility,sex_store_products.description,sex_store_products.how_to_use_app,sex_store_products.specification FROM `sex_store_category`
INNER JOIN `sex_store_subcategory` ON sex_store_category.id=sex_store_subcategory.category_id
INNER JOIN sex_store_products ON sex_store_products.sub_category_id=sex_store_subcategory.id WHERE sex_store_products.is_bestseller='1' ORDER BY sex_store_products.id desc LIMIT 0,12");

        $sex_store_count1 = $sex_store_new_arrival->num_rows();
        $sex_store_count2 = $sex_store_best_seller->num_rows();

        $sex_store_count = ($sex_store_count1) + ($sex_store_count2);

        if ($sex_store_count > 0) {
            foreach ($sex_store_new_arrival->result_array() as $row) {

                $category_id = $row['category_id'];
                $sub_category_id = $row['sub_category_id'];
                $product_id = $row['product_id'];
                $product_code = 'THP' . $category_id . 'SC' . $sub_category_id . '000' . $product_id; //RH-Thatspersonal + cat_id +SC +000 prod_id
                $category = $row['category'];
                $description = $row['description'];
                $subcategory = $row['subcategory'];
                $product_name = $row['product_name'];
                $img1 = '';
                $img2 = '';
                $img3 = '';

                $img1 = $row['image1'];
                $img2 = $row['image2'];
                $img3 = $row['image3'];

                if ($img1 != '') {
                    $image1 = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/sex_store_images/' . $row['image1'] . ',';
                } else {
                    $image1 = '';
                }
                if ($img2 != '') {
                    $image2 = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/sex_store_images/' . $row['image2'] . ',';
                } else {
                    $image2 = '';
                }
                if ($img3 != '') {
                    $image3 = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/sex_store_images/' . $row['image3'];
                } else {
                    $image3 = '';
                }


                $image = $image1 . $image2 . $image3;
                $image = rtrim($image, ',');
                $price = $row['price'];
                $availibility = $row['availibility'];
                $discount = $row['discount'];
                $discount_price = $price - ($price * ( $discount / 100));
                $discount_price = (string) $discount_price;
                $rating = '4';
                $review = '0';
                $description = str_replace('’', "'", $description);
                $description = "<style>strong{color:#404547;font-weight:400}p{color:#768188;text-align:justify;}</style>" . $row['description'];
                $how_to_use = $row['how_to_use_app'];
                $specification = $row['specification'];

                $sex_store_product_view_query = $this->db->query("SELECT id FROM `sex_store_product_view` where product_id='$product_id'");
                $product_view = $sex_store_product_view_query->num_rows();

                $resultpost_new_arrival[] = array(
                    "category_id" => $category_id,
                    "sub_category_id" => $sub_category_id,
                    "product_id" => $product_id,
                    "product_code" => $product_code,
                    "product_name" => $product_name,
                    "image" => $image,
                    "availibility" => $availibility,
                    "price" => $price,
                    "discount" => $discount,
                    "discount_price" => $discount_price,
                    "rating" => $rating,
                    "review" => $review,
                    "description" => $description,
                    "how_to_use" => $how_to_use,
                    'product_view' => $product_view);
            }
            $product_view = '0';

            foreach ($sex_store_best_seller->result_array() as $info) {

                $category_id = $info['category_id'];
                $sub_category_id = $info['sub_category_id'];
                $product_id = $info['product_id'];
                $product_code = 'THP' . $category_id . 'SC' . $sub_category_id . '000' . $product_id; //RH-Thatspersonal + cat_id +SC +000 prod_id
                $category = $info['category'];
                $subcategory = $info['subcategory'];
                $product_name = $info['product_name'];
                $img1 = '';
                $img2 = '';
                $img3 = '';

                $img1 = $info['image1'];
                $img2 = $info['image2'];
                $img3 = $info['image3'];

                if ($img1 != '') {
                    $image1 = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/sex_store_images/' . $info['image1'] . ',';
                } else {
                    $image1 = '';
                }
                if ($img2 != '') {

                    $image2 = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/sex_store_images/' . $info['image2'] . ',';
                } else {
                    $image2 = '';
                }
                if ($img3 != '') {
                    $image3 = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/sex_store_images/' . $info['image3'];
                } else {
                    $image3 = '';
                }


                $image = $image1 . $image2 . $image3;
                $image = rtrim($image, ',');
                $price = $info['price'];
                $availibility = $info['availibility'];
                $discount = $info['discount'];
                $discount_price = $price - ($price * ( $discount / 100));
                $discount_price = (string) $discount_price;
                $rating = '4';
                $review = '0';
                $description = str_replace('’', "'", $description);
                $description = "<style>strong{color:#404547;font-weight:400}p{color:#768188;text-align:justify;}</style>" . $info['description'];
                $how_to_use = $info['how_to_use_app'];
                $specification = $info['specification'];

                $sex_store_product_view_query = $this->db->query("SELECT id FROM `sex_store_product_view` where product_id='$product_id'");
                $product_view = $sex_store_product_view_query->num_rows();

                $resultpost_best_seller[] = array(
                    "category_id" => $category_id,
                    "sub_category_id" => $sub_category_id,
                    "product_id" => $product_id,
                    "product_code" => $product_code,
                    "product_name" => $product_name,
                    "image" => $image,
                    "availibility" => $availibility,
                    "price" => $price,
                    "discount" => $discount,
                    "discount_price" => $discount_price,
                    "rating" => $rating,
                    "review" => $review,
                    "description" => $description,
                    "how_to_use" => $how_to_use,
                    'product_view' => $product_view);
            }

            return array('status' => 200, 'message' => 'success', "count_new_arrival" => sizeof($resultpost_new_arrival), "count_best_seller" => sizeof($resultpost_best_seller), "image_banner" => $image_banner, "data_new_arrival" => $resultpost_new_arrival, "data_best_seller" => $resultpost_best_seller);
        } else {
            return array("status" => 404, "message" => "failure", "count" => 0);
        }
    }

    public function sex_store_category() {
        $query = $this->db->query("SELECT id,category,image FROM `sex_store_category` order by id asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $category = $row['category'];
                $image = $row['image'];
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/sex_store_images/' . $image;

                $resultpost[] = array(
                    "category_id" => $id,
                    "category" => $category,
                    'image' => $image
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function sex_store_subcategory($category_id) {
        $query = $this->db->query("SELECT id,category_id,subcategory FROM `sex_store_subcategory` WHERE category_id='$category_id' order by id asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $subcategory = $row['subcategory'];

                $resultpost[] = array(
                    "sub_category_id" => $id,
                    "subcategory" => $subcategory,
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function sex_store_products($sub_category_id) {
        $query = $this->db->query("SELECT sex_store_products.description,sex_store_category.id AS category_id,sex_store_category.category,sex_store_subcategory.id AS sub_category_id,sex_store_subcategory.subcategory,sex_store_products.id AS product_id,sex_store_products.name AS product_name,sex_store_products.image1,sex_store_products.image2,sex_store_products.image3,sex_store_products.price,sex_store_products.discount,sex_store_products.availibility,sex_store_products.description,sex_store_products.how_to_use_app,sex_store_products.specification FROM `sex_store_category`
		INNER JOIN `sex_store_subcategory` ON sex_store_category.id=sex_store_subcategory.category_id
		INNER JOIN sex_store_products ON sex_store_products.sub_category_id=sex_store_subcategory.id
		WHERE sex_store_products.sub_category_id='$sub_category_id' ORDER BY sex_store_products.id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            $count = $query->num_rows();
            foreach ($query->result_array() as $row) {


                $category_id = $row['category_id'];
                $sub_category_id = $row['sub_category_id'];
                $product_id = $row['product_id'];
                $product_code = 'THP' . $category_id . 'SC' . $sub_category_id . '000' . $product_id; //RH-Thatspersonal + cat_id +SC +000 prod_id
                $category = $row['category'];
                $description = $row['description'];
                $subcategory = $row['subcategory'];
                $product_name = $row['product_name'];
                $img1 = '';
                $img2 = '';
                $img3 = '';

                $img1 = $row['image1'];
                $img2 = $row['image2'];
                $img3 = $row['image3'];

                if ($img1 != '') {
                    $image1 = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/sex_store_images/' . $row['image1'] . ',';
                } else {
                    $image1 = '';
                }
                if ($img2 != '') {
                    $image2 = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/sex_store_images/' . $row['image2'] . ',';
                } else {
                    $image2 = '';
                }
                if ($img3 != '') {
                    $image3 = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/sex_store_images/' . $row['image3'];
                } else {
                    $image3 = '';
                }


                $image = $image1 . $image2 . $image3;
                $image = rtrim($image, ',');
                $price = $row['price'];
                $availibility = $row['availibility'];
                $discount = $row['discount'];
                $discount_price = $price - ($price * ( $discount / 100));
                $discount_price = (string) $discount_price;
                $rating = '4';
                $review = '0';
                $description = str_replace('’', "'", $description);
                $description = "<style>strong{color:#404547;font-weight:400}p{color:#768188;text-align:justify;}</style>" . $row['description'];
                $how_to_use = $row['how_to_use_app'];
                $specification = $row['specification'];

                $sex_store_product_view_query = $this->db->query("SELECT id FROM `sex_store_product_view` where product_id='$product_id'");
                $product_view = $sex_store_product_view_query->num_rows();



                $resultpost[] = array(
                    "category_id" => $category_id,
                    "sub_category_id" => $sub_category_id,
                    "product_id" => $product_id,
                    "product_code" => $product_code,
                    "product_name" => $product_name,
                    "image" => $image,
                    "availibility" => $availibility,
                    "price" => $price,
                    "discount" => $discount,
                    "discount_price" => $discount_price,
                    "rating" => $rating,
                    "review" => $review,
                    "description" => $description,
                    "how_to_use" => $how_to_use,
                    'product_view' => $product_view);
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function sex_store_related_prod($product_id, $category_id, $sub_category_id) {
        $query = $this->db->query("SELECT sex_store_products.description,sex_store_category.id AS category_id,sex_store_category.category,sex_store_subcategory.id AS sub_category_id,sex_store_subcategory.subcategory,sex_store_products.id AS product_id,sex_store_products.name AS product_name,sex_store_products.image1,sex_store_products.image2,sex_store_products.image3,sex_store_products.price,sex_store_products.discount,sex_store_products.availibility,sex_store_products.description,sex_store_products.how_to_use_app,sex_store_products.specification FROM `sex_store_category`
		INNER JOIN `sex_store_subcategory` ON sex_store_category.id=sex_store_subcategory.category_id
		INNER JOIN sex_store_products ON sex_store_products.sub_category_id=sex_store_subcategory.id
		WHERE sex_store_products.sub_category_id='$sub_category_id' AND sex_store_products.id<>'$product_id' ORDER BY sex_store_products.id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            $count = $query->num_rows();
            foreach ($query->result_array() as $row) {


                $category_id = $row['category_id'];
                $sub_category_id = $row['sub_category_id'];
                $product_id = $row['product_id'];
                $product_code = 'THP' . $category_id . 'SC' . $sub_category_id . '000' . $product_id; //RH-Thatspersonal + cat_id +SC +000 prod_id
                $category = $row['category'];
                $description = $row['description'];
                $subcategory = $row['subcategory'];
                $product_name = $row['product_name'];
                $img1 = '';
                $img2 = '';
                $img3 = '';

                $img1 = $row['image1'];
                $img2 = $row['image2'];
                $img3 = $row['image3'];

                if ($img1 != '') {
                    $image1 = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/sex_store_images/' . $row['image1'] . ',';
                } else {
                    $image1 = '';
                }
                if ($img2 != '') {
                    $image2 = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/sex_store_images/' . $row['image2'] . ',';
                } else {
                    $image2 = '';
                }
                if ($img3 != '') {
                    $image3 = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/sex_store_images/' . $row['image3'];
                } else {
                    $image3 = '';
                }

                $image = $image1 . $image2 . $image3;
                $image = rtrim($image, ',');
                $price = $row['price'];
                $availibility = $row['availibility'];
                $discount = $row['discount'];
                $discount_price = $price - ($price * ( $discount / 100));
                $discount_price = (string) $discount_price;
                $rating = '4';
                $review = '0';
                $description = str_replace('’', "'", $description);
                $description = "<style>strong{color:#404547;font-weight:400}p{color:#768188;text-align:justify;}</style>" . $row['description'];
                $how_to_use = $row['how_to_use_app'];
                $specification = $row['specification'];

                $sex_store_product_view_query = $this->db->query("SELECT id FROM `sex_store_product_view` where product_id='$product_id'");
                $product_view = $sex_store_product_view_query->num_rows();

                $resultpost[] = array(
                    "category_id" => $category_id,
                    "sub_category_id" => $sub_category_id,
                    "product_id" => $product_id,
                    "product_code" => $product_code,
                    "product_name" => $product_name,
                    "image" => $image,
                    "availibility" => $availibility,
                    "price" => $price,
                    "discount" => $discount,
                    "discount_price" => $discount_price,
                    "rating" => $rating,
                    "review" => $review,
                    "description" => $description,
                    "how_to_use" => $how_to_use,
                    'product_view' => $product_view);
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function sex_store_about_us() {
        $sex_store_array = array();
        $about_query = $this->db->query("SELECT about_us FROM `sex_store_aboutus`");
        $sex_store_count1 = $about_query->num_rows();

        $gallery_query = $this->db->query("SELECT GROUP_CONCAT(`title`) AS title, GROUP_CONCAT(CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/sex_store_images/',media)) AS media FROM `sex_store_gallery`");
        if ($sex_store_count1 > 0) {

            $row = $about_query->row();
            $row2 = $gallery_query->row();

            $about_us = $row->about_us;
            $images = $row2->media;
            $image_title = $row2->title;

            $sex_store_array[] = array(
                'images' => $images,
                'image_title' => $image_title);


            $followers = '221';
            $following = '471';
            $rating = '4.3';
            $profile_views = '244';
            $reviews = '240';
            $is_follow = 'no';

            return array("status" => 200, "message" => "success", "count" => sizeof($sex_store_array), 'about_us' => $about_us, 'rating' => $rating, 'followers' => $followers, 'following' => $following, 'profile_views' => $profile_views, 'reviews' => $reviews, 'is_follow' => $is_follow, "data" => $sex_store_array);
        } else {
            return array("status" => 404, "message" => "failure", "count" => 0);
        }
    }

    public function sex_store_contact_us($user_id, $name, $message, $mobile) {

        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $sex_store_contactus_data = array(
            'user_id' => $user_id,
            'name' => $name,
            'message' => $message,
            'mobile' => $mobile,
            'date' => $date
        );
        $insert = $this->db->insert('sex_store_contactus', $sex_store_contactus_data);

        if ($insert) {
            return array('status' => 201, 'message' => 'success');
        } else {

            return array('status' => 404, 'message' => 'failure');
        }
    }

    public function sex_store_country() {
        $query = $this->db->query("SELECT id,country FROM sex_store_country order by country asc");
        $count = $query->num_rows();
        if ($count > 0) {

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $country = $row['country'];

                $resultpost[] = array(
                    "id" => $id,
                    "country" => $country);
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function sex_store_state($country) {
        $query = $this->db->query("SELECT id,city as state FROM sex_store_city where country='$country' order by city asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $state = $row['state'];

                $resultpost[] = array(
                    "id" => $id,
                    "state" => $state);
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function sex_store_get_quotes($pincode) {
        $query = $this->db->query("SELECT id,pincode FROM sex_store_pincode where pincode='$pincode' order by pincode asc");
        $pincode_count = $query->num_rows();
        if ($pincode_count > 0) {

            $resultpincode = '100';
            return array("status" => 200, "message" => "success", "delivery" => $resultpincode);
        } else {
            return array("status" => 404, "message" => "failure");
        }
    }

    public function sex_store_pincode_check($pincode) {
        $query = $this->db->query("select id from sex_store_pincode WHERE pincode='$pincode' limit 1");
        $pincode_count = $query->num_rows();
        if ($pincode_count > 0) {


            return array("status" => 200, "message" => "success");
        } else {
            return array("status" => 404, "message" => "failure");
        }
    }

    public function sex_store_cart_order($user_id, $address_id, $product_id, $product_quantity, $product_price) {

        $status = "Pending";
        $product_status = 'Pending';

        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d');
        $b = date("Y");
        $c = date("m");
        $d = date("d");
        $e = date("H");
        $f = date("i");
        $g = date("s");
        $uni_id = $b . $c . $d . $e . $f . $g;

        $discount = '0';
        $grand_total = '0';
        $final_total = '0';
        $discount_rate = '0';
        $payType = '0';
        $store_status = '0';
        $customer_status = '0';


        $product_id = explode(",", $product_id);
        $product_quantity = explode(",", $product_quantity);
        $product_price = explode(",", $product_price);
        $cnt = count($product_id);
        for ($i = 0; $i < $cnt; $i++) {
            $final_total = $final_total + ($product_price[$i] * $product_quantity[$i]);
        }
        $grand_total = $final_total;


        $address_query = $this->db->query("SELECT name,mobile,pincode,address1,address2,landmark,city,state FROM `user_address` WHERE address_id='$address_id' AND user_id='$user_id' ");

        $get_list = $address_query->row();


        if ($get_list) {
            $name = $get_list->name;
            $mobile = $get_list->mobile;
            $pincode = $get_list->pincode;
            $address1 = $get_list->address1;
            $address2 = $get_list->address2;
            $landmark = $get_list->landmark;
            $city = $get_list->city;
            $state = $get_list->state;
        } else {
            $name = '';
            $mobile = '';
            $pincode = '';
            $address1 = '';
            $address2 = '';
            $landmark = '';
            $city = '';
            $state = '';
        }


        $sex_store_cart_order_data = array(
            'user_id' => $user_id,
            'address_id' => $address_id,
            'date' => $date,
            'status' => $status,
            'store_status' => $store_status,
            'customer_status' => $customer_status,
            'total' => $grand_total,
            'discount' => $discount,
            'payType' => $payType,
            'uni_id' => $uni_id,
            'name' => $name,
            'mobile' => $mobile,
            'pincode' => $pincode,
            'address1' => $address1,
            'address2' => $address2,
            'landmark' => $landmark,
            'city' => $city,
            'state' => $state
        );
        $insert1 = $this->db->insert('sex_store_cart_order', $sex_store_cart_order_data);
        $order_id = $this->db->insert_id();

        $cnt = count($product_id);

        for ($i = 0; $i < $cnt; $i++) {
            $sub_total = $product_price[$i] * $product_quantity[$i];

            $cart_order_products_data = array(
                'order_id' => $order_id,
                'product_id' => $product_id[$i],
                'product_quantity' => $product_quantity[$i],
                'product_price' => $product_price[$i],
                'sub_total' => $sub_total,
                'product_status' => 'pending',
                'product_status_type' => '',
                'product_status_value' => '',
                'uni_id' => $uni_id
            );
            $insert2 = $this->db->insert('sex_store_cart_order_products', $cart_order_products_data);
        }

        if ($insert1 & $insert2) {
            return array('status' => 200, 'message' => 'success');
        } else {
            return array('status' => 404, 'message' => 'failure');
        }
    }

    public function sex_store_cart_order_list($user_id) {

        $query = $this->db->query("SELECT sex_store_cart_order.date,sex_store_cart_order.status,sex_store_cart_order_products.order_id,sex_store_cart_order_products.uni_id  FROM `sex_store_cart_order`
		INNER JOIN `sex_store_cart_order_products`
		ON sex_store_cart_order.id=sex_store_cart_order_products.order_id
		WHERE sex_store_cart_order.user_id='$user_id' GROUP BY sex_store_cart_order.uni_id");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $order_id = $row['order_id'];
                $order_no = $row['uni_id'];
                $order_status = $row['status'];
                $order_date = $row['date'];

                $resultpost[] = array(
                    "order_id" => $order_id,
                    "order_no" => $order_no,
                    'order_status' => $order_status,
                    'order_date' => $order_date);
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }

    public function sex_store_cart_order_details($user_id, $order_id) {


        $query = $this->db->query("SELECT sex_store_cart_order.uni_id,sex_store_cart_order.date,sex_store_cart_order.status,GROUP_CONCAT(sex_store_products.name) AS product_name,GROUP_CONCAT(sex_store_products.price) AS product_price,GROUP_CONCAT(sex_store_cart_order_products.product_quantity) AS product_quantity,sex_store_products.image1,sex_store_cart_order.name ,sex_store_cart_order.mobile,sex_store_cart_order.pincode,sex_store_cart_order.address1,sex_store_cart_order.address2,sex_store_cart_order.landmark,sex_store_cart_order.city,sex_store_cart_order.state
		FROM `sex_store_cart_order`
		INNER JOIN `sex_store_cart_order_products`
		ON sex_store_cart_order.id=sex_store_cart_order_products.order_id
		INNER JOIN sex_store_products
		ON sex_store_products.id=sex_store_cart_order_products.product_id
		WHERE sex_store_cart_order.user_id='$user_id' AND sex_store_cart_order.id='$order_id'");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $order_no = $row['uni_id'];
                $order_date = $row['date'];
                $order_status = $row['status'];
                $product_name = $row['product_name'];
                $product_price = $row['product_price'];
                $product_quantity = $row['product_quantity'];

                $addr_patient_name = $row['name'];
                $addr_mobile = $row['mobile'];
                $addr_pincode = $row['pincode'];
                $addr_address1 = $row['address1'];
                $addr_address2 = $row['address2'];
                $addr_landmark = $row['landmark'];
                $addr_city = $row['city'];
                $addr_state = $row['state'];

                $resultpost[] = array(
                    'order_no' => $order_no,
                    'order_date' => $order_date,
                    'order_status' => $order_status,
                    'product_name' => $product_name,
                    'product_price' => $product_price,
                    'product_quantity' => $product_quantity,
                    'addr_patient_name' => $addr_patient_name,
                    'addr_mobile' => $addr_mobile,
                    'addr_pincode' => $addr_pincode,
                    'addr_address1' => $addr_address1,
                    'addr_address2' => $addr_address2,
                    'addr_landmark' => $addr_landmark,
                    'addr_city' => $addr_city,
                    'addr_state' => $addr_state
                );
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }

    public function sex_store_product_review($user_id, $listing_id, $rating, $review, $service) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'user_id' => $user_id,
            'product_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('sex_store_product_review', $review_array);
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
      public function edit_sex_store_product_review($review_id,$user_id, $listing_id, $rating, $review, $service) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
        );
           $this->db->where('id',$review_id);
           $this->db->where('user_id',$user_id);
           $this->db->update('sex_store_product_review',$review_array);
        //$this->db->insert('fitness_center_review', $review_array);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    public function sex_store_product_review_list($user_id, $listing_id) {

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

        $resultpost = '';
        $review_count = $this->db->select('id')->from('sex_store_product_review')->where('product_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT sex_store_product_review.id,sex_store_product_review.user_id,sex_store_product_review.product_id,sex_store_product_review.rating,sex_store_product_review.review, sex_store_product_review.service,sex_store_product_review.date as review_date,users.id as user_id,users.name as firstname FROM `sex_store_product_review` INNER JOIN `users` ON sex_store_product_review.user_id=users.id WHERE sex_store_product_review.product_id='$listing_id' order by sex_store_product_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '5') {
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
                $review_date = get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('sex_store_product_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('sex_store_product_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('sex_store_product_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

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

    public function sex_store_product_review_likes($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from sex_store_product_review_likes where post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `sex_store_product_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from sex_store_product_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $sex_store_product_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('sex_store_product_review_likes', $sex_store_product_review_likes);
            $like_query = $this->db->query("SELECT id from sex_store_product_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }

    public function sex_store_product_review_comment($user_id, $post_id, $comment) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');

        $sex_store_product_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('sex_store_product_review_comment', $sex_store_product_review_comment);
        $sex_store_product_review_comment_query = $this->db->query("SELECT id from sex_store_product_review_comment where post_id='$post_id'");
        $total_comment = $sex_store_product_review_comment_query->num_rows();
        return array(
            'status' => 200,
            'message' => 'success',
            'comment' => '1',
            'total_comment' => $total_comment
        );
    }

    public function sex_store_product_review_comment_like($user_id, $comment_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from sex_store_product_review_comment_like where comment_id='$comment_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `sex_store_product_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from sex_store_product_review_comment_like where comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $sex_store_product_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('sex_store_product_review_comment_like', $sex_store_product_review_comment_like);
            $comment_query = $this->db->query("SELECT id from sex_store_product_review_comment_like where comment_id='$comment_id'");
            $total_comment_like = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'comment_like' => '1',
                'total_comment_like' => $total_comment_like
            );
        }
    }

    public function sex_store_product_review_comment_list($user_id, $post_id) {

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
                return intval($time_differnce / $years) . ' yrs ago';
            } elseif (intval($time_differnce / $years) > 0) {
                return intval($time_differnce / $years) . ' yr ago';
            } elseif (intval($time_differnce / $months) > 1) {
                return intval($time_differnce / $months) . ' months ago';
            } elseif (intval(($time_differnce / $months)) > 0) {
                return intval(($time_differnce / $months)) . ' month ago';
            } elseif (intval(($time_differnce / $days)) > 1) {
                return intval(($time_differnce / $days)) . ' days ago';
            } elseif (intval(($time_differnce / $days)) > 0) {
                return intval(($time_differnce / $days)) . ' day ago';
            } elseif (intval(($time_differnce / $hours)) > 1) {
                return intval(($time_differnce / $hours)) . ' hrs ago';
            } elseif (intval(($time_differnce / $hours)) > 0) {
                return intval(($time_differnce / $hours)) . ' hr ago';
            } elseif (intval(($time_differnce / $minutes)) > 1) {
                return intval(($time_differnce / $minutes)) . ' min ago';
            } elseif (intval(($time_differnce / $minutes)) > 0) {
                return intval(($time_differnce / $minutes)) . ' min ago';
            } elseif (intval(($time_differnce)) > 1) {
                return intval(($time_differnce)) . ' sec ago';
            } else {
                return 'few seconds ago';
            }
        }

        $review_list_count = $this->db->select('id')->from('sex_store_product_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT sex_store_product_review_comment.id,sex_store_product_review_comment.post_id,sex_store_product_review_comment.comment as comment,sex_store_product_review_comment.date,users.name,sex_store_product_review_comment.user_id as post_user_id FROM sex_store_product_review_comment INNER JOIN users on users.id=sex_store_product_review_comment.user_id WHERE sex_store_product_review_comment.post_id='$post_id' order by sex_store_product_review_comment.id asc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if ($id > '2') {
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
                $username = $row['name'];
                $date = $row['date'];
                $post_user_id = $row['post_user_id'];

                $like_count = $this->db->select('id')->from('sex_store_product_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('sex_store_product_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
                $comment_date = get_time_difference_php($date);

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();

                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $date = get_time_difference_php($date);
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

    public function sex_store_review($user_id, $listing_id, $rating, $review, $service) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'user_id' => $user_id,
            'sex_store_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('sex_store_review', $review_array);
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
     public function edit_sex_store_review($user_id, $listing_id, $rating, $review, $service) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'user_id' => $user_id,
            'sex_store_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('sex_store_review', $review_array);
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }

    public function sex_store_review_list($user_id, $listing_id) {

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

        $resultpost = '';
        $review_count = $this->db->select('id')->from('sex_store_review')->where('sex_store_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT sex_store_review.id,sex_store_review.user_id,sex_store_review.sex_store_id,sex_store_review.rating,sex_store_review.review, sex_store_review.service,sex_store_review.date as review_date,users.id as user_id,users.name as firstname FROM `sex_store_review` INNER JOIN `users` ON sex_store_review.user_id=users.id WHERE sex_store_review.sex_store_id='$listing_id' order by sex_store_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '5') {
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
                $review_date = get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('sex_store_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('sex_store_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('sex_store_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

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

    public function sex_store_review_likes($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from sex_store_review_likes where post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `sex_store_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from sex_store_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $sex_store_product_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('sex_store_review_likes', $sex_store_product_review_likes);
            $like_query = $this->db->query("SELECT id from sex_store_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }

    public function sex_store_review_comment($user_id, $post_id, $comment) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');

        $sex_store_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('sex_store_review_comment', $sex_store_review_comment);
        $sex_store_review_comment_query = $this->db->query("SELECT id from sex_store_review_comment where post_id='$post_id'");
        $total_comment = $sex_store_review_comment_query->num_rows();
        return array(
            'status' => 200,
            'message' => 'success',
            'comment' => '1',
            'total_comment' => $total_comment
        );
    }

    public function sex_store_review_comment_like($user_id, $comment_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from sex_store_review_comment_like where comment_id='$comment_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `sex_store_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from sex_store_review_comment_like where comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $sex_store_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('sex_store_review_comment_like', $sex_store_review_comment_like);
            $comment_query = $this->db->query("SELECT id from sex_store_review_comment_like where comment_id='$comment_id'");
            $total_comment_like = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'comment_like' => '1',
                'total_comment_like' => $total_comment_like
            );
        }
    }

    public function sex_store_review_comment_list($user_id, $post_id) {

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
                return intval($time_differnce / $years) . ' yrs ago';
            } elseif (intval($time_differnce / $years) > 0) {
                return intval($time_differnce / $years) . ' yr ago';
            } elseif (intval($time_differnce / $months) > 1) {
                return intval($time_differnce / $months) . ' months ago';
            } elseif (intval(($time_differnce / $months)) > 0) {
                return intval(($time_differnce / $months)) . ' month ago';
            } elseif (intval(($time_differnce / $days)) > 1) {
                return intval(($time_differnce / $days)) . ' days ago';
            } elseif (intval(($time_differnce / $days)) > 0) {
                return intval(($time_differnce / $days)) . ' day ago';
            } elseif (intval(($time_differnce / $hours)) > 1) {
                return intval(($time_differnce / $hours)) . ' hrs ago';
            } elseif (intval(($time_differnce / $hours)) > 0) {
                return intval(($time_differnce / $hours)) . ' hr ago';
            } elseif (intval(($time_differnce / $minutes)) > 1) {
                return intval(($time_differnce / $minutes)) . ' min ago';
            } elseif (intval(($time_differnce / $minutes)) > 0) {
                return intval(($time_differnce / $minutes)) . ' min ago';
            } elseif (intval(($time_differnce)) > 1) {
                return intval(($time_differnce)) . ' sec ago';
            } else {
                return 'few seconds ago';
            }
        }

        $review_list_count = $this->db->select('id')->from('sex_store_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT sex_store_review_comment.id,sex_store_review_comment.post_id,sex_store_review_comment.comment as comment,sex_store_review_comment.date,users.name,sex_store_review_comment.user_id as post_user_id FROM sex_store_review_comment INNER JOIN users on users.id=sex_store_review_comment.user_id WHERE sex_store_review_comment.post_id='$post_id' order by sex_store_review_comment.id asc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if ($id > '2') {
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
                $username = $row['name'];
                $date = $row['date'];
                $post_user_id = $row['post_user_id'];

                $like_count = $this->db->select('id')->from('sex_store_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('sex_store_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
                $comment_date = get_time_difference_php($date);

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();

                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $date = get_time_difference_php($date);
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

    public function sex_expert_character($type) {
        $review_list_count = $this->db->select('id')->from('user_character')->where('type', $type)->order_by('id', 'desc')->get()->num_rows();
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

    public function sex_expert_add_question($user_id, $user_name, $user_image, $question, $age, $post_location) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $sex_education_question = array(
            'user_id' => $user_id,
            'user_name' => $user_name,
            'user_image' => $user_image,
            'question' => $question,
            'age' => $age,
            'post_location' => $post_location,
            'date' => $created_at
        );
        $this->db->insert('sex_education_question', $sex_education_question);
        $post_id = $this->db->insert_id();
        $doctor_id = '3058';
        $this->insert_notification_post_question($user_id, $post_id, 'user',$doctor_id);
        ///////////////////////////
        $post_data    = array(
                 'userId' => $user_id,
                'postId' => $post_id,
                 'text' => $this->decrypt($question)
             ); 
           /* $new_post_data=json_encode($post_data);  
            echo $url='http://52.66.208.83:8003/doctor/lelo/forum/auto/answer/';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $new_post_data);
            curl_setopt($ch, CURLOPT_FAILONERROR, true); 
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
            $result = curl_exec($ch);
            print_r($result);
            die;
             if ($result === FALSE) {
               //die('Problem occurred: ' . curl_error($ch));
             }
             curl_close($ch);*/
   
   
  /*  $new_post_data=json_encode($post_data);  
              // print_r($new_post_data); die();
            $url='http://52.66.208.83:8003/doctor/lelo/forum/auto/answer/';
            $ch = curl_init();
           // curl_setopt($ch, CURLOPT_VERBOSE, 1);
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
                die('Problem occurred: ' . curl_error($ch));
                      //  $info = curl_getinfo($ch);
                      //  echo "cURL error number:" .$errorno=curl_errno($ch).'<br>';
                        //print_r($info);
                        //  die('Problem occurred: ' . curl_error($ch));
            }
             curl_close($ch);
   
   
   die;
   */
        //////////////////////////
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
     public function add_comment($data,$comment1,$user_id){
       
		$this->db->insert("sex_education_answer", $data);

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


    public function insert_notification_post_question($user_id, $post_id, $name, $doctor_id){
        $data = array(
							'user_id'		=> $user_id,
							'post_id' 		=> $post_id,
							'timeline_id'   => $user_id,
							'type'          => 'comment',
							'seen'          => '1',
							'notified_by'   => $doctor_id,
							'description'   => ' Aske a question',
							'created_at'	=> curr_date(), 
							'updated_at'	=> curr_date()
							
					);
		//print_r($data);
		$this->db->insert("notifications", $data);

		if($this->db->affected_rows() > 0)
		{
		    
		    return true; // to the controller
		}
		else{
			return false;
		}
	}

    public function sex_expert_add_reply($user_id, $doctor_id, $post_id, $type, $answer) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $sex_education_question = array(
            'type' => $type,
            'doctor_id' => $doctor_id,
            'post_id' => $post_id,
            'answer' => $answer,
            'date' => $created_at
        );
        //print_r($sex_education_question); die();
        $this->db->insert('sex_education_answer', $sex_education_question);
        
        // WEB NOTIFICATIONS TO USERS
        $this->insert_notification_post_follow($user_id, $post_id, 'user',$doctor_id);
      
	    //question list
        //         function send_gcm_notify($title,$reg_id,$msg,$img_url,$tag,$agent,$post_id)
        //         {
        // 		date_default_timezone_set('Asia/Kolkata');
        // 		$date = date('j M Y h:i A');
        
        // 		 	if (!defined("GOOGLE_GCM_URL"))
        //             define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
         
                 
        //             $fields  = array(
        //                 'to' => $reg_id,
        //                 'priority' => "high",
        //                   $agent === 'android' ? 'data' : 'notification' => array(
        //                     "title" => $title,
        //                     "message" => $msg,
        //                     "notification_image" => $img_url,
        //                     "tag" => $tag,
        // 					"notification_type" => "sex_expert_notifications",
        // 					"notification_date"=>$date,
        // 					"post_id"=>$post_id		
        //                 )
        //             );
        //             $headers = array(
        //                 GOOGLE_GCM_URL,
        //                 'Content-Type: application/json',
        //               //'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' 
        //              //'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4'
        // 			 $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
        //             );
        //             $ch      = curl_init();
        //             curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
        //             curl_setopt($ch, CURLOPT_POST, true);
        //             curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //             curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //             curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        //             $result = curl_exec($ch);
        //             if ($result === FALSE) {
        //                 die('Problem occurred: ' . curl_error($ch));
        //             }
        //             curl_close($ch);
        //             //echo $result;
        //           // echo $reg_id;
        //         }

    	   // $querys= $this->db->query("SELECT `id`, `user_id`, `post_id` FROM `sex_education_is_notify` WHERE post_id='$post_id'");
        //     $count = $querys->num_rows();
        //     if ($count > 0) {
        //         foreach ($querys->result_array() as $rows) {
        //     	    $user_id    = $rows['user_id'];
        //             $customer_token = $this->db->select('token,agent,token_status')->from('users')->where('id', $user_id)->get()->row();
        //             $token_status   = $customer_token->token_status;
        //             if ($token_status > 0) {
        //                     $agent    = $customer_token->agent;
        //                     $reg_id    = $customer_token->token;
        //                     $img_url   = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/dr-lelo.jpg';
        //                     $tag       = 'text';
        //                     $key_count = '1';
        //                     $title = 'Check what Dr. LeLo have answered'; //shivani 23-1-18
        //                     $msg   = 'Tap here to check';
        //         			//When active by admin
        //                     //$title = 'Welcome, your pharmacy has been approved.';
        //         			//$msg   = 'Congratulations! Your pharmacy listing has been live now.';
        //                     send_gcm_notify($title,$reg_id,$msg,$img_url,$tag,$agent,$post_id);
        //                 }
        // 	    }
        //     }
	  
	    //self notify
        //         $customer_token = $this->db->select('token,agent,token_status')->from('users')->where('id', $doctor_id)->get()->row();
        //         $token_status   = $customer_token->token_status;
        
        //         if ($token_status > 0) { 
        //      		$agent      = $customer_token->agent;
        //             $reg_id     = $customer_token->token;
        //             $img_url    = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/dr-lelo.jpg';
        //             $tag        = 'text';
        //             $key_count  = '1';
        //             $title      = 'Dr. Lelo have answered your question.';
        //             $msg        = 'Tap here to check';
        //             send_gcm_notify($title,$reg_id,$msg,$img_url,$tag,$agent,$post_id);
        // 	        //self notify	    
        // 		}
                
        $answer_id = $this->db->insert_id();
        return array(
            'answer_id' => $answer_id,
            'status' => 200,
            'message' => 'success'
        );
        }
    
    public function insert_notification_post_follow($user_id, $post_id, $name,$doctor_id){
        $data = array(
							'user_id'		=> $user_id,
							'post_id' 		=> $post_id,
							'timeline_id'   => $user_id,
							'type'          => 'comment',
							'seen'          => '1',
							'notified_by'   => $doctor_id,
							'description'   => ' is replied your comment',
							'created_at'	=> curr_date(), 
							'updated_at'	=> curr_date()
							
					);
		//print_r($data);
		$this->db->insert("notifications", $data);

		if($this->db->affected_rows() > 0)
		{
		    
		    return true; // to the controller
		}
		else{
			return false;
		}
	}

    public function sex_expert_question_list($user_id, $page) {

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

        //$query = $this->db->query("SELECT sex_education_question.id,sex_education_question.age,sex_education_question.user_image,sex_education_question.user_name,sex_education_question.user_id,sex_education_question.question,sex_education_question.date,sex_education_character.image AS c_image  FROM  `sex_education_question` INNER JOIN `sex_education_character` ON sex_education_question.user_image=sex_education_character.id order by sex_education_question.id desc");


        $limit = 5;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;


        $query = $this->db->query("SELECT sex_education_question.id,sex_education_question.age,sex_education_question.user_image,sex_education_question.user_name,sex_education_question.user_id,sex_education_question.question,sex_education_question.date,IFNULL(sex_education_question.post_location,'') AS post_location,user_character.image AS c_image  FROM  `sex_education_question` INNER JOIN `user_character` ON sex_education_question.user_image=user_character.id  WHERE sex_education_question.id NOT IN (SELECT post_id FROM sex_education_hide WHERE post_id=sex_education_question.id AND user_id='$user_id')  order by sex_education_question.id DESC limit $start, $limit");

        $count_query = $this->db->query("SELECT sex_education_question.id,sex_education_question.age,sex_education_question.user_image,sex_education_question.user_name,sex_education_question.user_id,sex_education_question.question,sex_education_question.date,user_character.image AS c_image  FROM  `sex_education_question` INNER JOIN `user_character` ON sex_education_question.user_image=user_character.id  WHERE sex_education_question.id NOT IN (SELECT post_id FROM sex_education_hide WHERE post_id=sex_education_question.id AND user_id='$user_id')  order by sex_education_question.id DESC");
        $count_post = $count_query->num_rows();


        if ($count_post > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $age = $row['age'];
                $post_location = $row['post_location'];
                $images = $row['c_image'];
                $user_name = $row['user_name'];
                $post_user_id = $row['user_id'];
                $question = $row['question'];
                $question = preg_replace('~[\r\n]+~', '', $question);
                if ($id > '1') {
                    $decrypt = $this->decrypt($question);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $question) {
                        $question = $decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($question)) === $question) {
                        $question = base64_decode($question);
                    }
                }
                $date = $row['date'];
                $date = get_time_difference_php($date);
                if ($images != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $images;
                } else {
                    $image = '';
                }

                $is_notify_query = $this->db->query("SELECT id FROM `sex_education_is_notify` where post_id='$id' AND user_id='$user_id'");
                $is_notify = $is_notify_query->num_rows();


                $is_follow = '0';

                $answer_list = array();
                $answer_query = $this->db->query("SELECT id,type,answer,post_id FROM `sex_education_answer` WHERE `post_id`='$id'");
                $count_answers = $answer_query->num_rows();
                if ($count_answers > 0) {
                    foreach ($answer_query->result_array() as $rows) {

                        $answer_id = $rows['id'];
                        $answer = $rows['answer'];
                        $type = $rows['type'];
			    if($type=='Dr .Lelo'){
			   	 $type='Dr. LeLo';
			    }
			    
                        $answer = preg_replace('~[\r\n]+~', '', $answer);
                        if ($answer_id > '411') {
                            $decrypt = $this->decrypt($answer);
                            $encrypt = $this->encrypt($decrypt);
                            if ($encrypt == $answer) {
                                $answer = $decrypt;
                            }
                        } else {
                            if (base64_encode(base64_decode($answer)) === $answer) {
                                $answer = base64_decode($answer);
                            }
                        }
                        $answer_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/dr-lelo.jpg';

                        $answer_list[] = array(
                            'answer_id' => $answer_id,
                            'type' => $type,
                            'answer' => $answer,
                                //'answer_image' => $answer_image
                        );
                    }
                } else {
                    $answer_list = array();
                }


                $share_url = "https://medicalwale.com/share/drlelo/" . $id;

                $count_query = $this->db->query("SELECT id FROM `sex_education_likes` where post_id='$id'");
                $like_count = $count_query->num_rows();

                $like_count_query = $this->db->query("SELECT id FROM `sex_education_likes` where user_id='$user_id' and post_id='$id'");
                $like_yes_no = $like_count_query->num_rows();

                $is_post_save_query = $this->db->query("SELECT id from post_save WHERE post_id='$id' AND user_id='$user_id' AND post_type='drlelo'");
                $is_post_save = $is_post_save_query->num_rows();

                $is_reported_query = $this->db->query("SELECT id from post_report WHERE post_id='$id' AND reporter_id='$user_id' AND post_type='drlelo'");
                $is_reported = $is_reported_query->num_rows();


                $resultpost[] = array(
                    'id' => $id,
                    'post_user_id' => $post_user_id,
		            "post_type"=> "",
                    'post_location' => $post_location,
                    'user_name' => $user_name,
                    'question' => $question,
                    'is_notify' => $is_notify,
                    'age' => $age,
                    'image' => $image,
                    'answer_image' => "https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/dr-lelo.jpg",
                    'answer_list' => $answer_list,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'is_follow' => $is_follow,
                    'share_url' => $share_url,
                    'is_reported' => $is_reported,
                    'is_post_save' => $is_post_save,
                    'date' => $date,
 			"vendor_type" => "0",
			"ad_type" => "",
 			"ad_cat_id" => "0",
 			"ad_title" => "",
 			"ad_link" => "",
			"ad_image" => "",
			"new_ad_pd_id" => "0",
			"new_ad_brand_name" => "",
 			"new_ad_pd_added_v_id" => "0",
 			"new_ad_pd_name" => "",
 			"new_ad_pd_photo_1" => "",
 			"new_ad_pd_mrp_price" => "0",
			"new_ad_pd_vendor_price" => "0",
			"new_ad_background_image" => "",
			"ad_colour_code" => ""
                );
		    
		     if(count($resultpost) % 5 == 0 && count($resultpost)> 0)  {
                    if($page>1){
                        $lim_srt=$page-1;
                        $count_limit=$lim_srt.',1';
                    }
                    else{
                        $count_limit='1';
                    }
                    
                    
                        $this11 = new \stdClass();
                       $url="http://10.0.1.198:8011/ads/drlelo/ads/";
                       $this11->user_id=$user_id;
                       $data11 = json_encode($this11);
                       
                       $data12=healthwall_adver_crul($url,$data11);
                       $data131 = json_decode($data12); 
                        
                       if(!empty($data131))
                       {
                       $resultpost[] =$data131->data;
                       }
                    
                    
                   
                }
		    
		    
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
    
    public function sex_expert_question_list_web($user_id, $page) {

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

        //$query = $this->db->query("SELECT sex_education_question.id,sex_education_question.age,sex_education_question.user_image,sex_education_question.user_name,sex_education_question.user_id,sex_education_question.question,sex_education_question.date,sex_education_character.image AS c_image  FROM  `sex_education_question` INNER JOIN `sex_education_character` ON sex_education_question.user_image=sex_education_character.id order by sex_education_question.id desc");


        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;


        $query = $this->db->query("SELECT sex_education_question.id,sex_education_question.age,sex_education_question.user_image,sex_education_question.user_name,sex_education_question.user_id,sex_education_question.question,sex_education_question.date,IFNULL(sex_education_question.post_location,'') AS post_location,user_character.image AS c_image  FROM  `sex_education_question` INNER JOIN `user_character` ON sex_education_question.user_image=user_character.id  WHERE sex_education_question.id NOT IN (SELECT post_id FROM sex_education_hide WHERE post_id=sex_education_question.id AND user_id='$user_id')  order by sex_education_question.id DESC limit $start, $limit");

        $count_query = $this->db->query("SELECT sex_education_question.id,sex_education_question.age,sex_education_question.user_image,sex_education_question.user_name,sex_education_question.user_id,sex_education_question.question,sex_education_question.date,user_character.image AS c_image  FROM  `sex_education_question` INNER JOIN `user_character` ON sex_education_question.user_image=user_character.id  WHERE sex_education_question.id NOT IN (SELECT post_id FROM sex_education_hide WHERE post_id=sex_education_question.id AND user_id='$user_id')  order by sex_education_question.id DESC");
        $count_post = $count_query->num_rows();


        if ($count_post > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $age = $row['age'];
                $post_location = $row['post_location'];
                $images = $row['c_image'];
                $user_name = $row['user_name'];
                $post_user_id = $row['user_id'];
                $question = $row['question'];
                $question = preg_replace('~[\r\n]+~', '', $question);
                /*if ($id > '1') {
                    $decrypt = $this->decrypt($question);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $question) {
                        $question = $decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($question)) === $question) {
                        $question = base64_decode($question);
                    }
                }*/
                $date = $row['date'];
                $date = get_time_difference_php($date);
                if ($images != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $images;
                } else {
                    $image = '';
                }

                $is_notify_query = $this->db->query("SELECT id FROM `sex_education_is_notify` where post_id='$id' AND user_id='$user_id'");
                $is_notify = $is_notify_query->num_rows();


                $is_follow = '0';

                $answer_list = array();
                $answer_query = $this->db->query("SELECT id,type,answer,post_id FROM `sex_education_answer` WHERE `post_id`='$id'");
                $count_answers = $answer_query->num_rows();
                if ($count_answers > 0) {
                    foreach ($answer_query->result_array() as $rows) {

                        $answer_id = $rows['id'];
                        $answer = $rows['answer'];
                        $type = $rows['type'];
                        $answer = preg_replace('~[\r\n]+~', '', $answer);
                       /* if ($answer_id > '411') {
                            $decrypt = $this->decrypt($answer);
                            $encrypt = $this->encrypt($decrypt);
                            if ($encrypt == $answer) {
                                $answer = $decrypt;
                            }
                        } else {
                            if (base64_encode(base64_decode($answer)) === $answer) {
                                $answer = base64_decode($answer);
                            }
                        }*/
                        $answer_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/dr-lelo.jpg';

                        $answer_list[] = array(
                            'answer_id' => $answer_id,
                            'type' => $type,
                            'answer' => $answer,
                                //'answer_image' => $answer_image
                        );
                    }
                } else {
                    $answer_list = array();
                }


                $share_url = "https://medicalwale.com/share/drlelo/" . $id;

                $count_query = $this->db->query("SELECT id FROM `sex_education_likes` where post_id='$id'");
                $like_count = $count_query->num_rows();

                $like_count_query = $this->db->query("SELECT id FROM `sex_education_likes` where user_id='$user_id' and post_id='$id'");
                $like_yes_no = $like_count_query->num_rows();

                $is_post_save_query = $this->db->query("SELECT id from post_save WHERE post_id='$id' AND user_id='$user_id' AND post_type='drlelo'");
                $is_post_save = $is_post_save_query->num_rows();

                $is_reported_query = $this->db->query("SELECT id from post_report WHERE post_id='$id' AND reporter_id='$user_id' AND post_type='drlelo'");
                $is_reported = $is_reported_query->num_rows();


                $resultpost[] = array(
                    'id' => $id,
                    'post_user_id' => $post_user_id,
                    'post_location' => $post_location,
                    'user_name' => $user_name,
                    'question' => $question,
                    'is_notify' => $is_notify,
                    'age' => $age,
                    'image' => $image,
                    'answer_image' => "https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/dr-lelo.jpg",
                    'answer_list' => $answer_list,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'is_follow' => $is_follow,
                    'share_url' => $share_url,
                    'is_reported' => $is_reported,
                    'is_post_save' => $is_post_save,
                    'date' => $date
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
    

    public function sex_expert_question_details($user_id, $post_id) {

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

        $query = $this->db->query("SELECT sex_education_question.id,sex_education_question.age,sex_education_question.user_image,sex_education_question.user_name,sex_education_question.user_id,IFNULL(sex_education_question.post_location,'') AS post_location,sex_education_question.question,sex_education_question.date,user_character.image AS c_image  FROM  `sex_education_question` INNER JOIN `user_character` ON sex_education_question.user_image=user_character.id  WHERE sex_education_question.id='$post_id' order by sex_education_question.id DESC");
        $count_post = $query->num_rows();

        if ($count_post > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $age = $row['age'];
                $post_location = $row['post_location'];
                $images = $row['c_image'];
                $user_name = $row['user_name'];
                $post_user_id = $row['user_id'];
                $question = $row['question'];
                $question = preg_replace('~[\r\n]+~', '', $question);
                if ($id > '469') {
                    $decrypt = $this->decrypt($question);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $question) {
                        $question = $decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($question)) === $question) {
                        $question = base64_decode($question);
                    }
                }
                $date = $row['date'];
                $date = get_time_difference_php($date);
                if ($images != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $images;
                } else {
                    $image = '';
                }

                $is_notify_query = $this->db->query("SELECT id FROM `sex_education_is_notify` where post_id='$id' AND user_id='$user_id'");
                $is_notify = $is_notify_query->num_rows();


                $is_follow = '0';


                $answer_list = array();
                $answer_query = $this->db->query("SELECT id,type,answer,post_id FROM `sex_education_answer` WHERE `post_id`='$id'");
                $count_answers = $answer_query->num_rows();
                if ($count_answers > 0) {
                    foreach ($answer_query->result_array() as $rows) {

                        $answer_id = $rows['id'];
                        $answer = $rows['answer'];
                        $type = $rows['type'];
                        $answer = preg_replace('~[\r\n]+~', '', $answer);
                        if ($answer_id > '411') {
                            $decrypt = $this->decrypt($answer);
                            $encrypt = $this->encrypt($decrypt);
                            if ($encrypt == $answer) {
                                $answer = $decrypt;
                            }
                        } else {
                            if (base64_encode(base64_decode($answer)) === $answer) {
                                $answer = base64_decode($answer);
                            }
                        }
                        $answer_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/dr-lelo.jpg';

                        $answer_list[] = array(
                            'answer_id' => $answer_id,
                            'type' => $type,
                            'answer' => $answer,
                                //'answer_image' => $answer_image
                        );
                    }
                } else {
                    $answer_list = array();
                }

                $share_url = "https://medicalwale.com/share/drlelo/" . $id;

                $count_query = $this->db->query("SELECT id FROM `sex_education_likes` where post_id='$id'");
                $like_count = $count_query->num_rows();

                $like_count_query = $this->db->query("SELECT id FROM `sex_education_likes` where user_id='$user_id' and post_id='$id'");
                $like_yes_no = $like_count_query->num_rows();
                $is_post_save_query = $this->db->query("SELECT id from post_save WHERE post_id='$post_id' AND user_id='$user_id' AND post_type='drlelo'");
                $is_post_save = $is_post_save_query->num_rows();

                $is_reported_query = $this->db->query("SELECT id from post_report WHERE post_id='$post_id' AND reporter_id='$user_id' AND post_type='drlelo'");
                $is_reported = $is_reported_query->num_rows();

                $resultpost[] = array(
                    'id' => $id,
                    'post_user_id' => $post_user_id,
                    'post_location' => $post_location,
                    'user_name' => $user_name,
                    'question' => $question,
                    'answer_list' => $answer_list,
                    'answer_image' => "https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/dr-lelo.jpg",
                    'is_notify' => $is_notify,
                    'age' => $age,
                    'image' => $image,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'is_follow' => $is_follow,
                    'share_url' => $share_url,
                    'is_reported' => $is_reported,
                    'is_post_save' => $is_post_save,
                    'date' => $date);
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }

    public function sex_expert_like($user_id, $post_id, $user_image, $user_name, $post_user_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `sex_education_likes` WHERE post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `sex_education_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from sex_education_likes WHERE post_id='$post_id'");
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
            $this->db->insert('sex_education_likes', $sex_education_likes);



            if ($user_name == '0' || $user_name == '') {
                $user_name = 'Someone';
            }

            if ($user_image == '0') {
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/dr-lelo.jpg';
            } else {
                $img_query = $this->db->query("select user_character.image as character_image FROM sex_education_likes INNER JOIN user_character on user_character.id=sex_education_likes.user_image  WHERE  sex_education_likes.user_id='$user_id' AND sex_education_likes.post_id='$post_id'");
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
                 if($this->get_stop_notification_for_user($post_user_id))
                {
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id);
                }
            }



            $like_query = $this->db->query("SELECT id from sex_education_likes WHERE post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }

    public function sex_expert_user_like_list($post_id) {
        $query = $this->db->query("SELECT sex_education_likes.id,sex_education_likes.user_image,sex_education_likes.user_name,sex_education_likes.user_id,user_character.image AS c_image  FROM  `sex_education_likes` INNER JOIN `user_character` ON sex_education_likes.user_image=user_character.id WHERE sex_education_likes.post_id='$post_id' order by sex_education_likes.id desc");

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

    public function sex_education_is_notify($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `sex_education_is_notify` WHERE  user_id='$user_id' and post_id='$post_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `sex_education_is_notify` WHERE user_id='$user_id' and post_id='$post_id'");

            return array(
                'status' => 200,
                'message' => 'deleted',
                'is_notify' => '0'
            );
        } else {
            $sex_education_is_notify = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('sex_education_is_notify', $sex_education_is_notify);

            return array(
                'status' => 200,
                'message' => 'success',
                'is_notify' => '1'
            );
        }
    }

    public function sex_education_hide($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `sex_education_hide` WHERE  user_id='$user_id' and post_id='$post_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `sex_education_hide` WHERE user_id='$user_id' and post_id='$post_id'");
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
            $this->db->insert('sex_education_hide', $sex_education_hide);


            $this->db->query("DELETE FROM `sex_education_is_notify` WHERE user_id='$user_id' and post_id='$post_id'");

            return array(
                'status' => 200,
                'message' => 'success',
                'is_hide' => '1'
            );
        }
    }

    public function sex_education_user_update($user_id, $user_name, $user_image) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id FROM `sex_education_ask_expert` WHERE user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("UPDATE `sex_education_ask_expert` SET `user_name`='$user_name',`user_image`='$user_image' WHERE user_id='$user_id'");
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
            $this->db->insert('sex_education_ask_expert', $ask_saheli_ask_user);
            return array(
                'status' => 200,
                'message' => 'success'
            );
        }
    }

    public function sex_education_user_check($user_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id FROM `sex_education_ask_expert` WHERE user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $query = $this->db->query("SELECT sex_education_ask_expert.user_id, sex_education_ask_expert.user_name, sex_education_ask_expert.user_image,user_character.image AS c_image  FROM `sex_education_ask_expert` INNER JOIN `user_character` ON sex_education_ask_expert.user_image=user_character.id  WHERE sex_education_ask_expert.user_id='$user_id'");


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

    public function sex_education_delete($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `sex_education_question` WHERE  user_id='$user_id' and id='$post_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `sex_education_question` WHERE user_id='$user_id' and id='$post_id'");

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

    public function sex_education_reply_delete($answer_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `sex_education_answer` WHERE  post_id='$post_id' and id='$answer_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `sex_education_answer` WHERE post_id='$post_id' and id='$answer_id'");

            return array(
                'status' => 200,
                'message' => 'deleted'
            );
        } else {
            return array(
                'status' => 401,
                'message' => 'unauthorizeded'
            );
        }
    }
    
    public function kamashastra_audio_list($user_id)
    {
        $query = $this->db->query("SELECT * from `kamashastra_audio`");
        $count = $query->num_rows();
        if($count>0)
        {
             $i = 01;
            foreach ($query->result_array() as $rows) {

                        $audio_name = $rows['audio_name'];
                        $id = $rows['id'];
                        $details =  'https://s3.amazonaws.com/medicalwale/audio/'.$audio_name;
                       
                        
                        $detail_array[] = array(
                              'count' => $i,
                              'track_name' => 'Track '.$i,
                              'track_link' => $details
                            ); 
                            $i++;
                          
            }
              return array(
                'status' => 200,
                'message' => 'success',
                'data' => $detail_array
            );
            
        }
    }
    
    public function python_api($user_id, $postId, $text){
             $post_data    = array(
                 'userId' => 46055,
                'postId' => 494,
                 'text' => "You need not join the Red Cross; just visit a sexpert for some pre-marriage counselling. Oral sex is safe and healthy and she will not conceive through it. long lasting in bed"
             ); 
             

           $new_post_data=json_encode($post_data);  
              // print_r($new_post_data); die();
            $url='http://52.66.208.83:8003/doctor/lelo/forum/auto/answer/';
            $ch = curl_init();
           // curl_setopt($ch, CURLOPT_VERBOSE, 1);
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
               // die('Problem occurred: ' . curl_error($ch));
                        $info = curl_getinfo($ch);
                        echo "cURL error number:" .$errorno=curl_errno($ch).'<br>';
                        //print_r($info);
                          die('Problem occurred: ' . curl_error($ch));
            }
             curl_close($ch);
    }    
}
