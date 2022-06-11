<?php
 class MedicalMall_model_2 extends CI_Model {
       
    public function __construct(){
          
        
        $this->load->database();
         $this->load->model('MedicalMall_model');
        
       
      }
      
    var $client_service = "frontend-client";
    var $auth_key = "medicalwalerestapi";
    
    /*public function check_auth_client() {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key = $this->input->get_request_header('Auth-Key', TRUE);
        if ($client_service == $this->client_service && $auth_key == $this->auth_key) {
            return true;
        } else {
            $resp = array(
		        "status" => "Unauthorized",
                "statuspic_root_code" => "401",
		        );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        }
    }*/
    
   /* public function auth() {
        date_default_timezone_set('Asia/Kolkata');
        $users_id = $this->input->get_request_header('User-ID', TRUE);
        $token = $this->input->get_request_header('Authorizations', TRUE);
        $q = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id', $users_id)->where('token', $token)->get()->row();
        if ($q == "") {
            $resp = array(
		        "status" => "Unauthorized",
                "statuspic_root_code" => "401",
		        );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            
             if ($q->expired_at < date("Y-m-d H:i:s", strtotime("-1 days"))) {
                // return json_output(401, array(
                //     'status' => 401,
                //     'message' => 'Your session has been expired.'
                // ));
                return  array(
                    'status' => 401,
                    'message' => 'Your session has been expired.'
                );
            } else {
                $updated_at = date('Y-m-d H:i:s');
                $expired_at = date("Y-m-d H:i:s", strtotime("+1 days"));//'2020-12-12 08:57:58';
                $this->db->where('users_id', $users_id)->where('token', $token)->update('api_users_authentication', array(
                    'expired_at' => $expired_at,
                    'updated_at' => $updated_at
                ));
                //echo $this->db->last_query(); die();
                return 200;
            }
        }
    }*/
    
    
    public function home_page($user_id){ 
        $data = array();
        
        // getestseller_post
        
        
       
       
        $data['banner_images'] = $this->MedicalMall_model->get_banner_images();
        $data['recently_viewed'] = $this->MedicalMall_model->get_recently_viewed($user_id);
        $data['brand_images'] = $this->MedicalMall_model_2->brand_images();
        $data['featured_products'] = $this->MedicalMall_model_2->get_featured_products();
        $data['new_arrival'] = $this->MedicalMall_model_2->get_new_arrival();
        $data['ayurvedic_products'] = $this->MedicalMall_model_2->get_ayurvedic_products();
        $data['fitness_products'] = $this->MedicalMall_model_2->get_fitness_products();
        $data['personal_care_products'] = $this->MedicalMall_model_2->get_personal_care_products();
        $data['luxurious_brand_products'] = $this->MedicalMall_model_2->get_luxurious_brand_products();
        $data['best_seller_products'] = $this->MedicalMall_model->getBestSellerProducts();
        $data['get_adv'] = $this->MedicalMall_model->getallAdvertisements();
        $data['get_sponsored_advertisements'] = $this->MedicalMall_model_2->get_sponsored_advertisements($user_id);
        
        
        
        
        
        
        return $data;
    }
    public function get_featured_products(){
        $query = $this->db->query("SELECT pd.pd_id,pd.pd_pc_id,pd.pd_psc_id,pd.pd_added_v_id,pd.pd_vendor_price,pd.pd_short_desc,pd.pd_name,pd.pd_photo_1,vd.v_name,vd.v_delivery_charge,vd.v_company_logo,pd.pd_mrp_price,pd.pd_overall_ratting as ratting
        FROM product_details_hm pd 
        left join vendor_details_hm vd
        on vd.v_id=pd.pd_added_v_id
        WHERE pd.is_featured = '1' AND pd.pd_status = '1' ORDER BY RAND()");
        $res = $query->result_array();
        return $res;
    }
    
    
      
    public function get_ayurvedic_products(){
        $query = $this->db->query("SELECT pd.pd_id,pd.pd_pc_id,pd.pd_psc_id,pd.pd_added_v_id,pd.pd_vendor_price,pd.pd_short_desc,pd.pd_name,pd.pd_photo_1,vd.v_name,vd.v_delivery_charge,vd.v_company_logo,pd.pd_mrp_price,pd.pd_overall_ratting as ratting
        FROM vendor_details_hm vd 
        join product_details_hm pd 
        on vd.v_id=pd.pd_added_v_id
        WHERE 	landing_screen = '1' AND pd_status = '1' AND pd_pc_id = '123' ORDER BY RAND()");
        $res = $query->result_array();
        return $res;
    }
    
     public function get_fitness_products(){
        $query = $this->db->query("SELECT pd.pd_id,pd.pd_pc_id,pd.pd_psc_id,pd.pd_added_v_id,pd.pd_vendor_price,pd.pd_short_desc,pd.pd_name,pd.pd_photo_1,vd.v_name,vd.v_delivery_charge,vd.v_company_logo,pd.pd_mrp_price,pd.pd_overall_ratting as ratting
        FROM vendor_details_hm vd 
        join product_details_hm pd 
        on vd.v_id=pd.pd_added_v_id
        WHERE 	landing_screen = '1' AND pd_status = '1' AND pd_pc_id = '81' ORDER BY RAND()");
        $res = $query->result_array();
        return $res;
    }
    
    public function get_personal_care_products(){
        $query = $this->db->query("SELECT pd.pd_id,pd.pd_pc_id,pd.pd_psc_id,pd.pd_added_v_id,pd.pd_vendor_price,pd.pd_short_desc,pd.pd_name,pd.pd_photo_1,vd.v_name,vd.v_delivery_charge,vd.v_company_logo,pd.pd_mrp_price,pd.pd_overall_ratting as ratting
        FROM vendor_details_hm vd 
        join product_details_hm pd 
        on vd.v_id=pd.pd_added_v_id
        WHERE 	landing_screen = '1' AND pd_status = '1' AND pd_pc_id = '15' ORDER BY RAND()");
        $res = $query->result_array();
        return $res;
    }
    
     // luxurious_brand
    
    public function get_luxurious_brand_products(){
        $query = $this->db->query("SELECT pd.pd_id,pd.pd_pc_id,pd.pd_psc_id,pd.pd_added_v_id,pd.pd_vendor_price,pd.pd_short_desc,pd.pd_name,pd.pd_photo_1,vd.v_name,vd.v_delivery_charge,vd.v_company_logo,pd.pd_mrp_price,pd.pd_overall_ratting as ratting
        FROM vendor_details_hm vd 
        join product_details_hm pd 
        on vd.v_id=pd.pd_added_v_id
        WHERE 	pd_luxurious_brand = '1' AND pd_status = '1' AND landing_screen = '1' ORDER BY RAND()");
        $res = $query->result_array();
        return $res;
    }
    
    //get_new_arrival
    
    public function get_new_arrival(){
        $today = date('Y-m-d');
         //echo $today;
        // die();
        $query = $this->db->query("SELECT pd.pd_id,pd.pd_pc_id,pd.pd_psc_id,pd.pd_added_v_id,pd.pd_vendor_price,pd.pd_short_desc,pd.pd_name,pd.pd_photo_1,
        vd.v_name,vd.v_delivery_charge,vd.v_company_logo,pd.pd_mrp_price,pd.pd_overall_ratting as ratting 
        FROM vendor_details_hm vd join product_details_hm pd on vd.v_id=pd.pd_added_v_id 
        WHERE `new_arrival` = 1 AND `pd_status` = 1 AND `new_arrival_till` >= '$today'
");
        $res = $query->result_array();
        return $res;
    }
    
    public function placeOrder($data){
	   // print_r($data); die();
	    date_default_timezone_set('Asia/Kolkata');
        $order_date = date('Y-m-d H:i:s');
        // work on this
            $delivery_charge = 0;
            $listing_name = "";
            $listing_type = 34;
            $chat_id = "";
            $lat = "";
            $lng = "";
            $address_id = "";
        // work on this ends
		$invoice = date("YmdHis");
		if($data['payment_method'] == 1){
		    $paym = "Cash On Delivery";
		    $title = $data['name'] . 'has booked an Product with Cash On Delivery';
		    $Payment_type = '1';
		  $this->notifyMethod($data['cust_id'],$title,$Payment_type);
		}
		elseif($data['payment_method'] == 2){
		    $paym = "Credit/Debit";
		    $title = $data['name'] . 'has booked an Product with Credit/Debit';
		     $Payment_type = '2';
		  $this->notifyMethod($data['cust_id'],$title,$Payment_type);
		}else{
		    $paym = "Points";
		   $title = $data['name'] . 'has booked an Product with Ledger Balance';
		   $Payment_type = '6';
		  $this->notifyMethod($data['cust_id'],$title,$Payment_type);
		}
		$paym = $data['payment_method'];
// actual_total  '".$data['amount']."',
		$query_for_order = "INSERT INTO user_order (invoice_no,order_status,name,mobile,user_id,order_total,pincode,address1,landmark,city,state,payment_method,order_date,action_by,listing_type)
         VALUES ('$invoice','Confirmed','".$data['name']."','".$data['mobileno']."','".$data['cust_id']."','".$data['offer_amount']."',".$data['pincode'].",'".$data['address']."','".$data['landmark']."','".$data['city']."','".$data['state']."','$paym','$order_date','customer','34')";
		
		$query = $this->db->query($query_for_order);
		
		$insert_id = $this->db->insert_id();
		
		if($insert_id != ""){
		    for($i=0;$i<$data['total_products'];$i++){
		    	$query_for_order_data = "INSERT INTO orders_details_hm (order_id,product_id,product_qty,vendor_id,is_offer,offer_id,offer_price)
         VALUES ('".$insert_id."','".$data['product_id'.$i]."','".$data['product_qty'.$i]."','".$data['vendor_id'.$i]."','".$data['is_offer'.$i]."','".$data['offer_id'.$i]."','".$data['offer_total'.$i]."')";
		 
    		$this->db->query($query_for_order_data);
		    }
    		$dataAll = json_decode($data['products'],TRUE);
    		for($i=0;$i<count($dataAll['products']);$i++){
    		    $dataAll['products'][$i]['total_price'] = $dataAll['products'][$i]['price'] * $dataAll['products'][$i]['quantity'];
    		}
    		//print_r($dataAll['products']);die;
    		$query1=$this->db->query("SELECT * FROM `orders_details_hm` WHERE `order_id`='$insert_id' GROUP BY vendor_id")->result_array();
    		//print_r($query1);die;
    		$del_amt = 0;
    		foreach($query1 as $q1){
    		    $tmp="SELECT v_delivery_charge FROM vendor_details_hm WHERE v_id = '".$q1['vendor_id']."'";
    		    $t=$this->db->query($tmp)->result_array()[0]['v_delivery_charge'];
    		    $del_amt += $t;
    		}
    		$this->db->query("UPDATE user_order SET delivery_charge = '$del_amt' WHERE order_id = '$insert_id'");
    		$res = $this->db->query("SELECT order_id,invoice_no,order_status,user_id,payment_method,order_date,delivery_charge FROM user_order WHERE order_id = '$insert_id'");
    		$result = array(
    			"status" => "true",
    			"statuspic_root_code" => "200",
    			"message" => "Order Created Succesfully",
    			"original_total_price" => $data['amount'],
    			"offer_total_price" => $data['offer_amount'],
    			"saved_amount" => $data['savings'],
    			"products" => array_values($dataAll),
    			"order_details" => $res->result_array()
    			);
    		$empty_cart = "DELETE FROM user_cart WHERE customer_id = '".$data['cust_id']."'";
    		$this->db->query($empty_cart);
    		
    // 		white here ****************
            $trans_id = date("YmdHis");
            $debit = 1;
            $this->MedicalMall_model_2->insert_payment_status($data['cust_id'], '34', $trans_id, $debit, $paym, $insert_id, $paym, $data['amount'], 'Confirm', $data['amount'], $data['offer_amount'], $paym);
    		
    		
		} else {
		    	$result = array(
        			"status" => "false",
        			"message" => "something went wrong"
    			);
		}
		return $result;
	}
	
// 	new place_order add order by vendor
	
	
		 public function place_order1($data, $allowed){
	   // print_r(); die();
	  // print_r($data['vendor_id0']);  die(); 
	    date_default_timezone_set('Asia/Kolkata');
        $order_date = date('Y-m-d H:i:s');
        // work on this
            $delivery_charge = 0;
            $listing_name = "";
            $listing_type = 34;
            $chat_id = "";
            $lat = "";
            $lng = "";
            $address_id = "";
            $v_name = $data['vendor_name0'];
            $v_id = $data['vendor_id0'];
        // work on this ends
		$invoice = date("YmdHis")."".$data['number'];
		if($data['payment_method'] == 3){
		    $paym = "Cash On Delivery";
		    $order_status = "Processing";
		}
		elseif($data['payment_method'] == 2){
		    $paym = "Credit/Debit";
		    $order_status = "Payment Pending";
		}else{
		    $paym = "Points";
		    $order_status = "Processing";
		}
		$paym = $data['payment_method'];
  //		print_r($data['v_name']); die();
 		$name = $data['name'];
 	$invoice_no = $invoice;	   
    $name = $data['name'];;
    $mobileno = $data['mobileno']; 
    $pincode = $data['pincode'];
    $address = $data['address']; 
    $address1 = $data['address1'];  
    $landmark = $data['landmark']; 
    $city = $data['city'];  
    $state = $data['state']; 
    $cust_id = $data['cust_id'];
    $amount = $data['amount'];
    
// actual_total  '".$data['amount']."',
		$query_for_order = "INSERT INTO user_order
(invoice_no,order_status,listing_id,listing_type,listing_name,name,mobile,user_id,order_total,pincode,address1,landmark,city,state,payment_method,order_date,action_by) 
VALUES
('$invoice_no','$order_status','$v_id',$listing_type,'$v_name','$name','$mobileno','$cust_id','$amount','$pincode','$address','$landmark','$city','$state','$paym','$order_date','customer')";

		$query = $this->db->query($query_for_order);
		
		$insert_id = $this->db->insert_id();
		
		if($insert_id != ""){
		    
		     //  insert into pending_payments table
		  //INSERT INTO `order_pending_payments` (`user_id`, `order_id`, `listing_id`) VALUES ('1', '2', '34');
		  // for website; in app not calling place_order untill payment is successful
		    
		    if($data['is_website'] == 1){
		      $query_for_order_pending = "INSERT INTO `order_pending_payments` (`user_id`, `order_id`, `listing_id`) VALUES ('$cust_id', '$insert_id', '34')";
		      $query = $this->db->query($query_for_order_pending);
		    
		  }
		  
		  
		    for($i=0;$i<$data['total_products'];$i++){
		        
		        if(!empty($data['referral_code'.$i])){
		            
		            $referralCode['referral_code_id'] = $data['referral_code'.$i];
		            $referralCode['pd_id'] = $data['product_id'.$i];
		            $referralCode['used_by'] = $data['cust_id'];
		            $referralCode['order_id'] = $insert_id;
		            
		            for($a=0;$a<sizeof($allowed['allowedArray']);$a++){
		                if( $referralCode['pd_id'] == $allowed['allowedArray'][$a]['pd_id']){
		                    $referralCode['user_id'] = $allowed['allowedArray'][$a]['user_id'];   
		                   
		                }
		                 
		             //   
		            }
		            
		            $this->db->insert('used_referral_codes',$referralCode);
		            
		          //  ````````
		        }
		      //  print_r($referralCode);
		        
		      //  die();
		    
		    	$query_for_order_data = "INSERT INTO orders_details_hm (user_id,order_id,product_id,product_qty,vendor_id,is_offer,offer_id,offer_price,price)
         VALUES ('".$cust_id."','".$insert_id."','".$data['product_id'.$i]."','".$data['product_qty'.$i]."','".$data['vendor_id'.$i]."','".$data['is_offer'.$i]."','".$data['offer_id'.$i]."','".$data['offer_total'.$i]."','".$data['total_products_price'.$i]."')";
		 
    		$this->db->query($query_for_order_data);
		    }
    // 		$dataAll = json_decode($data['products'],TRUE);
    		$dataAll = $data['products'];
    		for($i=0;$i<count($dataAll['products']);$i++){
    		    $dataAll['products'][$i]['total_price'] = $dataAll['products'][$i]['price'] * $dataAll['products'][$i]['quantity'];
    		}
    		//print_r($dataAll['products']);die;
    		$query1=$this->db->query("SELECT * FROM `orders_details_hm` WHERE `order_id`='$insert_id' GROUP BY vendor_id")->result_array();
    		//print_r($query1);die;
    		$del_amt = 0;
    		foreach($query1 as $q1){
    		    $tmp="SELECT v_delivery_charge FROM vendor_details_hm WHERE v_id = '".$q1['vendor_id']."'";
    		  //   print_r($this->db->query($tmp)->row_array()['v_delivery_charge']) ; die();
    		    $t=$this->db->query($tmp)->row_array()['v_delivery_charge'];
    		   
    		    $del_amt += $t;
    		}
    		$this->db->query("UPDATE user_order SET delivery_charge = '$del_amt' WHERE order_id = '$insert_id'");
    		$res = $this->db->query("SELECT order_id,invoice_no,order_status,user_id,payment_method,order_date,delivery_charge FROM user_order WHERE order_id = '$insert_id'");
    		$result = array(
    			"status" => "true",
    			"original_total_price" => $data['amount'],
    			"offer_total_price" => $data['offer_amount'],
    			"saved_amount" => $data['savings'],
    			"products" => array_values($dataAll),
    			"order_details" => $res->result_array()
    			);
    		$empty_cart = "DELETE FROM user_cart WHERE customer_id = '".$data['cust_id']."'";
    		$this->db->query($empty_cart);
    		
    // 		white here ****************
            $trans_id = date("YmdHis");
            $debit = 1;
            $this->MedicalMall_model_2->insert_payment_status($data['cust_id'], '34', $trans_id, $debit, $paym, $insert_id, $paym, $data['amount'], 'Processing', $data['amount'], $data['offer_amount'], $paym);
    		
    		
		} 
		else {
		    	$result = array(
        			"status" => "false",
        			"message" => "something went wrong"
    			);
		}
		return $result;
	}
	
	
	
	 public function place_order($data, $allowed, $products){
	   
	 //  print_r($data);  die(); 
	    date_default_timezone_set('Asia/Kolkata');
        $order_date = date('Y-m-d H:i:s');
        // work on this
            $delivery_charge = 0;
            $listing_name = "";
            $listing_type = 34;
            $chat_id = "";
            $lat = "";
            $lng = "";
            $address_id = "";
            $v_name = $data['vendor_name0'];
            $v_id = $data['vendor_id0'];
            $chc = $data['chc0'];
            //  $chc = 0;
            // print_r($chc); die();
        // work on this ends
		$invoice = date("YmdHis")."".$data['number'];
		if($data['payment_method'] == 3){
		    $paym = "Cash On Delivery";
		    $order_status = "Processing";
		}
		elseif($data['payment_method'] == 2){
		    $paym = "Credit/Debit";
		    $order_status = "Processing";
		}else{
		    $paym = "Points";
		    $order_status = "Processing";
		}
		$paym = $data['payment_method'];
  //		print_r($data['v_name']); die();
 		$name = $data['name'];
 	$invoice_no = $invoice;	   
    $name = $data['name'];;
    $mobileno = $data['mobileno']; 
    $pincode = $data['pincode'];
    $address = $data['address']; 
    $address1 = $data['address1'];  
    $landmark = $data['landmark']; 
    $city = $data['city'];  
    $state = $data['state']; 
    $cust_id = $data['cust_id'];
    $amount = $data['amount'];
    
// actual_total  '".$data['amount']."',



for($i=0;$i<$data['total_products'];$i++){
 $pdIds[] = $data['product_id'.$i];   
}
$pdIds = implode(", ",$pdIds);

 $discountQuery = $this->db->query("SELECT SUM(`pd_mrp_price`) as mrps FROM `product_details_hm` WHERE `pd_id` IN ($pdIds)")->row_array();
 $mrps = $discountQuery['mrps'];
 $dis = $mrps - $amount;
 

		$query_for_order = "INSERT INTO user_order
(invoice_no,order_status,listing_id,listing_type,listing_name,name,mobile,user_id,actual_cost,order_total,discount,chc,pincode,address1,landmark,city,state,payment_method,order_date,action_by) 
VALUES
('$invoice_no','$order_status','$v_id',$listing_type,'$v_name','$name','$mobileno','$cust_id','$mrps','$amount','$dis','$chc','$pincode','$address','$landmark','$city','$state','$paym','$order_date','customer')";

		$query = $this->db->query($query_for_order);
		
		$insert_id = $this->db->insert_id();
		
		if($insert_id != ""){
		    
		     //  insert into pending_payments table
		  //INSERT INTO `order_pending_payments` (`user_id`, `order_id`, `listing_id`) VALUES ('1', '2', '34');
		  // for website; in app not calling place_order untill payment is successful
		    
		    if($data['is_website'] == 1){
		      $query_for_order_pending = "INSERT INTO `order_pending_payments` (`user_id`, `order_id`, `listing_id`) VALUES ('$cust_id', '$insert_id', '34')";
		      $query = $this->db->query($query_for_order_pending);
		    
		  }
		  
		  
		    for($i=0;$i<$data['total_products'];$i++){
		        
		        if(!empty($data['referral_code'.$i])){
		            
		            $referralCode['referral_code_id'] = $data['referral_code'.$i];
		            $referralCode['pd_id'] = $data['product_id'.$i];
		            $referralCode['used_by'] = $data['cust_id'];
		            $referralCode['order_id'] = $insert_id;
		            
		            for($a=0;$a<sizeof($allowed['allowedArray']);$a++){
		                if( $referralCode['pd_id'] == $allowed['allowedArray'][$a]['pd_id']){
		                    $referralCode['user_id'] = $allowed['allowedArray'][$a]['user_id'];   
		                   
		                }
		                 
		             //   
		            }
		            
		            $this->db->insert('used_referral_codes',$referralCode);
		            
		          //  ````````
		        }
		      //  print_r($referralCode);
		        
		      //  die();
		    
		    	$query_for_order_data = "INSERT INTO orders_details_hm (user_id,order_id,product_id,product_qty,vendor_id,is_offer,offer_id,offer_price,price,variable_pd_id,sku)
         VALUES ('".$cust_id."','".$insert_id."','".$data['product_id'.$i]."','".$data['product_qty'.$i]."','".$data['vendor_id'.$i]."','".$data['is_offer'.$i]."','".$data['offer_id'.$i]."','".$data['offer_total'.$i]."','".$data['total_products_price'.$i]."','".$data['variable_pd_id'.$i]."','".$data['sku'.$i]."')";
		 
    		    $this->db->query($query_for_order_data);
    		
    			$empty_cart = "DELETE FROM user_cart WHERE customer_id = '".$data['cust_id']."' AND variable_pd_id = '".$data['variable_pd_id'.$i]."' AND product_id = '".$data['product_id'.$i]."'";
    		    $this->db->query($empty_cart);
    		
		    }
    // 		$dataAll = json_decode($data['products'],TRUE);
           
    		$dataAll = $products;
    		  //print_r($dataAll); die();
    		for($i=0;$i<count($dataAll['products']);$i++){
    		    $dataAll['products'][$i]['total_price'] = $dataAll['products'][$i]['price'] * $dataAll['products'][$i]['quantity'];
    		}
    		//print_r($dataAll['products']);die;
    		$query1=$this->db->query("SELECT * FROM `orders_details_hm` WHERE `order_id`='$insert_id' GROUP BY vendor_id")->result_array();
    		//print_r($query1);die;
    		$del_amt = 0;
    		foreach($query1 as $q1){
    		    $tmp="SELECT v_delivery_charge FROM vendor_details_hm WHERE v_id = '".$q1['vendor_id']."'";
    		  //   print_r($this->db->query($tmp)->row_array()['v_delivery_charge']) ; die();
    		    $t=$this->db->query($tmp)->row_array()['v_delivery_charge'];
    		   
    		    $del_amt += $t;
    		}
    		$this->db->query("UPDATE user_order SET delivery_charge = '$del_amt' WHERE order_id = '$insert_id'");
    		$res = $this->db->query("SELECT order_id,invoice_no,order_status,user_id,payment_method,order_date,delivery_charge FROM user_order WHERE order_id = '$insert_id'");
    		$result = array(
    			"status" => "true",
    			"original_total_price" => $data['amount'],
    			"offer_total_price" => $data['offer_amount'],
    			"saved_amount" => $data['savings'],
    			"products" => array_values($dataAll),
    			"order_details" => $res->result_array()
    			);
    	
    		
    // 		white here ****************
            $trans_id = date("YmdHis");
            $debit = 1;
            if($data['is_website'] ==1 ){
                $this->MedicalMall_model_2->insert_payment_status($data['cust_id'], '34', $trans_id, $debit, $paym, $insert_id, $paym, $data['amount'], 'Processing', $data['amount'], $data['offer_amount'], $paym);
    		}
		} 
		else {
		    	$result = array(
        			"status" => "false",
        			"message" => "something went wrong"
    			);
		}
// 		print_r($result); die();
		return $result;
	}
	
// 	placing_order_allowed

    public function placing_order_allowed($data){
       // print_r($data); die();
        $allowed = $notAllowed = 0;
        $reasonArray = $allowedArray = $notAllowedArray  = $result = array();
        $today = date('Y-m-d H:s:i');
       
        $cust_id = $data['cust_id'];    
        $orders = json_decode($data['orders'])->orders;    
        for($a=0;$a<sizeof($orders);$a++){
            $products = $orders[$a]->products;
            $proCount = sizeof($products);
            for($p=0;$p<sizeof($products);$p++){
                $referralCodeRows = array();
                $dataPdId = $products[$p]->id;
                if(!empty($products[$p]->referral_code)){
                    $referral_code_id = $products[$p]->referral_code;
                    $referralCodeRows = $this->db->query("SELECT * FROM `refer_product_hm` WHERE `id` = '$referral_code_id' AND `pd_id` = $dataPdId")->row_array();
                    //print_r($referralCodeRows); die();
                    if(sizeof($referralCodeRows)>0){
                        $codeExp = $referralCodeRows['expiry'];
                        $codePdId = $referralCodeRows['pd_id'];
                        $codeUser_id = $referralCodeRows['user_id'];
                        if($today < $codeExp){
                            
                            $reasonArray = array("referralCode"=>$referral_code_id,"pd_id"=>$dataPdId,'user_id'=>$codeUser_id,"message" => "Code Available to use");
                            $allowedArray[] = $reasonArray;
                            $allowed++;
                            
                            
                        } else {
                            $reasonArray = array("referralCode"=>$referral_code_id,"pd_id"=>$dataPdId,'user_id'=>$codeUser_id,"message" => "Code expired");
                            $notAllowedArray[] = $reasonArray;
                            $notAllowed++;
                            
                        }
                    } else {
                        $reasonArray = array("referralCode"=>$referral_code_id,"pd_id"=>$dataPdId,'user_id'=>$codeUser_id,"message" => "Code does not exists or allowed for another product");
                        $notAllowedArray[] = $reasonArray;
                        $notAllowed++;
                       
                    }
                    

                         
                } 
            }
        }
        
        if($notAllowed > 0){
            $result['status']=400;
            $result['message']="Can not use the code";
        } else {
            
            
            $result['status']=200;
            $result['message']="Can use the code";
            
            
           
        }
        
        $result['allowedArray'] = $allowedArray;
        $result['notAllowedArray'] = $notAllowedArray;
        
        return $result;
    }


	public function change_payment_status($user_id, $status){
	    $getOrders  = $this->db->query("SELECT * FROM `order_pending_payments` WHERE `user_id` = '$user_id'")->result_array();
        // DELETE FROM `order_pending_payments` WHERE `user_id` = 1
        if($status == 1){
            
            // print_r($getOrders); die();
            foreach($getOrders as $getOrder){
                $order_id = $getOrder['order_id'];
                $updatedOrders  = $this->db->query("UPDATE `user_order` SET `order_status` = 'Processing' WHERE `order_id` = '$order_id'");
              
                if($updatedOrders == 1){
                    $this->db->query("DELETE FROM `order_pending_payments` WHERE `order_id` = '$order_id'");
                }
                  
            }
             $resp = array(
    	        "status" => 200,
                "message" => "success",
                "Desctription" => "Order status updated and deleted from order_pending_payments"
	        );
           
        } else {
            
               foreach($getOrders as $getOrder){
                $order_id = $getOrder['order_id'];
                    $this->db->query("DELETE FROM `order_pending_payments` WHERE `order_id` = '$order_id'");
                  
            }
            
            $resp = array(
    	        "status" => 200,
                "message" => "success",
                "Desctription" => "Only deleted from order_pending_payments"
	        );
             
             }
        		         
        return $resp;
   }

	
	//  added by zak for helthmall notification
	//start
	
	/*
   Confirm Status is used to confirm the 
   status of the appointment.
   Doubt in query call doctor name can be called  from parent method using join
   which is better way.
   */
   public function cash_on_delivery_status($user_id,$title,$payment_type)
   {
       $appointment_status = '2';
      // $table_record = $this->db->query("SELECT doctor_name FROM doctor_list WHERE user_id='$listing_id' ");
     //  $count_user = $table_record->num_rows();
     //  if($count_user>0)
       {
            $this->notifyMethod($user_id, $title, $booking_id, $doctor_name,$title,$msg);
       }
      
   }
   
     /*
   Reschedule Status 
   Doctor has not confirmed the appointment.
   */
   public function reschedule_status($user_id, $booking_id, $listing_id)
   {
       $appointment_status = '4';
       $table_record = $this->db->query("SELECT doctor_name FROM doctor_list WHERE user_id='$listing_id' ");
       $count_user = $table_record->num_rows();
       if($count_user>0)
       {
            $row = $table_record->row();
            $doctor_name = $row->doctor_name; 
             $title = $doctor_name . ' has Reschedule an appointment';
             $msg = $doctor_name . '  has Reschedule an appointment for'.$consultation_type;
            $this->notifyMethod($user_id, $appointment_status, $booking_id, $doctor_name,$title,$msg);
       }
      
   }
   
   
     /*
   Cancel Status 
   Doctor has canceled the appointment.
   */
   
   
  public function cancel_status($user_id, $booking_id, $listing_id)
   {
       $appointment_status = '3';
       $table_record = $this->db->query("SELECT doctor_name FROM doctor_list WHERE user_id='$listing_id' ");
       $count_user = $table_record->num_rows();
       if($count_user>0)
       {
            $row = $table_record->row();
            $doctor_name = $row->doctor_name; 
             $title = $doctor_name . ' has Cancel an appointment';
             $msg = $doctor_name . '  has Cancel an appointment for'.$consultation_type;
            $this->notifyMethod($user_id, $appointment_status, $booking_id, $doctor_name,$title,$msg);
       }
      
   }
   
	
	
	
	
	 /*
    This method is used for notification.
    Left doctor service.
    */
    public function notifyMethod($user_id,$title,$payment_type)
    {

          $customer_token = $this->db->query("SELECT name,token,agent,token_status FROM users WHERE id='$user_id'");
          $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
            //    $getusr = $user_plike->row_array();

                $usr_name = $token_status['name'];
                $agent = $token_status['agent'];
                $reg_id = $token_status['token'];
                $img_url = 'https://medicalwale.com/img/medical_logo.png';
                $tag = 'text';
                $key_count = '1';
               // $title = $doctor_name . ' has confirmed an appointment';
               // $msg = $doctor_name . '  has confirmed an appointment.\n';
                $title = $title;
                $msg = $title;
                $this->send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent);
            }
    }


        //send notification through firebase
        /* notification to send in the doctor app for appointment confirmation*/
    public function send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent) {
     
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
                "notification_type" => 'HealthMall_order',
                "notification_date" => $date
                
            )
        );
       
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
            $agent === 'android' ? 'Authorization: key=AIzaSyDTr3BzDHKj_6FnUiZXT__VCmamH-_gupM' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
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
    }
    
	
	
	//end
	
	  public function insert_payment_status($user_id, $listing_id, $trans_id, $debit, $type, $order_id, $order_type,$amount, $status_mesg, $discount, $discount_rupee, $payment_type) {
        
      
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $status = 1;
        $Expire_date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($date)) . " + 1 year"));
        if($debit == "1"){
            $creadit_debit = 1;
        }else{
            $creadit_debit = 0;
        }    
        $upadte_ledger_array = array(
            'user_id'       => $user_id,
            'listing_id'    => $listing_id,
            'trans_id'      => $trans_id,
            'trans_type'    => $creadit_debit,
            'order_id'      => $order_id,
            'amount'        => $amount,
            'order_status'  => '1',
            'order_type'    => $order_type,
            'status_message'=> $status_mesg,
            'discount'      => $discount,
            'discount_rupee'=> $discount_rupee,
            'trans_time'    => $date,
            'trans_mode'=> $payment_type,
            //'vendor_category'=> $vendor_category
            'vendor_category'=>  $type
        );
        
        
        $upadte_ledger_array_point = array(
            'user_id'       => $user_id,
            'listing_id'    => $listing_id,
            'trans_id'      => $trans_id,
            'trans_type'    => 4,
            'order_id'      => $order_id,
            'amount'        => $amount,
            'order_status'  => $status=='1'?'success':'failed',
            'order_type'    => $order_type,
            'status_message'=> $status_mesg,
            'discount'      => $discount,
            'discount_rupee'=> $discount_rupee,
            'trans_time'    => $date,
            'trans_mode'=> $payment_type,
             //   'vendor_category'=> $vendor_category
             'vendor_category'=>  $type
        );
        
        $upadte_user_points = array(
            'user_id'        => $user_id,
            'order_id'       => $order_id,
            'trans_id'       => $trans_id,
            'points'         => $amount,
            'created_at'     => $date,
            'expire_at'      => $Expire_date,    
            'status'         => 'active'
        );
       
       
        if($payment_type != '3'){
            
           
            $this->db->insert('user_ledger',$upadte_ledger_array);
         
         //update ledger balance but this balance is locked
            $query = $this->db->query("select * from user_ledger_balance where user_id='$user_id'");
            $row = $query->row();
            $row_count =$query ->num_rows();
            //die();
            
            if($row_count>0)
            {
                //update ledger balance 
                $existing_balance = $row->ledger_balance;
                $existing_lock_balance =$row->lock_amount;
                $total_balance = $existing_balance + $amount; 
                $total_lock_amount = $existing_lock_balance + $amount;
                $user_ledger_balance = array(
                    'ledger_balance' =>$total_balance,
                    'lock_amount' => $total_lock_amount
                    );
                     $this->db->where('user_id', $user_id);  
                $this->db->update('user_ledger_balance', $user_ledger_balance); 
            }
            else
            {
              // insert into ledger balance 
               $user_ledger_balance = array(
                    'user_id' => $user_id,
                    'ledger_balance' =>$amount,
                    'lock_amount' => $amount                                                                  
                    );
              $this->db->insert('user_ledger_balance',$user_ledger_balance);
            }
           //end
            
            // }
            
        }
      
        return array(
            'status' => 201,
            'message' => 'success'
        );
        
    }
 
	
	
	public function get_products($type=0,$id,$term=""){
	    
	    if($type == 0){
	        $stmt = "SELECT d.pd_id,vd.v_id,vd.v_name,vd.v_delivery_charge,d.pd_name,d.pd_pc_id,d.pd_psc_id,d.pd_photo_1,d.pd_photo_2,d.pd_photo_3,d.pd_photo_4,d.pd_mrp_price,d.pd_vendor_price,d.pd_quantity,d.pd_long_desc,d.pd_view_count as total_view FROM `product_details_hm` d LEFT join vendor_details_hm vd on vd.v_id=d.pd_added_v_id where (vd.v_name LIKE '%$term%' OR d.pd_name LIKE '%$term%') AND d.pd_status = '1'";
	        
	       
	    }
	    elseif($type == 1){
	        $stmt = "SELECT d.pd_id,vd.v_id,vd.v_name,vd.v_delivery_charge,d.pd_name,d.pd_pc_id,d.pd_psc_id,d.pd_photo_1,d.pd_photo_2,d.pd_photo_3,d.pd_photo_4,d.pd_mrp_price,d.pd_vendor_price,d.pd_quantity,d.pd_long_desc,d.pd_view_count as total_view FROM `product_details_hm` d LEFT join vendor_details_hm vd on vd.v_id=d.pd_added_v_id where d.pd_pc_id='$id' AND (vd.v_name LIKE '%$term%' OR d.pd_name LIKE '%$term%')  AND d.pd_status = '1'";
	        
	    }
	    else{
	        $stmt = "SELECT d.pd_id,vd.v_id,vd.v_name,vd.v_delivery_charge,d.pd_name,d.pd_pc_id,d.pd_psc_id,d.pd_photo_1,d.pd_photo_2,d.pd_photo_3,d.pd_photo_4,d.pd_mrp_price,d.pd_vendor_price,d.pd_quantity,d.pd_short_desc,d.pd_long_desc,d.pd_view_count as total_view FROM `product_details_hm` d LEFT join vendor_details_hm vd on vd.v_id=d.pd_added_v_id where d.pd_psc_id='$id' AND (vd.v_name LIKE '%$term%' OR d.pd_name LIKE '%$term%')  AND d.pd_status = '1'";
	        
	    }
	    
	   // $resp = array(
	   //         "status" => "true",
	   //         "statuspic_root_code" => "200",
	   //         "data" => $this->db->query($stmt)->result_array()
	   //        );
	   return $this->db->query($stmt);
	    
	}
	
