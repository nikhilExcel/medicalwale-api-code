<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Health_quizModel extends CI_Model {

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
                 if ($q->expired_at < date("Y-m-d H:i:s", strtotime("-1 days"))) {
                return json_output(401, array(
                    'status' => 401,
                    'message' => 'Your session has been expired.'
                ));
            } else {
                $updated_at = date('Y-m-d H:i:s');
                $expired_at = date("Y-m-d H:i:s", strtotime("+1 days"));//'2020-12-12 08:57:58';
                $this->db->where('users_id', $users_id)->where('token', $token)->update('api_users_authentication', array(
                    'expired_at' => $expired_at,
                    'updated_at' => $updated_at
                ));
               // echo $this->db->last_query();
                return array(
                    'status' => 200,
                    'message' => 'Authorized.'
                );
            }
                
            }
        }
    }

    public function encrypt($str) {
        //echo $str;
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
    
    
    public function Get_all_quizdata($user_id,$test_no)
    {
         date_default_timezone_set('Asia/Kolkata');
        $system_date = date('Y-m-d H:i:s');
        $page = rand('00','99');
         $limit = 10;
        $start = 0;
        if ($page > 0 || $page > 00) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        
        $sql = $this->db->query("select id,Question,Option1,Option2,Option3,Option4,Answer from health_quiz limit 10");
        $count = $sql->num_rows();
        
        if($count>0)
        {
            foreach($sql->result_array() as $row)
            {
                $id = $row['id'];
                $question = $row['Question'];
                $options1 = $row['Option1'];
                $options2 = $row['Option2'];
                $options3 = $row['Option3'];
                $options4 = $row['Option4'];
                $answer =   $row['Answer'];
                $point  = '5';
                $time   =  '10';
                
                
                
                $result_array[] = array(
                           'id' => $id,
                           'question' => $question,
                            'options' => array($options1,$options2,$options3,$options4),
                              'answer' => $answer,
                              'point'  => $point,
                              'time'   => $time,
                              'type'   => 'text',
                    );
                
            }
            
            return array('status' => '200',
                         'message' => "success",
                         'data' => $result_array
                        );
        }
        else
        {
            return array('status' => '200',
                         'message' => "success",
                         'data' => array()
                        );
        }
        
    }
    
    public function Get_result_data($user_id,$is_winner,$total_score,$total_question,$total_point,$opponent_id)
    {
         $system_date = date('Y-m-d');
           $health_quiz_result = array(
                'user_id' => $user_id,
                'opponent_id' => $opponent_id,
                'total_score' => $total_score,
                'total_question' => $total_question,
                'total_point'  => $total_point,
                'is_winner' => $is_winner,
                'date' => $system_date
            );
            $this->db->insert('health_quiz_result', $health_quiz_result);
            
             return array('status' => '200',
                         'message' => "success",
                         'data' => array()
                        );
    }
    
    public function Get_all_history($user_id)
	{
        date_default_timezone_set('Asia/Kolkata');
        $system_date = date('Y-m-d H:i:s');
        
        
        $sql = $this->db->query("select id,user_id,opponent_id,total_score,total_question,total_point,is_winner,date from health_quiz_result where user_id = '$user_id' order by id desc");
     
        $count = $sql->num_rows();
        
        if($count > 0)
        {
             foreach($sql->result_array() as $row)
            {
                $test_id = $row['id'];
                $user_id = $row['opponent_id'];
                $opponent_id = $row['opponent_id'];
                $total_score = $row['total_score'];
                $total_question = $row['total_question'];
                $total_point = $row['total_point'];
                $is_winner = $row['is_winner'];
                $date = $row['date'];
                
                
                 $result_array[] = array( 
                     'test_id' => $test_id,
                     'user_id' => $user_id,
                     'opponent_id' => $opponent_id,
                     'total_score'  => $total_score,
                     'total_question' => $total_question,
                     'total_point'  => $total_point,
                     'is_winner'   => $is_winner,
                     'date'     => $date
                     );
            }
            
            return array('status' => '200',
                         'message' => "success",
                         'data' => $result_array
                        );
        }
        else
        {
            return array('status' => '200',
                         'message' => "success",
                         'data' => array()
                        );
        }
	}
    
    
    // by swapnali
    
    // get_levels
    public function get_levels1($user_id){
        $data = array();
        $levels = $this->db->query("SELECT quiz_levels.*, qul.unlocked_levels, qul.`user_id`  FROM `quiz_levels` LEFT JOIN quiz_unlocked_levels as qul ON (quiz_levels.level_id = qul.unlocked_levels)")->result_array();
        foreach($levels as $lev){
            
            // print_r(sizeof($levels)); die();
            
            $levelData['level_id'] = $lev['level_id'];
            $levelData['level_name'] = $lev['level_name'];
            $levelData['weightage'] = $lev['weightage'];
            $levelData['description'] = $lev['description'];
            
            if($lev['user_id'] == $user_id){
                $levelData['unlocked_levels'] = "1";
            } else {
                $levelData['unlocked_levels'] = "0";
            }
            
            
            foreach($levelData as $key => $l){
                if($key == 'unlocked_levels' && $l != 1){
                    $levelData[$key] = "0";
                }
                if($l == null && $l != 0){
                    $levelData[$key] = "";
                }
                print_r($levelData);
            }
            $data[] = $levelData;
    
        }
        //  die();
        return $data;
	}
    
    public function get_levels($user_id){
        $data = array();
        $levels = $this->db->query("SELECT * FROM `quiz_levels`")->result_array();
        foreach($levels as $lev){
            
           // print_r(sizeof($levels)); die();
            $levId = $lev['level_id'];
            $levelData['level_id'] = $lev['level_id'];
            $levelData['level_name'] = $lev['level_name'];
            $levelData['weightage'] = $lev['weightage'];
            $levelData['description'] = $lev['description'];
            
            $count = $this->db->query("SELECT COUNT(*) as totalCount FROM `quiz_unlocked_levels` WHERE `user_id` = '$user_id' AND `unlocked_levels` = '$levId'")->row_array();
            if($count['totalCount'] > 0 || $levId == 1){
                $levelData['unlocked_levels'] = "1";
            } else {
                $levelData['unlocked_levels'] = "0";
                
            }
            
            
            
            
            foreach($levelData as $key => $l){
                if($key == 'unlocked_levels' && $l != 1){
                    $levelData[$key] = "0";
                }
                if($l == null && $l != 0){
                    $levelData[$key] = "";
                }
                
            }
            $data[] = $levelData;
    
        }
        
        return $data;
	}
	
	
// 	get_chances
    public function get_chances($user_id){
         $next_free_chance =  "00:00:00";
        $free_chance_sec = "";
        $next_chance_at = "";
        $data = array();
       $created_at = 0;
        $allowedChances = 5;
        date_default_timezone_set('Asia/Kolkata');
        // 'Asia/Kolkata'
        $today_date = date('Y-m-d H:i:s');
        $yesterday = date('Y-m-d',strtotime("-1 days"));
          $tomorrow = date('Y-m-d 00:00:00',strtotime("+1 days"));
       // die();
        //   $tomorrow = date('Y-m-d',strtotime("6:00:00"));
        $startTime = strtotime($today_date);
        $sixHrsBeforeTime = date("Y-m-d H:i:s", strtotime('-6 hours', $startTime));
        $getAllRows = $this->db->query("SELECT * FROM `quiz_users_answer` WHERE `user_id` = $user_id AND `created_at` > '$sixHrsBeforeTime' AND `created_at` < '$today_date' AND `created_at` > '$yesterday' GROUP BY `created_at`   ORDER BY `created_at` DESC ")->result_array();
        
        $usedChances = sizeof($getAllRows);
        foreach($getAllRows as $r){
            $created_at = $r['created_at'];
        }
        // echo $created_at;
        if($created_at > 0){
            $next_at = strtotime($created_at);
            $next_free_chance_time =  date("Y-m-d H:i:s", strtotime('+6 hours', $next_at));
            $next_chance_at = date("h:i A", strtotime('+6 hours', $next_at));
            // echo $tomorrow;
            // echo $next_free_chance_time;
            if($next_free_chance_time > $tomorrow){
                 $datetime1 = new DateTime($tomorrow);
            } else {
                $datetime1 = new DateTime($next_free_chance_time);
            }
               
               
               // $datetime1 = new DateTime($next_free_chance_time);
                $datetime2 = new DateTime($today_date);
                $interval = $datetime1->diff($datetime2);
                $hour  = $interval->h;
                $min = $interval->i;
                $sec = $interval->s;
                $hour_num = sprintf("%02d", $hour);
                $min_num = sprintf("%02d", $min);
                $sec_num = sprintf("%02d", $sec);
                $next_free_chance = $hour_num.":".$min_num.":".$sec_num;
                
                $free_chance_sec = ($hour_num*3600)+($min_num*60)+($sec_num);
            
            // die();
            // $next_free_chance_in = strtotime($next_free_chance_time) - strtotime($today_date);
            
           
        }
         
        $remainingChances = $allowedChances - $usedChances;
        $data['remaining_chances'] = $remainingChances;
        $data['next_free_chance'] = $next_free_chance;
        $data['free_chance_sec'] = $free_chance_sec;
        $data['next_chance_at'] = $next_chance_at;
     //   print_r($data); die();
        return $data;
    }

// 	get_quiz_questions
    public function get_quiz_questions($user_id,$level_id){
        date_default_timezone_set('Asia/Kolkata');
        $system_date = date('Y-m-d H:i:s');
        $limit = 40;
        $number_of_options = $count = 0;
        $index = 1;
        $level_name = "";
        $quedata = $data = array();
       // $hintsNlevel = $this->db->query("SELECT * FROM `quiz_users_info` WHERE `user_id` = '$user_id' AND `level` = '$level_id'")->row_array();
      
        $hintsNlevel = $this->db->query("SELECT m.source ,u.avatar_id, qu.*,ql.lives_to_play as lives, ql.hints_to_play as hints, ql.gni_help FROM quiz_users_info as qu LEFT JOIN quiz_levels as ql ON(qu.`level` = ql.level_id) LEFT JOIN users as u ON ( qu.user_id = u.id ) LEFT JOIN media as m ON ( u.avatar_id = m.id )  WHERE `user_id` = '$user_id' AND `level` = '$level_id'")->row_array();
        $unlocked = $this->db->query("SELECT * FROM `quiz_unlocked_levels` WHERE `user_id` = '$user_id' AND `unlocked_levels` = '$level_id'")->result_array();

        
        $avatar = $hintsNlevel['source'];
        if($avatar == "" || $avatar == null){
            $profile_pic = "";
        } else {
            $profile_pic = "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$avatar;
        }
        
        if(!empty($unlocked) && sizeof($hintsNlevel) > 0){
            
            $rows = $this->db->query("SELECT qq.*, ql.level_name  FROM `quiz_questions` as qq LEFT JOIN `quiz_levels` as ql ON (qq.`level` = ql.`level_id`) WHERE qq.`level` = '$level_id' ORDER BY RAND()")->result_array();
            $count = sizeof($rows);
            
            foreach($rows as $r){
                
                $options = array();
                $ques['index']  = $index;
                $ques['que_id']  = $r['que_id'];
                $ques['question']  = $r['question'];
                $ques['que_type']  = $r['que_type'];
                $ques['que_media_url']  = $r['que_media_url'];
                $ques['answer']  = $r['answer'];
                $ques['points']  = $r['points'];
                $level_name = $r['level_name'];
                
                for($i = 1;$i <= 4;$i++){
                    $o = "option_".$i;
                    if($r[$o] != null || $r[$o] == 0){
                        $count++;
                        if($r[$o] != ''){
                            $option['option'] = $r[$o];
                            $option['options_id'] = $i;
                            
                            $options[] = $option;    
                        }
                        
                        
                    }
                }
                $number_of_options = sizeof($options); 
                $ques['number_of_options']  = $number_of_options;
                
                shuffle($options);
                
                $ques['options']  = $options;
                
                
                $quedata[] = $ques;
                $index++;
                
            }
            $querevdata = array();
            $querevdata = array_reverse($quedata);
            
            $data['user_image'] = $profile_pic;
            $data['question_count'] = sizeof($quedata);
            $data['level_id'] = $level_id;
            $data['level_name'] = $level_name;
            $data['hints'] = $hintsNlevel['hints']; 
            $data['lives'] = $hintsNlevel['lives']; 
            $data['gni_help'] = $hintsNlevel['gni_help']; 
            $data['questions'] = $querevdata;
        } else {
            $data = array();
        }

        
        
        
        return $data;
    }
    
    // post_quiz_answers
    
    public function post_quiz_answers($user_id,$quiz_type,$result,$level_id,$hints,$lives,$question_count,$used_lives,$used_gni){
        // print_r(json_decode($result)); die();
        $results = json_decode($result);
        
       
        $date = new DateTime();
        $currentTime =  $date->format('Y-m-d h:i:s');
        $totalQues = $earnedPoints = $attempts = $attempedQuestions = $rightAns = $wrongAns = $ansNotGiven =  0;
        // $userLevelInfo = $this->db->query("SELECT * FROM `quiz_users_history` WHERE `user_id` = '$user_id' AND `level` = '$level_id'")->row_array();
        $userLevelInfo = $this->db->query("SELECT * FROM `quiz_users_info` WHERE `user_id` = '$user_id' AND `level` = '$level_id'")->row_array();
        
        if(sizeof($userLevelInfo) > 0){
            $attempts = $userLevelInfo['attempts'] + 1;
            $user_points = $userLevelInfo['points'];
        } else {
            $attempts =  1;
        }
        $totalQues =  $question_count - $used_lives - $used_gni;
        if($quiz_type == 'single'){
            $data = array();
            date_default_timezone_set('Asia/Kolkata');
                // 'Asia/Kolkata'
                $today_date = date('Y-m-d H:i:s');
            if(sizeof($results) > 0){
                foreach($results as $result){
           
                $points = $result->points;
                $que_id = $result->que_id;
                $answer = $result->answer;
                $given_ans = $result->given_ans;
                $que_level_id = $result->level_id;
               
                
                $this->db->query("INSERT INTO `quiz_users_answer`(`user_id`, `que_no`, `que_ans`, `given_answer`, `attempt`,`created_at`) VALUES ('$user_id','$que_id','$answer','$given_ans','$attempts','$today_date')");
                
                if($answer == $given_ans){
                    $earnedPoints = $earnedPoints + $points;
                    $rightAns = $rightAns + 1;
                    $attempedQuestions = $attempedQuestions + 1;
                }  else if($given_ans == null) {
                    
                    $ansNotGiven = $ansNotGiven + 1;
                } else {
                    $attempedQuestions = $attempedQuestions + 1;
                    $wrongAns = $wrongAns + 1;
                }
                
               
             }
           
                if(sizeof($userLevelInfo) > 0){
                    $id = $userLevelInfo['id'];
                    $user_total_points = $user_points + $earnedPoints;
                    $this->db->query("UPDATE `quiz_users_info` SET `points` = $user_total_points, `attempts` = '$attempts', `hints` = '$hints', `lives` ='$lives' WHERE `id` = '$id'");
                } else {
                    $this->db->query("INSERT INTO `quiz_users_info`(`user_id`, `level`, `points`, `attempts`, `created_at`,`hints`,`lives`) VALUES ('$user_id','$level_id','$earnedPoints','$attempts','$currentTime','$hints','$lives')");
                    $id = $this->db->insert_id();
                    
                }
                                                                                                                                                                                                                                          
                $this->db->query("INSERT INTO `quiz_users_history`(`user_id`, `level`, `earned_points`, `wrong_ans`,`right_ans`,`not_given`,`attempts`) VALUES ('$user_id','$level_id','$earnedPoints','$wrongAns','$rightAns','$ansNotGiven','$attempts')");
                
               // $this->db->query("INSERT INTO `quiz_users_info`(`user_id`,`user_points`,`created_at`) VALUES ('$user_id','$created_at')");
                
                $existingPoints = $this->db->query("SELECT * FROM `quiz_user_points` WHERE `user_id` = '$user_id' ")->row_array();
                
                $user_points = $existingPoints['user_points'] + $earnedPoints;    
                 
                $this->db->query("UPDATE `quiz_user_points` SET `user_points` = $user_points, `created_at` = '$currentTime' WHERE `user_id` = '$user_id'");
                    
                
                
                $bonus = $this->db->query("SELECT * FROM `quiz_levels` WHERE `level_id` = '$level_id'")->row_array();
                
                if($rightAns >= $totalQues){
                    $data['tag'] = "Congratulations!";
                    $data['level_completed'] = "yes";
                    $data['Bonus'] = $bonus['bonus']; 
                    
                    
                    
                    $newLevel = $level_id + 1;
                    $unlockedInfo = $this->db->query("SELECT * FROM `quiz_unlocked_levels` WHERE `user_id` = '$user_id' AND  `unlocked_levels` = '$newLevel'")->row_array();
                    
                    if(sizeof($unlockedInfo) > 0){
                        $uId = $unlockedInfo['id'];
                        $data['unlocked_level'] = 0;  
                    } else {
                        $data['unlocked_level'] = $newLevel;
                        $newAttempts = 0;
                        
                        // echo "INSERT INTO `quiz_users_info`( `user_id`, `level`,  `attempts`, `created_at`) VALUES ('$user_id','$newLevel','$newAttempts','$currentTime')";
                        // die();
                        $this->db->query("UPDATE `quiz_users_info` SET `level_completed` = 1 WHERE `id` = '$id'");
                        
                        $this->db->query("INSERT INTO `quiz_users_info`( `user_id`, `level`,  `attempts`, `created_at`) VALUES ('$user_id','$newLevel','$newAttempts','$currentTime')");
                        
                        $this->db->query("INSERT INTO `quiz_unlocked_levels`(`user_id`, `unlocked_levels`) VALUES ('$user_id','$newLevel')");
                        
                        
                    }
                } else {
                    $data['tag'] = "Oops!";
                    $data['level_completed'] = "no";
                    $data['Bonus'] = 0;
                }
                
                $ansNotGiven = $question_count - $rightAns - $wrongAns;
                
                
            
                $level_rank = $this->Health_quizModel->get_level_rank($user_id,$level_id,$attempts);
                
                
                 $chances = $this->Health_quizModel->get_chances($user_id); 
					   // print_r($chances); die();
			    $remaining_chances = strval($chances['remaining_chances']);
			    $next_free_chance = $chances['next_free_chance'];
			    $free_chance_sec = strval($chances['free_chance_sec']);
			    $next_chance_at = $chances['next_chance_at'];
                
                $data['current_level_id'] = $bonus['level_id'];
                $data['current_level_name'] = $bonus['level_name'];
                
                
                $data['total_questions'] = $question_count;
                $data['earned_points'] = $earnedPoints;
                $data['attempt'] = $attempts;
                $data['attemped_questions'] = $attempedQuestions;
                $data['right_ans'] = $rightAns;
                $data['wrong_ans'] = $wrongAns;
                $data['non_ans_que'] = $ansNotGiven;
                $data['level_rank'] = $level_rank;
                
                 $data['remaining_chances'] = $remaining_chances;
                $data['next_free_chance'] = $next_free_chance;
                $data['free_chance_sec'] = $free_chance_sec;
                $data['next_chance_at'] = $next_chance_at;
               
            } else {
                $data = 3;
            }
            
            // quiz_users_answer
            // quiz_users_history 
            // quiz_users_info
        } else if($quiz_type == 'multiplayer'){
            $data = 1;
        } else {
            $data = 2;
        }
        return $data;
    }
    // quiz_user_info
    
    public function quiz_user_info($user_id){
        $data = array();
        
        $userInfo = $this->db->query("SELECT * FROM `quiz_users_info` WHERE `user_id` = '$user_id'")->result_array();
        
        if(sizeof($userInfo) == 0){
            $level = 1;
            $points = 0;
            $hints = 0;
            $lives = 0;
            $attempts = 0;
            $created_at = Date("Y-m-d H:i:s");
            
            $this->db->query("INSERT INTO `quiz_users_info`(`user_id`, `level`, `points`, `hints`, `lives`, `attempts`, `created_at`) VALUES ('$user_id','$level','$points','$hints','$lives','$attempts','$created_at')");
            
            $this->db->query("INSERT INTO `quiz_unlocked_levels`(`user_id`, `unlocked_levels`) VALUES ('$user_id','$level')");
            
            $this->db->query("INSERT INTO `quiz_user_points`(`user_id`,`created_at`) VALUES ('$user_id','$created_at')");
            
            
            
            
            $userInfo = $this->db->query("SELECT * FROM `quiz_users_info` WHERE `user_id` = '$user_id'")->result_array();
        }
        
        foreach($userInfo as $u){
            foreach($u as $k => $v){
                if($v == null){
                    $u[$k] = "";
                }
                unset($u['created_at']);
                unset($u['updated_at']);
                unset($u['id']);
                
            }
            $data[] = $u;
        }
        return $data;
    }
    
    public function quiz_user_scoreboard($user_id){
        $data = array();
        $level_rank = $level_id = $level_name = $attempts = $earned_points = $total_rank = $total_points = "0";
        
        $basic_info = $this->db->query("SELECT qup.user_points, un.*, u.name, u.avatar_id, m.source, ql.level_id, ql.level_name, qui.points, qui.attempts, qui.level_completed FROM `quiz_unlocked_levels` as un LEFT JOIN users as u ON (un.user_id = u.id) left join media as m ON (u.parent_id = m.id) LEFT join quiz_levels as ql ON (un.unlocked_levels = ql.level_id ) LEFT JOIN quiz_users_info as qui ON (un.user_id = qui.user_id AND un.unlocked_levels = qui.level)  LEFT JOIN quiz_user_points as qup ON(un.user_id = qup.user_id )  WHERE un.`user_id` = '$user_id' GROUP by level_id")->result_array();
        if(sizeof($basic_info) > 0){
            foreach($basic_info as $b){
                // print_r($b); die();
                $name = $b['name'];
                $avatar = $b['source'];
                $attempts = $b['attempts'];
                $level_completed = $b['level_completed'];
                
                $earned_points = $b['points'];
                if($avatar == "" || $avatar == null){
                    $profile_pic = "";
                } else {
                    $profile_pic = "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$avatar;
                }
                $level_id = $b['unlocked_levels'];
                $level_name = $b['level_name'];
                
                $level_rank = $this->Health_quizModel->get_level_rank($user_id,$level_id,$attempts);
                if($level_completed == 1){
                    $level_comple = 'Yes';
                } else {
                    $level_comple = 'No';
                }
                $l['level_id'] = $level_id;
                $l['level_name'] =  $level_name;
                $l['attempts'] =  $attempts;
                $l['earned_points'] =  $earned_points;
                $l['level_rank'] =  $level_rank;
                $l['level_completed'] =  $level_comple;
                $levels[] = $l;
                // $total_points = $total_points + $earned_points;
                $total_points = $b['user_points'];
                
            }
                
            $data['user_id'] = $user_id;
            $data['user_name'] = $name;
            $data['user_image'] = $profile_pic;  
            $data['total_rank'] = $total_rank; 
            $data['total_points'] = $total_points;
            $data['levels'] = $levels;
            
        } else {
            $data = (object)[];
        }
        
        
        return $data;

    }
    
    // get_level_rank
    
    public function get_level_rank($user_id_rank,$level_id,$attempts){
        // echo $user_id; die();
        $all_ranks = array();
        $rank = 0;
        $all_users = $this->db->query("SELECT * FROM `quiz_users_info` WHERE `level` = '$level_id' and attempts != 0")->result_array();
        foreach($all_users as $a){
            // print_r($a); die();
            $user_id = $a['user_id'];
            $points = $a['points'];
            $attempts = $a['attempts'];
            if($points != 0 && $attempts != 0){
                $ratio = $points / $attempts;    
            } else {
                $ratio = 0;
            }
            $ranks['user_id'] = $user_id;
            $ranks['ratio'] = $ratio;
            $all_ranks[] = $ranks; 
            
        }
        
        $ak = array();
        foreach ($all_ranks as $key => $row){
           
                $ak[$key] = $row['ratio'];    
            
        }
        array_multisort($ak, SORT_DESC, $all_ranks);
        $c = 1;
        
        foreach($all_ranks as $ar){
            // print_r($ar); 
            if($ar['user_id'] == $user_id_rank){
               $rank = $c;    
            }
            $c++;
        }
        
        if($rank == 0){
            $rank = sizeof($all_ranks) + 1;
        }
        
        return $rank;
        
    }
    // quiz_leader_board
    public function quiz_leader_board($user_id,$level_id){
        $user_details = $data = array();
        $level_name = '';
        $user_info = $this->db->query("SELECT u.id, u.name, m.source , ql.level_name,  qui.* FROM `quiz_users_info` as qui LEFT JOIN users as u ON( qui.`user_id` = u.id ) LEFT join media as m ON (u.avatar_id = m.id) LEFT JOIN `quiz_levels` as ql ON (qui.`level` = ql.level_id )   WHERE `level` = '$level_id'  and qui.`attempts` != 0 ")->result_array();
        
        foreach($user_info as $u){
            $u_id = $u['user_id'];
            $attempts = $u['attempts'];
            $avatar = $u['source'];
            if($avatar == null){
                $user_image = "";
            } else {
                $user_image = "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$avatar;
            }
            
            $rank = $this->Health_quizModel->get_level_rank($u_id,$level_id,$attempts);
            
            $user['user_id'] = $u['user_id'];
            $user['user_name'] = $u['name'];
            $user['user_image'] = $user_image;
            $user['user_earned_points'] = $u['points'];
            $user['user_level_attempts'] = $attempts;
            $user['user_level_rank'] = $rank;
            $level_name = $u['level_name'];
            
            $user_details[] = $user;
        }
        
        $ak = array();
        foreach ($user_details as $key => $row){
            $ak[$key] = $row['user_level_rank'];    
        }
        array_multisort($ak, SORT_ASC, $user_details);
        
        // print_r($user_details); die();
        
        $data['level_id'] = $level_id;
        $data['level_name'] = $level_name;
        $data['users'] = $user_details;
        return $data;
    }
    // quiz_unlock_level
    public function quiz_unlock_level($user_id,$level_id){
        $message = '';
        $status = '';
        $data = array();
        $count=0;
        $levels_till_given_level = $this->db->query("SELECT `level_id`,`level_name` FROM `quiz_levels` WHERE `level_id` < '$level_id' ")->result_array();
        $unlocked_levels = $this->db->query("SELECT * FROM `quiz_unlocked_levels` WHERE `user_id` = '$user_id' AND `unlocked_levels` = '$level_id'")->result_array();
       
        if(sizeof($unlocked_levels) > 0){
            $data['status'] = 400;
            $data['required_points'] = '';
             $data['user_existing_points'] = '';
             $data['message'] = 'Level already unlocked';
            
            
            // return $data;
            
        } else {
            
            foreach($levels_till_given_level as $levels){
                $old_level_id = $levels['level_id'];
                $old_unlocked_levels = $this->db->query("SELECT * FROM `quiz_unlocked_levels` WHERE `user_id` = '$user_id' AND `unlocked_levels` = '$old_level_id'")->result_array();
                if(sizeof($old_unlocked_levels) == 0){
                    $l_name = $levels['level_name']; 
                    
                    $count++;
                    break;
                    
                }
            }
            if($count > 0){
                 $data['status'] = 201;
                $data['required_points'] = '';
                 $data['user_existing_points'] = '';
                 $data['message'] = 'Please first unlock '.$l_name;
               
            } else {
                $date = new DateTime();
                $currentTime =  $date->format('Y-m-d h:i:s');
                
                $level_info = $this->db->query("SELECT * FROM `quiz_levels` WHERE `level_id` ='$level_id' ")->row_array();
                
                $user_points_info = $this->db->query("SELECT * FROM `quiz_user_points` WHERE `user_id` = '$user_id'")->row_array();
                $required_points = $level_info['weightage'];
                $user_current_points = $user_points_info['user_points'];
               
                
                if($required_points <= $user_current_points){
                    $this->db->query("INSERT INTO `quiz_users_info`( `user_id`, `level`,   `created_at`) VALUES ('$user_id','$level_id','$currentTime')");
                    $this->db->query("INSERT INTO `quiz_unlocked_levels`(`user_id`, `unlocked_levels`) VALUES ('$user_id','$level_id')");
                    $remaining_points = $user_current_points - $required_points;
                    $this->db->query("UPDATE `quiz_user_points` SET `user_points` = '$remaining_points' WHERE `user_id` = '$user_id'");        
                    $data['status'] = 200;
                    $data['required_points'] = $required_points;
                     $data['user_existing_points'] = $remaining_points;
                     $data['message'] = 'Level successfully unlocked';
                    
                }   else {
                           
                    $data['status'] = 200;
                    $data['required_points'] = $required_points;
                     $data['user_existing_points'] = $user_current_points;
                     $data['message'] = 'This level requires '.$required_points.' points, you have '.$user_current_points.' points';
                    
                }             
                
            }
            
            
        }
        return $data;
    }
    
  public function sponsored_ads_list()
   {
       $today = date('Y-m-d');
       $res = $this->db->query("SELECT sa.*, vt.vendor_name FROM sponsored_advertisements as sa LEFT JOIN vendor_type as vt ON (sa.vendor_type = vt.id) WHERE sa.status = 1 AND sa.expiry >= '$today' AND sa.show_vendor_type='43' ")->result_array();
        if(!empty($res))
        {
            foreach($res as $res1)
            {
               $row['id'] = $res1['id'];

               $row['ad_main_cat'] = $res1['ad_main_cat'];
               $row['main_cat_id'] = $res1['main_cat_id'];
               $main_cat_id = $res1['main_cat_id'];
               if($res1['ad_main_cat'] == "1")            // brand
               {
                   $query_info = $this->db->query("SELECT v_company_name FROM vendor_details_hm WHERE  v_id='$main_cat_id'");
                   $data_info= $query_info->row();
                   $main_cat_name = $data_info->v_company_name;
               }
               else if($res1['ad_main_cat'] == "2")            // brand
               {
                   $query_info = $this->db->query("SELECT pd_name FROM product_details_hm WHERE  pd_id='$main_cat_id'");
                   $data_info= $query_info->row();
                   $main_cat_name = $data_info->pd_name;
               }
               $row['main_cat_name'] = $main_cat_name;
               $row['ad_image'] = "https://s3.amazonaws.com/medicalwale/images/Sponsored_files/".$res1['ad_image'];
               $row['vendor_type'] = $res1['vendor_type'];
               $row['vendor_name'] = $res1['vendor_name'];
               if($res1['vendor_type']=="13" or $res1['vendor_type']=="34" or $res1['vendor_type']=="38")
               {
                   $row['Type'] = "Buy Now";
               }
               else
               {
                   $row['Type'] = "Book Now";
               }
               $data[] = $row;
            }
        }
        else
        {
            $data=array();
        }

       return $data;
   }
}