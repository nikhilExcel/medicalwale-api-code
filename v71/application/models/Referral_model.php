<?php
defined('BASEPATH') OR exit('No direct script access allowed');
  define("GOOGLE_GCM_URL_", "https://fcm.googleapis.com/fcm/send");
class Referral_model extends CI_Model {
	
	public function get_user_by_id($id){   
	    $this->db->select('id,name,phone,email,referral_code,avatar_id, token, agent, token_status');
		$this->db->where('id', $id);
		$query = $this->db->get('users');
		return $query->row_array();
	}
	
	public function get_user_by_refferal($referral_code){   
	    $this->db->select('id,name,phone,email,referral_code,avatar_id, token, agent, token_status');
		$this->db->where('referral_code', $referral_code);
		$query = $this->db->get('users');
		return $query->row_array();
	}
	

	
    function password_generate($chars) {
       $data = '1234567890';
       return substr(str_shuffle($data), 0, $chars);
    }

    public function get_referral_code_generate(){
        $referral_code_generate_new = strtoupper($this->password_generate(6) . rand(11, 99));
        $query = $this->db->query("SELECT id FROM users WHERE referral_code='$referral_code_generate_new' limit 1");  
        if ($query->num_rows() > 0) { 
            $referral_code_generate_new = strtoupper($this->password_generate(5) . rand(11, 99) . $query->num_rows());
            $referral_code_generate_new = substr($referral_code_generate_new, -8);
        }
       return $referral_code_generate_new;
    }


    public function get_referral_details($user_id) {   
        $resultpost  = array();
        $referral_code='';
        $referral_code_url='';
        
        $this->db->select('id,referral_code');
        $this->db->where('id', $user_id);
        $query = $this->db->get('users');
        $count = $query->num_rows();
       
        if ($count > 0) {
         $user=$query->row_array();
         $referral_code='';
         $referral_code_url='';
         if($user['referral_code']==NULL){
            $referral_code=$this->get_referral_code_generate();
            $data_['referral_code'] = $referral_code;
            $this->db->where('id', $user_id);
            $update=$this->db->update('users', $data_);	
            
            $referral_code_url=base_url().'share-referral/'.$referral_code;
         } 
         else{
             $referral_code=$user['referral_code'];
             $referral_code_url=base_url().'share-referral/'.$referral_code;
         }
      	$referral=$this->get_oc_referral();
	  	$joined_points=$referral['joined'];
	  	
	  	
	  	 $sql = $this->db->query("SELECT COUNT(*) as invited, SUM(`joiner_points`) as earned FROM `oc_referral_history` WHERE joiner_user_id='$user_id'");  
	  	 if($sql->num_rows()>0){ 
	  	     $mrow=$sql->row_array();
	  	     $invited=$mrow['invited'];
	  	     $earned=$mrow['earned'];
	  	 }
	  	 else{
	  	     $invited='0';
	  	     $earned='0';
	  	 }
      
        $data = array();
        $data[] = array(
            'image' => "https://d2c8oti4is0ms3.cloudfront.net/images/Icons+for+Apps/Artboard+3+copy+2+(1).png",
            'title' => "Invite your friends and family to Medicalwale.com",
            'details' => "",
         ); 
         
       
         
         $data[] = array(
            'image' => "https://d2c8oti4is0ms3.cloudfront.net/images/Icons+for+Apps/Artboard+3+(1).png",
            'title' => "They will receive a reward of P2500 on signup",
            'details' => "",
         ); 
         
         $data[] = array(
            'image' => "https://d2c8oti4is0ms3.cloudfront.net/images/Icons+for+Apps/Artboard+3+copy+(1).png",
            'title' => "You will receive a reward of P1000 once they signup",
            'details' => "",
         ); 
      
         $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'referral_code' => $referral_code,
            'referral_code_url' =>$referral_code_url,
            'point' =>(int)$joined_points,
            'invited' =>(int)$invited,
            'earned' =>(int)$earned,
            'how_it_works' =>$data
         );
            
        } 
        else {
             $resultpost = array(
            'status' => 404,
            'message' => 'failure',
            'referral_code' => '',
            'referral_code_url' => '',
         );
            
        }