// 	public function first_child_category($id){
// 	     $cat = $this->db->query("SELECT * FROM `categories` WHERE `id` = '$id'")->row_array();
// 	        if($cat['parent_cat_id'] == 0){
// 	            return $id;
// 	        } else {
	            
// 	        }
// 	        die();
// 	}



    	public function get_products_v($type=0,$id,$term="",$page_no, $per_page,$vid){
	    
	    if($type == 0){
	        $stmt = "SELECT d.pd_id,vd.v_id,vd.v_name,vd.v_delivery_charge,d.pd_name,d.pd_pc_id,d.pd_psc_id,d.pd_photo_1,d.pd_photo_2,d.pd_photo_3,d.pd_photo_4,d.pd_mrp_price,d.pd_vendor_price,d.pd_quantity,d.pd_long_desc,d.pd_view_count as total_view FROM `product_details_hm` d LEFT join vendor_details_hm vd on vd.v_id=d.pd_added_v_id where vd.v_id=$vid AND (vd.v_name LIKE '%$term%' OR d.pd_name LIKE '%$term%') AND d.pd_status = '1'";
	        
	       
	    }
	    elseif($type == 1){
	        $stmt = "SELECT d.pd_id,vd.v_id,vd.v_name,vd.v_delivery_charge,d.pd_name,d.pd_pc_id,d.pd_psc_id,d.pd_photo_1,d.pd_photo_2,d.pd_photo_3,d.pd_photo_4,d.pd_mrp_price,d.pd_vendor_price,d.pd_quantity,d.pd_long_desc,d.pd_overall_ratting,d.pd_view_count as total_view FROM `product_details_hm` d LEFT join vendor_details_hm vd on vd.v_id=d.pd_added_v_id where d.pd_pc_id='$id' AND vd.v_id=$vid AND (vd.v_name LIKE '%$term%' OR d.pd_name LIKE '%$term%')  AND d.pd_status = '1'";
	        
	    }
	    else{
	        //echo "vid:".$vid;
	        $stmt = "SELECT d.pd_id,vd.v_id,vd.v_name,vd.v_delivery_charge,d.pd_name,d.pd_pc_id,d.pd_psc_id,d.pd_photo_1,d.pd_photo_2,d.pd_photo_3,d.pd_photo_4,d.pd_mrp_price,d.pd_vendor_price,d.pd_quantity,d.pd_short_desc,d.pd_long_desc,d.pd_view_count as total_view FROM `product_details_hm` d LEFT join vendor_details_hm vd on vd.v_id=d.pd_added_v_id where d.pd_psc_id='$id' AND vd.v_id=$vid AND (vd.v_name LIKE '%$term%' OR d.pd_name LIKE '%$term%')  AND d.pd_status = '1'";
	     //   $stmt = "SELECT d.pd_id,vd.v_id,vd.v_name,vd.v_delivery_charge,d.pd_name,d.pd_pc_id,d.pd_psc_id,d.pd_photo_1,d.pd_photo_2,d.pd_photo_3,d.pd_photo_4,d.pd_mrp_price,d.pd_vendor_price,d.pd_quantity,d.pd_short_desc,d.pd_long_desc,avg(r.pr_rating),d.pd_view_count as total_view FROM `product_details_hm` d,vendor_details_hm vd, product_reviews r WHERE vd.v_id=d.pd_added_v_id AND d.pd_psc_id='$id'AND r.pr_pd_id=d.pd_id AND (vd.v_name LIKE '%$term%' OR d.pd_name LIKE '%$term%')  AND d.pd_status = '1'";
	        
	    }
	    
	   // $resp = array(
	   //         "status" => "true",
	   //         "statuspic_root_code" => "200",
	   //         "data" => $this->db->query($stmt)->result_array()
	   //        );
	   return $this->db->query($stmt);
	    
	}
	
	
    public function first_child_category($id){
	        $cat = $this->db->query("SELECT * FROM `categories` WHERE `id` = '$id'")->row_array();
	        if($cat['parent_cat_id'] == 0){
	            return $id;
	        } else {
	            $cat = $this->db->query("SELECT * FROM `categories` WHERE `parent_cat_id` = '$id'")->result_array();
	            print_r($cat);
	        }
	        die();
	}
	
	public function get_products1($type=0,$id,$term=""){
	   if($id != ""){
	       $resp = $this->MedicalMall_model_2->first_child_category($id);
	        
	       //first_child_category
	     
	   }
	   die();
	    if($type == 0){
	        $stmt = "SELECT d.pd_id,vd.v_id,vd.v_name,vd.v_delivery_charge,d.pd_name,d.pd_pc_id,d.pd_psc_id,d.pd_photo_1,d.pd_photo_2,d.pd_photo_3,d.pd_photo_4,d.pd_mrp_price,d.pd_vendor_price,d.pd_quantity,d.pd_long_desc,d.pd_view_count as total_view FROM `product_details_hm` d LEFT join vendor_details_hm vd on vd.v_id=d.pd_added_v_id where d.pd_name LIKE '%$term%' AND d.pd_status = '1'";
	        
	       
	    }
	    elseif($type == 1){
	        $stmt = "SELECT d.pd_id,vd.v_id,vd.v_name,vd.v_delivery_charge,d.pd_name,d.pd_pc_id,d.pd_psc_id,d.pd_photo_1,d.pd_photo_2,d.pd_photo_3,d.pd_photo_4,d.pd_mrp_price,d.pd_vendor_price,d.pd_quantity,d.pd_long_desc,d.pd_view_count as total_view FROM `product_details_hm` d LEFT join vendor_details_hm vd on vd.v_id=d.pd_added_v_id where d.pd_pc_id='$id' AND d.pd_name LIKE '%$term%'  AND d.pd_status = '1'";
	        
	    }
	    else{
	        $stmt = "SELECT d.pd_id,vd.v_id,vd.v_name,vd.v_delivery_charge,d.pd_name,d.pd_pc_id,d.pd_psc_id,d.pd_photo_1,d.pd_photo_2,d.pd_photo_3,d.pd_photo_4,d.pd_mrp_price,d.pd_vendor_price,d.pd_quantity,d.pd_short_desc,d.pd_long_desc,d.pd_view_count as total_view FROM `product_details_hm` d LEFT join vendor_details_hm vd on vd.v_id=d.pd_added_v_id where d.pd_psc_id='$id' AND d.pd_name LIKE '%$term%'  AND d.pd_status = '1'";
	        
	    }
	    
	   // $resp = array(
	   //         "status" => "true",
	   //         "statuspic_root_code" => "200",
	   //         "data" => $this->db->query($stmt)->result_array()
	   //        );
	   return $this->db->query($stmt);
	    
	}
	
	public function placeOrder_ledger($data){
	    
	    date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $order_date = date('Y-m-d H:i:s');
        // work on this
            $delivery_charge = 0;
            $listing_name = "";
            $listing_type = 34;
            $chat_id = "";
            $lat = "";
            $lng = "";
            $address_id = "";
        // work on this ends
		//$order_id=uniqid();
		$invoice = date("YmdHis");
		$query_for_order = "INSERT INTO user_order (invoice_no,order_status,name,mobile,user_id,order_total,pincode,address1,landmark,city,state,payment_method,order_date,action_by)
         VALUES ('$invoice','Confirmed','".$data['name']."','".$data['mobileno']."','".$data['cust_id']."','".$data['amount']."',".$data['pincode'].",'".$data['address']."','".$data['landmark']."','".$data['city']."','".$data['state']."','".$data['payment_method']."','$order_date','customer')";
		
		$query = $this->db->query($query_for_order);
		
		$insert_id = $this->db->insert_id();
		if($insert_id != ""){
		    for($i=0;$i<$data['total_products'];$i++){
		    	$query_for_order_data = "INSERT INTO orders_details_hm (order_id,product_id,product_qty,vendor_id)
         VALUES ('".$insert_id."','".$data['product_id'.$i]."','".$data['product_qty'.$i]."','".$data['vendor_id'.$i]."')";
		 
    		$this->db->query($query_for_order_data);
		    }
    		if($data['status'] == "1"){
                $credit_debit = 0;
            }else{
                $credit_debit = 2;
            }    
    		$upadte_ledger_array = array(
            'user_id'       => $data['cust_id'],
            'trans_id'      => $data['trans_id'],
            'trans_type'    => $credit_debit,
            'order_id'      => $insert_id,
            'amount'        => $data['amount'],
            'trans_time'    => $date,
            'order_status'  => $data['status']=='1'?'success':'failed',
            'order_type'    => 'order',
            'status_message'=> $data['status']=='1'?'success':'failed',
            'trans_mode'    => $data['payment_type'],
            // 'vendor_category'=>  $type
            );
    		if($data['payment_type'] != '3'){
                $this->db->insert('user_ledger',$upadte_ledger_array);
                $cust_id = $data['cust_id'];
                $query = $this->db->query("select * from user_ledger_balance where user_id='$cust_id'");
                $row = $query->row();
                $row_count =$query->num_rows();
                
                if($row_count>0)
                {
                    //update ledger balance 
                    $existing_balance = $row->ledger_balance;
                    // $existing_lock_balance =$row->lock_amount;
                    $total_balance = $existing_balance + $amount; 
                    // $total_lock_amount = $existing_lock_balance + $amount;
                    $user_ledger_balance = array(
                        'ledger_balance' =>$total_balance,
                        // 'lock_amount' => $total_lock_amount
                        );
                         $this->db->where('user_id', $user_id);  
                    $this->db->update('user_ledger_balance', $user_ledger_balance); 
                }
                else
                {
                  // insert into ledger balance 
                   $user_ledger_balance = array(
                        'user_id' => $data['cust_id'],
                        'ledger_balance' =>$data['amount'],
                        // 'lock_amount' => $amount                                                                  
                        );
                  $this->db->insert('user_ledger_balance',$user_ledger_balance);
                }
    		}
    		$dataAll = json_decode($data['products'],TRUE);
    		$result = array(
    			"status" => "true",
    			"statuspic_root_code" => "201",
    			"message" => "Order Success",
    			"products" => array_values($dataAll)
    			);
		} else {
		    	$result = array(
        			"status" => "false",
        			"message" => "Order failed"
    			);
		}
		return $result;
	}
	
