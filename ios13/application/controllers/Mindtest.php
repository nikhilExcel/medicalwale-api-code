<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mindtest extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
         $this->load->model('MindtestModel');
          $this->load->model('LoginModel');
       
    }
	public function index()
	{
	    json_output(400,array('status' => 400,'message' => 'Bad request.'));
	}
	
	
	
    public function mindtest_list() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->MindtestModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                if ($params['user_id'] == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter fields'
                    );
                } else {
                    $user_id = $params['user_id'];
                    $resp = $this->MindtestModel->mindtest_list($user_id);
                }
                json_output($resp['status'], $resp);
            }
        }
    }
    
    public function update_mindtest() {
        $this->load->model('MindtestModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $user_id = $this->input->post('user_id');
            $question = $this->input->post('question');
            $questions = json_decode($question);
         //   print_r($question); die;
            $pre_final_que = $questions->quastion;
           
            $final_que = $pre_final_que[0];
            $q_user_id = $final_que->user_id;
            $final_q = $final_que->qas;
            $data2 = array();
            $resp = $this->MindtestModel->delete_mindtest($q_user_id);
            for($i=0;$i<sizeof($final_q);$i++){
                 $data2['user_id'] = $q_user_id;   
                 $data2['question_id'] = $final_q[$i]->qid;
                 $data2['answer'] = $final_q[$i]->qans;
                 $resp = $this->MindtestModel->update_mindtest($data2);
            }
            simple_json_output($resp);
        }
    }


}
