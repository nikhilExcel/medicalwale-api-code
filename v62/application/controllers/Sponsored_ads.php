<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sponsored_ads extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
         $this->load->model('Sponsored_adsModel');
          $this->load->model('LoginModel');
    }
	public function index()
	{
	    json_output(400,array('status' => 400,'message' => 'Bad request.'));
	}
	
	
	public function sponsored_ads_list()
	{
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Sponsored_adsModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Sponsored_adsModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					/*print_r($params);
					die();*/
					if ($params['user_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields', 'description' => 'user_id is mandatory');
					} else {
					    $user_id = $params['user_id'];
					 
					     $result = $this->Sponsored_adsModel->sponsored_ads_list($user_id);
					     $resp = array('status' => 200,'message' =>  'success', 'count'=>count($result) ,'description' => ' in healthmall ad_for => 1 means brands and 2 for products', 'data' => $result);
					}
					    
					    simple_json_output($resp);
				
		        }
			}
		}
	}
	public function add_sponsored_ads() {
        $this->load->model('Sponsored_adsModel');
         	$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
	      	if ($this->input->post('user_id') == "" || count($_FILES) == 0 ) {
						$resp = array('status' => 400,'message' =>  'please enter fields');
			} 
			else {
					    $user_id = $this->input->post('user_id');
                        $add_for = $this->input->post('add_for');   //brand or product
                        $add_for_id = $this->input->post('add_for_id');
                        
                        $listing_type = $this->input->post('listing_type');
                        $expiry_date = $this->input->post('expiry_date');
                        $gender = $this->input->post('gender');
                        $age_from = $this->input->post('age_from');
                        $age_to = $this->input->post('age_to');
                        $blood_group = $this->input->post('blood_group');
                        $diet_fitness = $this->input->post('diet_fitness');
                        $addiction = $this->input->post('addiction');
                        $med_condition = $this->input->post('med_condition');
                        $allergies = $this->input->post('allergies');
                        
					    $source_file = "";
					    $cny = count($_FILES);
					    if($cny > 0){
					    $source_file = $_FILES['source']['name'];
					    }
					    
					    $resp = $this->Sponsored_adsModel->add_sponsored_ads($user_id,$add_for,$add_for_id,$listing_type,$expiry_date,$gender,$age_from,$age_to,$source_file,$blood_group,$diet_fitness,$addiction,$med_condition,$allergies);
					
					}
			simple_json_output($resp);
		}
    }
    
    public function question_list($user_id){
        $query = $this->db->query("SELECT * FROM userprofile_question");
        $count = $query->num_rows();
        $items = $query->result_array();

             $id = '';   
             $ar = array();
              foreach ($items as $item) {
                
                if($item['question_type'] == 0){
                    
                    
                    $ar['question'] =  $item['question'];
                    $id = $item['id'];
                    $ar['que_id'] = $item['id'];
                    $query = $this->db->query("SELECT * FROM userprofile_question_answer WHERE question_id='$id' and user_id='$user_id'");
                    
                    $items2 = $query->row_array();
                   
                    $r = $this->sub($items, $id, $user_id);
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
    function sub($items,$id,$user_id){
        $ar1 =  array();
        $ar = array();      
       foreach ($items as $item) {
           $ar = array();  
       if($item['question_type'] == $id){
           
             $ar['question'] = $item['question'];
            $ID = $item['id'];
             $ar['que_id'] = $item['id'];
            $query = $this->db->query("SELECT * FROM userprofile_question_answer WHERE question_id='$ID' and user_id='$user_id'");
            
            //echo "SELECT * FROM userprofile_question_answer WHERE question_id='$ID'";
            $items2 = $query->row_array();
            
            $r = $this->sub($items,$item['id'],$user_id); 
            
            if(!empty($r)){
            $ar['sub_que'] = $r;
            }
            if($items2['answer'] == null)
                    {
                        $ar['ans'] = "";
                    }
                    else{
                        
                        $query12 = $this->db->query("SELECT id,user_id,question_id,GROUP_CONCAT(answer) as new_ans FROM userprofile_question_answer WHERE  question_id='$ID' and user_id='$user_id'");
                        //echo "SELECT * FROM userprofile_question_answer WHERE question_id='$ID'";
                        $items23 = $query12->row_array();    
                        $ar['ans'] = $items23['new_ans'];
                    }
            $ar1[] = $ar;
        }
      }
      
      return $ar1;  
    }
}