// 	public function payment_gateway($posted){
// 	    $hashSequence ="key|txnid|amount|productinfo|firstname|email";
// 	    if(['mode'] == 'live'){
// 	        $PAYU_BASE_URL = "https://secure.payu.in/_payment";
//         $key = 'truYTi';
//         $SALT = '2Wov4ydo';
// 	    } else {
// 	       // Add live server credentials
	        
// 	        $PAYU_BASE_URL = "https://test.payu.in/_payment";
//             $key = 'gtKFFx';
//             $SALT = 'eCwWELxi';
	        
// 	    }
	    
//         if(empty($posted['hash']) && sizeof($posted) > 0) {
//             if(!empty($posted['hash'])) {
//                 $hash = $posted['hash'];
//                 $action = $PAYU_BASE_URL . '/_payment';
//             }
//             else{
//                 $hashVarsSeq = explode('|', $hashSequence);
//                 $hash_string = '';
//                 foreach($hashVarsSeq as $hash_var) {
//                     $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
//                     $hash_string .= '|';
//                 }
//                 $hash_string .= '||||||||||'.$SALT;
//                 //print_r($hash_string);die;
//                 $hash = strtolower(hash('sha512', $hash_string));
//                 $action = $PAYU_BASE_URL . '/_payment';
//             }
//         } 
//         $data_array =  array(
//           "key" => $key,
//           "txnid" => $posted['txnid'],
//           "amount" => $posted['amount'],
//           "productinfo" => $posted['productinfo'],
//           "firstname" => $posted['firstname'],
//           "email" => $posted['email'],
//           "phone" => $posted['phone'],
//           "surl" => $posted['surl'],
//           "furl" => $posted['furl'],
//           "HASH" => $hash,
//           "action" => $action
//         );
//         //print_r(json_encode($data_array));die;
//         // $this->load->view('payu',$data_array);
//         return $data_array;
// 	}


	public function payment_gateway($posted){
	   // sha512(key|txnid|amount|productinfo|firstname|email|||||||||||SALT)

	    $hashSequence ="key|txnid|amount|productinfo|firstname|email";
	    
	    if(['mode'] == 'live'){
	        $PAYU_BASE_URL = "https://secure.payu.in";
        $key = 'truYTi';
        $SALT = '2Wov4ydo';
	    } else {
	       // Add live server credentials
	        	        $PAYU_BASE_URL = "https://secure.payu.in";

	       // $PAYU_BASE_URL = "https://test.payu.in";
	         $key = 'truYTi';
             $SALT = '2Wov4ydo';
             $posted['key'] = 'truYTi';
            //  $posted['SALT'] = '2Wov4ydo';
            // $key = 'gtKFFx';
            // $SALT = 'eCwWELxi';
            $surl = 'https://medicalwale.com/healthmall/success.html';
            $furl = 'https://medicalwale.com/healthmall/failed.html';
	        
	    }
	   // $hashSequence ="key|txnid|amount|productinfo|firstname|email";
        if(empty($posted['hash']) && sizeof($posted) > 0) {
            if(!empty($posted['hash'])) {
                $hash = $posted['hash'];
                $action = $PAYU_BASE_URL . '/_payment';
            }
            else{
                
                $hashVarsSeq = explode('|', $hashSequence);
                $hash_string = '';
                foreach($hashVarsSeq as $hash_var) {
                    // echo $hash_var."<br>"; 
                    $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
                    $hash_string .= '|';
                }
                //  print_r($hash_string);die;
                $hash_string .= '||||||||||'.$SALT;
               
                $hash = strtolower(hash('sha512', $hash_string));
                $action = $PAYU_BASE_URL . '/_payment';
            }
        } 
        // print_r(gettype(floatval($posted['amount']))); die();
        $data_array =  array(
          "key" => $key,
          "txnid" => $posted['txnid'],
          "amount" => floatval($posted['amount']),
          "productinfo" => $posted['productinfo'],
          "firstname" => $posted['firstname'],
          "email" => $posted['email'],
          "phone" => $posted['phone'],
        //   "surl" => $posted['surl'],
        //   "furl" => $posted['furl'],
          "surl" => $surl,
          "furl" => $furl,
          "HASH" => $hash,
          "action" => $action
        );
        // print_r($data_array); die();
        
        //print_r(json_encode($data_array));die;
        // $this->load->view('payu',$data_array);
        return $data_array;
	}
	
	public function cancel_order($order_id, $reason){
        
        $checkAvailable = $this->db->query("SELECT order_id,invoice_no,order_status,user_id,payment_method,order_date,cancel_reason FROM user_order WHERE order_id = '$order_id'");
        
        $count = $checkAvailable->num_rows();
        
        if($count > 0){
            if($checkAvailable->result_array()[0]['order_status'] == 'Order Cancelled'){
                $res = array(
                    "status" => 400,
                    "message" => "Order already Cancelled",
                    "order_details" => $checkAvailable->result_array()
                );
            }else{
                $this->db->query("UPDATE user_order SET order_status = 'Order Cancelled', cancel_reason = '$reason' WHERE order_id = '$order_id'");
                $checkAvailable = $this->db->query("SELECT order_id,invoice_no,order_status,user_id,payment_method,order_date,cancel_reason FROM user_order WHERE order_id = '$order_id'");
                $res = array(
                    "status" => 200,
                    "message" => "Order Cancelled",
                    "order_details" => $checkAvailable->result_array()
                );
            }
        } else {
            
             $res = array(
                "status" => 400,
                "message" => "No such order"
            );
            
        
        }
	    return $res;
	}
	
