<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Charity_model extends CI_Model
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
   
    public function check_time_format($time) 
    {
        $time_filter = preg_replace('/\s+/', '', $time);
        $final_time = date("h:i A", strtotime($time_filter));
        return $final_time;
    } 
  
    public function charity_list($user_id,$type,$page,$search)
    {
        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        $resultpost=array();
       
       
       
            
          if($search!="")
          {
               $sql   = "SELECT * FROM charity_detail WHERE is_approval='1' AND charity_cat = '$type' AND (name_1 LIKE '%%$search%%' OR  name_2 LIKE '%%$search%%') ORDER BY id desc LIMIT $start, $limit";
           }
          else
          {
            
             $sql   = "SELECT * FROM charity_detail WHERE is_approval='1' AND charity_cat = '$type'  ORDER BY id desc LIMIT $start, $limit";
          }
            $query = $this->db->query($sql);
            $count = $query->num_rows();
            if($count>0){
                
                  $sal_title="SELECT * FROM `charity_category` WHERE id='$type'";
                  $query1 = $this->db->query($sal_title);
                  $row1=$query1->row_array();
                  $title=$row1['category'];
                  $info=$row1['info'];
                foreach ($query->result_array() as $row) 
                {
                   
                    $id = $row['id'];
                    $charity_cat = $row['charity_cat'];
                    $name_1 = $row['name_1'];
                    $name_2 = $row['name_2'];
                    $image="";
                    $imager = $row['image'];
                    if(!empty($imager))
                    {
                       $image="https://d2c8oti4is0ms3.cloudfront.net/images/Charity/".$imager;
                    }
                    else
                    {
                      $image="";  
                    }
                    $totalfund = $row['totalfund'];
                    $receive_fund = $row['receive_fund'];
                    $donate_count = $row['donate_count'];
                    $share_link = 'https://medicalwale.com/';
                   
                        
                    $resultpost[] = array(
                        'id' => $id,
                        'charity_type' => $charity_cat,
                        'charity_name_1' => $name_1,
                        'charity_name_2' => $name_2,
                        'image' => $image,
                        'total_goal_amount' => $totalfund,
                        'raised_amount' => $receive_fund,
                        'no_people_donated' => $donate_count,
                        'share_link' => $share_link,
                        
                    );
                }
                 
                $res = array(
                                "status" => 200,
                                "message" => "success",
                                "title"=>$title,
                                "info"=>"",
                                "count"=>$count,
                                "data" => $resultpost
                            );
              
            }
            else{
            $res = array(
                          "status" => 200,
                          "message" => "success",
                          "title"=>"",
                          "info"=>"",
                          "count"=>0,
                          "data" => array()
                        );
                
            }
        
     
      
        
       return $res;
       
    }
    
  
   
   
}
   
?>