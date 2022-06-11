<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MindtestModel extends CI_Model {
    
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
    
    public function mindtest_list($user_id){
        
        $query_lang = $this->db->select('language')->from('users')->where('id', $user_id)->get()->row();
        $lang=$query_lang->language; 
        
        $query = $this->db->query("SELECT * FROM tbl_mindtest_master where language='$lang'");
        $count = $query->num_rows();
        $data = array();
        if($count > 0 )
        {
            $items = $query->result_array();
        
             $id = '';   
             $ar = array();
              foreach ($items as $item) {
                
                if($item['sub_cat'] == 0){
                    
                    
                    $ar['mind_test'] =  $item['mind_test'];
                    $id = $item['id'];
                    $ar['que_id'] = $item['id'];
                    $r = $this->sub($items, $id, $user_id);
                    $ar['sub_que'] = $r;
                
                    $data[] = $ar;
                }
                   
              }
    
              return array(
                    'status' => 201,
                    'message' => 'success',
                    'data'=>$data
                );
        }else
        {
             return array(
                    'status' => 200,
                    'message' => 'fail',
                    'data'=>$data
                );
        }
              
    }
    function sub($items,$id,$user_id){
        $ar1 =  array();
        $ar = array();      
       foreach ($items as $item) {
           $ar = array();  
       if($item['sub_cat'] == $id){
           
            $ar['mind_test'] = $item['mind_test'];
            $ID = $item['id'];
             $ar['que_id'] = $item['id'];
             //echo "SELECT * FROM mindtest_answer WHERE question_id='$ID' and user_id='$user_id'";
            $query = $this->db->query("SELECT * FROM mindtest_answer WHERE question_id='$ID' and user_id='$user_id'");
            
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
                   $ar['ans'] = $items2['answer'];
                    }
            $ar1[] = $ar;
        }
      }
      
      return $ar1;  
    }   
    
    public function update_mindtest($data2){
       
       
        $this->db->insert('mindtest_answer', $data2);
        return array(
                    'status' => 200,
                    'message' => 'success'
                );
    }
    
    public function delete_mindtest($user_id){
        
       $this->db->where('user_id', $user_id);
        $this->db->delete('mindtest_answer');
   
    }
}