// 	brand_images
/*	public function brand_images(){
        $finalDataNew = $finalData =  array();
        // $vendorRow = $this->db->query("SELECT * FROM `vendor_details_hm` WHERE `v_status` = '1' ");
        // foreach($vendorRow->result_array() as $row){
        //     $p_id = $row['v_id'];
        //     $checkProds = $this->db->query("SELECT * FROM `product_details_hm` WHERE `pd_added_v_id` = '$p_id'");
        //     if($checkProds->num_rows() != 0 ){
        //         $data['v_id'] = $row['v_id'];
        //         $data['v_name'] = $row['v_name'];
        //         $data['v_company_name'] = $row['v_company_name'];
        //         $data['v_company_logo'] = $row['v_company_logo'];
                
        //         $finalData[] = $data;
        //     } 
        // }
        
        // print_r($finalData);
        // echo "new";
        $finalDataNew = $this->db->query("SELECT DISTINCT vd.v_id, vd.v_name, vd.v_company_name, vd.v_company_logo FROM vendor_details_hm as vd join product_details_hm as pd ON vd.v_id = pd.pd_added_v_id  WHERE `v_status` = '1' ")->result_array();
        // print_r($finalDataNew); die();
        return $finalDataNew;
        
	}*/
	
	
	public function brand_images(){
        $withrank = $withoutrank = $finalDataNew = $finalData =  array();
        // $vendorRow = $this->db->query("SELECT * FROM `vendor_details_hm` WHERE `v_status` = '1' ");
        // foreach($vendorRow->result_array() as $row){
        //     $p_id = $row['v_id'];
        //     $checkProds = $this->db->query("SELECT * FROM `product_details_hm` WHERE `pd_added_v_id` = '$p_id'");
        //     if($checkProds->num_rows() != 0 ){
        //         $data['v_id'] = $row['v_id'];
        //         $data['v_name'] = $row['v_name'];
        //         $data['v_company_name'] = $row['v_company_name'];
        //         $data['v_company_logo'] = $row['v_company_logo'];
                
        //         $finalData[] = $data;
        //     } 
        // }
        
        // print_r($finalData);
        // echo "new";
        $finalDataNew = $this->db->query("SELECT DISTINCT vd.v_id, vd.v_name, vd.v_company_name, vd.v_company_logo FROM vendor_details_hm as vd join product_details_hm as pd ON vd.v_id = pd.pd_added_v_id  WHERE `v_status` = '1' ")->result_array();
        // print_r($finalDataNew); die();
        $withrank = $this->db->query("SELECT DISTINCT vd.v_id, vd.v_name, vd.v_company_name, vd.v_company_logo, vd.rank FROM vendor_details_hm as vd join product_details_hm as pd ON vd.v_id = pd.pd_added_v_id WHERE `v_status` = '1' and `rank` != 0 ORDER BY `rank`")->result_array();
        
        $withoutrank = $this->db->query("SELECT DISTINCT vd.v_id, vd.v_name, vd.v_company_name, vd.v_company_logo, vd.rank FROM vendor_details_hm as vd join product_details_hm as pd ON vd.v_id = pd.pd_added_v_id WHERE `v_status` = '1' and `rank` = 0 ORDER BY `rank`")->result_array();
        
        $result = array_merge($withrank, $withoutrank);
        // print_r($result); die();
        return $result;
        
	}
	
	
		public function get_cat_subcat(){
	    
	    $results = $this->db->query("SELECT * FROM `categories` WHERE `parent_cat_id`='0'");
	    foreach($results->result_array() as $result){
	        
	        $catId = $result['id'];
	        
            $result_sub = $this->db->query("SELECT * FROM `categories` WHERE `parent_cat_id` = '$catId'");
            
            $prods = $this->db->query("SELECT * from product_details_hm WHERE pd_pc_id = '$catId'");
		    
		    $pro = $prods->result_array();
		    if(sizeof($pro) != 0){
		        
		    
            $data_subcat_all = [] ;
            
            
             $data_subcat_all[] = $this->MedicalMall_model_2->get_subcat($result['id']);
            
            foreach($result_sub->result_array() as $subcat){
                // $data_subcat[] = $subcat;
                $dataSubcatId = $subcat['id'];
                $results1 = $this->MedicalMall_model_2->get_subcat($dataSubcatId);
                
                $dataSubcat['psc_id'] = $subcat['id'];
                $dataSubcat['psc_name'] = $subcat['cat_name'];
                $dataSubcat['psc_photo'] = $subcat['photo'];
                $dataSubcat['subcategory'] = $results1;
                // $dataSubcat['psc_short_desc'] = $subcat['psc_short_desc'];
                // $dataSubcat['psc_long_desc'] = $subcat['psc_long_desc'];
                $data_subcat_all[] = $dataSubcat;
            }
	       
	       
	       
	        $cat['pc_id'] = $result['id'];
	        $cat['pc_name'] = $result['cat_name'];
	        $cat['pc_photo'] = $result['photo'];
            // $cat['pc_short_desc'] = $result['pc_short_desc'];
            // $cat['pc_long_desc'] = $result['pc_long_desc'];
	        $cat['subcategory'] = $data_subcat_all;
	        $data[] = $cat;
	    }
	    }
	       
	    return $data_subcat_all;
	}
	
	
	public function get_subcat($dataSubcatId){
	    $data = array();
	    $results = $this->db->query("SELECT * FROM `categories` WHERE `parent_cat_id`='$dataSubcatId'");

	    foreach($results->result_array() as $result){
	        
	        $catId = $result['id'];
	        
            $result_sub = $this->db->query("SELECT * FROM `categories` WHERE `parent_cat_id` = '$catId'");
            $prods = $this->db->query("SELECT * from product_details_hm WHERE pd_pc_id = '$catId' OR pd_psc_id = '$catId'")->result_array();
            $data_subcat_all = [] ;
            $data_subcat_all[] = $catId;
            if(sizeof($prods) != 0){
                foreach($result_sub->result_array() as $subcat){
                    
                    $dataSubcatIdInSub = $subcat['id'];
                    $results1 = $this->MedicalMall_model_2->get_subcat($dataSubcatIdInSub);
                    
                    $data_subcat_all[] = $subcat['id'];
                    
                     
                    // $results1 = $this->MedicalMall_model_2->getProductByCategories($subcat['id'])->result_array();;
                    
                    
                }
	        $data[] = $data_subcat_all;
            }
	    }
 	  
	    return $data;
	}
	
	
	public function getProductByCategories($catId){
	
		$stmt = "SELECT categories.*,v_distance,product_details_hm.pd_id,product_details_hm.pd_name,product_details_hm.pd_mrp_price,product_details_hm.pd_vendor_price,product_details_hm.pd_overall_ratting,vendor_details_hm.v_id,product_details_hm.pd_photo_1,product_details_hm.pd_photo_2,product_details_hm.pd_photo_3,product_details_hm.pd_photo_4
		FROM product_details_hm 
		LEFT JOIN vendor_details_hm ON vendor_details_hm.v_id = product_details_hm.pd_added_v_id
		LEFT JOIN categories ON product_details_hm.pd_pc_id = categories.id OR product_details_hm.pd_psc_id = categories.id
		WHERE categories.id=".$catId."";
		
		$query = $this->db->query($stmt);

		return $query;
	}
	
	