        return $resultpost;
    }
    
    
	public function get_oc_referral(){   
	    $this->db->select('id,joined,joiner,days');
		$this->db->where('id', '1');
		$query = $this->db->get('oc_referral');
		return $query->row_array();
	}
    
     public function add_new_referral_history($user_id,$referral_code) {          
       if($referral_code!=''){
		$this->get_referral_details($user_id);	
		$user=$this->get_user_by_id($user_id);
		$joiner_user=$this->get_user_by_refferal($referral_code);
    	$joiner_user_id=$joiner_user['id'];
	
		$referral=$this->get_oc_referral();
		$joined_points=$referral['joined'];
		$joiner_points=$referral['joiner'];
		$days=$referral['days'];
		
	    date_default_timezone_set('Asia/Kolkata');
        $transaction_date = date('Y-m-d H:i');
        $trans_time = date('Y-m-d H:i');
        $points = 100;
        $listing_type = 0;
        $comments = 'Referral';
        $expire_at =  date('Y-m-d H:i:s',strtotime(date("Y-m-d H:i:s", time()) . " + $days day"));
        $status = 'active';
        $order_id =  'REFERRAL_' . substr(hash('sha256', mt_rand() . microtime()), 0, 20);
        $trans_id   =   date('YmdHis');
		
		$data['order_id']  	          = $order_id;
		$data['trans_id']  	          = $trans_id;
		$data['joined_user_id']  	  = $user_id;
        $data['joined_referral_code'] = $user['referral_code'];
        $data['joiner_user_id']  	  = $joiner_user_id;
        $data['joiner_referral_code'] = $referral_code;
        $data['points']      		  = $joined_points;
        $data['joiner_points']        = $joiner_points;
        $data['days']      		      = $days;
        $data['added_date'] 	 	  = date("Y-m-d, H:i:s");;
        $this->db->insert('oc_referral_history', $data);   
        
        //joined entry - 2500
        $add_points1 = $this->db->query("INSERT INTO `user_points` (`user_id`, `order_id`, `trans_id`, `transaction_date`, `points`, `listing_type`, `comments`, `expire_at`, `status`) VALUES ('$user_id',  '$order_id' , '$trans_id' , '$transaction_date', '$joined_points', '$listing_type','$comments','$expire_at','$status')");
		
		$user_comment='Refferal code shared by '.$joiner_user['name'];
        $user_ledger1 = $this->db->query("INSERT INTO `user_ledger` (`user_id`, `listing_id`, `order_id`, `trans_id`, `trans_type`, `trans_time`, `amount`, `order_status`, `user_comment`) VALUES ('$user_id', '0' , '$order_id' , '$trans_id' , '4', '$trans_time', '$joined_points', 'success','$user_comment')");
        
       
        //joiner entry - 1000
        $add_points2 = $this->db->query("INSERT INTO `user_points` (`user_id`, `order_id`, `trans_id`, `transaction_date`, `points`, `listing_type`, `comments`, `expire_at`, `status`) VALUES ('$joiner_user_id',  '$order_id' , '$trans_id' , '$transaction_date', '$joiner_points', '$listing_type','$comments','$expire_at','$status')");
		
		$user_comment2='Refferal code shared with  '.$user['name'];
        $user_ledger1 = $this->db->query("INSERT INTO `user_ledger` (`user_id`, `listing_id`, `order_id`, `trans_id`, `trans_type`, `trans_time`, `amount`, `order_status`, `user_comment`) VALUES ('$joiner_user_id', '0' , '$order_id' , '$trans_id' , '4', '$trans_time', '$joiner_points', 'success','$user_comment2')");
      
      
      
         //joined entry - 2500         
            $order_date = date('j M Y h:i A', strtotime($transaction_date));
            $token_status =  $user['token_status'];
            if ($token_status > 0) {
                $reg_id =  $user['token'];
                $agent =  $user['agent'];
                $msg = "You've received ". $joined_points ." points since you were referred by ".$joiner_user['name'];
                $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/logo.png';
                $tag = 'text';
                $key_count = '1';
                $title = 'Awarded Welcome Gift!'; 
				$name = $user['name'];
				$order_status =$order_id=$invoice_no=$listing_name="";        
                       
                $notification_array = array(
                       'title' => $title,
                       'msg'  => $msg,
                       'img_url' => $img_url,
                       'tag' => $tag,
                       'order_status' => "",
                       'order_date' => $order_date,
                       'order_id'   =>  "",
                       'post_id'  => "",
                       'listing_id'  => "",
                       'booking_id'  => "",
                       'invoice_no' => "",
                       'user_id'  => $user_id,
                       'notification_type'  => 'points',
                       'notification_date'  => date('Y-m-d H:i:s')
                       
             );
           $this->db->insert('All_notification_Mobile', $notification_array);
           
           $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent); 
           }
           
      
            //joined entry - 1000         
            $token_status2 =  $joiner_user['token_status'];
            if ($token_status2 > 0) {
                $reg_id2 =  $joiner_user['token'];
                $agent2 =  $joiner_user['agent'];
                $msg2 = "You've received ". $joiner_points ." points since you referred  ".$user['name'];
                $img_url2 = 'https://s3.amazonaws.com/medicalwale/images/img/logo.png';
                $tag2 = 'text';
                $key_count2 = '1';
                $title2 = 'Congratulations!'; 
				$name2 = $joiner_user['name'];
				$order_status2=$order_id2=$invoice_no2=$listing_name2="";        
                       
                $notification_array2 = array(
				   'title' => $title2,
				   'msg'  => $msg2,
				   'img_url' => $img_url2,
				   'tag' => $tag2,
				   'order_status' => "",
				   'order_date' => $order_date,
				   'order_id'   =>  "",
				   'post_id'  => "",
				   'listing_id'  => "",
				   'booking_id'  => "",
				   'invoice_no' => "",
				   'user_id'  => $joiner_user_id,
				   'notification_type'  => 'points',
				   'notification_date'  => date('Y-m-d H:i:s')
				 );
                $this->db->insert('All_notification_Mobile', $notification_array2);
                
           $this->send_gcm_notify($title2, $reg_id2, $msg2, $img_url2, $tag2, $key_count2, $order_status2, $order_date, $order_id2, $invoice_no2, $name2, $listing_name2, $agent2);
         }
	  }
    }
    
        public function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent) {
          
            $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "message" => $msg, "image" => $img_url, "notifivation_image" => $img_url, "tag" => $tag, "notification_type" => "prescription", "order_status" => $order_status, "order_date" => $order_date, "order_id" => $order_id, "invoice_no" => $invoice_no, "name" => $name, "listing_name" => $listing_name)
            );
            if ($key_count == '1') {
                $headers = array(
                    GOOGLE_GCM_URL_,
                    'Content-Type: application/json',
                    $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
                );
            }
            if ($key_count == '2') {
                $headers = array(
                    GOOGLE_GCM_URL_,
                    'Content-Type: application/json',
                    $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
                );
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL_);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            if ($result === FALSE) {
                //die('Problem occurred: ' . curl_error($ch));
            }
            curl_close($ch);
            //echo $result;
        }
}
