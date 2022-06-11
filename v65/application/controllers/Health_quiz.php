<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Health_quiz extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
         $this->load->model('Health_quizModel');
          $this->load->model('LoginModel');
        /*
        $check_auth_client = $this->SexeducationModel->check_auth_client();
		if($check_auth_client != true){
			die($this->output->get_output());
		}
		*/
    }
	public function index()
	{
	    json_output(400,array('status' => 400,'message' => 'Bad request.'));
	}
	
	
	public function Get_all_quizdata()
	{
	       	$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Health_quizModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					/*print_r($params);
					die();*/
					if ($params['user_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $user_id       = $params['user_id'];
					    $test_no       = $params['test_no'];
					 $resp  = $this->Health_quizModel->Get_all_quizdata($user_id,$test_no);
					}
					    simple_json_output($resp);
				
		        }
			}
		}
	}
	
	public function Get_result_data()
	{
	       	$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Health_quizModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					/*print_r($params);
					die();*/
					if ($params['user_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $user_id       = $params['user_id'];
					    $is_winner       = $params['is_winner'];
					    $total_score   = $params['total_score'];
					    $total_question = $params['total_question'];
					    $total_point = $params['total_point'];
					    $opponent_id = $params['opponent_id'];
					   $resp  = $this->Health_quizModel->Get_result_data($user_id,$is_winner,$total_score,$total_question,$total_point,$opponent_id);
					}
					    simple_json_output($resp);
				
		        }
			}
		}
	}
	
	public function Get_all_history()
	{
	       	$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Health_quizModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					/*print_r($params);
					die();*/
					if ($params['user_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $user_id       = $params['user_id'];
					 $resp  = $this->Health_quizModel->Get_all_history($user_id);
					}
					    simple_json_output($resp);
				
		        }
			}
		}
	}
	
// 	by swapnali

	public function get_levels(){
	       	$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->LoginModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					/*print_r($params);
					die();*/
					if ($params['user_id'] == "") {
						$result = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $user_id  = $params['user_id'];
					    $resp  = $this->Health_quizModel->get_levels($user_id);
					    $chances = $this->Health_quizModel->get_chances($user_id); 
					   // print_r($chances); die();
					    $remaining_chances = strval($chances['remaining_chances']);
					    $next_free_chance = $chances['next_free_chance'];
					    $free_chance_sec = strval($chances['free_chance_sec']);
					    $next_chance_at = $chances['next_chance_at'];
					    $result = array('status' => 200,'message' =>  'success', 'desciption' => 'Get levels and locked - unlocked status wrt user','remaining_chances' => $remaining_chances, 'next_free_chance' => $next_free_chance ,'free_chance_sec' => $free_chance_sec, 'next_chance_at' => $next_chance_at, 'data' => $resp);
					}
					    simple_json_output($result);
				
		        }
			}
		}
	}
	
// 	get_quiz_questions

    public function get_quiz_questions(){
	       	$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Health_quizModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					/*print_r($params);
					die();*/
					if ($params['user_id'] == "" || $params['level_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields user_id and level_id');
					} else {
					    $user_id  = $params['user_id'];
					    $level_id = $params['level_id'];
					 $result  = $this->Health_quizModel->get_quiz_questions($user_id,$level_id);
					 if(sizeof($result) > 0){
    					 $resp = array('status' => 200,'message' =>  'success','description' => 'que_type will be text / image / gif / video (ALL SMALL), if its other than text you will get url in que_media_url key','data' => $result);					     
					 } else {
					     $resp = array('status' => 400,'message' =>  'failed', 'description' => 'Level is not unlocked yet.');		
					 }

					}
					    simple_json_output($resp);
				
		        }
			}
		}
	}
	
	
	
// 	post_quiz_answers

    public function post_quiz_answers(){
        $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
		    $check_auth_client = $this->LoginModel->check_auth_client();
			if($check_auth_client == true){
			    $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
		            
		            $user_id = $this->input->post('user_id');
		            $quiz_type = $this->input->post('quiz_type');
		            $result = $this->input->post('result');
		            $level_id = $this->input->post('level_id');
		            $hints = $this->input->post('remaining_hints');
		            $lives = $this->input->post('remaining_lives');
		            $question_count = $this->input->post('question_count');
		            $used_lives = $this->input->post('used_lives');
		            $used_gni = $this->input->post('used_gni');
		            if(empty($used_gni)){
		                $used_gni = 0;
		            }
		             
		            
                    if ($user_id == "" || $quiz_type == "" || $result == "" || $level_id == "" || $hints == "" || $lives == "" || $question_count == "" || $used_lives == "") {
					    
						$resp = array('status' => 400,'message' =>  'please enter fields user_id, quiz_type, level_id, result, remaining_hints, remaining_lives, question_count and used_lives');
					} else {
					    
					    $res  = $this->Health_quizModel->post_quiz_answers($user_id,$quiz_type,$result,$level_id,$hints,$lives,$question_count,$used_lives,$used_gni);
					    $resp = array('status' => 200,'message' =>  'success','description' => ' ','data' => $res);
					}
					
				    if($res == 1){
				        $resp = array('status' => 201,'message' =>  'coming soon');
				    }else  if($res == 2){
				        $resp = array('status' => 201,'message' =>  'please add quiz_type either single or multiplayer');
				    } else if($res == 3){
				        $resp = array('status' => 201,'message' =>  'result is not in perfect form');
				    } 
				    simple_json_output($resp);
				        
				
		        }
			} else {
			    json_output(400,array('status' => 400,'message' => 'Authentication failed.'));
			}
		}
	}
	