// 	filters
	public function get_filters(){
        $finalData =  array();
        
        $finalData['categories'] = array();
        $finalData['brands'] = array();
        // $finalData['size'] = array();
        // $finalData['offers'] = array();
        // $finalData['availability'] = array();
        
        // $vendorRow = $this->db->query("SELECT * FROM `vendor_details_hm` WHERE `v_status` = '1' ");
         $finalData['categories'] = $this->MedicalMall_model_2->get_cat_subcat();
        $finalData['brands'] = $this->MedicalMall_model_2->brand_images(); 
       
        return $finalData;
	}
	
	public function get_subcatIds($catId){
	    $subCatIds = $data = array();
	     $result_sub = $this->db->query("SELECT id FROM `categories` WHERE `parent_cat_id` = '$catId'");
	    foreach($result_sub->result_array() as $result){
	       $subCatIds[] = $result['id'];
	   }
	   
	   // print_r($subCatIds); die();
	    return $subCatIds;
	}
	
	
	public function get_products_by_cat($id){
	       $stmt = "SELECT d.pd_id,vd.v_id,vd.v_name,vd.v_delivery_charge,d.pd_name,d.pd_pc_id,d.pd_psc_id,d.pd_photo_1,d.pd_photo_2,d.pd_photo_3,d.pd_photo_4,d.pd_mrp_price,d.pd_vendor_price,d.pd_quantity,d.pd_long_desc,d.pd_view_count as total_view FROM `product_details_hm` d LEFT join vendor_details_hm vd on vd.v_id=d.pd_added_v_id where d.pd_pc_id = '$id' OR d.pd_psc_id = '$id' AND d.pd_status = '1'";
	       return $this->db->query($stmt);
	   
	}
	
// 	add_filters
//     public function add_filters($filter){
//       $filtered = $brnd = $finalDataArray = $cats = $subCatsProducts = $subCatsProducts1 = $allSubcategories =$finalData =  array();
//         // $finalData['size'] = array();
//         // $finalData['offers'] = array();
//         // $finalData['availability'] = array();
        
        
//         if(!empty($filter['category_ids'])){
            
//             $catFilterArray = explode(",",$filter['category_ids']);
//             $allSubcategories[] = $catFilterArray;
//             foreach($catFilterArray as $catId){
    
//             // get subcategories
//                 $subCatsProducts1 =  $this->MedicalMall_model_2->get_subcat($catId);
                 
//                 $subCatsProducts = call_user_func_array("array_merge", $subCatsProducts1);
//                 $allSubcategories[] = $subCatsProducts;
                
