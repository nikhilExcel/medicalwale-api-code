<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Qrcodemodel extends CI_Model {

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

    public function encrypt($str) {
        echo $str;
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
    
       public function get_vendor_details($qrcode,$userid) {
        
      
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        
         //$query = $this->db->query("SELECT barcode_coupon.vendor_id,barcode_coupon.coupon,users.name FROM `barcode_coupon` LEFT JOIN users ON (barcode_coupon.vendor_id = users.vendor_id) WHERE barcode_coupon.vendor_id = '$qrcode'");
       // $query = $this->db->query("SELECT qrcode_vendor.vendor_id,qrcode_vendor.vendor_qrcode,users.name FROM `qrcode_vendor` LEFT JOIN users ON (qrcode_vendor.vendor_id = users.vendor_id) WHERE qrcode_vendor.vendor_qrcode = '$qrcode'");
        $query = $this->db->query("SELECT vendor_id,vendor_qrcode FROM `qrcode_vendor` WHERE vendor_qrcode='$qrcode'");
        
        $raw = $query->row();
        $num = $query->num_rows();
        
        
        
        
        if($num >0)
        {
            
            $query1 = $this->db->query("SELECT * FROM `users` WHERE id='$raw->vendor_id'");
            $user = $query1->row();
            $vendor_id = $raw->vendor_id;
             if(!empty($user))
            {
                $liting_type  = $user->vendor_id;
                $user_name = $user->name;
            }
            else
            {
                $liting_type = 0;
                $user_name = "";
            }
           // echo $liting_type;
            if($liting_type == '13')
             {
                 //pharmacy
                 $pquery = $this->db->query("SELECT `discount`,IFNULL(discount,'0') AS discount FROM medical_stores  WHERE  user_id='$vendor_id'");
                 $pcount = $pquery->num_rows();
                 if($pcount>0)
                 {
                      $p = $pquery->row();
                      $main_discount = $p->discount;
                 }
                 else
                 {
                      $p_query = $this->db->query("SELECT `discount`,IFNULL(discount,'0') AS discount FROM pharmacy_branch  WHERE  pharmacy_branch_user_id='$vendor_id'");
                      $p_count = $p_query->num_rows();
                     if($p_count>0)
                 {
                      $p_q = $p_query->row();
                      $main_discount = $p_q->discount;
                 }
                 else
                 {
                     $main_discount = '0';
                 }
                 }
             }
             else if ($liting_type == '5')
             {
                 //doctor
                 $dquery = $this->db->query("SELECT `discount`,IFNULL(discount,'0') AS discount FROM doctor_list  WHERE  user_id='$vendor_id'");
                 $dcount = $dquery->num_rows();
                 if($dcount>0)
                 {
                      $d = $dquery->row();
                      $main_discount = $d->discount;
                 }
                 else
                 {
                     $main_discount = '0';
                 }
             }
              else if ($liting_type == '8')
             {
                 //hospital user_discount
                   $hquery = $this->db->query("SELECT `user_discount`,IFNULL(user_discount,'0') AS user_discount FROM hospitals WHERE  user_id='$vendor_id'");
                 $hcount = $hquery->num_rows();
                 if($hcount>0)
                 {
                      $h = $hquery->row();
                      $main_discount = $h->user_discount;
                 }
                 else
                 {
                     $main_discount = '0';
                 }
             }
             else if($liting_type == '6' || $liting_type == '36')
             {
                 //fitness 
                   $fquery = $this->db->query("SELECT `discount`,IFNULL(discount,'0') AS discount FROM fitness_center WHERE  user_id='$vendor_id'");
                 $fcount = $fquery->num_rows();
                 if($fcount>0)
                 {
                      $f = $fquery->row();
                      $main_discount = $f->discount;
                 }
                 else
                 {
                     $main_discount = '0';
                 }
             }
             else if($liting_type == '10')
             {
                 //lab
                  $lquery = $this->db->query("SELECT `discount`,IFNULL(discount,'0') AS discount FROM lab_center WHERE  user_id='$vendor_id'");
                 $lcount = $lquery->num_rows();
                 if($lcount>0)
                 {
                      $l = $lquery->row();
                      $main_discount = $l->discount;
                 }
                 else
                 {
                     $main_discount = '0';
                 }
             }
               else if($liting_type == '39')
             {
                 //store
                  $lquery = $this->db->query("SELECT `user_discount`,IFNULL(user_discount,'0') AS discount FROM dentists_clinic_list WHERE  user_id='$vendor_id'");
                 $lcount = $lquery->num_rows();
                 if($lcount>0)
                 {
                      $l = $lquery->row();
                      $main_discount = $l->discount;
                 }
                 else
                 {
                     $main_discount = '0';
                 }
             }
              else if($liting_type == '40')
             {
                 //store
                  $lquery = $this->db->query("SELECT `discount`,IFNULL(discount,'0') AS discount FROM sports_store WHERE  user_id='$vendor_id'");
                  //echo "SELECT `discount`,IFNULL(discount,'0') AS discount FROM sports_store WHERE  user_id='$vendor_id'";
                 $lcount = $lquery->num_rows();
                 if($lcount>0)
                 {
                      $l = $lquery->row();
                      $main_discount = $l->discount;
                 }
                 else
                 {
                     $main_discount = '0';
                 }
             }
              else if($liting_type == '17')
             {
                 //optic_store
                  $lquery = $this->db->query("SELECT `discount`,IFNULL(discount,'0') AS discount FROM optic_eyecare_list WHERE  user_id='$vendor_id'");
                 $lcount = $lquery->num_rows();
                 if($lcount>0)
                 {
                      $l = $lquery->row();
                      $main_discount = $l->discount;
                 }
                 else
                 {
                     $main_discount = '0';
                 }
             }
             else if($liting_type == '1')
             {
                 //ayurveda store
                  $lquery = $this->db->query("SELECT `discount`,IFNULL(discount,'0') AS discount FROM ayurveda_store WHERE  user_id='$vendor_id'");
                 $lcount = $lquery->num_rows();
                 if($lcount>0)
                 {
                      $l = $lquery->row();
                      $main_discount = $l->discount;
                 }
             }
             else
             {
                 $main_discount = '0';
             }
             
            //Vendor Ledger
            $code_details = array(
                'status' => 200,
                'message' => 'success',
                'description' => '',
                 'data' => array(
                'coupon_code' => $this->generate_coupons($qrcode,$userid,$vendor_id),
                'vendor_id'       => $raw->vendor_id,
                'vendor_name'     => $user_name.' (Discount : '.$main_discount.' )',
                'user_id'           => $userid,
                'Vendor_message'  => 'To get the Reward Points, make sure partner generates your Ledger'
                     )
            );
        }
        else
        {
            $code_details = array(
                 'status' => 200,
                'message' => 'Failure',
                'description' => 'Vendor not found',
                'data'=>array()    
            );
        }
        
        
        return  $code_details;
    
    }
    
    public function sendCCToVendor1($coupon,$userid,$vendorid,$bcno,$uname) {
        
      
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        
        //$query = $this->db->query("SELECT qrcode_vendor.vendor_id,qrcode_vendor.vendor_qrcode,users.name FROM `qrcode_vendor` LEFT JOIN users ON (qrcode_vendor.vendor_id = users.vendor_id) WHERE qrcode_vendor.vendor_qrcode = '$qrcode'");
        $query = $this->db->query("SELECT vendor_discount.discount_min,vendor_discount.discount_max,vendor_discount.discount_type,vendor_discount.discount_limit,barcode_coupon.user_id,user_privilage_card.card_no,barcode_coupon.vendor_id,barcode_coupon.coupon,users.id,users.name FROM barcode_coupon LEFT JOIN users  ON (barcode_coupon.user_id = users.id)
        LEFT JOIN user_privilage_card ON (user_privilage_card.user_id = barcode_coupon.user_id) LEFT JOIN vendor_discount ON (barcode_coupon.vendor_id = vendor_discount.vendor_id)  WHERE barcode_coupon.coupon = '$coupon' AND barcode_coupon.user_id = '$userid' AND barcode_coupon.vendor_id = '$vendorid'");
        $raw = $query->row();
        
        print_r($raw);
      
            //Vendor Ledger
            $code_details = array(
                'coupon_code'       => $coupon,
                'vendor_id'         => $raw->vendor_id,
                'user_name'         => $raw->name,
                'user_id'           => $raw->id,
                'bachatcard_no'     => $raw->card_no,
                'min_discount'      => $raw->discount_min,
                'max_discount'      => $raw->discount_max,
                'discount_type'     => $raw->discount_type,
                'discount_limit'    => $raw->discount_limit
            );
           
        
        
        return  $code_details;
    
    }
    
    
      public function sendCCToVendor($coupon,$userid,$vendorId) {
        
      
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        
        //$query = $this->db->query("SELECT qrcode_vendor.vendor_id,qrcode_vendor.vendor_qrcode,users.name FROM `qrcode_vendor` LEFT JOIN users ON (qrcode_vendor.vendor_id = users.vendor_id) WHERE qrcode_vendor.vendor_qrcode = '$qrcode'");
        $query = $this->db->query("SELECT * FROM `barcode_coupon` WHERE coupon='$coupon' AND user_id='$userid' AND vendor_id = '$vendorId' AND status = 'active'");
        
        $raw = $query->row();
        if(count($raw) > 0){
        //   userr name
            $userInfoRow = $this->db->query("SELECT * FROM `users` WHERE `id` = '$userid'");
            $userInfo = $userInfoRow->row();
            $userName = $userInfo->name;
          
            // cardNo
            $userBachatInfoRow = $this->db->query("SELECT * FROM `user_privilage_card` WHERE `user_id` = '$userid'");
            $userBachatInfo = $userBachatInfoRow->row();
            $userBachat = $userBachatInfo->card_no;
            
            // discounts
            $userDiscountInfoRow = $this->db->query("SELECT * FROM `vendor_discount` WHERE `vendor_id` = '$vendorId'");
            $userDiscountInfo = $userDiscountInfoRow->row();
          
            $discount_min = $userDiscountInfo['discount_min'] ;
            $discount_max = $userDiscountInfo['discount_max'] ;
            $discount_type = $userDiscountInfo['discount_type'];
            $discount_limit = $userDiscountInfo['discount_limit'] ;
            
              //Vendor Ledger
            $code_details = array(
                'coupon_code'       => $coupon,
                'vendor_id'         => $vendorId,
                'user_name'         => $userName,
                'user_id'           => $userid,
                'bachatcard_no'     => $userBachat,
                'min_discount'      => $discount_min,
                'max_discount'      => $discount_max,
                'discount_type'     => $discount_type,
                'discount_limit'    => $discount_limit,
            );
            
            $resp = array(
                'status'=>200,
                'message'=>'success',
                'data' =>  $code_details
            );
          
        } else {
              //Vendor Ledger
            $code_details = array();
            
            $resp = array(
                'status'=>400,
                'message'=>'No coupon code found',
                'data' =>  $code_details
            );
        }
        
        
        
        return  $resp;
    
    }
    
    
    public function generate_coupons($vtype,$userid,$vendor_id){
        do{
		$coupon = "";
		for($counter = 0; $counter < 4; $counter++){
			/*if(random_int(0, 1))
				$coupon .= chr(random_int(97,122));
			else*/ 
			$coupon .= chr(random_int(65,90));
		}
		$coupon .= strval(random_int(10000, 99999));
		$query = $this->db->query("SELECT coupon FROM `barcode_coupon` WHERE vendor_id='$vendor_id' and user_id='$userid' and status='active'");
	    
	    	if($query->num_rows()>0)
	         {
		    //$query = $this->db->query("INSERT INTO barcode_coupon (vendor_id, user_id, coupon) VALUES('$vendor_id', '$userid', '$coupon')");
		     $query = $this->db->query("UPDATE `barcode_coupon` SET coupon='$coupon' WHERE user_id = '$userid' AND vendor_id ='$vendor_id' AND status ='active'");
	         }
	    	else
	        	{
	      	$query = $this->db->query("INSERT INTO barcode_coupon (vendor_id, user_id, coupon) VALUES('$vendor_id', '$userid', '$coupon')");
		       }
        }while($query!=true);
		return $coupon;
	}
	
	public function vendor_txn_details($coupon){
	    $query = $this->db->query("SELECT * FROM barcode_coupon LEFT JOIN vendor_discount ON (barcode_coupon.vendor_id = vendor_discount.vendor_id) WHERE coupon='$coupon'");
	    $raw = $query->row();
	    if($raw->coupon == $coupon){
	        $amt = $perc = null;
	        if($raw->discount_type == 'percent')
	            $perc = $raw->discount_limit;
	       else $amt = $raw->discount_limit;
	        return array(
	            'coupon'    => $coupon,
	            'user_id'   => $raw->user_id,
	            'vendor_id' => $raw->vendor_id,
	            'discount_amount'   => $amt,
	            'discount_perc'     => $perc
	       );
	    }
	    else{
	        return array(
	           'status' => 400,
	           'message'    => 'Please enter correct coupon code.'
	       );
	    }
	}
    
    
    public function add_qrcode($vendor_id, $vendor_qrcode, $vendor_type) {
        
        $query = $this->db->query("SELECT * FROM `qrcode_vendor` WHERE vendor_id='$vendor_id'  ");
        
          $query1 = $this->db->query("SELECT * FROM `qrcode_vendor` WHERE vendor_qrcode = '$vendor_qrcode'");
	  
	    if(sizeof($query->row_array()) > 0  ){
            
          
            $this->db->query("UPDATE `qrcode_vendor` SET vendor_qrcode='$vendor_qrcode', vendor_type='$vendor_type' WHERE vendor_id = '$vendor_id' " );
            
            $resp = array(
                'status' => 200,
                'message' => 'success',
                'Description' => 'successfully updated'
            );
            
        } else if( sizeof($query1->row_array()) > 0){
             $resp = array(
                'status' => 400,
                'message' => 'failed',
                'Description' => 'Duplicate entry, please try again'
            );
            
            
        }
        else {
            
            
        
            $qr_array = array(
                'vendor_id' => $vendor_id,
                'vendor_qrcode' => $vendor_qrcode,
                'vendor_type' => $vendor_type
            );
            
            $status = $this->db->insert('qrcode_vendor', $qr_array);
            
            $resp = array(
            'status' => 200,
            'message' => 'success',
            'Description' => 'successfully added' 
        );
        
        }
        
        return $resp;
    }
    
    public function add_user_comment($trans_id, $user_comment, $user_id){
        $queryNOw = $this->db->query("UPDATE `user_ledger` SET user_comment='$user_comment' WHERE 	trans_id = '$trans_id' AND user_id = '$user_id' AND	trans_type != '4' AND trans_type != '0' " );
        if($queryNOw == 1){
             $resp = array('status'=>200, 'message'=>'success');
        }
        return $resp;
    }
  
    
}