// 	9th march
     public function quiz_user_info(){
        $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
		    $check_auth_client = $this->LoginModel->check_auth_client();
			if($check_auth_client == true){
			    $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
		            
		            $user_id = $this->input->post('user_id');
		           
		            
                    if ($user_id == "") {
					    
						$resp = array('status' => 400,'message' =>  'please enter user_id');
					} else {
					    
					    $res  = $this->Health_quizModel->quiz_user_info($user_id);
					    $resp = array('status' => 200,'message' =>  'success','description' => ' ','data' => $res);
					}
					
				    simple_json_output($resp);
				        
				
		        }
			} else {
			    json_output(400,array('status' => 400,'message' => 'Authentication failed.'));
			}
		}
	}
	
// 	

    public function quiz_user_scoreboard(){
        $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
		    $check_auth_client = $this->LoginModel->check_auth_client();
			if($check_auth_client == true){
			    $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
		            
		            $user_id = $this->input->post('user_id');
		           
                    if ($user_id == "") {
						$resp = array('status' => 400,'message' =>  'please enter user_id');
					} else {
					    $res  = $this->Health_quizModel->quiz_user_scoreboard($user_id);
					    
					    if(array_key_exists('user_id',$res)){
					        $resp = array('status' => 200,'message' =>  'success','description' => ' ','data' => $res);    
					    } else {
					        $resp = array('status' => 201,'message' =>  'failed','description' => 'Please play quiz first ','data' => $res);
					    }
					}
				    simple_json_output($resp);
		        }
			} else {
			    json_output(400,array('status' => 400,'message' => 'Authentication failed.'));
			}
		}
	}

// leader
	public function quiz_leader_board(){
        $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
		    $check_auth_client = $this->LoginModel->check_auth_client();
			if($check_auth_client == true){
			    $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
		            
		            $user_id = $this->input->post('user_id');
		            $level_id = $this->input->post('level_id');
		           
                    if ($user_id == "" || $level_id =="") {
						$resp = array('status' => 400,'message' =>  'please enter user_id and level_id');
					} else {
					    $res  = $this->Health_quizModel->quiz_leader_board($user_id,$level_id);
					    
					    
					        $resp = array('status' => 200,'message' =>  'success','description' => ' ','data' => $res);    
					    
					}
				    simple_json_output($resp);
		        }
			} else {
			    json_output(400,array('status' => 400,'message' => 'Authentication failed.'));
			}
		}
	}
// 	quiz_unlock_level
	public function quiz_unlock_level(){
        $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
		    $check_auth_client = $this->LoginModel->check_auth_client();
			if($check_auth_client == true){
			    $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
		            
		            $user_id = $this->input->post('user_id');
		            $level_id = $this->input->post('level_id');
		           
                    if ($user_id == "" || $level_id =="") {
						$resp = array('status' => 400,'message' =>  'please enter user_id and level_id');
					} else {
					    $resp  = $this->Health_quizModel->quiz_unlock_level($user_id,$level_id);
					    
					    
					       // $resp = array('status' => 200,'message' =>  'success','description' => ' ','data' => $res);    
					    
					}
				    simple_json_output($resp);
		        }
			} else {
			    json_output(400,array('status' => 400,'message' => 'Authentication failed.'));
			}
		}
	}
	
	
// sponsored_ads_list
	public function sponsored_ads_list()
	{
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->LoginModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					/*print_r($params);
					die();*/
					if ($params['user_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields', 'description' => 'user_id is mandatory');
					} else {
					    $user_id = $params['user_id'];
					 
					     $result = $this->Health_quizModel->sponsored_ads_list($user_id);
					     $resp = array('status' => 200,'message' =>  'success', 'count'=>count($result) ,'description' => ' in healthmall ad_for => 1 means brands and 2 for products', 'data' => $result);
					}
					    
					    simple_json_output($resp);
				
		        }
			}
		}
	}
    
    
}