//             }
//             $allSubcategories = call_user_func_array("array_merge", $allSubcategories);
//             $all=implode(",",$allSubcategories);
//         }
//         else {
//           $allcats =  $this->db->query("SELECT id FROM categories WHERE 1")->result_array();
//         //   print_r($allcats);
//           foreach($allcats as $cat){
//               $cats[] = $cat['id'];
//           }
           
//         $all = implode(",",$cats);
       
//         }
        
//         if(!empty($filter['brands'])){
//             $brnd = $filter['brands'];
//         } else {
//             $allBrands =  $this->db->query("SELECT `v_id` FROM `vendor_details_hm` WHERE `v_status` = 1")->result_array();
//         //   print_r($allBrands);
//           foreach($allBrands as $brand){
//               $brnd[] = $brand['v_id'];
//           }
           
//         $brnd = implode(",",$brnd);
//             // $brnd = 0;
//         }
        
//         if($filter['in_stock']>=1){
//             $stock = $filter['in_stock'];
//             $finalData = "SELECT DISTINCT * FROM `product_details_hm` WHERE (pd_added_v_id IN (".$brnd.") OR (`pd_pc_id` IN (".$all.") OR `pd_psc_id` IN (".$all."))) AND pd_quantity >= 1 AND pd_status = 1	";
//         } else {
//             $finalData = "SELECT DISTINCT * FROM `product_details_hm` WHERE pd_added_v_id IN (".$brnd.") OR  (`pd_pc_id` IN (".$all.") OR `pd_psc_id` IN (".$all.")) AND pd_status = 1";
//           // $stock = 0;
            
//         }
       
//       // get_offer_products
// 	    if($filter['offers']==1){
// 	        $query = $this->db->query($finalData)->result_array();
	        
// 	        foreach($query as $q){
	           
// 	             $checkOffers = $this->MedicalMall_model->get_product_offer($q['pd_id']);
// 	           //  print_r($q['pd_id']); echo "<br>";
// 	             if(!empty($checkOffers)){
// 	                 $finalDataArray[] = $checkOffers;
// 	             } 
// 	           //  die();
	            
// 	        }
	        
//             // die();
//             $filtered =  $finalDataArray[0];
            
//         } else {
//             $filtered = $this->db->query($finalData)->result_array();
//         }
	     
	     
// 	    if(!empty($filter['price_low_high'])){
// 	        $price = array();
// 	        foreach ($filtered as $key => $row)
//                 {
//                     $price[$key] = $row['pd_vendor_price'];
//                 }
// 	        if($filter['price_low_high'] == 1){
//                 array_multisort($price, SORT_ASC, $filtered);
// 	        } else {
// 	            array_multisort($price, SORT_DESC, $filtered);
// 	        }
	        
            
//     	   //   print_r($filtered); die();
    	      
	      
// 	    } else {
// 	        $filtered = $filtered;
// 	    }  
	    
// 	    if(!empty($filter['sort_a_z'])){
// 	        $letter = array();
// 	        foreach ($filtered as $key => $row)
//                 {
//                     $letter[$key] = $row['pd_name'];
//                 }
// 	        if($filter['sort_a_z'] == 1){
//                 array_multisort($letter, SORT_ASC, $filtered);
// 	        } else if($filter['sort_a_z'] == 2){
// 	            array_multisort($letter, SORT_DESC, $filtered);
// 	        } else {
// 	            $filtered = $filtered;
// 	        }
// 	    } else {
// 	        $filtered = $filtered;
// 	    }  
	    
	      
// 	      return $filtered; 
       
// 	}

public function add_filters($filter){
    
    
    
       $bestSeller = $filtered = $brnd = $finalDataArray = $cats = $subCatsProducts = $subCatsProducts1 = $allSubcategories =$finalData =  array();
        // $finalData['size'] = array();
        // $finalData['offers'] = array();
        // $finalData['availability'] = array();
        
        
        if(!empty($filter['category_ids'])){
            
            $catFilterArray = explode(",",$filter['category_ids']);
            $allSubcategories[] = $catFilterArray;
            foreach($catFilterArray as $catId){
    
            // get subcategories
                $subCatsProducts1 =  $this->MedicalMall_model_2->get_subcat($catId);
                
                if(sizeof($subCatsProducts1) > 0){ 
                            $subCatsProducts = call_user_func_array("array_merge", $subCatsProducts1);
        
                    
                } 
                $allSubcategories[] = $subCatsProducts;
                
            }
            $allSubcategories = call_user_func_array("array_merge", $allSubcategories);
            $all=implode(",",$allSubcategories);
        }
        else {
           $allcats =  $this->db->query("SELECT id FROM categories WHERE 1")->result_array();
        //   print_r($allcats);
           foreach($allcats as $cat){
               $cats[] = $cat['id'];
           }
           
        $all = implode(",",$cats);
       
        }
        
        if(!empty($filter['brands'])){
            $brnd = $filter['brands'];
        } else {
            $allBrands =  $this->db->query("SELECT `v_id` FROM `vendor_details_hm` WHERE `v_status` = 1")->result_array();
        //   print_r($allBrands);
           foreach($allBrands as $brand){
               $brnd[] = $brand['v_id'];
           }
           
        $brnd = implode(",",$brnd);
            // $brnd = 0;
        }
        
        if($filter['in_stock']>=1){
            $stock = $filter['in_stock'];
            $finalData = "SELECT DISTINCT * FROM `product_details_hm` WHERE (pd_added_v_id IN (".$brnd.") AND (`pd_pc_id` IN (".$all.") OR `pd_psc_id` IN (".$all."))) AND pd_quantity >= 1 AND pd_status = 1	";
        } else {
            $finalData = "SELECT DISTINCT * FROM `product_details_hm` WHERE pd_added_v_id IN (".$brnd.") AND  (`pd_pc_id` IN (".$all.") OR `pd_psc_id` IN (".$all.")) AND pd_status = 1";
           // $stock = 0;
            
        }
      
    //   is_bestseller
    
        
    
       // get_offer_products
	    if($filter['offers']==1){
	        $query = $this->db->query($finalData)->result_array();
	        
	        foreach($query as $q){
	           
	             $checkOffers = $this->MedicalMall_model->get_product_offer($q['pd_id']);
	           //  print_r($q['pd_id']); echo "<br>";
	             if(!empty($checkOffers)){
	                 $finalDataArray[] = $checkOffers;
	             } 
	           //  die();
	            
	        }
	        
            // die();
            $filtered =  $finalDataArray[0];
            
        } else {
            $filtered = $this->db->query($finalData)->result_array();
        }
	     
	     
	    if(!empty($filter['price_low_high'])){
	        $price = array();
	        foreach ($filtered as $key => $row)
                {
                    if($row['pd_status']==1) {
                        $price[$key] = $row['pd_vendor_price'];
                    }
                }
	        if($filter['price_low_high'] == 1){
                array_multisort($price, SORT_ASC, $filtered);
	        } else {
	            array_multisort($price, SORT_DESC, $filtered);
	        }
	        
            
    	   //   print_r($filtered); die();
    	      
	      
	    } else {
	        $filtered = $filtered;
	    }  
	    
	    if(!empty($filter['sort_a_z'])){
	        $letter = array();
	        foreach ($filtered as $key => $row)
                {
                    if($row['pd_status']==1) {
                        $letter[$key] = $row['pd_name'];
                    }
                }
	        if($filter['sort_a_z'] == 1){
                array_multisort($letter, SORT_ASC, $filtered);
	        } else if($filter['sort_a_z'] == 2){
	            array_multisort($letter, SORT_DESC, $filtered);
	        } else {
	            $filtered = $filtered;
	        }
	    } else {
	        $filtered = $filtered;
	    }  
	    
	   // price_max price_min
	    
	      
	      
	    if(!empty($filter['price_max'])){
	        $maxFinal = $priceMax= $maxArray = array();
	        $forMaxPrice = $filtered;
	        $maxPrice = $filter['price_max'];
	       // echo $maxPrice; die();
	        foreach ($filtered as $key => $row){
                    $priceMax[$key] = $row['pd_vendor_price'];
                    if($row['pd_vendor_price'] <= $maxPrice){
                        // echo $row['pd_vendor_price']."<br>";
                        $maxFinal[] = $row;
                       
                    }  
            }
            
            $filtered = $maxFinal;
            
	    } else {
	        $filtered = $filtered;
	    } 
	   // print_r($filtered); die();
	    
	     
	    if(!empty($filter['price_min'])){
	        $minFinal = $priceMin= $minArray = array();
	        $forMinPrice = $filtered;
	        $minPrice = $filter['price_min'];
	       // echo $maxPrice; die();
	        foreach ($filtered as $key => $row){
                    $priceMin[$key] = $row['pd_vendor_price'];
                    if($row['pd_vendor_price'] >= $minPrice){
                        // echo $row['pd_vendor_price']."<br>";
                        $minFinal[] = $row;
                       
                    }  
            }
            
            $filtered = $minFinal;
            
	    } else {
	        $filtered = $filtered;
	    } 
	    
	    
	    if($filter['is_bestseller']==1){
	        foreach($filtered as $filter){
	            if($filter['pd_best_seller'] > 0){
	                $bestSeller[] = $filter;
	            }
	           
	        }
            $filtered = $bestSeller;  
        }
        
        
	      return $filtered; 
       
	}
	
// 	brand_categories

	
	public function get_brand_subcat($dataSubcatId,$brand_id){
	    $data = array();
	    $results = $this->db->query("SELECT * FROM `categories` WHERE `parent_cat_id`='$dataSubcatId'");

	    foreach($results->result_array() as $result){
	        
	        $catId = $result['id'];
	        
            $result_sub = $this->db->query("SELECT * FROM `categories` WHERE `parent_cat_id` = '$catId'");
            $prods = $this->db->query("SELECT * from product_details_hm WHERE (pd_pc_id = '$catId' OR pd_psc_id = '$catId') AND pd_added_v_id = '$brand_id'")->result_array();
            $data_subcat_all = [] ;
            // $data_subcat_all[] = $catId;
            $data_subcat_all[] = $result;
            //  print_r(sizeof($prods)); die();
            if(sizeof($prods) != 0){
                foreach($result_sub->result_array() as $subcat){
                    
                    $dataSubcatIdInSub = $subcat['id'];
                    $results1 = $this->MedicalMall_model_2->get_subcat($dataSubcatIdInSub);
                   
                    $data_subcat_all[] = $subcat;
                    
                     
                    // $results1 = $this->MedicalMall_model_2->getProductByCategories($subcat['id'])->result_array();;
                    
                    
                }
                
               
	        $data[] = $data_subcat_all;
            }
	    }
 	  
	    return $data;
	}
	
	
// 	public function brand_categories($brand_id){
// 	    $cats = $subcatIds = $catIds = array();
// 	    $query = "SELECT `pd_pc_id`,`pd_psc_id`,`pd_added_v_id`,`cat_name` FROM `product_details_hm` pd JOIN `categories` c ON pd_pc_id = id WHERE `pd_added_v_id` = ".$brand_id;
// 	    $rows = $this->db->query($query);
	    
// 	    foreach($rows->result_array() as $row){
// 	        $catIds[] = $row['pd_pc_id'];
// 	        $subcatIds[] = $row['pd_psc_id'];
// 	    }
// 	    $catIds = array_unique($catIds);
// 	    $subcatIds = array_unique($subcatIds);
// 	   // print_r($subcatIds);
// 	    foreach($catIds as $catId){
// 	       // echo $catId;
// 	        $cat = $this->db->query("SELECT * FROM categories WHERE `id` = $catId")->row_array();
// 	        $getSubcats = $this->MedicalMall_model_2->get_subcat_brands($brand_id);
// 	        print_r($getSubcats); 
// 	        foreach($getSubcats as $getSubcat){
// 	            print_r($getSubcats); 
// 	        }
// 	        die();
// 	        $cat['categories'] = $getSubcats;
// 	        $cats[] = $cat;
// 	    }
// 	    print_r($getSubcats);
// 	    die();
// 	    return $cats;
	    
