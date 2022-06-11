<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class HealthDictionaryModel extends CI_Model {

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
    
   public function WordOfTheDayModel($userid)
   {
        date_default_timezone_set('Asia/Calcutta');
           $created_at = date('Y-m-d');
        $date = date('Y-m-d');
   $first_day_moth = date('Y-m-01');
    //Get the first day of the month.
   //  $firstOfMonth = strtotime(date("Y-m-d", $date));
    //Apply above formula.
  
         
         
          
           
           // echo 'test '.$week_number =  intval(date("W", $date)) - intval(date("W", $firstOfMonth)) + 1;
           
            $Quatequery = $this->db->query("SELECT * FROM Health_quote_dictionary WHERE date<='$date' ORDER BY id DESC LIMIT 1 ");
            $QuateCount = $Quatequery->num_rows();
            $QuateData = $Quatequery->row();
             
             
             if ($QuateCount>0)
             {
                 $quotes = $QuateData->Health_Quote;
             }
             else
             {
                 $quotes = "";
             }
                  //echo "SELECT * FROM HealthDictionary WHERE date='$date'";
                  $query = $this->db->query("SELECT * FROM HealthDictionary_new WHERE date='$date'");
                  $count = $query->num_rows();
                  $resultData = $query->row();
              
              if($count>0)
              {
                 $word = $resultData->word;
                 $meaning = $resultData->meaning;
                 $today = $resultData->Date;
              }
              else
              {
                  $word ="";
                  $meaning = "";
                  $today = "";
              }
                 
                 $finalRersult = array(
                          'word' =>$word,
                          'meaning' => $meaning,
                          'today' =>$today,
                          'quotes' =>$quotes 
                     );
              
                return array(
                    'status' => 200,
                    'message' => 'success',
                    'data' => $finalRersult
                );
             
   }
   
   
    public function WordOfTheDay_By_date($userid,$date)
   {
       //    date_default_timezone_set('Asia/Calcutta');
       //    $created_at = date('Y-m-d');
        //    $date = date('Y-m-d');
           
            
           
                  $query = $this->db->query("SELECT * FROM HealthDictionary_new WHERE date='$date'");
                  $count = $query->num_rows();
                  $resultData = $query->row();
              
              if($count>0)
              {
                 $word = $resultData->word;
                 $meaning = $resultData->meaning;
                 $today = $resultData->Date;
              }
              else
              {
                  $word ="";
                  $meaning = "";
                  $today = "";
              }
                 
                 $finalRersult = array(
                          'word' =>$word,
                          'meaning' => $meaning,
                          'today' =>$today
                     );
              
                return array(
                    'status' => 200,
                    'message' => 'success',
                    'data' => $finalRersult
                );
             
             
   }

public function SearchwordModel($keyword)
   {
       $temparray = array();
       /*$query = $this->db->query("select * from HealthDictionary where word LIKE '%$keyword%' limit 15");
       $count = $query->num_rows();
       
       $query1 = $this->db->query("select * from HealthDictionary_disease where word LIKE '%$keyword%' limit 15");
       $count1 = $query1->num_rows();*/
       
       //Edit By Mrunalini as per new HealthDictionary list
       
     //  $query = $this->db->query("select h.*,m.meaning as mt from HealthDictionary as h LEFT JOIN medical_terms as m ON m.medical_term LIKE '%$keyword%' where h.word LIKE '%$keyword%' limit 15");
     //  $count = $query->num_rows();
       
       $query1 = $this->db->query("select h.*,m.meaning as mt from HealthDictionary_disease as h LEFT JOIN medical_terms as m ON m.medical_term LIKE '%$keyword%' where word LIKE '%$keyword%' limit 15");
       $count1 = $query1->num_rows();
       
       
     
   /*    if($count > 0)
       {
           foreach($query->result_array() as $row)
           {
               $id = $row['id'];
               $word = $row['word'];
               $meaning = $row['meaning'];
               $Symptoms =$row['symptoms'];
               $causes = $row['causes'];
               $Complications = $row['complications'];
               $Treatment = $row['treatments_and_drugs'];
               $medical_terms = $row['mt'];
               $temparray[] = array(
                        
                       'id' => $id,
                       'word' => $word,
                       'meaning' =>$meaning,
                       'Symptoms' =>$Symptoms,
                       'causes' =>$causes,
                       'Complications' =>$Complications,
                       'Treatment'=>$Treatment,
                       'medical_terms' => $medical_terms
                   );
           }
           
            //  return array(
            // 'status' => 200,
            // 'message' => 'success',
            // 'data' => $temparray
            // );
       }
       */
       
        if($count1 > 0)
       {
         
           foreach($query1->result_array() as $row)
           {
               $id = $row['id'];
               $word = $row['word'];
               $meaning = $row['meaning'];
               $Symptoms =$row['symptoms'];
               $causes = $row['causes'];
               $Complications = $row['complications'];
               $Treatment = $row['treatments_and_drugs'];
               if(!empty($row['mt'])){
               $medical_terms = $row['mt'];
               }else
               {
                   $medical_terms = "";
               }
               
               if($row['images']!="")
               {
               $img=explode(",",$row['images']);
              
               for($i=0; $i < count($img) ; $i++)
               {
                 $image[]=array(
                   'image' =>'https://medicalwale.s3.amazonaws.com/images/Health_Dictionary/'.$img[$i],
                   
                   );
               }
               }
               else
               {
                $img_arr = array('image' =>'');
                  $image=array(
                        $img_arr
                      ); 
               }
               $temparray[] = array(
                       'id' => $id,
                       'word' => $word,
                       'meaning' =>$meaning,
                       'Symptoms' =>$Symptoms,
                       'causes' =>$causes,
                       'Complications' =>$Complications,
                       'Treatment'=>$Treatment,
                       'Health_image'=>$image,
                       'medical_terms' => $medical_terms
                   );
                   $image=array();
           }
           
            //  return array(
            // 'status' => 200,
            // 'message' => 'success',
            // 'data' => $temparray
            // );
       }
       
       
       
        return array(
            'status' => 200,
            'message' => 'success',
            'data' => $temparray
            );
   }


    public function Get_health_quates($user_id)
    {
             $query = $this->db->query("SELECT * FROM Health_quote_dictionary where status='0' ORDER BY id DESC");
             $count = $query->num_rows();
             $Health_quate = array();
             if($count>0)
             {
                 foreach($query->result_array() as $row)
                 {
                     $id    = $row['id'];
                     $quate = $row['Health_Quote'];
                     $week  = $row['week'];
                     $date  = $row['date'];
                     
                     
                     $Health_quate[] = array(
                            'id' => $id,
                            'quate' => $quate,
                            'week' => $week,
                            'date'  => $date
                         );
                         
                         
                 }
                 
                    return array(
                      'status' => 200,
                      'message' => 'success',
                      'data' => $Health_quate
            );
             }
              return array(
            'status' => 200,
            'message' => 'success',
            'data' => array()
            );
    }
    
}