// 	}

	public function brand_categories($brand_id){
	    $data = array();
	    $results = $this->db->query("SELECT * FROM `categories` WHERE `parent_cat_id`='0'");
	   // print_r($results->result_array()); die();
	    foreach($results->result_array() as $result){
	        
	        $catId = $result['id'];
	        
            $result_sub = $this->db->query("SELECT * FROM `categories` WHERE `parent_cat_id` = '$catId'");
            
            $prods = $this->db->query("SELECT * from product_details_hm WHERE pd_pc_id = '$catId' AND pd_added_v_id = '$brand_id'");
		    
		    $pro = $prods->result_array();
		    if(sizeof($pro) != 0){
		        
		    
            $data_subcat_all = [] ;
            
            
            foreach($result_sub->result_array() as $subcat){
                // $data_subcat[] = $subcat;
                $dataSubcatId = $subcat['id'];
                $results1 = $this->MedicalMall_model_2->get_brand_subcat($dataSubcatId,$brand_id);
                //
                if(sizeof($results1) > 0){
                    $dataSubcat['psc_id'] = $subcat['id'];
                    $dataSubcat['psc_name'] = $subcat['cat_name'];
                    $dataSubcat['psc_photo'] = $subcat['photo'];
                    $dataSubcat['parent_cat_id'] = $subcat['parent_cat_id'];
                    $dataSubcat['subcategory'] = $results1;
                    $data_subcat_all[] = $dataSubcat;
                }
                
                // $dataSubcat['psc_short_desc'] = $subcat['psc_short_desc'];
                // $dataSubcat['psc_long_desc'] = $subcat['psc_long_desc'];
               
            }
        //      print_r($data_subcat_all); 
	       
	       //die();
	       
	        $cat['pc_id'] = $result['id'];
	        $cat['pc_name'] = $result['cat_name'];
	        $cat['pc_photo'] = $result['photo'];
            // $cat['pc_short_desc'] = $result['pc_short_desc'];
            // $cat['pc_long_desc'] = $result['pc_long_desc'];
	        $cat['subcategory'] = $data_subcat_all;
	        $data[] = $cat;
	    }
	    }
	        $res = array(
                "status" => 200,
                "message" => "success",
                "data" => $data 
         );
	    return $res;
	}
	
	
		public function brand_categories1($brand_id){
	    $data = array();
	    $results = $this->db->query("SELECT * FROM `categories` WHERE `parent_cat_id`='0'");
	   // print_r($results->result_array()); die();
	    foreach($results->result_array() as $result){
	        
	        $catId = $result['id'];
	        
            $result_sub = $this->db->query("SELECT * FROM `categories` WHERE `parent_cat_id` = '$catId'");
            
            $prods = $this->db->query("SELECT * from product_details_hm WHERE pd_pc_id = '$catId' AND pd_added_v_id = '$brand_id'");
		    
		    $pro = $prods->result_array();
		    if(sizeof($pro) != 0){
		        
		    
            $subCatsArray = $data_subcat_all = [] ;
            
            
            foreach($result_sub->result_array() as $subcat){
                // $data_subcat[] = $subcat;
                $dataSubcatId = $subcat['id'];
                $results1 = $this->MedicalMall_model_2->get_brand_subcat($dataSubcatId,$brand_id);
                //
                if(sizeof($results1) > 0){
                       $subCatsArray = array();
                    $dataSubcat['psc_id'] = $subcat['id'];
                    $dataSubcat['psc_name'] = $subcat['cat_name'];
                    $dataSubcat['psc_photo'] = $subcat['photo'];
                    $dataSubcat['parent_cat_id'] = $subcat['parent_cat_id'];
                   
                  
                    // foreach($results1 as $result ){
                    //     $subCatsArray[] = $result[0];
                    // }
                    foreach($results1 as $res ){
                        $subCatsArray[] = $res[0];
                    }
                    
                    $dataSubcat['subcategory'] = $subCatsArray;
                    // print_r( $dataSubcat['subcategory']);
                    // die();
                    $data_subcat_all[] = $dataSubcat;
                }
                
                // $dataSubcat['psc_short_desc'] = $subcat['psc_short_desc'];
                // $dataSubcat['psc_long_desc'] = $subcat['psc_long_desc'];
               
            }
        //      print_r($data_subcat_all); 
	       
	       //die();
	       
	        $cat['pc_id'] = $result['id'];
	        $cat['pc_name'] = $result['cat_name'];
	        $cat['pc_photo'] = $result['photo'];
            // $cat['pc_short_desc'] = $result['pc_short_desc'];
            // $cat['pc_long_desc'] = $result['pc_long_desc'];
	        $cat['subcategory'] = $data_subcat_all;
	        $data[] = $cat;
	    }
	    }
	        $res = array(
                "status" => 200,
                "message" => "success",
                "data" => $data 
         );
	    return $res;
	}
	
    public function get_product_image($pd_id){
	    
	    $results = $this->db->query("SELECT `pd_photo_1` FROM `product_details_hm` WHERE `pd_id` = '$pd_id'")->row_array();
	   // print_r("SELECT `pd_photo_1` FROM `product_details_hm` WHERE `pd_id` = '$pd_id'"); die();
	   return $results; 
		    
	}
	
// 	get_variable_product_image
	
	public function get_variable_product_image($pd_id){
	    
	    $results = $this->db->query("SELECT `image` FROM `variable_products_hm` WHERE `id` = '$pd_id'")->row_array();
	   // print_r("SELECT `pd_photo_1` FROM `product_details_hm` WHERE `pd_id` = '$pd_id'"); die();
	   return $results; 
		    
	}
	
	
	// 	pd_best_seller 
	public function quantity_bestseller($pd_id,$ordered_quantity){
	    $new_pd_best_seller = 0;
	    $results = $this->db->query("SELECT `pd_quantity`,`pd_best_seller`  FROM `product_details_hm` WHERE `pd_id` = '$pd_id'")->row_array();
	    $pd_quantity = $results['pd_quantity'];
	    $pd_best_seller = $results['pd_best_seller'];
	    if($pd_quantity > 0){
	       $newQuantity = $pd_quantity - $ordered_quantity;
	       $this->db->query("UPDATE `product_details_hm` SET `pd_quantity` = '$newQuantity' WHERE `pd_id` = '$pd_id';");
	       
	    } 
	    
	    if($pd_best_seller == null){
	        $this->db->query("UPDATE `product_details_hm` SET `pd_best_seller` = 1 WHERE `pd_id` = '$pd_id';");
	    } else {
	         $new_pd_best_seller = $pd_best_seller + 1;
	         $this->db->query("UPDATE `product_details_hm` SET `pd_best_seller` = '$new_pd_best_seller' WHERE `pd_id` = '$pd_id';");
	    }
	   
	    
	  
	    
	   return 1; 
		    
	}
	
// 	variable_quantity_bestseller
	
	
	public function variable_quantity_bestseller($pd_id,$pd_variable_id,$ordered_quantity){
	    $new_pd_best_seller = 0;
	    $results = $this->db->query("SELECT `pd_quantity`,`pd_best_seller`  FROM `product_details_hm` WHERE `pd_id` = '$pd_id'")->row_array();
	    $results1 = $this->db->query("SELECT *  FROM `variable_products_hm` WHERE `id` = '$pd_variable_id'")->row_array();
	    $pd_quantity = $results['pd_quantity'];
	    $pd_best_seller = $results['pd_best_seller'];
	     $pd_variable_quantity = $results1['quantity'];
	   // if($pd_quantity > 0){
	   //    $newQuantity = $pd_quantity - $ordered_quantity;
	   //    $this->db->query("UPDATE `product_details_hm` SET `pd_quantity` = '$newQuantity' WHERE `pd_id` = '$pd_id';");
	   // } 
	    
	    if($pd_variable_quantity > 0){
	       $newQuantity = $pd_variable_quantity - $ordered_quantity;
	       $this->db->query("UPDATE `variable_products_hm` SET `quantity` = '$newQuantity' WHERE `id` = '$pd_variable_id';");
	    } 
	    
	    
	    
	    if($pd_best_seller == null){
	        $this->db->query("UPDATE `product_details_hm` SET `pd_best_seller` = 1 WHERE `pd_id` = '$pd_id';");
	    } else {
	         $new_pd_best_seller = $pd_best_seller + 1;
	         $this->db->query("UPDATE `product_details_hm` SET `pd_best_seller` = '$new_pd_best_seller' WHERE `pd_id` = '$pd_id';");
	    }
	   
	    
	  
	    
	   return 1; 
		    
	}




// 	get ledger
 public function get_user_ledger_details($user_id){
        $debit_list =array();
        $point_list =array();
        $credit_list =array();
        $failure_list = array();
        $bachat_list = array();
        $credit_list_trans = array();
        //echo "SELECT * FROM user_ledger_balance WHERE user_id='$user_id'";
        
          
        $query = $this->db->query("SELECT * FROM user_ledger_balance WHERE user_id='$user_id'");

        $row = $query->row();
  
        $num_row = $query->num_rows();
     
        if ($num_row>0)
        {
                $ledger_balance =  $row->ledger_balance;
                $lock_amount =  $row->lock_amount;
        }else{
                $ledger_balance = 0;
                $lock_amount =  0;
        }
       
        
        $pnts_rate = $this->db->select('rate')->where('id', 1)->get('points_rate')->row();
        if (!empty($pnts_rate)) {
            $rate = $pnts_rate->rate;
        } else {
            $rate = "";
        }    
        
        $query_point = $this->db->query("SELECT * FROM user_ledger WHERE user_id='$user_id' AND (trans_type='4' or trans_type='5') GROUP BY order_id order by id DESC");

        $count_point = $query_point->num_rows();
        // print_r($count_point); die();
        //$ttl_pointds = array();
        if ($count_point > 0) {

            foreach ($query_point->result_array() as $row) {
               $ttl_pointds = array();
                $listing_id = $row['listing_id'];
                
                //added for vendor details 
                $listing_id = $row['listing_id'];
                
                $doctor_details = $this->db->query("SELECT * FROM users WHERE id='$listing_id'");
                $details = $doctor_details->row();
                $doctor_count = $doctor_details->num_rows();
                if($doctor_count>0)
                {
                    $doctor_name = $details->name;
                }
                else
                {
                    $doctor_name = "";
                }
                
                 $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                //end
            
                
                $order_id      = $row['order_id'];
                $trans_type      = $row['trans_type'];
                 
                //echo "SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') order by id DESC";
                // $query_point_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') order by id DESC");
                $query_point_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') and user_id='$user_id' order by id DESC");
                $count_query_point_trans = $query_point_trans->num_rows();
                
                
                //CHECK INVOICE IS ADDED OR NOT
                $query_inovce = $this->db->query("SELECT * FROM tbl_invoice WHERE order_id='$order_id'");
                $count_inovce = $query_inovce->num_rows();
                if($count_inovce > 0){
                    $invoice = '1';
                }else{
                    $invoice = '0';
                }
                 
                if ($count_query_point_trans > 0) {
                    $points_list_trans = array();
                    foreach ($query_point_trans->result_array() as $row_nest) {
                            
                            if($row_nest['trans_type']  == '5'){   
                                $trans_id       = $row_nest['trans_id'];
                                $trans_type     = $row_nest['trans_type'];
                                $trans_mode     = $row_nest['trans_mode'];
                                $amount        = $row_nest['amount'];
                                $amount_saved        = $row_nest['amount_saved'];
                                $trans_time     = $row_nest['trans_time'];
                                
                                
                                $trans_time = date('m/d/Y h:i a', strtotime($trans_time));
                            
                                array_push($ttl_pointds,$row_nest['amount']);
                                $points_list_trans[] = array(
                                    'order_id' => $order_id,
                                    'trans_id' => $trans_id,
                                    'amount' => $amount-$amount_saved,
                                    'trans_mode' => $trans_mode,
                                    'trans_time'=>$trans_time
                                ); 
                            }else if($row_nest['trans_type']  == '4'){
                                $trans_id       = $row_nest['trans_id'];
                                $trans_type     = $row_nest['trans_type'];
                                $trans_mode     = $row_nest['trans_mode'];
                                $amount        = $row_nest['amount'];
                                $amount_saved        = $row_nest['amount_saved'];
                                $trans_time     = $row_nest['trans_time'];
                                $trans_time = date('m/d/Y h:i a', strtotime($trans_time));
                                
                                array_push($ttl_pointds,$row_nest['amount']);
                                $points_list_trans[] = array(
                                    'order_id' => $order_id,
                                    'trans_id' => $trans_id,
                                    'amount' => $amount-$amount_saved,
                                    'trans_mode' => $trans_mode,
                                    'trans_time'=>$trans_time,
                                    'invoice' =>$invoice
                                );
                            }
                        
                    }
                      // print_r($ttl_pointds); 
                    
                }
                if($row_nest['trans_type']  == '5'){        
                     $convert_rs = array_sum($ttl_pointds);
                     $point_list[] = array(
                         'vendor_image' => $vendor_image,
                         'vendor_name' =>$doctor_name,
                        'order_id' => $order_id,
                        'trans_type'=>$trans_type,
                        //'total_points'=>$amount-$amount_saved,
                        'total_points'=>$convert_rs*$rate,
                        'point_rupee'=>array_sum($ttl_pointds),
                        'trans_details'=>$points_list_trans,
                         'invoice' =>$invoice
                    ); 
                }else if($row_nest['trans_type']  == '4'){
                    $convert_rs = round(array_sum($ttl_pointds)/$rate,2);
                     $point_list[] = array(
                         'vendor_image' => $vendor_image,
                         'vendor_name' =>$doctor_name,
                        'order_id' => $order_id,
                        'trans_type'=>$trans_type,
                        'total_points'=>$amount-$amount_saved,
                        'point_rupee'=>$convert_rs,
                        'trans_details'=>$points_list_trans,
                         'invoice' =>$invoice
                        
                    ); 
                }
               
            }
           
           
        }      
        
        
        $query_credit = $this->db->query("SELECT * FROM user_ledger WHERE user_id='$user_id' and (trans_type='0' or trans_type='1' or trans_type='2') GROUP BY order_id order by id DESC");
        
        $count_credit = $query_credit->num_rows();
        if ($count_credit > 0) {

            foreach ($query_credit->result_array() as $row) {
                
                $order_id      = $row['order_id'];
                $listing_id = $row['listing_id'];
                
               //added for vendor details 
                $listing_id = $row['listing_id'];
                
                $doctor_details = $this->db->query("SELECT * FROM users WHERE id='$listing_id'");
                $details = $doctor_details->row();
                $doctor_count = $doctor_details->num_rows();
                if($doctor_count>0)
                {
                    $doctor_name = $details->name;
                }
                else
                {
                    $doctor_name = "";
                }
                
                 $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                //end

                $query_debit_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='0' or trans_type='1' or trans_type = '2') order by id DESC");
                $count_query_debit_trans = $query_debit_trans->num_rows();
                
                //CHECK INVOICE IS ADDED OR NOT
                //echo "SELECT * FROM tbl_invoice WHERE order_id='$order_id'";
                $query_inovce = $this->db->query("SELECT * FROM tbl_invoice WHERE order_id='$order_id'");
                $count_inovce = $query_inovce->num_rows();
                if($count_inovce > 0){
                    $invoice = '1';
                }else{
                    $invoice = '0';
                }
                 
                if ($count_query_debit_trans > 0) {
                    $credit_list_trans = array();
                    foreach ($query_debit_trans->result_array() as $row_nest) {
                        
                            $trans_id       = $row_nest['trans_id'];
                            $trans_type     = $row_nest['trans_type'];
                            $trans_mode     = $row_nest['trans_mode'];
                             $amount         = $row_nest['amount'];
                            $amount_saved        = $row_nest['amount_saved'];
                            
                          
                           
                            $trans_time     = $row_nest['trans_time'];
                            $trans_time = date('m/d/Y h:i a', strtotime($trans_time));
                            $credit_list_trans[] = array(
                                'order_id' => $order_id,
                                'trans_id' => $trans_id,
                                'trans_type' => $trans_type,
                                'trans_mode' => $trans_mode,
                                'amount' => $amount-$amount_saved,
                                'trans_time'=>$trans_time
                            ); 
                         
                    }
                        
                    
                }
                 
                $credit_list[] = array(
                    'vendor_image' => $vendor_image,
                     'vendor_name' =>$doctor_name,
                    'order_id' => $order_id,
                    'trans_details'=>$credit_list_trans,
                    'invoice' =>$invoice
                ); 
                
                
            }
        }  
        
        
        
       
        $query_failure = $this->db->query("SELECT * FROM user_ledger WHERE user_id='$user_id' AND trans_type = '2' order by id DESC");
        $count_failure = $query_failure->num_rows();
        if ($count_failure > 0) {

            foreach ($query_failure->result_array() as $row) {
                //added for vendor details 
                $listing_id = $row['listing_id'];
                
                $doctor_details = $this->db->query("SELECT * FROM users WHERE id='$listing_id'");
                $details = $doctor_details->row();
                $doctor_count = $doctor_details->num_rows();
                if($doctor_count>0)
                {
                    $doctor_name = $details->name;
                }
                else
                {
                    $doctor_name = "";
                }
                
                 $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                //end
                $order_id      = $row['order_id'];
                $trans_id      = $row['trans_id'];
                $trans_type    = $row['trans_type'];
                $amount        = $row['amount'];
                
                //CHECK INVOICE IS ADDED OR NOT
                
                $query_inovce = $this->db->query("SELECT * FROM tbl_invoice WHERE order_id='$order_id'");
                $count_inovce = $query_inovce->num_rows();
                if($count_inovce > 0){
                    $invoice = '1';
                }else{
                    $invoice = '0';
                }
                
                
                
                $failure_list[] = array(
                    'vendor_image' => $vendor_image,
                    'vendor_name' =>$doctor_name,
                    'order_id' => $order_id,
                    'trans_id' => $trans_id,
                    'trans_type' => $trans_type,
                    'amount' => $amount,
                     'invoice' =>$invoice
                );
            }
           
           
        }
     
     
     //added by zak for to show  invoice list in ledger details 
     
     $query_invoice_details = $this->db->query("SELECT * FROM tbl_invoice WHERE user_id='$user_id' ");
     $count_invoice_details = $query_invoice_details->num_rows();
     if($count_invoice_details > 0){
          foreach ($query_invoice_details->result_array() as $row) {
              $id =$row['id'];
              $order_id = $row['order_id'];
              $invoice_no = $row['invoice_no'];
              $comment =$row['comment'];
              $invoice_date = $row['invoice_date'];
              $query_attachment = $this->db->query("select * from tbl_invoice_attachment WHERE invoice_id='$id'");
              
              $attachment_file = array();
              foreach ($query_attachment->result_array() as $row) {
                  $file_name = $row['file_name'];
              
              $attachment_file[] = array(
                        'https://medicalwale.s3.amazonaws.com/images/invoice_images/'.$file_name
                  );
              }
              $invoice_details[] = array(
                   'order_id' => $order_id,
                   'invoice_no' => $invoice_no,
                   'comment' => $comment,
                   'invoice_date' => $invoice_date,
                   'attachment' =>$attachment_file
                  );
          }
     }
     else
     {
         $invoice_details = array();
     }
        
        $query_bachat = $this->db->query("SELECT * FROM user_ledger WHERE user_id='$user_id' AND (trans_type='1') AND amount_saved!='0' GROUP BY order_id order by id DESC");

        $count_bachat = $query_bachat->num_rows();
        // print_r($count_point); die();
        //$ttl_pointds = array();
        if ($count_bachat > 0) {

            foreach ($query_bachat->result_array() as $row) {
                $ttl_bachat = array();
                $order_id      = $row['order_id'];
                $trans_type      = $row['trans_type'];
                 $discount = "";
                 
                 
                 $listing_id = $row['listing_id'];
                
               //added for vendor details 
                $listing_id = $row['listing_id'];
                
                $doctor_details = $this->db->query("SELECT * FROM users WHERE id='$listing_id'");
                $details = $doctor_details->row();
                $doctor_count = $doctor_details->num_rows();
                if($doctor_count>0)
                {
                    $doctor_name = $details->name;
                }
                else
                {
                    $doctor_name = "";
                }
                
                 $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                //end
                // $query_bachat_trans = $this->db->query();
                
                //CHECK INVOICE IS ADDED OR NOT
                $query_inovce = $this->db->query("SELECT * FROM tbl_invoice WHERE order_id='$order_id'");
                $count_inovce = $query_inovce->num_rows();
                if($count_inovce > 0){
                    $invoice = '1';
                }else{
                    $invoice = '0';
                }
                 
                //echo "SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') order by id DESC";
                // $query_point_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') order by id DESC");
                $query_bachat_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='1') order by id DESC");
                
                $count_query_bachat_trans = $query_bachat_trans->num_rows();
                 //print_r($query_bachat_trans->result_array());
                if ($count_query_bachat_trans > 0) {
                    $bachat_list_trans = array();
                    foreach ($query_bachat_trans->result_array() as $row_nest) {
                            
                        if(!empty($row_nest['amount_saved'])){
                            $trans_id       = $row_nest['trans_id'];
                            $trans_type     = $row_nest['trans_type'];
                            $trans_mode     = $row_nest['trans_mode'];
                            $amount         = $row_nest['amount'];
                            $amount_saved   = $row_nest['amount_saved'];
                            $discount       = $row_nest['discount'];
                            $trans_time     = $row_nest['trans_time'];
                               $trans_time = date('m/d/Y h:i a', strtotime($trans_time));
                                
                            array_push($ttl_bachat,$row_nest['amount']);
                            $bachat_list_trans[] = array(
                                'order_id' => $order_id,
                                'trans_id' => $trans_id,
                                'trans_mode' => $trans_mode,
                                'trans_time'=>$trans_time,
                                'invoice' =>$invoice
                            ); 
                        }
                        
                    }
                    // print_r($ttl_pointds); 
                    
                }
                
                $convert_rs = round(array_sum($ttl_bachat)/$rate);
                 $bachat_list[] = array(
                     'vendor_image' => $vendor_image,
                     'vendor_name' =>$doctor_name,
                    'order_id' => $order_id,
                    'trans_type'=>$trans_type,
                    // 'total_points'=>array_sum($ttl_bachat),
                    // 'point_rupee'=>$convert_rs,
                    'amount' => $amount,
                    'amount_saved' => $amount_saved,
                    'discount' => $discount,
                    'trans_details'=>$bachat_list_trans,
                    'invoice' =>$invoice
                ); 
               
            }
           
           
        }
       // echo "SELECT SUM(points) as total_points FROM user_points WHERE user_id='$user_id' and status='active' order by id DESC";
                $query = $this->db->query("SELECT SUM(points) as total_points FROM user_points WHERE user_id='$user_id' and status='active' order by id DESC");

                $rows = $query->row();
                
                if (isset($rows))
                {
                        $total_points =  $rows->total_points;
                }else{
                        $total_points = 0;
                }
     
        $ledger_details = array();
        $ledger_details['balance_sheet'] =  $credit_list;
        $ledger_details['points']   =  $point_list;
        $ledger_details['failure'] =  $failure_list;
        $ledger_details['bachat_card'] =  $bachat_list;
      //  if($ledger_balance == $lock_amount)
        $ledger_details['balance'] = $ledger_balance;
        $ledger_details['total_points']   =  $total_points;
        $ledger_details['invoice_details'] = $invoice_details;
        return $ledger_details;
        
    }
    
    
    public function get_size_chart($size_id){
        $oldLabel = $oldSize = "";
        $data = array();
        $typeRow = $this->db->query("SELECT `size_type`,`gender` FROM `size_chart_hm` WHERE `id` = '$size_id'")->row_array();
        $typeName = $typeRow['size_type'];
        $gender = $typeRow['gender'];
        $allData = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `size_type` = '$typeName' and `gender` = '$gender' ORDER BY  `size_name` ASC ")->result_array();
        $i=0;
        foreach($allData as $d){
            
            if($i != 0){
                if($d['size_name'] == $oldLabel){ 
                    if($d['size_area'] == $oldSize){
                        $s['size_area'] = $d['size_area'];
                        $s['size'][] = $d;
                        $size['size_info'][] = $s;
                    }else{
                        $s = array();
                        $s['size_area'] = $d['size_area'];
                        $s['size'][] = $d;
                        $size['size_info'][] = $s;
                    }
                        
                   
                } else {
                        $sizeData[] = $size; 
                        $s = $size =$size['size_info'][] = $s['size'][]  = array();
                        $size['size_name'] = $d['size_name'];
                        $s['size_area'] = $d['size_area'];
                        $s['size'][] = $d;
                        $size['size_info'][] = $s;
                   
                       
                }
            } else {
                $size['size_name'] = $d['size_name'];
                $s['size_area'] = $d['size_area'];
                $s['size'][] = $d;
                $size['size_info'][] = $s;
                
            }
            $prevSize = $oldSize;
            $prevLabel = $oldLabel;
            $oldSize = $d['size_area'];
            $oldLabel = $d['size_name'];
            $i++;
        }
       // if($prevLabel == $oldLabel){
            $sizeData[] = $size;
    //    }
        $data = $sizeData;
       // print_r($data); die(); 
        return $data;
    }
	
// 	get_sponsored_advertisements
    public function get_sponsored_advertisements($user_id){
        $data = array();
        $today = date('Y-m-d');
        // $today = $datetime->format('Y-m-d');
        // print_r($today); die();
        $res = $this->db->query("SELECT * FROM `sponsored_advertisements` WHERE `status` = 1 AND `expiry` >= '$today' AND `vendor_type` = 34 ORDER BY RAND() ")->result_array();
        foreach($res as $r){
            $row['ad_id'] = $r['main_cat_id'];
            $row['ad_for'] = $r['ad_main_cat'];
            $row['id'] = $r['id'];
            $row['ad_image'] = $r['ad_image'];
            $row['listing_type'] = $r['vendor_type'];
            
            foreach($row as $key => $value){
                if($value == null){
                    $row[$key] = "";
                }
            }
            $data[] = $row; 
        }
        return $data;
    }
 }